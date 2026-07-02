<?php

declare(strict_types=1);

namespace Modules\Authorization\Http\Controllers\Api;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Modules\Authorization\Http\Resources\AuthorizationResource;
use Modules\Authorization\Http\Support\FormRequestValidator;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiController
{
    /**
     * @param array<string, list<string>> $rules
     *
     * @return array<string, mixed>
     */
    protected function validated(Request $request, array $rules): array
    {
        return FormRequestValidator::validate($request->all(), $rules);
    }

    protected function validationError(ValidationException $exception): JsonResponse
    {
        return new JsonResponse(['errors' => $exception->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function notFound(string $message): JsonResponse
    {
        return new JsonResponse(['message' => $message], Response::HTTP_NOT_FOUND);
    }

    protected function forbidden(): JsonResponse
    {
        return new JsonResponse(['message' => 'Forbidden.'], Response::HTTP_FORBIDDEN);
    }

    /**
     * @param Collection<int, Model> $items
     */
    protected function collection(Collection $items): JsonResponse
    {
        return new JsonResponse([
            'data' => $items->map(static fn (Model $item): array => AuthorizationResource::base($item))->values()->all(),
        ]);
    }

    protected function paginated(LengthAwarePaginator $paginator): JsonResponse
    {
        return new JsonResponse([
            'data' => collect($paginator->items())
                ->map(static fn (Model $item): array => AuthorizationResource::base($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    protected function item(Model $model, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['data' => AuthorizationResource::base($model)], $status);
    }
}
