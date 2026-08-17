<?php

namespace Modules\Ordering\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Catalog\Models\Option;
use Modules\Catalog\Models\OptionGroup;

#[Fillable([
    'order_item_id', 'option_group_id', 'option_id', 'foodics_option_id',
    'option_group_name_en', 'option_group_name_ar', 'option_name_en',
    'option_name_ar', 'price_delta',
])]
class OrderItemOption extends Model
{
    public $timestamps = false;

    protected $table = 'kims_order_item_options';

    protected function casts(): array
    {
        return [
            'price_delta' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function optionGroup(): BelongsTo
    {
        return $this->belongsTo(OptionGroup::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }
}
