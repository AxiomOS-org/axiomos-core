<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Controllers\Web;

use Illuminate\Http\Request;
use Modules\Identity\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;

final class IdentityCrudWebController
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private const PAGES = [
        'identities' => ['title' => 'Identities', 'api' => '/api/identities', 'columns' => ['code', 'display_name', 'identity_type', 'status'], 'fields' => ['organization_id', 'identity_type', 'code', 'display_name', 'legal_name', 'email', 'phone', 'status']],
        'teams' => ['title' => 'Teams', 'api' => '/api/teams', 'columns' => ['code', 'name', 'organization_id', 'status'], 'fields' => ['organization_id', 'code', 'name', 'description', 'leader_identity_id', 'status']],
        'team-members' => ['title' => 'Team Members', 'api' => '/api/team-members', 'columns' => ['team_id', 'identity_id', 'role'], 'fields' => ['team_id', 'identity_id', 'role']],
        'employee-profiles' => ['title' => 'Employee Profiles', 'api' => '/api/employee-profiles', 'columns' => ['employee_number', 'identity_id', 'organization_id', 'status'], 'fields' => ['identity_id', 'organization_id', 'employee_number', 'job_title', 'department_id', 'hire_date', 'status']],
        'contacts' => ['title' => 'Contacts', 'api' => '/api/contacts', 'columns' => ['contact_type', 'value', 'identity_id', 'status'], 'fields' => ['identity_id', 'contact_type', 'value', 'is_primary', 'status']],
        'devices' => ['title' => 'Devices', 'api' => '/api/devices', 'columns' => ['device_type', 'fingerprint', 'identity_id', 'status'], 'fields' => ['identity_id', 'device_type', 'fingerprint', 'user_agent', 'last_seen_at', 'status']],
        'identity-sessions' => ['title' => 'Identity Sessions', 'api' => '/api/identity-sessions', 'columns' => ['identity_id', 'ip_address', 'started_at', 'status'], 'fields' => ['identity_id', 'session_token_hash', 'ip_address', 'user_agent', 'started_at', 'expires_at', 'status']],
        'login-history' => ['title' => 'Login History', 'api' => '/api/login-history', 'columns' => ['identity_id', 'user_id', 'success', 'logged_at'], 'fields' => ['identity_id', 'user_id', 'ip_address', 'user_agent', 'success', 'logged_at', 'status']],
        'api-tokens' => ['title' => 'API Tokens', 'api' => '/api/api-tokens', 'columns' => ['name', 'identity_id', 'status', 'expires_at'], 'fields' => ['identity_id', 'name', 'scopes', 'expires_at', 'status']],
    ];

    public function index(Request $request, string $entity): Response
    {
        $page = self::PAGES[$entity] ?? null;

        if ($page === null) {
            return new Response('Identity admin page not found.', Response::HTTP_NOT_FOUND);
        }

        return BladeRenderer::render('crud.index', [
            'title' => $page['title'],
            'active' => $entity,
            'entity' => $entity,
            'entityLabel' => $page['title'],
            'apiBase' => $page['api'],
            'columns' => $page['columns'],
            'fields' => $page['fields'],
        ]);
    }
}