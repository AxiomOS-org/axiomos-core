<?php

declare(strict_types=1);

namespace Modules\Users\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Users\Application\DTOs\CreateUserDTO;
use Modules\Users\Application\DTOs\UpdateUserDTO;
use Modules\Users\Application\Services\UserService;
use Modules\Users\Application\Support\ListQuery;
use Modules\Users\Http\Requests\UserRequestRules;
use Modules\Users\Policies\UserPolicy;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class UserApiController extends ApiController
{
    public function __construct(
        private readonly UserService $service,
        private readonly UserPolicy $policy,
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
            $user = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->view($user)) {
            return $this->forbidden();
        }

        return $this->item($user);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->policy->create()) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, UserRequestRules::create());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        $user = $this->service->create(CreateUserDTO::fromArray($validated));

        return $this->item($user, Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->update($user)) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, UserRequestRules::update());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        return $this->item($this->service->update($id, UpdateUserDTO::fromArray($validated)));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $user = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->delete($user)) {
            return $this->forbidden();
        }

        $this->service->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
