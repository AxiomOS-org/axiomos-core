<?php

declare(strict_types=1);

namespace Modules\Organization\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Modules\Organization\Domain\Models\Branch;
use Modules\Organization\Domain\Models\Company;
use Modules\Organization\Domain\Models\Department;
use Modules\Organization\Domain\Models\Organization;

final class OrganizationResource
{
    public function __construct(private readonly Organization $organization)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return self::base($this->organization);
    }

    /**
     * @return array<string, mixed>
     */
    public static function base(Model $model): array
    {
        $data = [
            'id' => $model->getKey(),
            'code' => $model->getAttribute('code'),
            'name' => $model->getAttribute('name'),
            'description' => $model->getAttribute('description'),
            'slug' => $model->getAttribute('slug'),
            'logo' => $model->getAttribute('logo'),
            'status' => $model->getAttribute('status')?->value ?? $model->getAttribute('status'),
            'timezone' => $model->getAttribute('timezone'),
            'currency' => $model->getAttribute('currency'),
            'locale' => $model->getAttribute('locale'),
            'country' => $model->getAttribute('country'),
            'settings' => $model->getAttribute('settings'),
            'created_by' => $model->getAttribute('created_by'),
            'updated_by' => $model->getAttribute('updated_by'),
            'deleted_by' => $model->getAttribute('deleted_by'),
            'created_at' => $model->getAttribute('created_at')?->toIso8601String(),
            'updated_at' => $model->getAttribute('updated_at')?->toIso8601String(),
        ];

        if ($model instanceof Company) {
            $data['organization_id'] = $model->organization_id;
        }

        if ($model instanceof Branch) {
            $data['company_id'] = $model->company_id;
        }

        if ($model instanceof Department) {
            $data['branch_id'] = $model->branch_id;
            $data['parent_id'] = $model->parent_id;
        }

        return $data;
    }
}
