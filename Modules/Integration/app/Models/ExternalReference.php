<?php

namespace Modules\Integration\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['integration_id', 'entity_type', 'entity_id', 'external_type', 'external_id', 'metadata'])]
class ExternalReference extends Model
{
    use HasFactory;

    protected $table = 'kims_external_references';

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }
}
