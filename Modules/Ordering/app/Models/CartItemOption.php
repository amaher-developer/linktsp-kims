<?php

namespace Modules\Ordering\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Catalog\Models\Option;
use Modules\Catalog\Models\OptionGroup;

#[Fillable(['cart_item_id', 'option_group_id', 'option_id', 'price_delta'])]
class CartItemOption extends Model
{
    public $timestamps = false;

    protected $table = 'kims_cart_item_options';

    protected function casts(): array
    {
        return [
            'price_delta' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    public function cartItem(): BelongsTo
    {
        return $this->belongsTo(CartItem::class);
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
