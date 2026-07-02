<?php

declare(strict_types=1);

namespace Modules\Users\Http\Controllers\Web;

use Illuminate\Http\Request;
use Modules\Users\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;

final class UserWebController
{
    public function index(Request $request): Response
    {
        return BladeRenderer::render('users.index', [
            'title' => 'Users',
            'active' => 'users',
            'apiBase' => '/api/users',
        ]);
    }
}
