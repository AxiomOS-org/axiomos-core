<?php

declare(strict_types=1);

namespace Modules\Organization\Domain\Models\Concerns;

use App\Platform\Support\PlatformEntityInterface;

trait HasPlatformEntityType
{
    public function getEntityType(): string
    {
        return static::platformEntityType();
    }

    public function getEntityId(): string
    {
        return (string) $this->getKey();
    }

    abstract public static function platformEntityType(): string;
}
