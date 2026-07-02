<?php

declare(strict_types=1);

namespace Modules\Organization\Http\Controllers\Web;

use Illuminate\Http\Request;
use Modules\Organization\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;

final class CompanyWebController
{
    public function index(Request $request): Response
    {
        return BladeRenderer::render('companies.index', [
            'title' => 'Companies',
            'active' => 'companies',
            'apiBase' => '/api/companies',
        ]);
    }
}
