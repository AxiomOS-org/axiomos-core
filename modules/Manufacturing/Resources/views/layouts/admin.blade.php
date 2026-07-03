<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'AxiomOS' }} — Manufacturing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 260px; }
        body { background: #f4f6f9; min-height: 100vh; }
        .sidebar { width: var(--sidebar-width); min-height: 100vh; background: #0f172a; color: #e2e8f0; }
        .sidebar .nav-link { color: #94a3b8; border-radius: .5rem; margin: .15rem .75rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #1e293b; color: #fff; }
        .brand { font-weight: 700; letter-spacing: .03em; color: #38bdf8 !important; }
        .content-wrap { margin-left: var(--sidebar-width); }
        .card { border: 0; box-shadow: 0 1px 3px rgba(15,23,42,.08); }
    </style>
</head>
<body>
<div class="d-flex">
    <aside class="sidebar position-fixed top-0 start-0 py-3">
        <div class="px-3 mb-4">
            <a href="/" class="brand text-decoration-none fs-5">AxiomOS ERP</a>
            <div class="small text-secondary mt-1">Manufacturing Foundation</div>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link {{ ($active ?? '') === 'dashboard' ? 'active' : '' }}" href="/manufacturing/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
            <a class="nav-link {{ ($active ?? '') === 'accounts' ? 'active' : '' }}" href="/manufacturing/accounts"><i class="bi bi-journal-text me-2"></i>Chart of Accounts</a>
            <a class="nav-link {{ ($active ?? '') === 'documents' ? 'active' : '' }}" href="/manufacturing/documents"><i class="bi bi-file-earmark-text me-2"></i>Documents</a>
            <a class="nav-link {{ ($active ?? '') === 'journals' ? 'active' : '' }}" href="/manufacturing/journals"><i class="bi bi-receipt me-2"></i>Journals</a>
            <a class="nav-link {{ ($active ?? '') === 'fiscal-years' ? 'active' : '' }}" href="/manufacturing/fiscal-years"><i class="bi bi-calendar-range me-2"></i>Fiscal Years</a>
            <a class="nav-link {{ ($active ?? '') === 'periods' ? 'active' : '' }}" href="/manufacturing/periods"><i class="bi bi-calendar3 me-2"></i>Periods</a>
            <hr class="border-secondary mx-3 my-2">
            <a class="nav-link" href="/organizations"><i class="bi bi-building me-2"></i>Organizations</a>
            <a class="nav-link" href="/health"><i class="bi bi-heart-pulse me-2"></i>Health</a>
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
