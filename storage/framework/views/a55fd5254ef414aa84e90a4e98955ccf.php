

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Identity Dashboard</h1>
        <p class="text-muted mb-0">Identity operations, user links, memberships, and security surfaces.</p>
    </div>
    <a class="btn btn-outline-primary" href="/identity/identities">
        <i class="bi bi-arrow-right-circle me-1"></i> Open Identity Admin
    </a>
</div>

<div class="row g-3 mb-4">
    <?php $__currentLoopData = $cards ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-6 col-xl-4">
            <a href="<?php echo e($card['path']); ?>" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small"><?php echo e($card['label']); ?></div>
                        <div class="display-6 fw-semibold mt-2"><?php echo e($card['count']); ?></div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="card">
    <div class="card-body">
        <h2 class="h5">Platform Links</h2>
        <p class="text-muted mb-3">Quick access to adjacent modules connected to identity lifecycle flows.</p>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-sm btn-outline-secondary" href="/users">Users</a>
            <a class="btn btn-sm btn-outline-secondary" href="/memberships">Memberships</a>
            <a class="btn btn-sm btn-outline-secondary" href="/organizations">Organizations</a>
            <a class="btn btn-sm btn-outline-secondary" href="/companies">Companies</a>
            <a class="btn btn-sm btn-outline-secondary" href="/departments">Departments</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\AxiomOS\axiomos-core\modules\Identity\Resources\views/dashboard/index.blade.php ENDPATH**/ ?>