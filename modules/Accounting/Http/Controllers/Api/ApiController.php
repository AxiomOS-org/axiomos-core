<?php
declare(strict_types=1);
namespace Modules\Accounting\Http\Controllers\Api;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
abstract class ApiController { protected function ok(array $payload = [], int $status = Response::HTTP_OK): JsonResponse { return new JsonResponse($payload, $status); } }

