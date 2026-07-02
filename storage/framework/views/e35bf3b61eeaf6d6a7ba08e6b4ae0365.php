<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?php echo e($title ?? 'Accounting'); ?></h1>
</div>
<div class="row g-3">
    <?php $__currentLoopData = $cards ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small"><?php echo e($card['label']); ?></div>
                    <div class="display-6"><?php echo e($card['count']); ?></div>
                    <a href="<?php echo e($card['path']); ?>" class="stretched-link">Open</a>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\AxiomOS\axiomos-core\modules\Accounting\Resources\views/dashboard/index.blade.php ENDPATH**/ ?>