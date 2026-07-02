<?php

declare(strict_types=1);

namespace App\Platform\Support;

trait HasPlatformAuditColumns
{
    /**
     * @return list<string>
     */
    protected function platformAuditFillable(): array
    {
        return [
            'status',
            'created_by',
            'updated_by',
            'deleted_by',
        ];
    }
}

