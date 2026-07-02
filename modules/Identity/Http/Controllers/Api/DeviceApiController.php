<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Identity\Application\Services\DeviceService;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Http\Requests\DeviceRequestRules;
use Modules\Identity\Policies\DevicePolicy;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class DeviceApiController extends ApiController
{
    public function __construct(
        private readonly DeviceService $service,
        private readonly DevicePolicy $policy,
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
            $device = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->view($device)) {
            return $this->forbidden();
        }

        return $this->item($device);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->policy->create()) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, DeviceRequestRules::create());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        $device = $this->service->create($validated);

        return $this->item($device, Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $device = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->update($device)) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, DeviceRequestRules::update());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        return $this->item($this->service->update($id, $validated));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $device = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->delete($device)) {
            return $this->forbidden();
        }

        $this->service->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
