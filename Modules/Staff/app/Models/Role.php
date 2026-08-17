<?php

namespace Modules\Staff\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'permissions'])]
class Role extends Model
{
    use HasFactory;

    protected $table = 'kims_roles';

    public const MANAGER = 'manager';

    public const ADMIN = 'admin';

    public const BARISTA = 'barista';

    public const CASHIER = 'cashier';

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
        ];
    }

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }
}
