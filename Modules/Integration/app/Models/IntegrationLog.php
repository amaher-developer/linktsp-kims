<?php

namespace Modules\Integration\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'integration_id', 'direction', 'operation', 'entity_type', 'entity_id',
    'external_id', 'status', 'request_payload', 'response_payload',
    'error_code', 'error_message', 'attempts', 'started_at', 'completed_at',
])]
class IntegrationLog extends Model
{
    public $timestamps = false;

    protected $table = 'kims_integration_logs';

    protected function casts(): array
    {
        return [
            'request_payload' => 'array',
            'response_payload' => 'array',
            'attempts' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }
}
