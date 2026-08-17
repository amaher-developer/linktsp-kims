# KIMS Architecture

KIMS is a multi-branch F&B customer/loyalty platform sitting on top of Foodics
as the POS/catalog system of record. This document describes the Laravel
application's architecture as built so far.

## Structure: modules, single domain

The app is organized with [nwidart/laravel-modules](https://laravelmodules.com)
into eight self-contained modules under `Modules/`, mirroring `kims_schema.sql`'s
own section groupings rather than one flat `app/` tree:

| Module | Owns (tables / models) | Owns (code) |
|---|---|---|
| **Catalog** | branches, branch_hours, categories, products, branch_products, option_groups, options, product_option_groups | Customer-facing catalog browse endpoints, their resources |
| **Ordering** | customers, customer_identifiers, carts, cart_items, cart_item_options, orders, order_items, order_item_options, order_status_history, invoices, payments, refunds | Customer auth, profile, cart, checkout, orders; `CheckoutService`, `OrderNumberGenerator`, order/payment/refund enums, `CartPolicy`/`OrderPolicy`, `EnsureApiCustomer` |
| **Loyalty** | loyalty_rules, loyalty_accounts, rewards, reward_redemptions, loyalty_transactions + the two balance-integrity triggers | Customer-facing loyalty read endpoints, loyalty enums |
| **Staff** | roles, staff, staff_branches | Staff auth (API login) |
| **Cashier** | *(none — reads Staff/Ordering/Loyalty)* | Cashier API: identify-customer, verify invoice, award points; `EnsureApiCashier` |
| **Barista** | *(none — reads Ordering)* | Barista API: list/update branch orders; `EnsureApiBarista` |
| **Admin** | *(none — reads everything)* | The Blade back-office: dashboard + all admin CRUD controllers/views, staff session auth (Breeze-derived), `EnsureStaffIsManager` |
| **Integration** | integrations, external_references, integration_logs, foodics_webhooks | *(models/migrations only — Foodics work itself is still deferred)* |

Each module has its own `app/`, `database/migrations`, `database/factories`,
`database/seeders`, `routes/{web,api}.php`, `resources/views`, and `tests/`.
Namespace convention: `Modules\<Name>\Models\...`, `Modules\<Name>\Http\...`,
`Modules\<Name>\Database\{Migrations,Seeders,Factories}`. Cross-module
references (e.g. Admin's `BranchController` using Catalog's `Branch` model)
are normal `use` imports — laravel-modules is organizational, not a hard
isolation boundary. `php artisan migrate` and `php artisan test` discover
every module's migrations/tests automatically (`phpunit.xml` includes
`Modules/*/tests/{Feature,Unit}` and `Modules/*/app` in coverage).

**Everything lives on one domain**, split by URL prefix rather than by
subdomain:

| Prefix | Purpose | Stack |
|---|---|---|
| `/admin/*` | Manager/admin back-office | Blade + session auth (`staff` guard) |
| `/api/*` | Mobile/API clients (Customer App, Cashier Page, Barista App) | REST JSON, Sanctum token auth |

An earlier iteration tried subdomains (`admin.kims.test` / `api.kims.test`)
via `Route::domain(...)`, first on top of `gecche/laravel-multidomain`, later
without it. Both were removed in favor of this simpler single-domain,
prefix-based split — one less moving part (no per-domain `.env`/hosts-file
setup) for no loss of separation, since the prefix plus the guard/token
distinction already keeps admin and API traffic apart. `Modules/Admin/routes/web.php`
wraps everything in `Route::prefix('admin')`; each API module's
`routes/api.php` registers routes directly (the outer `/api` prefix and
`api` middleware group are added automatically by the module's
`RouteServiceProvider`) — no `/v1` version segment, by request.

## Authentication

Two independent auth mechanisms, deliberately not mixed:

