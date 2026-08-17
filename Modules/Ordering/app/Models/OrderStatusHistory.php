<?php

namespace Modules\Ordering\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'from_status', 'to_status', 'changed_by_type', 'changed_by_id', 'note'])]
class OrderStatusHistory extends Model
{
    public $timestamps = false;

    protected $table = 'kims_order_status_history';

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
