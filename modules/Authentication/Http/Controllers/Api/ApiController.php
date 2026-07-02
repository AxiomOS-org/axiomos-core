<?php

declare(strict_types=1);

namespace Modules\Authentication\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Authentication\Http\Support\FormRequestValidator;
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

    /**
     * @param array<string, mixed> $data
     */
    protected function ok(array $data = [], int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['data' => $data], $status);
    }

    protected function message(string $message, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['message' => $message], $status);
    }
}
