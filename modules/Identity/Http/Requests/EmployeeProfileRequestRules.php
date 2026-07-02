<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Requests;

final class EmployeeProfileRequestRules
{
    /**
     * @return array<string, list<string>>
     */
    public static function create(): array
    {
        return [
            'identity_id' => ['required', 'uuid'],
            'organization_id' => ['required', 'uuid'],
            'employee_number' => ['required', 'string', 'max:64'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'department_id' => ['nullable', 'uuid'],
            'hire_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:active,inactive'],
            'metadata' => ['nullable', 'array'],
            'created_by' => ['nullable', 'uuid'],
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function update(): array
    {
        return [
            'identity_id' => ['sometimes', 'uuid'],
            'organization_id' => ['sometimes', 'uuid'],
            'employee_number' => ['sometimes', 'string', 'max:64'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'department_id' => ['nullable', 'uuid'],
            'hire_date' => ['nullable', 'date'],
            'status' => ['sometimes', 'in:active,inactive'],
            'metadata' => ['nullable', 'array'],
            'updated_by' => ['nullable', 'uuid'],
        ];
    }
}
