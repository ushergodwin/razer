
<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-3">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-6 col-xl-6">
                <h4 class="font-weight-bold text-info">LOGIN</h4>
                <div class="card card-body shadow">
                    <form action="<?php echo e(url('auth/user')); ?>" method="post" id="loginForm">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label for="email" class="sr-only">Email</label>
                            <i class="fas fa-envelope text-success"></i> <input type="text" name="email" class="form-control bg-light" placeholder="enter your email or phone number" autocomplete="off" required/>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="sr-only">Password</label>
                            <div class="input-group">
                                <a href="javascript:void(0)" class="text-success" id="show-password"><i class="fas fa-eye-slash text-success"></i></a>
                                <a href="javascript:void(0)" class="text-success d-none" id="hide-password"><i class="fas fa-eye text-success"></i></a>
                            </div>
                            <input type="password" name="password" id="password" placeholder="password" class="form-control bg-light" autocomplete="off" required/>
                        </div>
                        <div class="mb-3">
                            <label for="login" class="sr-only">Login</label>
                            <input type="hidden" name="login" value="1"/>
                            <button type="submit" class="btn btn-success w-100 login-btn">PROCEED</button>
                        </div>
                        Don't have an account? <a href="<?php echo e(url('account')); ?>">Create Account</a>
                    </form>
                    <div class="response"></div>
                </div>
              
            </div>
        </div>
    </div> 
<?php $__env->stopSection(); ?>

<?php echo $__env->make('partials.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Inmate-management-system\app\views/index.blade.php ENDPATH**/ ?>