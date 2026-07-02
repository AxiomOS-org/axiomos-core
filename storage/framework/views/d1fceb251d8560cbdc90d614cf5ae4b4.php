<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'AxiomOS Security'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 280px; }
        body { background: #f4f6f9; min-height: 100vh; }
        .sidebar { width: var(--sidebar-width); min-height: 100vh; background: #0f172a; color: #cbd5e1; overflow-y: auto; }
        .sidebar .nav-link { color: #94a3b8; border-radius: .5rem; margin: .15rem .75rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #1e293b; color: #fff; }
        .sidebar .section-title { color: #64748b; font-size: .72rem; letter-spacing: .05em; margin: 1rem .9rem .35rem; text-transform: uppercase; }
        .brand { font-weight: 700; letter-spacing: .03em; color: #38bdf8 !important; }
        .content-wrap { margin-left: var(--sidebar-width); }
        .card { border: 0; box-shadow: 0 1px 3px rgba(15, 23, 42, .08); }
    </style>
</head>
<body>
<div class="d-flex">
    <aside class="sidebar position-fixed top-0 start-0 py-3">
        <div class="px-3 mb-3">
            <a href="/security" class="brand text-decoration-none fs-5">AxiomOS Security</a>
            <div class="small text-secondary mt-1">Authorization Platform</div>
        </div>
        <div class="section-title">Security</div>
        <nav class="nav flex-column">
            <a class="nav-link <?php echo e(($active ?? '') === 'dashboard' ? 'active' : ''); ?>" href="/security/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
            <a class="nav-link <?php echo e(($active ?? '') === 'roles' ? 'active' : ''); ?>" href="/security/roles"><i class="bi bi-shield-lock me-2"></i>Roles</a>
            <a class="nav-link <?php echo e(($active ?? '') === 'permissions' ? 'active' : ''); ?>" href="/security/permissions"><i class="bi bi-key me-2"></i>Permissions</a>
            <a class="nav-link <?php echo e(($active ?? '') === 'sessions' ? 'active' : ''); ?>" href="/security/sessions"><i class="bi bi-door-open me-2"></i>Sessions</a>
            <a class="nav-link <?php echo e(($active ?? '') === 'login-history' ? 'active' : ''); ?>" href="/security/login-history"><i class="bi bi-clock-history me-2"></i>Login History</a>
            <a class="nav-link" href="/users"><i class="bi bi-people me-2"></i>Users</a>
            <a class="nav-link" href="/identity"><i class="bi bi-person-badge me-2"></i>Identity</a>
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
<?php /**PATH D:\AxiomOS\axiomos-core\modules\Authorization\Resources\views/layouts/admin.blade.php ENDPATH**/ ?>