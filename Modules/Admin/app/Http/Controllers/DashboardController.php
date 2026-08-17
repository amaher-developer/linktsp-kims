<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Catalog\Models\Branch;
use Modules\Catalog\Models\Product;
use Modules\Ordering\Enums\OrderStatus;
use Modules\Ordering\Models\Customer;
use Modules\Ordering\Models\Order;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin::dashboard', [
            'branchCount' => Branch::count(),
            'productCount' => Product::count(),
            'customerCount' => Customer::count(),
            'openOrderCount' => Order::whereIn('status', [
                OrderStatus::Confirmed, OrderStatus::Preparing, OrderStatus::Ready,
            ])->count(),
            'recentOrders' => Order::with(['branch', 'customer'])->latest('placed_at')->limit(5)->get(),
        ]);
    }
}
