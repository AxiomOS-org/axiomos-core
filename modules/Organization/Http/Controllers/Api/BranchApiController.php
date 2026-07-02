<?php

declare(strict_types=1);

namespace Modules\Organization\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Organization\Application\DTOs\CreateEntityDTO;
use Modules\Organization\Application\DTOs\UpdateEntityDTO;
use Modules\Organization\Application\Support\ListQuery;
use Modules\Organization\Application\Services\BranchService;
use Modules\Organization\Http\Requests\EntityRequestRules;
use Modules\Organization\Policies\BranchPolicy;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class BranchApiController extends ApiController
{
    public function __construct(
        private readonly BranchService $service,
        private readonly BranchPolicy $policy,
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

        return $this->collection($this->service->list($request->query('company_id')));
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $branch = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->view($branch)) {
            return $this->forbidden();
        }

        return $this->item($branch);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->policy->create()) {
            return $this->forbidden();
        }

        $rules = array_merge(EntityRequestRules::create(), [
            'company_id' => ['required', 'uuid'],
        ]);

        try {
            $validated = $this->validated($request, $rules);
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        try {
            $branch = $this->service->create(
                (string) $validated['company_id'],
                CreateEntityDTO::fromArray($validated),
            );
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        return $this->item($branch, Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $branch = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->update($branch)) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, EntityRequestRules::update());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        return $this->item($this->service->update($id, UpdateEntityDTO::fromArray($validated)));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $branch = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->delete($branch)) {
            return $this->forbidden();
        }

        $this->service->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
