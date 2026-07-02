<?php

declare(strict_types=1);

namespace Modules\Organization\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Organization\Application\DTOs\CreateEntityDTO;
use Modules\Organization\Application\Support\ListQuery;
use Modules\Organization\Application\DTOs\UpdateEntityDTO;
use Modules\Organization\Application\Services\OrganizationService;
use Modules\Organization\Http\Requests\EntityRequestRules;
use Modules\Organization\Policies\OrganizationPolicy;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class OrganizationApiController extends ApiController
{
    public function __construct(
        private readonly OrganizationService $service,
        private readonly OrganizationPolicy $policy,
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

        return $this->collection($this->service->list());
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $organization = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->view($organization)) {
            return $this->forbidden();
        }

        return $this->item($organization);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->policy->create()) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, EntityRequestRules::create());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        $organization = $this->service->create(CreateEntityDTO::fromArray($validated));

        return $this->item($organization, Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $organization = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->update($organization)) {
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
            $organization = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->delete($organization)) {
            return $this->forbidden();
        }

        $this->service->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
