<?php

namespace Modules\Barista\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Barista\Http\Requests\UpdateOrderStatusRequest;
use Modules\Ordering\Enums\OrderStatus;
use Modules\Ordering\Http\Resources\OrderResource;
use Modules\Ordering\Models\Order;
use Modules\Ordering\Models\OrderStatusHistory;

/**
 * Barista App foundation only: list the digital orders (Grab & Go / Dine
 * In — Take Away never reaches kims_orders) at the barista's assigned
 * branches and move them through the existing status/history structure.
 * No kitchen-station or ticket routing tables exist in the schema, so
 * none are invented here.
 */
class OrderController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $branchIds = $request->user()->branches()->pluck('kims_branches.id');

        $orders = Order::whereIn('branch_id', $branchIds)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->with('branch')
            ->orderBy('placed_at')
            ->paginate(20);

        return OrderResource::collection($orders);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): OrderResource
    {
        $branchIds = $request->user()->branches()->pluck('kims_branches.id');
        abort_unless($branchIds->contains($order->branch_id), 403, 'This order does not belong to one of your branches.');

        $from = $order->status;
        $to = OrderStatus::from($request->string('status')->toString());

        $timestampColumn = match ($to) {
            OrderStatus::Confirmed => 'confirmed_at',
            OrderStatus::Preparing => 'preparing_at',
            OrderStatus::Ready => 'ready_at',
            OrderStatus::Collected => 'collected_at',
            OrderStatus::Cancelled => 'cancelled_at',
        };

        $order->update(['status' => $to, $timestampColumn => now()]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => $from->value,
            'to_status' => $to->value,
            'changed_by_type' => 'staff',
            'changed_by_id' => $request->user()->id,
        ]);

        return new OrderResource($order->load('branch'));
    }
}
