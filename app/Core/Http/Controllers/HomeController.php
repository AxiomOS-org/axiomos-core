<?php

declare(strict_types=1);

namespace App\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Root endpoint confirming the kernel booted.
 */
final class HomeController
{
    public function __invoke(Request $request): Response
    {
        return new Response(
            'AxiomOS Kernel Booted Successfully',
            Response::HTTP_OK,
            ['Content-Type' => 'text/plain; charset=UTF-8'],
        );
    }
}
