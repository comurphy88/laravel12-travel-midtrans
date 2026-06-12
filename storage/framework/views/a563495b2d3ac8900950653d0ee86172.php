<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <title><?php echo $__env->yieldContent('title', 'Raven Travel - Perjalanan Elegan & Nyaman'); ?></title>
        <?php if (! empty(trim($__env->yieldContent('meta')))): ?>
            <?php echo $__env->yieldContent('meta'); ?>
        <?php endif; ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Lato:wght@300;400;700&family=Cinzel:wght@400;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
        <?php echo $__env->yieldPushContent('styles'); ?>

        
        <link rel="stylesheet" href="<?php echo e(asset('assets/raven-travel.css')); ?>">
    </head>
    <body>
        <?php echo $__env->yieldContent('body'); ?>

        
        <script src="<?php echo e(asset('assets/raven-travel.js')); ?>" defer></script>
        <?php echo $__env->yieldPushContent('scripts'); ?>
    </body>
</html>
<?php /**PATH C:\xampp\htdocs\raven-travel\resources\views/layouts/app.blade.php ENDPATH**/ ?>