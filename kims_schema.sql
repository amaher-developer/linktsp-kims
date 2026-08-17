-- ============================================================================
-- KIMS DATABASE SCHEMA — MySQL 8 / InnoDB / utf8mb4
-- ============================================================================
-- Prefix `kims_` is a placeholder. Rename to match whatever the actual
-- Laravel module/domain is called in the codebase before running this.
--
-- KEY DESIGN DECISIONS (see chat history for the reasoning behind each):
--
-- 1. Catalog (branches/categories/products/options) is BRANCH-AGNOSTIC.
--    A product is one row, shared across branches. Per-branch price or
--    availability differences live in kims_branch_products, not by
--    duplicating the product. foodics_id sits directly on these tables
--    (high-read, sync-heavy) rather than going through a mapping table.
--
-- 2. Transactional/relationship entities (customers, orders, invoices,
--    staff) map to Foodics via kims_external_references instead — these
--    are low-frequency lookups, not hot paths, so the generic polymorphic
--    join is worth the flexibility there.
--
-- 3. Take Away NEVER creates a kims_orders row. It is created by the
--    cashier/POS entirely in Foodics. KIMS only verifies the invoice
--    (kims_invoices, order_id NULL) and credits kims_loyalty_transactions.
--    kims_orders is exclusively Grab & Go / Dine In, created by the
--    Customer App.
--
-- 4. Options/modifiers are stored ONCE, normalized (no duplicate JSON
--    snapshot column alongside the relational rows) to avoid the two
--    representations drifting out of sync.
--
-- 5. kims_order_items / kims_order_item_options snapshot product and
--    option names (not just price) so a later rename in Foodics doesn't
--    silently rewrite historical receipts.
--
-- 6. Staff "role" (cashier/barista/manager/admin) is a real FK to
--    kims_roles, not overloaded onto a `status` column — status stays a
--    plain active/inactive flag everywhere, consistently.
--
-- 7. No table selection, no saved payment methods, no cash/wallet on
--    Customer App payments (all Customer App orders are paid online) —
--    per confirmed business rules.
--
-- 8. kims_integrations.credentials is a TEXT column: encrypt at the
--    application layer (e.g. Laravel encrypted cast) — never store
--    Foodics client secret/tokens as plain JSON.
--
-- 9. No foodics_order_id / foodics_invoice_id columns on kims_orders /
--    kims_invoices — that would duplicate what kims_external_references
--    already owns. kims_orders.cart_id is NOT NULL (every order in this
--    table comes from a checked-out cart); kims_invoices.order_id is
--    UNIQUE (one order, at most one invoice — Take Away rows stay NULL,
--    and MySQL allows multiple NULLs under a UNIQUE key).
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- 1. CORE CATALOG (branch-agnostic, synced directly from Foodics)
-- ============================================================================

CREATE TABLE kims_branches (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    foodics_id      BIGINT UNSIGNED NOT NULL,
    name_en         VARCHAR(150) NOT NULL,
    name_ar         VARCHAR(150) NOT NULL,
    code            VARCHAR(50) NULL,
    address         TEXT NULL,
    city            VARCHAR(100) NULL,
    latitude        DECIMAL(10,7) NULL,
    longitude       DECIMAL(10,7) NULL,
    phone           VARCHAR(50) NULL,
    accepts_grab_go TINYINT(1) NOT NULL DEFAULT 1,
    accepts_dine_in TINYINT(1) NOT NULL DEFAULT 1,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    synced_at       DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_branches_foodics_id (foodics_id)
) ENGINE=InnoDB;

CREATE TABLE kims_branch_hours (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id   BIGINT UNSIGNED NOT NULL,
    day_of_week TINYINT UNSIGNED NOT NULL COMMENT '0=Sunday .. 6=Saturday',
    open_time   TIME NULL,
    close_time  TIME NULL,
    is_closed   TINYINT(1) NOT NULL DEFAULT 0,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_branch_hours_day (branch_id, day_of_week),
    CONSTRAINT fk_branch_hours_branch FOREIGN KEY (branch_id) REFERENCES kims_branches(id)
) ENGINE=InnoDB;

