<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Identity\Application\Services\TeamService;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Http\Requests\TeamRequestRules;
use Modules\Identity\Policies\TeamPolicy;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class TeamApiController extends ApiController
{
    public function __construct(
        private readonly TeamService $service,
        private readonly TeamPolicy $policy,
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
            $team = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->view($team)) {
            return $this->forbidden();
        }

        return $this->item($team);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->policy->create()) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, TeamRequestRules::create());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        $team = $this->service->create($validated);

        return $this->item($team, Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $team = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->update($team)) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, TeamRequestRules::update());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        return $this->item($this->service->update($id, $validated));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $team = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->delete($team)) {
            return $this->forbidden();
        }

        $this->service->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
