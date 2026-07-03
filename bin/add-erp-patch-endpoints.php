<?php

declare(strict_types=1);

/**
 * Adds update() to ERP entity services and PATCH routes for status/workflow transitions.
 * Usage: php bin/add-erp-patch-endpoints.php
 */

$modulesPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'modules';
$modules = [
    'Sales', 'Purchase', 'Inventory', 'HR', 'CRM', 'Projects',
    'Manufacturing', 'POS', 'FixedAssets', 'Budgeting', 'Reporting',
];

$updateMethod = <<<'PHP'

    public function update(array $payload): array
    {
        $id = (string) ($payload['id'] ?? '');
        if ($id === '') {
            throw new \InvalidArgumentException('id is required');
        }
        $model = $this->repository->find($id);
        if ($model === null) {
            throw new \RuntimeException('Record not found');
        }
        unset($payload['id']);

        return $this->repository->update($model, $payload)->toArray();
    }
PHP;

foreach ($modules as $moduleName) {
    $servicesDir = $modulesPath . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Services';
    if (! is_dir($servicesDir)) {
        continue;
    }

    foreach (glob($servicesDir . DIRECTORY_SEPARATOR . '*Service.php') as $serviceFile) {
        $base = basename($serviceFile);
        if (str_contains($base, 'Posting') || str_contains($base, 'Dashboard')) {
            continue;
        }
        $content = file_get_contents($serviceFile);
        if ($content === false || str_contains($content, 'function update(')) {
            continue;
        }
        $content = preg_replace('/\n}\s*$/', $updateMethod . "\n}\n", $content) ?? $content;
        file_put_contents($serviceFile, $content);
        echo "Service update: {$moduleName}/{$base}\n";
    }

    $apiFiles = glob($modulesPath . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Api' . DIRECTORY_SEPARATOR . '*ApiController.php');
    foreach ($apiFiles as $apiFile) {
        $content = file_get_contents($apiFile);
        if ($content === false || str_contains($content, "isMethod('patch')")) {
            continue;
        }
        $content = preg_replace_callback(
            '/public function (\w+)\(Request \$request\): Response \{\s*\n\s*if \(\$request->isMethod\(\'post\'\)\) \{\s*\n\s*return \$this->ok\(\[\'data\' => \$this->(\w+)Service->create\(\$request->all\(\)\)\], Response::HTTP_CREATED\);\s*\n\s*\}\s*\n/',
            static function (array $m): string {
                $serviceVar = $m[2];
                if ($m[1] === 'postingSubmit' || $m[1] === 'dashboard') {
                    return $m[0];
                }

                return $m[0] . "        if (\$request->isMethod('patch')) {\n            return \$this->ok(['data' => \$this->{$serviceVar}Service->update(\$request->all())]);\n        }\n";
            },
            $content,
        ) ?? $content;
        file_put_contents($apiFile, $content);
        echo "ApiController patch: {$moduleName}/" . basename($apiFile) . "\n";
    }

    $routesFile = $modulesPath . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . 'routes.php';
    if (! is_file($routesFile)) {
        continue;
    }
    $routes = file_get_contents($routesFile);
    if ($routes === false || str_contains($routes, '->patch(')) {
        continue;
    }
    $routes = preg_replace_callback(
        '/\$router->post\(\'(\/api\/[^\/]+\/[^\']+)\', static fn\(Request \$request\) => \$api->(\w+)\(\$request\)\);/',
        static function (array $m): string {
            return $m[0] . "\n    \$router->patch('{$m[1]}', static fn(Request \$request) => \$api->{$m[2]}(\$request));";
        },
        $routes,
    ) ?? $routes;
    file_put_contents($routesFile, $routes);
    echo "Routes patch: {$moduleName}\n";
}

echo "Done.\n";