CREATE TABLE kims_categories (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    foodics_id  BIGINT UNSIGNED NOT NULL,
    parent_id   BIGINT UNSIGNED NULL,
    name_en     VARCHAR(150) NOT NULL,
    name_ar     VARCHAR(150) NOT NULL,
    image_url   VARCHAR(255) NULL,
    sort_order  INT NOT NULL DEFAULT 0,
    is_active   TINYINT(1) NOT NULL DEFAULT 1,
    synced_at   DATETIME NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_categories_foodics_id (foodics_id),
    INDEX idx_categories_parent (parent_id),
    CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES kims_categories(id)
) ENGINE=InnoDB;

CREATE TABLE kims_products (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    foodics_id      BIGINT UNSIGNED NOT NULL,
    category_id     BIGINT UNSIGNED NULL,
    sku             VARCHAR(100) NULL,
    name_en         VARCHAR(150) NOT NULL,
    name_ar         VARCHAR(150) NOT NULL,
    description_en  TEXT NULL,
    description_ar  TEXT NULL,
    image_url       VARCHAR(255) NULL,
    base_price      DECIMAL(12,2) NOT NULL,
    is_available    TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'global kill switch, overridden per branch by kims_branch_products',
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    synced_at       DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_products_foodics_id (foodics_id),
    INDEX idx_products_category (category_id),
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES kims_categories(id)
) ENGINE=InnoDB;

CREATE TABLE kims_branch_products (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id       BIGINT UNSIGNED NOT NULL,
    product_id      BIGINT UNSIGNED NOT NULL,
    price_override  DECIMAL(12,2) NULL,
    is_available    TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_branch_product (branch_id, product_id),
    CONSTRAINT fk_branch_products_branch FOREIGN KEY (branch_id) REFERENCES kims_branches(id),
    CONSTRAINT fk_branch_products_product FOREIGN KEY (product_id) REFERENCES kims_products(id)
) ENGINE=InnoDB;

-- ============================================================================
-- 2. PRODUCT OPTIONS / MODIFIERS (branch-agnostic, reusable across products)
-- ============================================================================

CREATE TABLE kims_option_groups (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    foodics_id  BIGINT UNSIGNED NULL,
    name_en     VARCHAR(150) NOT NULL,
    name_ar     VARCHAR(150) NOT NULL,
    min_select  INT UNSIGNED NOT NULL DEFAULT 0,
    max_select  INT UNSIGNED NOT NULL DEFAULT 1,
    is_required TINYINT(1) NOT NULL DEFAULT 0,
    sort_order  INT NOT NULL DEFAULT 0,
    is_active   TINYINT(1) NOT NULL DEFAULT 1,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_option_groups_foodics_id (foodics_id)
) ENGINE=InnoDB;

CREATE TABLE kims_options (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    option_group_id BIGINT UNSIGNED NOT NULL,
    foodics_id      BIGINT UNSIGNED NULL,
    name_en         VARCHAR(150) NOT NULL,
    name_ar         VARCHAR(150) NOT NULL,
    price_delta     DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    sort_order      INT NOT NULL DEFAULT 0,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_options_group_foodics (option_group_id, foodics_id),
    INDEX idx_options_group (option_group_id),
    CONSTRAINT fk_options_group FOREIGN KEY (option_group_id) REFERENCES kims_option_groups(id)
) ENGINE=InnoDB;

CREATE TABLE kims_product_option_groups (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id      BIGINT UNSIGNED NOT NULL,
    option_group_id BIGINT UNSIGNED NOT NULL,
    sort_order      INT NOT NULL DEFAULT 0,
    UNIQUE KEY uq_product_option_group (product_id, option_group_id),
    CONSTRAINT fk_pog_product FOREIGN KEY (product_id) REFERENCES kims_products(id),
    CONSTRAINT fk_pog_group FOREIGN KEY (option_group_id) REFERENCES kims_option_groups(id)
) ENGINE=InnoDB;

-- ============================================================================
-- 3. CUSTOMERS & IDENTIFICATION
-- ============================================================================

