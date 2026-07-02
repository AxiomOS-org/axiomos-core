<?php

declare(strict_types=1);

namespace Modules\Authorization\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Modules\Authorization\Domain\Models\AuthorizationPermission;
use Modules\Authorization\Domain\Models\AuthorizationRole;
use Modules\Authorization\Domain\Models\AuthorizationRoleAssignment;

final class AuthorizationResource
{
    /**
     * @return array<string, mixed>
     */
    public static function base(Model $model): array
    {
        $data = [
            'id' => $model->getKey(),
            'status' => $model->getAttribute('status'),
            'created_by' => $model->getAttribute('created_by'),
            'updated_by' => $model->getAttribute('updated_by'),
            'deleted_by' => $model->getAttribute('deleted_by'),
            'created_at' => $model->getAttribute('created_at')?->toIso8601String(),
            'updated_at' => $model->getAttribute('updated_at')?->toIso8601String(),
        ];

        if ($model instanceof AuthorizationRole) {
            $data += [
                'organization_id' => $model->organization_id,
                'slug' => $model->slug,
                'name' => $model->name,
                'description' => $model->description,
                'is_system' => $model->is_system,
            ];
        }

        if ($model instanceof AuthorizationPermission) {
            $data += [
                'slug' => $model->slug,
                'name' => $model->name,
                'module' => $model->module,
                'action' => $model->action,
                'description' => $model->description,
                'is_system' => $model->is_system,
            ];
        }

        if ($model instanceof AuthorizationRoleAssignment) {
            $data += [
                'role_id' => $model->role_id,
                'assignable_type' => $model->assignable_type,
                'assignable_id' => $model->assignable_id,
                'organization_id' => $model->organization_id,
            ];
        }

        return $data;
    }
}
