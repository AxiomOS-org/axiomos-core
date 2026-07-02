<?php

declare(strict_types=1);

namespace Modules\Users\Http\Resources;

use Illuminate\Database\Eloquent\Model;

final class UserResource
{
    /**
     * @return array<string, mixed>
     */
    public static function base(Model $model): array
    {
        return [
            'id' => $model->getKey(),
            'identity_id' => $model->getAttribute('identity_id'),
            'username' => $model->getAttribute('username'),
            'email' => $model->getAttribute('email'),
            'display_name' => $model->getAttribute('display_name'),
            'status' => $model->getAttribute('status'),
            'settings' => $model->getAttribute('settings'),
            'created_by' => $model->getAttribute('created_by'),
            'updated_by' => $model->getAttribute('updated_by'),
            'deleted_by' => $model->getAttribute('deleted_by'),
            'created_at' => $model->getAttribute('created_at')?->toIso8601String(),
            'updated_at' => $model->getAttribute('updated_at')?->toIso8601String(),
        ];
    }
}
