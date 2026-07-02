<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Controllers\Web;

use Illuminate\Http\Request;
use Modules\Identity\Domain\Models\ApiToken;
use Modules\Identity\Domain\Models\Contact;
use Modules\Identity\Domain\Models\Device;
use Modules\Identity\Domain\Models\EmployeeProfile;
use Modules\Identity\Domain\Models\Identity;
use Modules\Identity\Domain\Models\IdentitySession;
use Modules\Identity\Domain\Models\LoginHistory;
use Modules\Identity\Domain\Models\Team;
use Modules\Identity\Domain\Models\TeamMember;
use Modules\Identity\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;

final class IdentityDashboardWebController
{
    public function index(Request $request): Response
    {
        return BladeRenderer::render('dashboard.index', [
            'title' => 'Identity Dashboard',
            'active' => 'dashboard',
            'cards' => [
                ['label' => 'Identities', 'count' => Identity::query()->count(), 'path' => '/identity/identities'],
                ['label' => 'Teams', 'count' => Team::query()->count(), 'path' => '/identity/teams'],
                ['label' => 'Team Members', 'count' => TeamMember::query()->count(), 'path' => '/identity/team-members'],
                ['label' => 'Employee Profiles', 'count' => EmployeeProfile::query()->count(), 'path' => '/identity/employee-profiles'],
                ['label' => 'Contacts', 'count' => Contact::query()->count(), 'path' => '/identity/contacts'],
                ['label' => 'Devices', 'count' => Device::query()->count(), 'path' => '/identity/devices'],
                ['label' => 'Identity Sessions', 'count' => IdentitySession::query()->count(), 'path' => '/identity/identity-sessions'],
                ['label' => 'Login History', 'count' => LoginHistory::query()->count(), 'path' => '/identity/login-history'],
                ['label' => 'API Tokens', 'count' => ApiToken::query()->count(), 'path' => '/identity/api-tokens'],
            ],
        ]);
    }
}