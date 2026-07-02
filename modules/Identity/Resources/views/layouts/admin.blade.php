<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'AxiomOS' }} — Identity Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 300px; }
        body { background: #f4f6f9; min-height: 100vh; }
        .sidebar { width: var(--sidebar-width); min-height: 100vh; background: #0f172a; color: #e2e8f0; overflow-y: auto; }
        .sidebar .nav-link { color: #94a3b8; border-radius: .5rem; margin: .15rem .75rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #1e293b; color: #fff; }
        .sidebar .section-label { color: #64748b; font-size: .72rem; letter-spacing: .06em; text-transform: uppercase; margin: 1rem .95rem .35rem; }
        .brand { font-weight: 700; letter-spacing: .03em; color: #38bdf8 !important; }
        .content-wrap { margin-left: var(--sidebar-width); }
        .card { border: 0; box-shadow: 0 1px 3px rgba(15,23,42,.08); }
        .table thead th { font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; color: #64748b; }
        .badge-status-active { background: #dcfce7; color: #166534; }
        .badge-status-inactive { background: #f1f5f9; color: #475569; }
        .badge-status-suspended, .badge-status-revoked, .badge-status-expired { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
<div class="d-flex">
    <aside class="sidebar position-fixed top-0 start-0 py-3">
        <div class="px-3 mb-3">
            <a href="/identity" class="brand text-decoration-none fs-5">AxiomOS Identity</a>
            <div class="small text-secondary mt-1">Identity Platform Admin</div>
        </div>

        <div class="section-label">Overview</div>
        <nav class="nav flex-column">
            <a class="nav-link {{ ($active ?? '') === 'dashboard' ? 'active' : '' }}" href="/identity"><i class="bi bi-grid-1x2 me-2"></i>Dashboard</a>
        </nav>

        <div class="section-label">Cross Module</div>
        <nav class="nav flex-column">
            <a class="nav-link" href="/users"><i class="bi bi-person-badge me-2"></i>Users</a>
            <a class="nav-link" href="/memberships"><i class="bi bi-people me-2"></i>Memberships</a>
            <a class="nav-link" href="/organizations"><i class="bi bi-diagram-3 me-2"></i>Org Links</a>
        </nav>

        <div class="section-label">Identity Core</div>
        <nav class="nav flex-column">
            <a class="nav-link {{ ($active ?? '') === 'identities' ? 'active' : '' }}" href="/identity/identities"><i class="bi bi-fingerprint me-2"></i>Identities</a>
            <a class="nav-link {{ ($active ?? '') === 'teams' ? 'active' : '' }}" href="/identity/teams"><i class="bi bi-collection me-2"></i>Teams</a>
            <a class="nav-link {{ ($active ?? '') === 'team-members' ? 'active' : '' }}" href="/identity/team-members"><i class="bi bi-person-lines-fill me-2"></i>Team Members</a>
            <a class="nav-link {{ ($active ?? '') === 'employee-profiles' ? 'active' : '' }}" href="/identity/employee-profiles"><i class="bi bi-person-vcard me-2"></i>Profiles</a>
        </nav>

        <div class="section-label">Channels & Security</div>
        <nav class="nav flex-column">
            <a class="nav-link {{ ($active ?? '') === 'contacts' ? 'active' : '' }}" href="/identity/contacts"><i class="bi bi-envelope me-2"></i>Contacts</a>
            <a class="nav-link {{ ($active ?? '') === 'devices' ? 'active' : '' }}" href="/identity/devices"><i class="bi bi-laptop me-2"></i>Devices</a>
            <a class="nav-link {{ ($active ?? '') === 'identity-sessions' ? 'active' : '' }}" href="/identity/identity-sessions"><i class="bi bi-clock-history me-2"></i>Sessions</a>
            <a class="nav-link {{ ($active ?? '') === 'login-history' ? 'active' : '' }}" href="/identity/login-history"><i class="bi bi-journal-check me-2"></i>Login History</a>
            <a class="nav-link {{ ($active ?? '') === 'api-tokens' ? 'active' : '' }}" href="/identity/api-tokens"><i class="bi bi-key me-2"></i>API Tokens</a>
        </nav>
    </aside>

    <main class="content-wrap flex-grow-1 p-4">
        @yield('content')
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
