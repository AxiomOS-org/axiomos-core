<?php

declare(strict_types=1);

namespace App\Platform\Theme\Contracts;

interface ThemeInterface
{
    public function name(): string;

    public function version(): string;

    /**
     * @return list<string>
     */
    public function assetPaths(): array;

    public function layout(string $name): string;
}
