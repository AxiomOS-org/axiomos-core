<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$kernel = App\Core\Http\HttpKernelFactory::create(dirname(__DIR__));
$response = $kernel->handle(Illuminate\Http\Request::create('/api/platform/plugins', 'GET'));
echo $response->getContent(), PHP_EOL;
