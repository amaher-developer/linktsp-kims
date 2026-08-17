<?php

namespace Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'option_group_id', 'foodics_id', 'name_en', 'name_ar',
    'price_delta', 'sort_order', 'is_active',
])]
class Option extends Model
{
    use HasFactory;

    protected $table = 'kims_options';

    protected function casts(): array
    {
        return [
            'price_delta' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function optionGroup(): BelongsTo
    {
        return $this->belongsTo(OptionGroup::class);
    }
}
