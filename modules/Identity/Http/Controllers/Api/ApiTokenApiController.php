<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Identity\Application\Services\ApiTokenService;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Http\Requests\ApiTokenRequestRules;
use Modules\Identity\Policies\ApiTokenPolicy;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class ApiTokenApiController extends ApiController
{
    public function __construct(
        private readonly ApiTokenService $service,
        private readonly ApiTokenPolicy $policy,
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

        return $this->collection($this->service->list($request->query('identity_id')));
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $token = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->view($token)) {
            return $this->forbidden();
        }

        return $this->item($token);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->policy->create()) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, ApiTokenRequestRules::create());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        $token = $this->service->create($validated);

        return $this->item($token, Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $token = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->update($token)) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, ApiTokenRequestRules::update());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        return $this->item($this->service->update($id, $validated));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $token = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->delete($token)) {
            return $this->forbidden();
        }

        $this->service->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
