<?php

declare(strict_types=1);

namespace Modules\Membership\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Membership\Application\DTOs\CreateMembershipDTO;
use Modules\Membership\Application\DTOs\UpdateMembershipDTO;
use Modules\Membership\Application\Services\MembershipService;
use Modules\Membership\Application\Support\ListQuery;
use Modules\Membership\Http\Requests\MembershipRequestRules;
use Modules\Membership\Policies\MembershipPolicy;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class MembershipApiController extends ApiController
{
    public function __construct(
        private readonly MembershipService $service,
        private readonly MembershipPolicy $policy,
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
            $membership = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->view($membership)) {
            return $this->forbidden();
        }

        return $this->item($membership);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->policy->create()) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, MembershipRequestRules::create());
            $membership = $this->service->create(CreateMembershipDTO::fromArray($validated));
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        } catch (RuntimeException $exception) {
            return $this->conflict($exception->getMessage());
        }

        return $this->item($membership, Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $membership = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->update($membership)) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, MembershipRequestRules::update());
            $updated = $this->service->update($id, UpdateMembershipDTO::fromArray($validated));
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        } catch (RuntimeException $exception) {
            return $this->conflict($exception->getMessage());
        }

        return $this->item($updated);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $membership = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->delete($membership)) {
            return $this->forbidden();
        }

        $this->service->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
