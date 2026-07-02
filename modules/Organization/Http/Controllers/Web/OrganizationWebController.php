<?php

declare(strict_types=1);

namespace Modules\Organization\Http\Controllers\Web;

use Illuminate\Http\Request;
use Modules\Organization\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;

final class OrganizationWebController
{
    public function index(Request $request): Response
    {
        return BladeRenderer::render('organizations.index', [
            'title' => 'Organizations',
            'active' => 'organizations',
            'apiBase' => '/api/organizations',
            'entityLabel' => 'Organization',
        ]);
    }
}