- **Admin (`/admin/*`)** — Laravel Breeze (Blade stack), a dedicated `staff`
  session guard/provider (`Modules\Staff\Models\Staff` / `kims_staff`),
  restricted to `manager`/`admin` roles via the `staff.manager` middleware.
  No registration, password reset, or email verification — staff accounts
  are provisioned by an admin, not self-served.
- **API (`/api/*`)** — [Laravel Sanctum](https://laravel.com/docs/sanctum)
  personal access tokens (`Authorization: Bearer <token>`), no cookies/CSRF,
  no stateful SPA mode. Two Eloquent models issue tokens:
  - `Modules\Ordering\Models\Customer` (`kims_customers`) — Customer App
  - `Modules\Staff\Models\Staff` (`kims_staff`) — Cashier Page / Barista App
    (same model the admin panel uses, but authenticated over the API guard
    here)

  Login: `POST /api/auth/customer/login` (mobile + password) and
  `POST /api/auth/staff/login` (email + password). Logout revokes only
  the current token: `POST /api/auth/logout`.

## Authorization

- **Role gating** (which endpoint group a token may call at all) is three
  small middleware classes mirroring the admin panel's existing
  `EnsureStaffIsManager` pattern: `EnsureApiCustomer` (Ordering module),
  `EnsureApiCashier` (Cashier module), `EnsureApiBarista` (Barista module) —
  aliased globally as `api.customer` / `api.cashier` / `api.barista` in
  `bootstrap/app.php`. They type-check `$request->user()` against the model
  class (`Customer` vs `Staff`) and, for staff, the assigned `Role`.
- **Object-level ownership** (can *this* customer touch *this* cart/order)
  is Laravel Policies: `CartPolicy`, `OrderPolicy` (Ordering module),
  registered in the root `AppServiceProvider`. A customer can only
  view/modify their own cart and view their own orders — enforced via
  `$this->authorize(...)` in the relevant controllers, not by trusting
  route-model-binding scoping alone.
- Barista order access is additionally scoped to the staff member's
  assigned branches (`Staff::branches()`, from `kims_staff_branches`).

No custom permission/ability framework was introduced — this is Gates,
Policies, and role-check middleware, all stock Laravel.

## API versioning

None yet, deliberately — routes live directly under `/api` via each API
module's own `routes/api.php`. If a breaking change is ever needed, that's
a `/api/v2` prefix and a parallel set of module routes/controllers at that
point, not framework work done speculatively now.

## No extra layers

No repositories, DTOs, managers, handlers, abstract base controllers, or
generic CRUD engine. Controllers talk to Eloquent models directly.

**`Modules\Ordering\Services\CheckoutService`** is the one exception to "no
service layer": turning a cart into an order touches five tables (order,
order items, order item options, an initial status-history row, and a
payment record) inside one DB transaction — a genuine multi-step business
transaction, not a place to duplicate that logic across controllers.

## API Resources

All API responses go through `Illuminate\Http\Resources\Json\JsonResource`
subclasses — controllers never return raw Eloquent models. Each resource
explicitly lists its output fields, so passwords, `staff.remember_token`,
and `kims_integrations.credentials` (encrypted at rest) are never reachable
through the API regardless of what's eager-loaded. Nested resources
(`ProductResource` → `OptionGroupResource` → `OptionResource`, etc.) are only
included via `whenLoaded()`, so a listing endpoint doesn't accidentally ship
a full nested tree unless the controller actually eager-loaded it.

## Core flows

**Catalog & pricing** — `ProductResource` exposes a computed `price` /
`is_available` pair: branch-agnostic (`base_price`) unless the controller
eager-loads `branches` scoped to a single `branch_id` (from
`kims_branch_products`), in which case the branch's price
override/availability wins. This mirrors the schema decision that branch
pricing is an override, not a duplicate catalog.

