<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Identity\Application\DTOs\CreateIdentityDTO;
use Modules\Identity\Application\DTOs\UpdateIdentityDTO;
use Modules\Identity\Application\Services\IdentityService;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Http\Requests\IdentityRequestRules;
use Modules\Identity\Policies\IdentityPolicy;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class IdentityApiController extends ApiController
{
    public function __construct(
        private readonly IdentityService $service,
        private readonly IdentityPolicy $policy,
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
            $identity = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->view($identity)) {
            return $this->forbidden();
        }

        return $this->item($identity);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->policy->create()) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, IdentityRequestRules::create());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        $identity = $this->service->create(CreateIdentityDTO::fromArray($validated));

        return $this->item($identity, Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $identity = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->update($identity)) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, IdentityRequestRules::update());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        return $this->item($this->service->update($id, UpdateIdentityDTO::fromArray($validated)));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $identity = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->delete($identity)) {
            return $this->forbidden();
        }

        $this->service->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
