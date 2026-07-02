

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body p-4">
            <h1 class="h4 mb-3">Forgot password</h1>
            <form method="post" action="/api/auth/password/forgot">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Send reset link</button>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\AxiomOS\axiomos-core\modules\Authentication\Resources\views/auth/forgot-password.blade.php ENDPATH**/ ?>