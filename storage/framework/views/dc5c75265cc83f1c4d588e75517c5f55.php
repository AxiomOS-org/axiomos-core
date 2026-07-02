<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'AxiomOS'); ?> — ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 260px; }
        body { background: #f4f6f9; min-height: 100vh; }
        .sidebar { width: var(--sidebar-width); min-height: 100vh; background: #1e293b; color: #e2e8f0; }
        .sidebar .nav-link { color: #94a3b8; border-radius: .5rem; margin: .15rem .75rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #334155; color: #fff; }
        .brand { font-weight: 700; letter-spacing: .03em; color: #38bdf8 !important; }
        .content-wrap { margin-left: var(--sidebar-width); }
        .card { border: 0; box-shadow: 0 1px 3px rgba(15,23,42,.08); }
        .table thead th { font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; color: #64748b; }
        .badge-status-active { background: #dcfce7; color: #166534; }
        .badge-status-inactive { background: #f1f5f9; color: #475569; }
        .badge-status-suspended { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
<div class="d-flex">
    <aside class="sidebar position-fixed top-0 start-0 py-3">
        <div class="px-3 mb-4">
            <a href="/" class="brand text-decoration-none fs-5">AxiomOS ERP</a>
            <div class="small text-secondary mt-1">Organization Module</div>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link <?php echo e(($active ?? '') === 'organizations' ? 'active' : ''); ?>" href="/organizations"><i class="bi bi-building me-2"></i>Organizations</a>
            <a class="nav-link <?php echo e(($active ?? '') === 'companies' ? 'active' : ''); ?>" href="/companies"><i class="bi bi-briefcase me-2"></i>Companies</a>
            <a class="nav-link <?php echo e(($active ?? '') === 'branches' ? 'active' : ''); ?>" href="/branches"><i class="bi bi-diagram-3 me-2"></i>Branches</a>
            <a class="nav-link <?php echo e(($active ?? '') === 'departments' ? 'active' : ''); ?>" href="/departments"><i class="bi bi-people me-2"></i>Departments</a>
            <hr class="border-secondary mx-3 my-2">
            <a class="nav-link" href="/identity"><i class="bi bi-shield-lock me-2"></i>Identity Dashboard</a>
            <a class="nav-link" href="/identity/identities"><i class="bi bi-person-badge me-2"></i>Identity Admin</a>
            <a class="nav-link" href="/identity/teams"><i class="bi bi-diagram-2 me-2"></i>Identity Teams</a>
            <a class="nav-link" href="/identity/employee-profiles"><i class="bi bi-file-earmark-person me-2"></i>Identity Profiles</a>
            <a class="nav-link" href="/health"><i class="bi bi-heart-pulse me-2"></i>Health</a>
        </nav>
    </aside>
    <main class="content-wrap flex-grow-1 p-4">
        <?php echo $__env->yieldContent('content'); ?>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\AxiomOS\axiomos-core\modules\Organization\Resources\views/layouts/admin.blade.php ENDPATH**/ ?>