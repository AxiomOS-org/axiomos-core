<?php

declare(strict_types=1);

use App\Core\Http\HttpKernel;
use Illuminate\Http\Request;

/**
 * AxiomOS public entrypoint.
 *
 * Load configuration -> Boot kernel -> Load modules -> Dispatch events -> Return response.
 */
/** @var HttpKernel $kernel */
$kernel = require __DIR__ . '/../bootstrap/app.php';

$request = Request::capture();

$response = $kernel->handle($request);
$response->send();

$kernel->terminate();
