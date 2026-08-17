<?php

namespace Modules\Ordering\Services;

use Illuminate\Support\Facades\DB;
use Modules\Ordering\Models\Cart;
use Modules\Ordering\Models\Order;
use Modules\Ordering\Models\Payment;
use Modules\Ordering\Support\OrderNumberGenerator;
use RuntimeException;

/**
 * Turns a checked-out cart into an order. Grab & Go / Dine In only — Take
 * Away never reaches this path (see kims_schema.sql section 3/8).
 *
 * Payment is recorded as 'pending': KIMS orders are always paid online per
 * business rule, but the Paymob gateway integration itself is deferred, so
 * this only creates the record a webhook would later confirm — it never
 * fabricates a 'success' state.
 */
class CheckoutService
{
    public function checkout(Cart $cart, ?string $customerNote = null): Order
    {
        if ($cart->status !== 'active') {
            throw new RuntimeException('Cart is not active.');
        }

        if ($cart->items()->count() === 0) {
            throw new RuntimeException('Cart is empty.');
        }

        return DB::transaction(function () use ($cart, $customerNote) {
            $cart->loadMissing('items.product', 'items.options.option');

            $order = Order::create([
                'order_number' => OrderNumberGenerator::next(),
                'cart_id' => $cart->id,
                'customer_id' => $cart->customer_id,
                'branch_id' => $cart->branch_id,
                'order_type' => $cart->order_type,
                'status' => 'confirmed',
                'subtotal' => $cart->subtotal,
                'discount_amount' => $cart->discount_amount,
                'service_charge' => 0,
                'tax_amount' => 0,
                'total_amount' => $cart->total_amount,
                'customer_note' => $customerNote,
                'placed_at' => now(),
                'confirmed_at' => now(),
            ]);

            foreach ($cart->items as $cartItem) {
                $orderItem = $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'product_name_en' => $cartItem->product->name_en,
                    'product_name_ar' => $cartItem->product->name_ar,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'discount_amount' => 0,
                    'tax_amount' => 0,
                    'total_amount' => $cartItem->total_price,
                ]);

                foreach ($cartItem->options as $cartItemOption) {
                    $orderItem->options()->create([
                        'option_group_id' => $cartItemOption->option_group_id,
                        'option_id' => $cartItemOption->option_id,
                        'option_group_name_en' => $cartItemOption->optionGroup->name_en,
                        'option_group_name_ar' => $cartItemOption->optionGroup->name_ar,
                        'option_name_en' => $cartItemOption->option->name_en,
                        'option_name_ar' => $cartItemOption->option->name_ar,
                        'price_delta' => $cartItemOption->price_delta,
                    ]);
                }
            }

            $order->statusHistory()->create([
                'from_status' => null,
                'to_status' => 'confirmed',
                'changed_by_type' => 'customer',
                'changed_by_id' => $cart->customer_id,
            ]);

            Payment::create([
                'order_id' => $order->id,
                'provider' => 'pending_integration',
                'method' => 'card',
                'amount' => $order->total_amount,
                'currency' => 'EGP',
                'status' => 'pending',
            ]);

            $cart->update(['status' => 'checked_out']);

            return $order->load('items.options', 'branch');
        });
    }
}
