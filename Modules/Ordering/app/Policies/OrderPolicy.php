<?php

namespace Modules\Ordering\Policies;

use Modules\Ordering\Models\Customer;
use Modules\Ordering\Models\Order;

class OrderPolicy
{
    public function view(Customer $customer, Order $order): bool
    {
        return $customer->id === $order->customer_id;
    }
}
