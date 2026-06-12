

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-16 text-center">
    <div class="max-w-md mx-auto">
        <h1 class="text-6xl font-bold text-gray-800 mb-4">404</h1>
        <h2 class="text-2xl font-semibold text-gray-600 mb-8">Halaman Tidak Ditemukan</h2>
        <p class="text-gray-500 mb-8">
            Halaman yang Anda cari tidak ada atau telah dipindahkan.
        </p>
        <a href="<?php echo e(route('home')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Kembali ke Beranda
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\raven-travel\resources\views/errors/404.blade.php ENDPATH**/ ?>