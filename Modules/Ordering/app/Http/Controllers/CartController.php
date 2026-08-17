<?php

namespace Modules\Ordering\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Ordering\Http\Requests\StartCartRequest;
use Modules\Ordering\Http\Resources\CartResource;

class CartController extends Controller
{
    public function show(Request $request): CartResource
    {
        $cart = $request->user()->carts()->where('status', 'active')->latest()->first();

        abort_unless($cart, 404, 'No active cart. Start one first.');

        return new CartResource($cart->load('items.product', 'items.options.option'));
    }

    public function store(StartCartRequest $request): CartResource
    {
        $request->user()->carts()->where('status', 'active')->update(['status' => 'abandoned']);

        $cart = $request->user()->carts()->create([
            'branch_id' => $request->integer('branch_id'),
            'order_type' => $request->string('order_type'),
            'status' => 'active',
            'note' => $request->input('note'),
        ]);

        return new CartResource($cart->load('items.product', 'items.options.option'));
    }
}
