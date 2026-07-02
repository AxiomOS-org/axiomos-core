<?php

declare(strict_types=1);

use App\Core\Http\HttpKernelFactory;

/**
 * Application bootstrap.
 *
 * Builds and returns the AxiomOS HTTP kernel. The public entrypoint captures the
 * request, hands it to the kernel and sends the response.
 */
require __DIR__ . '/../vendor/autoload.php';

return HttpKernelFactory::create(dirname(__DIR__));
