<?php

declare(strict_types=1);

namespace Modules\Membership\Http\Resources;

use Illuminate\Database\Eloquent\Model;

final class MembershipResource
{
    /**
     * @return array<string, mixed>
     */
    public static function base(Model $model): array
    {
        return [
            'id' => $model->getKey(),
            'user_id' => $model->getAttribute('user_id'),
            'organization_id' => $model->getAttribute('organization_id'),
            'membership_type' => $model->getAttribute('membership_type'),
            'status' => $model->getAttribute('status'),
            'scopes' => $model->getAttribute('scopes'),
            'created_by' => $model->getAttribute('created_by'),
            'updated_by' => $model->getAttribute('updated_by'),
            'deleted_by' => $model->getAttribute('deleted_by'),
            'created_at' => $model->getAttribute('created_at')?->toIso8601String(),
            'updated_at' => $model->getAttribute('updated_at')?->toIso8601String(),
        ];
    }
}
