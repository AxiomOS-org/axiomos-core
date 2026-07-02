<?php

declare(strict_types=1);

namespace Modules\Authorization\Http\Controllers\Web;

use Illuminate\Http\Request;
use Modules\Authorization\Domain\Models\AuthorizationPermission;
use Modules\Authorization\Domain\Models\AuthorizationRole;
use Modules\Authorization\Domain\Models\AuthorizationRoleAssignment;
use Modules\Authorization\Http\Support\BladeRenderer;
use Modules\Identity\Domain\Models\IdentitySession;
use Modules\Identity\Domain\Models\LoginHistory;
use Symfony\Component\HttpFoundation\Response;

final class SecurityDashboardWebController
{
    public function index(Request $request): Response
    {
        return BladeRenderer::render('dashboard.index', [
            'title' => 'Security Dashboard',
            'active' => 'dashboard',
            'cards' => [
                ['label' => 'Roles', 'count' => AuthorizationRole::query()->count(), 'path' => '/security/roles'],
                ['label' => 'Permissions', 'count' => AuthorizationPermission::query()->count(), 'path' => '/security/permissions'],
                ['label' => 'Assignments', 'count' => AuthorizationRoleAssignment::query()->count(), 'path' => '/security/roles'],
                ['label' => 'Sessions', 'count' => IdentitySession::query()->count(), 'path' => '/security/sessions'],
                ['label' => 'Login History', 'count' => LoginHistory::query()->count(), 'path' => '/security/login-history'],
            ],
        ]);
    }
}
