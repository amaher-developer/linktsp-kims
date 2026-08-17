<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Ordering\Enums\OrderStatus;
use Modules\Ordering\Models\Order;
use Modules\Ordering\Models\OrderStatusHistory;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::with(['branch', 'customer'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest('placed_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin::admin.orders.index', ['orders' => $orders, 'statuses' => OrderStatus::cases()]);
    }

    public function show(Order $order): View
    {
        $order->load(['branch', 'customer', 'items.options', 'statusHistory', 'invoice', 'payments']);

        return view('admin::admin.orders.show', ['order' => $order, 'statuses' => OrderStatus::cases()]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_column(OrderStatus::cases(), 'value'))],
        ]);

        $from = $order->status;
        $to = OrderStatus::from($data['status']);

        $order->status = $to;
        $timestampColumn = match ($to) {
            OrderStatus::Confirmed => 'confirmed_at',
            OrderStatus::Preparing => 'preparing_at',
            OrderStatus::Ready => 'ready_at',
            OrderStatus::Collected => 'collected_at',
            OrderStatus::Cancelled => 'cancelled_at',
        };
        $order->{$timestampColumn} = now();
        $order->save();

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => $from->value,
            'to_status' => $to->value,
            'changed_by_type' => 'staff',
            'changed_by_id' => $request->user('staff')?->id,
        ]);

        return redirect()->route('admin.orders.show', $order)->with('status', 'order-updated');
    }
}
