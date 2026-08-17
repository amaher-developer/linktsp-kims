<?php

namespace Modules\Cashier\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Cashier\Http\Requests\AwardLoyaltyPointsRequest;
use Modules\Loyalty\Http\Resources\LoyaltyTransactionResource;
use Modules\Loyalty\Models\LoyaltyRule;
use Modules\Loyalty\Models\LoyaltyTransaction;
use Modules\Ordering\Models\CustomerIdentifier;
use Modules\Ordering\Models\Invoice;

/**
 * Take Away loyalty award: scan the customer's QR/barcode, verify the
 * invoice already recorded in kims_invoices, and award points from it.
 * Balance integrity (balance_before/after, negative-balance rejection) is
 * entirely owned by the DB triggers from Phase 1 — this never touches
 * kims_loyalty_accounts.balance directly.
 */
class LoyaltyAwardController extends Controller
{
    public function store(AwardLoyaltyPointsRequest $request): JsonResponse
    {
        $identifier = CustomerIdentifier::where('value', $request->string('code'))
            ->where('is_active', true)
            ->with('customer')
            ->first();

        abort_unless($identifier && $identifier->customer?->is_active, 404, 'Customer not found for this code.');

        $invoice = Invoice::where('invoice_number', $request->string('invoice_number'))->firstOrFail();

        if (LoyaltyTransaction::where('invoice_id', $invoice->id)->exists()) {
            abort(422, 'Points have already been awarded for this invoice.');
        }

        $rule = LoyaltyRule::currentlyActive();
        abort_if(! $rule, 422, 'No active loyalty rule is configured.');

        $points = $rule->pointsForAmount((float) $invoice->total_amount);
        abort_if($points <= 0, 422, 'This invoice does not qualify for any loyalty points.');

        $customer = $identifier->customer;

        $transaction = DB::transaction(function () use ($customer, $invoice, $points, $request) {
            $account = $customer->loyaltyAccount ?? $customer->loyaltyAccount()->create(['status' => 'active']);

            if (! $invoice->verified_at) {
                $invoice->update(['verified_at' => now()]);
            }

            return LoyaltyTransaction::create([
                'loyalty_account_id' => $account->id,
                'customer_id' => $customer->id,
                'type' => 'earn',
                'points' => $points,
                'invoice_id' => $invoice->id,
                'description' => "Take Away earn for invoice {$invoice->invoice_number}",
                'created_by' => $request->user()->id,
            ]);
        });

        return (new LoyaltyTransactionResource($transaction))->response()->setStatusCode(201);
    }
}