**Cart → Checkout → Order** — `kims_carts.customer_id` stays `NOT NULL`
(no guest carts, per the business rules). `POST /cart` starts a fresh
active cart, abandoning (not deleting — `kims_cart_items` has no
`ON DELETE CASCADE`) any prior active one. Adding an item validates the
selected options against each option group's `min_select`/`max_select`/
`is_required` in the request's `withValidator()`. Checkout
(`CheckoutService`) snapshots product/option names onto the order the same
way the schema intends (`kims_order_items.product_name_en`, etc., so a
later Foodics rename doesn't rewrite historical receipts), and creates a
`kims_payments` row with `status = 'pending'` — Grab & Go / Dine In orders
are always paid online per the business rules, but the Paymob gateway call
itself is deferred, so this only records what a webhook would later confirm
and never fabricates a `'success'` state.

**Take Away never reaches `kims_orders`.** The Cashier API only ever reads
`kims_invoices` (assumed already present — populating it from Foodics is
part of the deferred integration) and writes `kims_loyalty_transactions`.

**Loyalty** — Balance integrity is entirely owned by the DB triggers
(`trg_loyalty_txn_before_insert` / `_after_insert`). The API never computes
or writes `kims_loyalty_accounts.balance` directly; `LoyaltyTransaction`'s
`creating()` hook only supplies the `0` placeholder for
`balance_before`/`balance_after` that the trigger then overwrites. The
cashier award endpoint picks the highest-priority currently-active
`kims_loyalty_rules` row (`LoyaltyRule::currentlyActive()`), refuses to
award twice for the same invoice, and refuses if no rule is configured —
it never accepts a manually typed amount, per the schema's protection rule.

**Barista** — Only the endpoints the current schema actually supports:
listing orders at the staff member's assigned branches and moving them
through the existing `kims_order_status_history` structure. No kitchen
station/ticket tables exist, so none were invented.

## Error responses

No custom exception framework. Laravel's default JSON error rendering is
used as-is (`shouldRenderJsonWhen` in `bootstrap/app.php` covers `api/*`
and any `Accept: application/json` request):

- `422` — validation failure: `{"message": "...", "errors": {"field": ["..."]}}`
- `401` — unauthenticated: `{"message": "Unauthenticated."}`
- `403` — forbidden (wrong role, or a Policy/`abort(403, ...)` denial):
  `{"message": "..."}`
- `404` — not found (including `abort(404, '...')` for "no active cart",
  etc.): `{"message": "..."}`

## Postman

`postman/KIMS-API.postman_collection.json` and
`postman/KIMS-API.postman_environment.json` — folders for Authentication,
Customer, Catalog, Cart, Orders, Loyalty, Cashier, and Barista. Login
requests auto-populate `{{customer_token}}` / `{{cashier_token}}` /
`{{barista_token}}` (and a generic `{{token}}` for the logout request) via
test scripts; catalog/cart/checkout requests chain
`{{branch_id}}`/`{{product_id}}`/`{{cart_item_id}}`/`{{order_id}}`
similarly. `{{base_url}}` defaults to `http://localhost:8000` — point it at
wherever the app is actually served locally.

## Testing

Pest, against a real MySQL database (`example_app_test`) — the schema's
loyalty triggers use `SIGNAL SQLSTATE` and row locking that SQLite can't
run, so `phpunit.xml` points `DB_CONNECTION`/`DB_DATABASE` at MySQL instead
of the default `:memory:` sqlite. Each module's `tests/Feature` covers its
own slice (auth, cross-role authorization, catalog response shape, cart
ownership, checkout, order ownership, cashier award flow including
double-award rejection, barista branch scoping, loyalty responses,
validation errors); 45/45 pass together.

## Deferred

Not built yet, on purpose:

- Foodics OAuth, catalog sync, order push, webhooks
- Paymob payment gateway integration (checkout creates a `pending` payment
  record only)
- Full refund processing (schema supports reading refund state; no
  refund-initiation endpoints yet)
- Full Barista/KDS UI (station routing, ticket printing — no such tables
  exist in the schema)
- Marketing/notification system
- `/api/v2`
