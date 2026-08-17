<?php

namespace Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Ordering\Models\Cart;
use Modules\Ordering\Models\Invoice;
use Modules\Ordering\Models\Order;
use Modules\Staff\Models\Staff;

#[Fillable([
    'foodics_id', 'name_en', 'name_ar', 'code', 'address', 'city',
    'latitude', 'longitude', 'phone', 'accepts_grab_go', 'accepts_dine_in',
    'is_active', 'synced_at',
])]
class Branch extends Model
{
    use HasFactory;

    protected $table = 'kims_branches';

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'accepts_grab_go' => 'boolean',
            'accepts_dine_in' => 'boolean',
            'is_active' => 'boolean',
            'synced_at' => 'datetime',
        ];
    }

    public function hours(): HasMany
    {
        return $this->hasMany(BranchHour::class);
    }

    public function branchProducts(): HasMany
    {
        return $this->hasMany(BranchProduct::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'kims_branch_products')
            ->withPivot('price_override', 'is_available')
            ->withTimestamps();
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'kims_staff_branches')->withPivot('created_at');
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
