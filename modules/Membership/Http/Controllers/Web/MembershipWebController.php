<?php

declare(strict_types=1);

namespace Modules\Membership\Http\Controllers\Web;

use Illuminate\Http\Request;
use Modules\Membership\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;

final class MembershipWebController
{
    public function index(Request $request): Response
    {
        return BladeRenderer::render('memberships.index', [
            'title' => 'Memberships',
            'active' => 'memberships',
            'apiBase' => '/api/memberships',
        ]);
    }
}
