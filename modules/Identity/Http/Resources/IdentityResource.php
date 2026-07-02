<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Modules\Identity\Domain\Models\ApiToken;
use Modules\Identity\Domain\Models\Contact;
use Modules\Identity\Domain\Models\Device;
use Modules\Identity\Domain\Models\EmployeeProfile;
use Modules\Identity\Domain\Models\Identity;
use Modules\Identity\Domain\Models\IdentitySession;
use Modules\Identity\Domain\Models\LoginHistory;
use Modules\Identity\Domain\Models\Team;
use Modules\Identity\Domain\Models\TeamMember;

final class IdentityResource
{
    public function __construct(private readonly Identity $identity)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return self::base($this->identity);
    }

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

        if ($model instanceof Identity) {
            $data += [
                'organization_id' => $model->organization_id,
                'identity_type' => $model->identity_type,
                'code' => $model->code,
                'display_name' => $model->display_name,
                'legal_name' => $model->legal_name,
                'email' => $model->email,
                'phone' => $model->phone,
                'metadata' => $model->metadata,
            ];
        }

        if ($model instanceof Team) {
            $data += [
                'organization_id' => $model->organization_id,
                'code' => $model->code,
                'name' => $model->name,
                'description' => $model->description,
                'leader_identity_id' => $model->leader_identity_id,
            ];
        }

        if ($model instanceof TeamMember) {
            $data += [
                'team_id' => $model->team_id,
                'identity_id' => $model->identity_id,
                'role' => $model->role,
            ];
        }

        if ($model instanceof EmployeeProfile) {
            $data += [
                'identity_id' => $model->identity_id,
                'organization_id' => $model->organization_id,
                'employee_number' => $model->employee_number,
                'job_title' => $model->job_title,
                'department_id' => $model->department_id,
                'hire_date' => $model->hire_date?->format('Y-m-d'),
                'metadata' => $model->metadata,
            ];
        }

        if ($model instanceof Contact) {
            $data += [
                'identity_id' => $model->identity_id,
                'contact_type' => $model->contact_type,
                'value' => $model->value,
                'is_primary' => $model->is_primary,
            ];
        }

        if ($model instanceof Device) {
            $data += [
                'identity_id' => $model->identity_id,
                'device_type' => $model->device_type,
                'fingerprint' => $model->fingerprint,
                'user_agent' => $model->user_agent,
                'last_seen_at' => $model->last_seen_at?->toIso8601String(),
            ];
        }

        if ($model instanceof IdentitySession) {
            $data += [
                'identity_id' => $model->identity_id,
                'session_token_hash' => $model->session_token_hash,
                'ip_address' => $model->ip_address,
                'user_agent' => $model->user_agent,
                'started_at' => $model->started_at?->toIso8601String(),
                'expires_at' => $model->expires_at?->toIso8601String(),
            ];
        }

        if ($model instanceof LoginHistory) {
            $data += [
                'identity_id' => $model->identity_id,
                'user_id' => $model->user_id,
                'ip_address' => $model->ip_address,
                'user_agent' => $model->user_agent,
                'success' => $model->success,
                'logged_at' => $model->logged_at?->toIso8601String(),
            ];
        }

        if ($model instanceof ApiToken) {
            $data += [
                'identity_id' => $model->identity_id,
                'name' => $model->name,
                'token_hash' => $model->token_hash,
                'scopes' => $model->scopes,
                'expires_at' => $model->expires_at?->toIso8601String(),
                'last_used_at' => $model->last_used_at?->toIso8601String(),
            ];
        }

        return $data;
    }
}
