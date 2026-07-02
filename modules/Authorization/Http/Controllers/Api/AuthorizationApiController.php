<?php

declare(strict_types=1);

namespace Modules\Authorization\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Authorization\Application\Services\AuthorizationService;
use Modules\Authorization\Http\Requests\RoleAssignmentRequestRules;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizationApiController extends ApiController
{
    public function __construct(private readonly AuthorizationService $service)
    {
    }

    public function assign(Request $request, string $roleId): JsonResponse
    {
        try {
            $validated = $this->validated($request, RoleAssignmentRequestRules::assign());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        $assignment = $this->service->assignRole(
            $roleId,
            (string) ($validated['assignable_type'] ?? 'Modules\\Users\\Domain\\Models\\User'),
            (string) $validated['assignable_id'],
            isset($validated['organization_id']) ? (string) $validated['organization_id'] : null
        );

        return $this->item($assignment, Response::HTTP_CREATED);
    }

    public function revoke(Request $request, string $roleId): JsonResponse
    {
        try {
            $validated = $this->validated($request, RoleAssignmentRequestRules::revoke());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        $this->service->revokeRole(
            $roleId,
            (string) ($validated['assignable_type'] ?? 'Modules\\Users\\Domain\\Models\\User'),
            (string) $validated['assignable_id'],
            isset($validated['organization_id']) ? (string) $validated['organization_id'] : null
        );

        return new JsonResponse(['status' => 'revoked']);
    }

    public function userPermissions(Request $request, string $userId): JsonResponse
    {
        return new JsonResponse([
            'data' => $this->service->getUserPermissions($userId, $request->query('organization_id')),
        ]);
    }

    public function userRoles(Request $request, string $userId): JsonResponse
    {
        return new JsonResponse([
            'data' => $this->service->getUserRoles($userId, $request->query('organization_id')),
        ]);
    }
}
