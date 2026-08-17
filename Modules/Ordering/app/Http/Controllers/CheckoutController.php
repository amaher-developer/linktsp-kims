<?php

namespace Modules\Ordering\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Ordering\Http\Requests\CheckoutRequest;
use Modules\Ordering\Http\Resources\OrderResource;
use Modules\Ordering\Services\CheckoutService;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(private readonly CheckoutService $checkoutService) {}

    public function store(CheckoutRequest $request): JsonResponse
    {
        $cart = $request->user()->carts()->where('status', 'active')->latest()->first();

        abort_unless($cart, 404, 'No active cart. Start one first.');
        $this->authorize('update', $cart);

        try {
            $order = $this->checkoutService->checkout($cart, $request->input('customer_note'));
        } catch (RuntimeException $e) {
            abort(422, $e->getMessage());
        }

        return (new OrderResource($order))->response()->setStatusCode(201);
    }
}
