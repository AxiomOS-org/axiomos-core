<?php

declare(strict_types=1);

namespace Modules\Organization\Application\Support;

use Illuminate\Support\Str;

final class SlugGenerator
{
    public function from(string $name, ?string $suffix = null): string
    {
        $slug = Str::slug($name);

        if ($suffix !== null && $suffix !== '') {
            $slug .= '-' . Str::slug($suffix);
        }

        return $slug;
    }
}
