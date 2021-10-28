

<?php $__env->startSection('content'); ?>
    <div class="row justify-content-center mt-2">
        <div class="col-md-12 col-lg-12 col-xl-12">
            <div class="card card-body shadow bg-light">
                <br/>
                <div class="row">
                    <div class="col-md-12 col-lg-5 col-xl-5">
                        <div class="card card-body border-0 bg-light">
                            <div class="row justify-content-center">
                                <img src="<?php echo e(assets('imgs/site/locator3.jpg')); ?> " class="rounded img-responsive"/>
                            </div>
                            <div class="row justify-content-center">
                                <h1 class="font-weight-bold text-success">YOSIL<i class="fas fa-check-circle"></i> </h1>
                                <span class="text-info">Welcome Your Supermart Item Locator, <br/>
                                Quickly locate your favorite items in a supermarket of your choice. Simply create an account and be good to go
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-7 col-xl-7">
                        <h4 class="font-weight-bold text-info">CREATE ACCOUNT</h4>
                        <br/>
                        <form action="<?php echo e(url('home/register')); ?>" method="post" id="accountForm">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <i class="fas fa-envelope text-success"></i> 
                                <input type="email" name="email" class="form-control-custom bg-light" placeholder="enter your email or phone number" autocomplete="off" required/>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label for="fname">First Name</label>
                                        <input type="text" name="fname" class="form-control-custom bg-light" placeholder="enter your first name " autocomplete="off" required/>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label for="lname">Last Name</label>
                                        <input type="text" name="lname" class="form-control-custom bg-light" placeholder="enter your last name " autocomplete="off" required/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="number" name="phone" class="form-control-custom bg-light" placeholder="enter your phone number" autocomplete="off" required/>
                            </div>
                            <div class="form-group">
                                <label for="password">Password &nbsp;
                                    <a href="javascript:void(0)" class="text-success" id="show-password"><i class="fas fa-eye-slash text-success"></i></a>
                                    <a href="javascript:void(0)" class="text-success d-none" id="hide-password"><i class="fas fa-eye text-success"></i></a>
                                </label>
                            
                                <input type="password" name="password" id="password" placeholder="form a password" class="form-control-custom bg-light" autocomplete="off" required/>
                            </div>
                            <div class="form-group">
                                <label for="password2">Confirm Password</label>
                                <input type="password" name="password2" id="password2" placeholder="retype password" class="form-control-custom bg-light" autocomplete="off" required/>
                            </div>
                            <div class="form-group">
                                <label for="login" class="sr-only">Login</label>
                                <input type="hidden" name="create_account" value="1"/>
                                <button type="submit" id="register-btn" class="btn btn-success w-100">CREATE ACCOUNT</button><br/>
                                Have an account? <a href="<?php echo e(url('/')); ?>">Login</a>
                            </div>
                        </form>
                        <div class="response"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('partials.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\supermarket\app\views/account.blade.php ENDPATH**/ ?>