CREATE TABLE kims_customers (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name  VARCHAR(100) NOT NULL,
    last_name   VARCHAR(100) NOT NULL,
    mobile      VARCHAR(30) NOT NULL,
    email       VARCHAR(150) NULL,
    password    VARCHAR(255) NOT NULL COMMENT 'hashed at application layer (bcrypt/argon2)',
    is_active   TINYINT(1) NOT NULL DEFAULT 1,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_customers_mobile (mobile),
    UNIQUE KEY uq_customers_email (email)
) ENGINE=InnoDB;

-- Kept as its own table (not flattened onto kims_customers) so a lost or
-- reissued QR/barcode doesn't touch the customer row, and a customer can
-- hold more than one active identifier if the product ever needs that.
CREATE TABLE kims_customer_identifiers (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED NOT NULL,
    type        ENUM('qr','barcode') NOT NULL,
    value       VARCHAR(150) NOT NULL,
    is_primary  TINYINT(1) NOT NULL DEFAULT 1,
    is_active   TINYINT(1) NOT NULL DEFAULT 1,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_identifiers_value (value),
    INDEX idx_identifiers_customer (customer_id, is_active),
    CONSTRAINT fk_identifiers_customer FOREIGN KEY (customer_id) REFERENCES kims_customers(id)
) ENGINE=InnoDB;

-- ============================================================================
-- 4. STAFF & ROLES (moved ahead of orders/loyalty so both can FK into it)
-- ============================================================================

CREATE TABLE kims_roles (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(50) NOT NULL COMMENT 'cashier, barista, manager, admin',
    permissions JSON NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_roles_name (name)
) ENGINE=InnoDB;

CREATE TABLE kims_staff (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id     BIGINT UNSIGNED NOT NULL,
    name        VARCHAR(150) NOT NULL,
    phone       VARCHAR(50) NULL,
    email       VARCHAR(150) NULL,
    is_active   TINYINT(1) NOT NULL DEFAULT 1,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_staff_role (role_id),
    CONSTRAINT fk_staff_role FOREIGN KEY (role_id) REFERENCES kims_roles(id)
) ENGINE=InnoDB;

CREATE TABLE kims_staff_branches (
    staff_id    BIGINT UNSIGNED NOT NULL,
    branch_id   BIGINT UNSIGNED NOT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (staff_id, branch_id),
    CONSTRAINT fk_sb_staff FOREIGN KEY (staff_id) REFERENCES kims_staff(id),
    CONSTRAINT fk_sb_branch FOREIGN KEY (branch_id) REFERENCES kims_branches(id)
) ENGINE=InnoDB;

-- ============================================================================
-- 5. CART (Customer App — Grab & Go / Dine In only)
-- ============================================================================

CREATE TABLE kims_carts (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id     BIGINT UNSIGNED NOT NULL,
    branch_id       BIGINT UNSIGNED NOT NULL,
    order_type      ENUM('grab_go','dine_in') NOT NULL,
    status          ENUM('active','checked_out','abandoned') NOT NULL DEFAULT 'active',
    subtotal        DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_amount    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    note            VARCHAR(255) NULL,
    expires_at      DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_carts_customer_status (customer_id, status),
    CONSTRAINT fk_carts_customer FOREIGN KEY (customer_id) REFERENCES kims_customers(id),
    CONSTRAINT fk_carts_branch FOREIGN KEY (branch_id) REFERENCES kims_branches(id)
) ENGINE=InnoDB;

CREATE TABLE kims_cart_items (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cart_id     BIGINT UNSIGNED NOT NULL,
    product_id  BIGINT UNSIGNED NOT NULL,
    quantity    INT UNSIGNED NOT NULL DEFAULT 1,
    unit_price  DECIMAL(12,2) NOT NULL,
    total_price DECIMAL(12,2) NOT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_cart_items_cart (cart_id),
    CONSTRAINT fk_cart_items_cart FOREIGN KEY (cart_id) REFERENCES kims_carts(id),
    CONSTRAINT fk_cart_items_product FOREIGN KEY (product_id) REFERENCES kims_products(id)
) ENGINE=InnoDB;

