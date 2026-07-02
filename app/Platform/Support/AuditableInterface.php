<?php

declare(strict_types=1);

namespace App\Platform\Support;

interface AuditableInterface extends PlatformEntityInterface
{
    /**
     * @return array<string, mixed>
     */
    public function toAuditSnapshot(): array;
}

