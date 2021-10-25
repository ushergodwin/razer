<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]>      <html class="no-js"> <!--<![endif]-->
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo e(APP_NAME . " | " . $title); ?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="<?php echo e(assets('bootstrap/css/bootstrap.min.css')); ?>"/>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <?php if(!empty(response()->message())): ?>
                    <?php echo response()->message(); ?>

                <?php endif; ?>
                
                    
                    <?php $__currentLoopData = response()->errors(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                        <span class="text-danger"><?php echo e($error); ?></span> <br/>

                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <div class="card card-body shadow mt-3">
                    <form action="<?php echo e(url('user/store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="text" class="mb-3 form-control" name="name" value="<?php echo e(old('name')); ?>"/>
                        <input type="text" class="mb-3 form-control" name="phone" value="<?php echo e(old('phone')); ?>"/>
                        <input type="text" class="mb-3 form-control" name="gender" value="<?php echo e(old('gender')); ?>"/>
                        <input type="text" class="mb-3 form-control" name="_token" value="<?php echo e(old('_token')); ?>"/>
                        <input type="text" class="mb-3 form-control" name="_method" value="<?php echo e(old('_method')); ?>"/>
                        <input type="email" class="mb-3 form-control" name="email" value="<?php echo e(old('email')); ?>"/>
                        <input type="date" class="mb-3 form-control" name="date_of_birth" value="<?php echo e(old('date_of_birth')); ?>"/>
                        <button type="submit" class="mb-3 btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html><?php /**PATH C:\xampp\htdocs\phaser\app\Templates/test.blade.php ENDPATH**/ ?>