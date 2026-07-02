

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body p-4">
            <h1 class="h4 mb-3">Email verification</h1>
            <form method="post" action="/api/auth/email/verify">
                <div class="mb-3">
                    <label class="form-label">Verification token</label>
                    <input type="text" name="token" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Verify email</button>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\AxiomOS\axiomos-core\modules\Authentication\Resources\views/auth/email-verification.blade.php ENDPATH**/ ?>