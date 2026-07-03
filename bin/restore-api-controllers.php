<?php

declare(strict_types=1);

$modules = [
    'Sales', 'Purchase', 'Inventory', 'HR', 'CRM', 'POS', 'Manufacturing',
    'Projects', 'FixedAssets', 'Budgeting', 'Reporting',
];

$template = <<<'PHP'
<?php
declare(strict_types=1);
namespace Modules\{MODULE}\Http\Controllers\Api;
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

PHP;

$base = dirname(__DIR__);

foreach ($modules as $module) {
    $path = $base . "/modules/{$module}/Http/Controllers/Api/ApiController.php";
    if (! is_file($path) || filesize($path) < 100) {
        file_put_contents($path, str_replace('{MODULE}', $module, $template));
        echo "Restored {$module}\n";
    }
}
