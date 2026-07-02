<?php

declare(strict_types=1);

namespace App\Platform\Support;

/**
 * Any ERP entity that participates in the platform layer.
 */
interface PlatformEntityInterface
{
    public function getEntityType(): string;

    public function getEntityId(): string;
}

