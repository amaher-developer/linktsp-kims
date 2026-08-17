<?php

namespace Modules\Integration\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['provider', 'name', 'status', 'credentials', 'settings', 'last_synced_at'])]
#[Hidden(['credentials'])]
class Integration extends Model
{
    use HasFactory;

    protected $table = 'kims_integrations';

    protected function casts(): array
    {
        return [
            'credentials' => 'encrypted',
            'settings' => 'array',
            'last_synced_at' => 'datetime',
        ];
    }

    public function externalReferences(): HasMany
    {
        return $this->hasMany(ExternalReference::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(IntegrationLog::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(FoodicsWebhook::class);
    }
}
