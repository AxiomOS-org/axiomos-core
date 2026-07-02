<?php

declare(strict_types=1);

namespace Modules\Organization\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Organization\Application\DTOs\CreateEntityDTO;
use Modules\Organization\Application\DTOs\UpdateEntityDTO;
use Modules\Organization\Application\Support\ListQuery;
use Modules\Organization\Application\Services\DepartmentService;
use Modules\Organization\Http\Requests\EntityRequestRules;
use Modules\Organization\Policies\DepartmentPolicy;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class DepartmentApiController extends ApiController
{
    public function __construct(
        private readonly DepartmentService $service,
        private readonly DepartmentPolicy $policy,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        if (! $this->policy->viewAny()) {
            return $this->forbidden();
        }

        if (ListQuery::wantsPagination($request)) {
            return $this->paginated($this->service->paginate(ListQuery::fromRequest($request)));
        }

        return $this->collection($this->service->list($request->query('branch_id')));
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $department = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->view($department)) {
            return $this->forbidden();
        }

        return $this->item($department);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->policy->create()) {
            return $this->forbidden();
        }

        $rules = array_merge(EntityRequestRules::create(), [
            'branch_id' => ['required', 'uuid'],
            'parent_id' => ['nullable', 'uuid'],
        ]);

        try {
            $validated = $this->validated($request, $rules);
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        try {
            $department = $this->service->create(
                (string) $validated['branch_id'],
                CreateEntityDTO::fromArray($validated),
                isset($validated['parent_id']) ? (string) $validated['parent_id'] : null,
            );
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        return $this->item($department, Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $department = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->update($department)) {
            return $this->forbidden();
        }

        $rules = array_merge(EntityRequestRules::update(), [
            'parent_id' => ['nullable', 'uuid'],
        ]);

        try {
            $validated = $this->validated($request, $rules);
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        return $this->item($this->service->update(
            $id,
            UpdateEntityDTO::fromArray($validated),
            isset($validated['parent_id']) ? (string) $validated['parent_id'] : null,
        ));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $department = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->delete($department)) {
            return $this->forbidden();
        }

        $this->service->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
