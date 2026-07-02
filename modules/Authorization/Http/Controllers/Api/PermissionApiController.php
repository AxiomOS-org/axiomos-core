<?php

declare(strict_types=1);

namespace Modules\Authorization\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Authorization\Application\Services\PermissionService;
use Modules\Authorization\Application\Support\ListQuery;
use Modules\Authorization\Http\Requests\PermissionRequestRules;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class PermissionApiController extends ApiController
{
    public function __construct(private readonly PermissionService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        if (ListQuery::wantsPagination($request)) {
            return $this->paginated($this->service->paginate(ListQuery::fromRequest($request)));
        }

        return $this->collection($this->service->list());
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            return $this->item($this->service->get($id));
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $this->validated($request, PermissionRequestRules::create());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        return $this->item($this->service->create($validated), Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        try {
            $validated = $this->validated($request, PermissionRequestRules::update());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        return $this->item($this->service->update($id, $validated));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        $this->service->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
