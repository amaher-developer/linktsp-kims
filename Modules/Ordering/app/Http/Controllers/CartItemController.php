<?php

namespace Modules\Ordering\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Modules\Catalog\Models\Product;
use Modules\Ordering\Http\Requests\AddCartItemRequest;
use Modules\Ordering\Http\Requests\UpdateCartItemRequest;
use Modules\Ordering\Http\Resources\CartResource;
use Modules\Ordering\Models\Cart;
use Modules\Ordering\Models\CartItem;

class CartItemController extends Controller
{
    public function store(AddCartItemRequest $request): CartResource
    {
        $cart = $request->user()->carts()->where('status', 'active')->latest()->first();

        abort_unless($cart, 404, 'No active cart. Start one first.');
        $this->authorize('update', $cart);

        $product = Product::with('optionGroups.options')->findOrFail($request->integer('product_id'));
        $selectedOptions = $product->optionGroups
            ->flatMap(fn ($group) => $group->options)
            ->whereIn('id', $request->input('option_ids', []));

        $unitPrice = $this->resolveUnitPrice($product, $cart->branch_id, $selectedOptions);
        $quantity = $request->integer('quantity');

        $item = $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => round($unitPrice * $quantity, 2),
        ]);

        foreach ($selectedOptions as $option) {
            $item->options()->create([
                'option_group_id' => $option->option_group_id,
                'option_id' => $option->id,
                'price_delta' => $option->price_delta,
            ]);
        }

        $this->recalculateCartTotals($cart);

        return new CartResource($cart->load('items.product', 'items.options.option'));
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): CartResource
    {
        $cart = $cartItem->cart;
        $this->authorize('update', $cart);

        $product = $cartItem->product()->with('optionGroups.options')->first();
        $selectedOptions = $product->optionGroups
            ->flatMap(fn ($group) => $group->options)
            ->whereIn('id', $request->input('option_ids', []));

        $unitPrice = $this->resolveUnitPrice($product, $cart->branch_id, $selectedOptions);
        $quantity = $request->integer('quantity');

        $cartItem->update([
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => round($unitPrice * $quantity, 2),
        ]);

        $cartItem->options()->delete();
        foreach ($selectedOptions as $option) {
            $cartItem->options()->create([
                'option_group_id' => $option->option_group_id,
                'option_id' => $option->id,
                'price_delta' => $option->price_delta,
            ]);
        }

        $this->recalculateCartTotals($cart);

        return new CartResource($cart->load('items.product', 'items.options.option'));
    }

    public function destroy(Request $request, CartItem $cartItem): CartResource
    {
        $cart = $cartItem->cart;
        $this->authorize('update', $cart);

        $cartItem->options()->delete();
        $cartItem->delete();

        $this->recalculateCartTotals($cart);

        return new CartResource($cart->load('items.product', 'items.options.option'));
    }

    private function resolveUnitPrice(Product $product, int $branchId, Collection $selectedOptions): float
    {
        $branchProduct = $product->branchProducts()->where('branch_id', $branchId)->first();

        $basePrice = $branchProduct?->price_override ?? $product->base_price;

        return round((float) $basePrice + $selectedOptions->sum(fn ($option) => (float) $option->price_delta), 2);
    }

    private function recalculateCartTotals(Cart $cart): void
    {
        $subtotal = $cart->items()->sum('total_price');

        $cart->update([
            'subtotal' => $subtotal,
            'total_amount' => $subtotal - $cart->discount_amount,
        ]);
    }
}
