<?php
declare(strict_types=1);
namespace Modules\Reporting\Http\Controllers\Api;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
abstract class ApiController {
    protected function ok(array $payload = [], int $status = Response::HTTP_OK): JsonResponse {
        return new JsonResponse($payload, $status);
    }
    protected function companyId(\Illuminate\Http\Request $request): string {
        $fromQuery = trim((string) $request->query('company_id', $request->input('company_id', '')));
        if ($fromQuery !== '') {
            return $fromQuery;
        }
        return trim((string) $request->headers->get('X-Company-Id', ''));
    }
}
