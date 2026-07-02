<?php

declare(strict_types=1);

namespace Modules\Authorization\Http\Controllers\Web;

use Illuminate\Http\Request;
use Modules\Authorization\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;

final class SecurityCrudWebController
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private const PAGES = [
        'roles' => [
            'title' => 'Roles',
            'api' => '/api/security/roles',
            'columns' => ['slug', 'name', 'organization_id', 'status'],
            'fields' => ['organization_id', 'slug', 'name', 'description', 'is_system', 'status'],
        ],
        'permissions' => [
            'title' => 'Permissions',
            'api' => '/api/security/permissions',
            'columns' => ['slug', 'name', 'module', 'action', 'status'],
            'fields' => ['slug', 'name', 'module', 'action', 'description', 'is_system', 'status'],
        ],
        'sessions' => [
            'title' => 'Security Sessions',
            'api' => '/api/identity-sessions',
            'columns' => ['identity_id', 'ip_address', 'started_at', 'expires_at', 'status'],
            'fields' => [],
        ],
        'login-history' => [
            'title' => 'Security Login History',
            'api' => '/api/login-history',
            'columns' => ['identity_id', 'user_id', 'ip_address', 'logged_at', 'success'],
            'fields' => [],
        ],
    ];

    public function index(Request $request, string $entity): Response
    {
        $page = self::PAGES[$entity] ?? null;

        if ($page === null) {
            return new Response('Security page not found.', Response::HTTP_NOT_FOUND);
        }

        return BladeRenderer::render('crud.index', [
            'title' => $page['title'],
            'active' => $entity,
            'entity' => $entity,
            'entityLabel' => $page['title'],
            'apiBase' => $page['api'],
            'columns' => $page['columns'],
            'fields' => $page['fields'],
            'readOnly' => in_array($entity, ['sessions', 'login-history'], true),
        ]);
    }
}
