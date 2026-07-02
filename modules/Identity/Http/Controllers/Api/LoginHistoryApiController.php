<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Identity\Application\Services\LoginHistoryService;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Http\Requests\LoginHistoryRequestRules;
use Modules\Identity\Policies\LoginHistoryPolicy;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class LoginHistoryApiController extends ApiController
{
    public function __construct(
        private readonly LoginHistoryService $service,
        private readonly LoginHistoryPolicy $policy,
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
            $history = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->view($history)) {
            return $this->forbidden();
        }

        return $this->item($history);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->policy->create()) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, LoginHistoryRequestRules::create());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        $history = $this->service->create($validated);

        return $this->item($history, Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $history = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->update($history)) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, LoginHistoryRequestRules::update());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        return $this->item($this->service->update($id, $validated));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $history = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->delete($history)) {
            return $this->forbidden();
        }

        $this->service->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
