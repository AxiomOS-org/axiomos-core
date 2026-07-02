<?php

declare(strict_types=1);

namespace App\Platform\Audit;

use App\Platform\Support\PlatformRecord;

final class AuditLog extends PlatformRecord
{
    public $timestamps = false;

    protected $table = 'universal_audit_logs';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'entity_type',
        'entity_id',
        'action',
        'old_values',
        'new_values',
        'metadata',
        'actor_id',
        'ip_address',
        'user_agent',
        'occurred_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];
}