CREATE TABLE kims_cart_item_options (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cart_item_id    BIGINT UNSIGNED NOT NULL,
    option_group_id BIGINT UNSIGNED NOT NULL,
    option_id       BIGINT UNSIGNED NOT NULL,
    price_delta     DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cio_item (cart_item_id),
    CONSTRAINT fk_cio_item FOREIGN KEY (cart_item_id) REFERENCES kims_cart_items(id),
    CONSTRAINT fk_cio_group FOREIGN KEY (option_group_id) REFERENCES kims_option_groups(id),
    CONSTRAINT fk_cio_option FOREIGN KEY (option_id) REFERENCES kims_options(id)
) ENGINE=InnoDB;

-- ============================================================================
-- 6. ORDERS (Customer App only — Grab & Go / Dine In)
-- Take Away NEVER creates a row here — see section 8 (Loyalty).
-- ============================================================================

CREATE TABLE kims_orders (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number      VARCHAR(50) NOT NULL,
    cart_id           BIGINT UNSIGNED NOT NULL,
    customer_id       BIGINT UNSIGNED NOT NULL,
    branch_id         BIGINT UNSIGNED NOT NULL,
    order_type        ENUM('grab_go','dine_in') NOT NULL,
    status            ENUM('confirmed','preparing','ready','collected','cancelled') NOT NULL DEFAULT 'confirmed',
    subtotal          DECIMAL(12,2) NOT NULL,
    discount_amount   DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    service_charge    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    tax_amount        DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_amount      DECIMAL(12,2) NOT NULL,
    customer_note     VARCHAR(255) NULL,
    placed_at         DATETIME NOT NULL,
    confirmed_at      DATETIME NULL,
    preparing_at      DATETIME NULL,
    ready_at          DATETIME NULL,
    collected_at      DATETIME NULL,
    cancelled_at      DATETIME NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_orders_number (order_number),
    INDEX idx_orders_branch_status (branch_id, status),
    INDEX idx_orders_customer (customer_id),
    CONSTRAINT fk_orders_cart FOREIGN KEY (cart_id) REFERENCES kims_carts(id),
    CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES kims_customers(id),
    CONSTRAINT fk_orders_branch FOREIGN KEY (branch_id) REFERENCES kims_branches(id)
) ENGINE=InnoDB;

