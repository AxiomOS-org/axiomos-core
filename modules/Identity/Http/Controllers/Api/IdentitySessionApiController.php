<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Identity\Application\Services\IdentitySessionService;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Http\Requests\IdentitySessionRequestRules;
use Modules\Identity\Policies\IdentitySessionPolicy;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class IdentitySessionApiController extends ApiController
{
    public function __construct(
        private readonly IdentitySessionService $service,
        private readonly IdentitySessionPolicy $policy,
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
            $session = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->view($session)) {
            return $this->forbidden();
        }

        return $this->item($session);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->policy->create()) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, IdentitySessionRequestRules::create());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        $session = $this->service->create($validated);

        return $this->item($session, Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $session = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->update($session)) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, IdentitySessionRequestRules::update());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        return $this->item($this->service->update($id, $validated));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $session = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->delete($session)) {
            return $this->forbidden();
        }

        $this->service->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
