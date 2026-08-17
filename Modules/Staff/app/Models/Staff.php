<?php

namespace Modules\Staff\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Catalog\Models\Branch;
use Modules\Ordering\Models\Refund;
use Modules\Loyalty\Models\RewardRedemption;

#[Fillable(['role_id', 'name', 'phone', 'email', 'password', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class Staff extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'kims_staff';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'kims_staff_branches')->withPivot('created_at');
    }

    public function approvedRefunds(): HasMany
    {
        return $this->hasMany(Refund::class, 'approved_by');
    }

    public function fulfilledRedemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class, 'created_by');
    }

    public function isManager(): bool
    {
        return in_array($this->role?->name, [Role::MANAGER, Role::ADMIN], true);
    }

    public function isCashier(): bool
    {
        return $this->role?->name === Role::CASHIER;
    }

    public function isBarista(): bool
    {
        return $this->role?->name === Role::BARISTA;
    }
}
