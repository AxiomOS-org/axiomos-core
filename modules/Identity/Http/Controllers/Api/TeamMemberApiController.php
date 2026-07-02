<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Identity\Application\Services\TeamMemberService;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Http\Requests\TeamMemberRequestRules;
use Modules\Identity\Policies\TeamMemberPolicy;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class TeamMemberApiController extends ApiController
{
    public function __construct(
        private readonly TeamMemberService $service,
        private readonly TeamMemberPolicy $policy,
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

        return $this->collection($this->service->list($request->query('team_id'), $request->query('identity_id')));
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $member = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->view($member)) {
            return $this->forbidden();
        }

        return $this->item($member);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->policy->create()) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, TeamMemberRequestRules::create());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        $member = $this->service->create($validated);

        return $this->item($member, Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $member = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->update($member)) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, TeamMemberRequestRules::update());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        return $this->item($this->service->update($id, $validated));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $member = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->delete($member)) {
            return $this->forbidden();
        }

        $this->service->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}