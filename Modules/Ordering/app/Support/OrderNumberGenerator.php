<?php

namespace Modules\Ordering\Support;

use Illuminate\Support\Str;
use Modules\Ordering\Models\Order;

class OrderNumberGenerator
{
    public static function next(): string
    {
        do {
            $candidate = 'KIMS-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
        } while (Order::where('order_number', $candidate)->exists());

        return $candidate;
    }
}
