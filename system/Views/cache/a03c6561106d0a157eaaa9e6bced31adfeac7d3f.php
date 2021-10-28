<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo e(APP_NAME . " | " . $title); ?></title>
    <!-- CSS file -->
    <link rel="shortcut icon" type="image/png" href="<?php echo e(assets('imgs/icons/favicon.ico')); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo e(assets('bootstrap/css/bootstrap.min.css')); ?>"/>
        <link rel="stylesheet" type="text/css" href="<?php echo e(assets('css/style.css')); ?>"/>
    <style> .no-js #loader { display: none;  }  .js #loader { display: block; position: absolute; left: 100px; top: 0; }
        .se-pre-con {position: fixed;left: 0;top: 0;width: 100%;height: 100%;z-index: 9999;background: url(<?php echo e(assets('imgs/site/preloader.gif')); ?>) center no-repeat #fff;
        }.list-group {overflow-x: auto;}.footer-link {text-decoration: none;}.footer-link:hover {color: #FFFFFF;text-decoration: none;}
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <?php echo $__env->yieldContent('css'); ?>
</head>
<body>
    <div class="se-pre-con"></div>
    <main class="container">
        <?php $__env->startSection('content'); ?>
            
        <?php echo $__env->yieldSection(); ?>
    </main>
    <div class="container-fluid bg-dark text-light mt-3">
        <br/>
        <div class="d-flex justify-content-between">
            <p>Copyright &copy; <?php echo e(date('Y')); ?> YOSIL</p>
            <div class="d-flex justify-content-end">
                <a href="#" class="footer-link text-muted mr-3">Privacy Policy</a>
                <a href="#" class="footer-link text-muted">Terms & Conditions</a>
            </div>
        </div>
        <br/>
    </div>
    <script src="<?php echo e(assets('jquery/jquery-3.6.0.min.js')); ?>"></script>
    <script src="<?php echo e(assets('bootstrap/js/popper.min.js')); ?>"></script>
    <script src="<?php echo e(assets('bootstrap/js/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(assets('js/script.js')); ?>"></script>
    <script>
        $(window).on('load', function() {
            // Animate loader off screen
            $(".se-pre-con").fadeOut("slow");;
        });
    </script>
    <?php echo $__env->yieldContent('scripts'); ?>
</body><?php /**PATH C:\xampp\htdocs\yosil\app\views/partials/app.blade.php ENDPATH**/ ?>