<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Identity\Application\Services\EmployeeProfileService;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Http\Requests\EmployeeProfileRequestRules;
use Modules\Identity\Policies\EmployeeProfilePolicy;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class EmployeeProfileApiController extends ApiController
{
    public function __construct(
        private readonly EmployeeProfileService $service,
        private readonly EmployeeProfilePolicy $policy,
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
            $profile = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->view($profile)) {
            return $this->forbidden();
        }

        return $this->item($profile);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->policy->create()) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, EmployeeProfileRequestRules::create());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        try {
            $profile = $this->service->create($validated);
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        return $this->item($profile, Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $profile = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->update($profile)) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, EmployeeProfileRequestRules::update());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        try {
            $updated = $this->service->update($id, $validated);
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        return $this->item($updated);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $profile = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->delete($profile)) {
            return $this->forbidden();
        }

        $this->service->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
