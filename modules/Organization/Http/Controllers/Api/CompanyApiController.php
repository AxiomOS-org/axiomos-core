<?php

declare(strict_types=1);

namespace Modules\Organization\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Organization\Application\DTOs\CreateEntityDTO;
use Modules\Organization\Application\DTOs\UpdateEntityDTO;
use Modules\Organization\Application\Support\ListQuery;
use Modules\Organization\Application\Services\CompanyService;
use Modules\Organization\Http\Requests\EntityRequestRules;
use Modules\Organization\Policies\CompanyPolicy;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class CompanyApiController extends ApiController
{
    public function __construct(
        private readonly CompanyService $service,
        private readonly CompanyPolicy $policy,
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

        return $this->collection($this->service->list($request->query('organization_id')));
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $company = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->view($company)) {
            return $this->forbidden();
        }

        return $this->item($company);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->policy->create()) {
            return $this->forbidden();
        }

        $rules = array_merge(EntityRequestRules::create(), [
            'organization_id' => ['required', 'uuid'],
        ]);

        try {
            $validated = $this->validated($request, $rules);
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        try {
            $company = $this->service->create(
                (string) $validated['organization_id'],
                CreateEntityDTO::fromArray($validated),
            );
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        return $this->item($company, Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $company = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->update($company)) {
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
            $company = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->delete($company)) {
            return $this->forbidden();
        }

        $this->service->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