CREATE TABLE kims_order_items (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id            BIGINT UNSIGNED NOT NULL,
    product_id          BIGINT UNSIGNED NULL,
    foodics_product_id  BIGINT UNSIGNED NULL,
    product_name_en     VARCHAR(150) NOT NULL COMMENT 'snapshot at order time',
    product_name_ar     VARCHAR(150) NOT NULL COMMENT 'snapshot at order time',
    quantity            INT UNSIGNED NOT NULL,
    unit_price          DECIMAL(12,2) NOT NULL,
    discount_amount     DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    tax_amount          DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_amount        DECIMAL(12,2) NOT NULL,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order_items_order (order_id),
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES kims_orders(id),
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES kims_products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE kims_order_item_options (
    id                    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_item_id         BIGINT UNSIGNED NOT NULL,
    option_group_id       BIGINT UNSIGNED NULL,
    option_id             BIGINT UNSIGNED NULL,
    foodics_option_id     BIGINT UNSIGNED NULL,
    option_group_name_en  VARCHAR(150) NOT NULL,
    option_group_name_ar  VARCHAR(150) NOT NULL,
    option_name_en        VARCHAR(150) NOT NULL,
    option_name_ar        VARCHAR(150) NOT NULL,
    price_delta           DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_oio_item (order_item_id),
    CONSTRAINT fk_oio_item FOREIGN KEY (order_item_id) REFERENCES kims_order_items(id),
    CONSTRAINT fk_oio_option_group FOREIGN KEY (option_group_id) REFERENCES kims_option_groups(id) ON DELETE SET NULL,
    CONSTRAINT fk_oio_option FOREIGN KEY (option_id) REFERENCES kims_options(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE kims_order_status_history (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id        BIGINT UNSIGNED NOT NULL,
    from_status     VARCHAR(20) NULL,
    to_status       VARCHAR(20) NOT NULL,
    changed_by_type ENUM('staff','system','customer') NOT NULL,
    changed_by_id   BIGINT UNSIGNED NULL COMMENT 'polymorphic: staff.id or customers.id depending on changed_by_type, NULL when system — not FK-enforced, a single hard FK to one table would silently mismatch rows from the other',
    note            VARCHAR(255) NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_osh_order (order_id),
    CONSTRAINT fk_osh_order FOREIGN KEY (order_id) REFERENCES kims_orders(id)
) ENGINE=InnoDB;

-- ============================================================================
-- 7. INVOICES & PAYMENTS
-- kims_invoices is the verified-invoice record shared by both flows:
--   order_id SET  -> generated from a Grab & Go / Dine In order
--   order_id NULL -> a Take Away invoice verified with Foodics at the
--                    cashier, cached here so loyalty math doesn't re-hit
--                    the Foodics API on every read.
-- ============================================================================

CREATE TABLE kims_invoices (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id            BIGINT UNSIGNED NULL,
    branch_id           BIGINT UNSIGNED NOT NULL,
    invoice_number      VARCHAR(100) NOT NULL,
    source              ENUM('customer_app','pos') NOT NULL,
    total_amount        DECIMAL(12,2) NOT NULL,
    issued_at           DATETIME NOT NULL,
    verified_at         DATETIME NULL COMMENT 'set when a cashier verifies this invoice against Foodics for Take Away loyalty',
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_invoices_number (invoice_number),
    UNIQUE KEY uq_invoices_order (order_id) COMMENT 'MySQL allows multiple NULLs, so Take Away rows are unaffected',
    CONSTRAINT fk_invoices_order FOREIGN KEY (order_id) REFERENCES kims_orders(id),
    CONSTRAINT fk_invoices_branch FOREIGN KEY (branch_id) REFERENCES kims_branches(id)
) ENGINE=InnoDB;

-- Online payments only. Customer App orders are always paid online — no
-- saved cards, no cash. Take Away payment is owned entirely by Foodics
-- POS and is NOT duplicated here; loyalty only needs the invoice total.
CREATE TABLE kims_payments (
    id                 BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id           BIGINT UNSIGNED NOT NULL,
    provider           VARCHAR(50) NOT NULL COMMENT 'payment gateway name',
    method             ENUM('card','wallet') NOT NULL,
    amount             DECIMAL(12,2) NOT NULL,
    currency           CHAR(3) NOT NULL DEFAULT 'EGP',
    transaction_id     VARCHAR(150) NULL,
    provider_reference VARCHAR(150) NULL,
    status             ENUM('pending','success','failed','refunded') NOT NULL DEFAULT 'pending' COMMENT 'refunded = at least one kims_refunds row against this payment has reached completed; app/webhook handler keeps this in sync',
    paid_at            DATETIME NULL,
    failed_at          DATETIME NULL,
    created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_payments_order (order_id),
    INDEX idx_payments_status (status),
    CONSTRAINT fk_payments_order FOREIGN KEY (order_id) REFERENCES kims_orders(id)
) ENGINE=InnoDB;

-- Shaped to be a superset of all three refund-authority models under
-- discussion (customer-initiated, admin-approved, fully automatic via
-- Paymob) so that choice stays an application policy, not a schema change:
--   customer-initiated -> initiated_by_type='customer', approved_by NULL
--                          until staff acts (or stays NULL if auto-approved)
--   admin-approved      -> approved_by set to the staff.id who approved it
--   fully automatic     -> initiated_by_type='system', approved_by NULL,
--                          status moves requested -> processing -> completed
--                          entirely via the Paymob refund API/webhook
CREATE TABLE kims_refunds (
    id                 BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_id         BIGINT UNSIGNED NOT NULL,
    order_id           BIGINT UNSIGNED NOT NULL,
    amount             DECIMAL(12,2) NOT NULL COMMENT 'independent of payments.amount to allow partial refunds',
    reason             VARCHAR(255) NULL,
    initiated_by_type  ENUM('customer','staff','system') NOT NULL,
    initiated_by_id    BIGINT UNSIGNED NULL COMMENT 'polymorphic: customers.id or staff.id depending on initiated_by_type — not FK-enforced, same reasoning as kims_order_status_history.changed_by_id',
    approved_by        BIGINT UNSIGNED NULL COMMENT 'staff.id — NULL if auto-approved or still pending review',
    status             ENUM('requested','approved','rejected','processing','completed','failed') NOT NULL DEFAULT 'requested',
    provider_reference VARCHAR(150) NULL COMMENT 'Paymob refund transaction id',
    requested_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at       DATETIME NULL,
    created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_refunds_payment (payment_id),
    INDEX idx_refunds_order (order_id),
    CONSTRAINT fk_refunds_payment FOREIGN KEY (payment_id) REFERENCES kims_payments(id),
    CONSTRAINT fk_refunds_order FOREIGN KEY (order_id) REFERENCES kims_orders(id),
    CONSTRAINT fk_refunds_approver FOREIGN KEY (approved_by) REFERENCES kims_staff(id)
) ENGINE=InnoDB;

-- ============================================================================
-- 8. LOYALTY (owned entirely by KIMS)
-- ============================================================================

CREATE TABLE kims_loyalty_rules (
    id                    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                  VARCHAR(100) NOT NULL,
    priority              INT NOT NULL DEFAULT 0 COMMENT 'higher wins when more than one rule is active for the same date',
    earn_points_rate      DECIMAL(12,4) NOT NULL COMMENT 'points earned per earn_amount_unit spent',
    earn_amount_unit      DECIMAL(12,4) NOT NULL DEFAULT 1.0000,
    redeem_points_unit    DECIMAL(12,4) NOT NULL COMMENT 'points required per redeem_value',
    redeem_value          DECIMAL(12,4) NOT NULL,
    minimum_redeem_points INT UNSIGNED NOT NULL DEFAULT 0,
    is_active             TINYINT(1) NOT NULL DEFAULT 1,
    starts_at             DATETIME NULL,
    ends_at               DATETIME NULL,
    created_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_loyalty_rules_active (is_active, priority)
) ENGINE=InnoDB;

CREATE TABLE kims_loyalty_accounts (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id       BIGINT UNSIGNED NOT NULL,
    balance           INT NOT NULL DEFAULT 0 COMMENT 'cached projection of SUM(points) in kims_loyalty_transactions — kept honest by trg_loyalty_txn_after_insert, never written directly',
    lifetime_earned   INT NOT NULL DEFAULT 0 COMMENT 'cached projection, same rule as balance',
    lifetime_redeemed INT NOT NULL DEFAULT 0 COMMENT 'cached projection, same rule as balance',
    status            ENUM('active','suspended') NOT NULL DEFAULT 'active',
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_loyalty_accounts_customer (customer_id),
    CONSTRAINT fk_loyalty_accounts_customer FOREIGN KEY (customer_id) REFERENCES kims_customers(id)
) ENGINE=InnoDB;

CREATE TABLE kims_rewards (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id          BIGINT UNSIGNED NULL,
    foodics_product_id  BIGINT UNSIGNED NULL,
    name_en             VARCHAR(150) NOT NULL,
    name_ar             VARCHAR(150) NOT NULL,
    points_cost         INT UNSIGNED NOT NULL,
    reward_type         ENUM('product','discount') NOT NULL,
    is_active           TINYINT(1) NOT NULL DEFAULT 1,
    starts_at           DATETIME NULL,
    ends_at             DATETIME NULL,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_rewards_product FOREIGN KEY (product_id) REFERENCES kims_products(id)
) ENGINE=InnoDB;

CREATE TABLE kims_reward_redemptions (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id         BIGINT UNSIGNED NOT NULL,
    loyalty_account_id  BIGINT UNSIGNED NOT NULL,
    reward_id           BIGINT UNSIGNED NOT NULL,
    order_id            BIGINT UNSIGNED NULL,
    points_cost         INT UNSIGNED NOT NULL,
    status              ENUM('pending','redeemed','cancelled','expired') NOT NULL DEFAULT 'pending',
    redemption_code     VARCHAR(50) NOT NULL,
    redeemed_at         DATETIME NULL,
    cancelled_at        DATETIME NULL,
    created_by          BIGINT UNSIGNED NULL COMMENT 'staff who fulfilled the redemption, if in-branch',
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_reward_redemptions_code (redemption_code),
    CONSTRAINT fk_rr_customer FOREIGN KEY (customer_id) REFERENCES kims_customers(id),
    CONSTRAINT fk_rr_account FOREIGN KEY (loyalty_account_id) REFERENCES kims_loyalty_accounts(id),
    CONSTRAINT fk_rr_reward FOREIGN KEY (reward_id) REFERENCES kims_rewards(id),
    CONSTRAINT fk_rr_order FOREIGN KEY (order_id) REFERENCES kims_orders(id),
    CONSTRAINT fk_rr_staff FOREIGN KEY (created_by) REFERENCES kims_staff(id)
) ENGINE=InnoDB;

CREATE TABLE kims_loyalty_transactions (
    id                    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    loyalty_account_id    BIGINT UNSIGNED NOT NULL,
    customer_id           BIGINT UNSIGNED NOT NULL,
    type                  ENUM('earn','redeem','refund','reversal','bonus','adjustment','expire') NOT NULL,
    points                INT NOT NULL COMMENT 'signed: positive for earn/bonus, negative for redeem/expire',
    balance_before        INT NOT NULL COMMENT 'set by trg_loyalty_txn_before_insert — app should pass a placeholder (e.g. 0), not compute this itself',
    balance_after         INT NOT NULL COMMENT 'set by trg_loyalty_txn_before_insert — app should pass a placeholder (e.g. 0), not compute this itself',
    order_id              BIGINT UNSIGNED NULL COMMENT 'set for Grab & Go / Dine In earn',
    invoice_id            BIGINT UNSIGNED NULL COMMENT 'set for Take Away earn (verified invoice)',
    reward_redemption_id  BIGINT UNSIGNED NULL COMMENT 'set for redeem',
    description           VARCHAR(255) NULL,
    created_by            BIGINT UNSIGNED NULL COMMENT 'staff, when awarded via the cashier flow',
    created_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_lt_account (loyalty_account_id),
    INDEX idx_lt_invoice (invoice_id),
    CONSTRAINT fk_lt_account FOREIGN KEY (loyalty_account_id) REFERENCES kims_loyalty_accounts(id),
    CONSTRAINT fk_lt_customer FOREIGN KEY (customer_id) REFERENCES kims_customers(id),
    CONSTRAINT fk_lt_order FOREIGN KEY (order_id) REFERENCES kims_orders(id),
    CONSTRAINT fk_lt_invoice FOREIGN KEY (invoice_id) REFERENCES kims_invoices(id),
    CONSTRAINT fk_lt_redemption FOREIGN KEY (reward_redemption_id) REFERENCES kims_reward_redemptions(id),
    CONSTRAINT fk_lt_staff FOREIGN KEY (created_by) REFERENCES kims_staff(id)
) ENGINE=InnoDB;

-- ============================================================================
-- 9. FOODICS INTEGRATION LAYER
-- Direct foodics_id lives on high-read catalog tables above (section 1-2).
-- This layer maps low-frequency transactional entities instead and logs
-- every sync / API call.
-- ============================================================================

CREATE TABLE kims_integrations (
    id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider       VARCHAR(50) NOT NULL DEFAULT 'foodics',
    name           VARCHAR(100) NOT NULL,
    status         ENUM('active','inactive') NOT NULL DEFAULT 'active',
    credentials    TEXT NOT NULL COMMENT 'application-layer encrypted (e.g. Laravel encrypted cast) — never plain JSON',
    settings       JSON NULL,
    last_synced_at DATETIME NULL,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE kims_external_references (
    id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    integration_id BIGINT UNSIGNED NOT NULL,
    entity_type    ENUM('customer','order','invoice','staff') NOT NULL,
    entity_id      BIGINT UNSIGNED NOT NULL,
    external_type  VARCHAR(50) NOT NULL COMMENT 'e.g. foodics_customer, foodics_order, foodics_invoice, foodics_user',
    external_id    VARCHAR(100) NOT NULL,
    metadata       JSON NULL,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_ext_ref_local (integration_id, entity_type, entity_id),
    UNIQUE KEY uq_ext_ref_external (integration_id, external_type, external_id),
    CONSTRAINT fk_ext_ref_integration FOREIGN KEY (integration_id) REFERENCES kims_integrations(id)
) ENGINE=InnoDB;

CREATE TABLE kims_integration_logs (
    id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    integration_id   BIGINT UNSIGNED NOT NULL,
    direction        ENUM('foodics_to_kims','kims_to_foodics') NOT NULL,
    operation        VARCHAR(50) NOT NULL COMMENT 'sync_products, sync_orders, verify_invoice, get_invoice, ...',
    entity_type      VARCHAR(50) NULL,
    entity_id        BIGINT UNSIGNED NULL,
    external_id      VARCHAR(100) NULL,
    status           ENUM('success','failed') NOT NULL,
    request_payload  JSON NULL,
    response_payload JSON NULL,
    error_code       VARCHAR(50) NULL,
    error_message    TEXT NULL,
    attempts         INT UNSIGNED NOT NULL DEFAULT 1,
    started_at       DATETIME NOT NULL,
    completed_at     DATETIME NULL,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_logs_integration_created (integration_id, created_at),
    INDEX idx_logs_status (status),
    CONSTRAINT fk_logs_integration FOREIGN KEY (integration_id) REFERENCES kims_integrations(id)
) ENGINE=InnoDB;

CREATE TABLE kims_foodics_webhooks (
    id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    integration_id BIGINT UNSIGNED NOT NULL,
    event_id       VARCHAR(150) NOT NULL,
    event_type     VARCHAR(100) NOT NULL,
    payload        JSON NOT NULL,
    status         ENUM('pending','processed','failed') NOT NULL DEFAULT 'pending',
    processed_at   DATETIME NULL,
    error_message  TEXT NULL,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_webhooks_event_id (event_id),
    INDEX idx_webhooks_integration (integration_id),
    CONSTRAINT fk_webhooks_integration FOREIGN KEY (integration_id) REFERENCES kims_integrations(id)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- 10. LOYALTY BALANCE INTEGRITY
-- kims_loyalty_transactions is the source of truth. kims_loyalty_accounts
-- (balance, lifetime_earned, lifetime_redeemed) is a cached projection of
-- it — these triggers are what keep that projection honest under
-- concurrent writes to the same account. The FOR UPDATE lock in the
-- BEFORE INSERT trigger serializes two simultaneous transactions on one
-- account (e.g. a double-tap redeem); different accounts still proceed
-- in parallel, since the lock is per-row, not table-wide.
--
-- In Laravel, add this via a migration using DB::unprepared() — MySQL
-- triggers aren't something the schema builder manages natively.
--
-- The application must never compute balance_before/balance_after itself
-- and must never write kims_loyalty_accounts.balance directly — pass a
-- placeholder (e.g. 0) for balance_before/after on insert; the trigger
-- overwrites both with the true, lock-serialized value before the row
-- is stored, and rejects the insert outright if it would take the
-- account negative.
--
-- Recommended companion (not a trigger — a scheduled job): periodically
-- compare
--   SELECT loyalty_account_id, SUM(points)
--   FROM kims_loyalty_transactions GROUP BY loyalty_account_id
-- against kims_loyalty_accounts.balance and alert on drift. A cache that
-- can't be independently verified against its source isn't really a cache.
-- ============================================================================

DELIMITER $$

CREATE TRIGGER trg_loyalty_txn_before_insert
BEFORE INSERT ON kims_loyalty_transactions
FOR EACH ROW
BEGIN
    DECLARE current_balance INT;

    SELECT balance INTO current_balance
    FROM kims_loyalty_accounts
    WHERE id = NEW.loyalty_account_id
    FOR UPDATE;

    IF current_balance + NEW.points < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Loyalty transaction would drive balance negative';
    END IF;

    SET NEW.balance_before = current_balance;
    SET NEW.balance_after  = current_balance + NEW.points;
END$$

CREATE TRIGGER trg_loyalty_txn_after_insert
AFTER INSERT ON kims_loyalty_transactions
FOR EACH ROW
BEGIN
    UPDATE kims_loyalty_accounts
    SET balance           = NEW.balance_after,
        lifetime_earned   = lifetime_earned + IF(NEW.points > 0, NEW.points, 0),
        lifetime_redeemed = lifetime_redeemed + IF(NEW.points < 0, -NEW.points, 0)
    WHERE id = NEW.loyalty_account_id;
END$$

DELIMITER ;
