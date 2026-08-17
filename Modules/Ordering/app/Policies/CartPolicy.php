<?php

namespace Modules\Ordering\Policies;

use Modules\Ordering\Models\Cart;
use Modules\Ordering\Models\Customer;

class CartPolicy
{
    public function view(Customer $customer, Cart $cart): bool
    {
        return $customer->id === $cart->customer_id;
    }

    public function update(Customer $customer, Cart $cart): bool
    {
        return $customer->id === $cart->customer_id && $cart->status === 'active';
    }
}
