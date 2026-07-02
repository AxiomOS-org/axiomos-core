<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?php echo e($title ?? 'Accounting'); ?></h1>
    <span class="badge text-bg-light">API: <?php echo e($apiBase ?? ''); ?></span>
</div>
<div class="card">
    <div class="card-body">
        <p class="text-muted mb-2">Entity: <?php echo e($entityLabel ?? ''); ?></p>
        <div class="row">
            <div class="col-md-6">
                <h6>Columns</h6>
                <ul class="mb-0"><?php $__currentLoopData = $columns ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><code><?php echo e($column); ?></code></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
            </div>
            <div class="col-md-6">
                <h6>Fields</h6>
                <ul class="mb-0"><?php $__currentLoopData = $fields ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><code><?php echo e($field); ?></code></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\AxiomOS\axiomos-core\modules\Accounting\Resources\views/crud/index.blade.php ENDPATH**/ ?>