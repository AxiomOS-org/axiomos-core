<?php
declare(strict_types=1);
namespace Modules\Accounting\Http\Controllers\Api;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
abstract class ApiController { protected function ok(array $payload = [], int $status = Response::HTTP_OK): JsonResponse { return new JsonResponse($payload, $status); } protected function safe(callable $callback, int $success = Response::HTTP_OK): JsonResponse { try { $payload = $callback(); return $this->ok(is_array($payload) ? $payload : ['data' => $payload], $success); } catch (\Throwable $exception) { return $this->ok(['errors' => [$exception->getMessage()]], Response::HTTP_UNPROCESSABLE_ENTITY); } } }

