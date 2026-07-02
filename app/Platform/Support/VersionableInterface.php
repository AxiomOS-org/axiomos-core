<?php

declare(strict_types=1);

namespace App\Platform\Support;

interface VersionableInterface extends PlatformEntityInterface
{
    /**
     * @return array<string, mixed>
     */
    public function toVersionSnapshot(): array;
}

