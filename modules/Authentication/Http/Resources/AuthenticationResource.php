<?php

declare(strict_types=1);

namespace Modules\Authentication\Http\Resources;

use Illuminate\Database\Eloquent\Model;

final class AuthenticationResource
{
    /**
     * @return array<string, mixed>
     */
    public static function base(Model $model): array
    {
        return [
            'id' => $model->getKey(),
            'status' => $model->getAttribute('status'),
            'created_at' => $model->getAttribute('created_at')?->toIso8601String(),
            'updated_at' => $model->getAttribute('updated_at')?->toIso8601String(),
        ];
    }
}
