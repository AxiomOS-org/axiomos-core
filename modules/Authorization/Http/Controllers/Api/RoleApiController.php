<?php

declare(strict_types=1);

namespace Modules\Authorization\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Authorization\Application\Services\RoleService;
use Modules\Authorization\Application\Support\ListQuery;
use Modules\Authorization\Http\Requests\RoleRequestRules;
use Modules\Authorization\Policies\AuthorizationRolePolicy;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class RoleApiController extends ApiController
{
    public function __construct(
        private readonly RoleService $service,
        private readonly AuthorizationRolePolicy $policy,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        if (! $this->policy->viewAny($request)) {
            return $this->forbidden();
        }

        if (ListQuery::wantsPagination($request)) {
            return $this->paginated($this->service->paginate(ListQuery::fromRequest($request)));
        }

        return $this->collection($this->service->list($request->query('organization_id')));
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $role = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->view($request, $role)) {
            return $this->forbidden();
        }

        return $this->item($role);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->policy->create($request)) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, RoleRequestRules::create());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        $permissionIds = $validated['permission_ids'] ?? null;
        unset($validated['permission_ids']);

        $role = $this->service->create($validated);

        if (is_array($permissionIds)) {
            $role = $this->service->syncPermissions($role->id, array_values($permissionIds));
        }

        return $this->item($role, Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $role = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->update($request, $role)) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, RoleRequestRules::update());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        $permissionIds = $validated['permission_ids'] ?? null;
        unset($validated['permission_ids']);

        $role = $this->service->update($id, $validated);

        if (is_array($permissionIds)) {
            $role = $this->service->syncPermissions($role->id, array_values($permissionIds));
        }

        return $this->item($role);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $role = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->delete($request, $role)) {
            return $this->forbidden();
        }

        $this->service->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
