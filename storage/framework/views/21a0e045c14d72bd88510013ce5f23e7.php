

<?php $__env->startSection('title', 'Destinasi Wisata | Raven Travel'); ?>

<?php $__env->startPush('styles'); ?>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <?php echo $__env->make('partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <section class="page-header">
        <div class="page-header__overlay"></div>
        <div class="page-header__content" data-aos="fade-up">
            <span class="hero__badge"><i class="ri-compass-3-line"></i> Jelajahi Destinasi</span>
            <h1>Destinasi <span class="text-gold">Wisata</span></h1>
            <p style="color: rgba(255,255,255,0.7); margin-top: 0.5rem;">Temukan berbagai destinasi wisata terbaik di seluruh Indonesia</p>
        </div>
    </section>

    
    <section class="destinations-section">
        <div class="section__container">

            
            <form action="<?php echo e(route('destinations.index')); ?>" method="GET" class="destinations-filter" data-aos="fade-up">
                <div class="destinations-filter__search">
                    <i class="ri-search-line"></i>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari destinasi...">
                </div>
                <div class="destinations-filter__sort">
                    <select name="sort" onchange="this.form.submit()">
                        <option value="">Urutkan</option>
                        <option value="rating" <?php echo e(request('sort') === 'rating' ? 'selected' : ''); ?>>Rating Tertinggi</option>
                        <option value="price_low" <?php echo e(request('sort') === 'price_low' ? 'selected' : ''); ?>>Harga Terendah</option>
                        <option value="price_high" <?php echo e(request('sort') === 'price_high' ? 'selected' : ''); ?>>Harga Tertinggi</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-gold"><i class="ri-search-line"></i> Cari</button>
            </form>

            
            <div class="destinations-grid">
                <?php $__empty_1 = true; $__currentLoopData = $destinations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $dest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="destination-card-elegant" data-aos="fade-up" data-aos-delay="<?php echo e(($index % 4) * 100); ?>">
                        <div class="card-image-wrapper">
                            <img src="<?php echo e($dest->image); ?>" alt="<?php echo e($dest->name); ?>" loading="lazy">
                            <div class="card-badge">
                                <i class="ri-star-fill"></i>
                                <?php echo e($dest->rating); ?>

                            </div>
                            <div class="card-overlay">
                                <a href="<?php echo e(route('destinations.show', $dest)); ?>" class="btn-overlay">
                                    <i class="ri-eye-line"></i>
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                        <div class="card-content-elegant">
                            <div class="card-location">
                                <i class="ri-map-pin-line"></i>
                                <?php echo e($dest->location); ?>

                            </div>
                            <h3><?php echo e($dest->name); ?></h3>
                            <p><?php echo e(Str::limit($dest->description, 100)); ?></p>
                            <div class="card-footer-elegant">
                                <div class="price-tag">
                                    <span class="price-label">Mulai</span>
                                    <span class="price-amount"><?php echo e($dest->formatted_price); ?></span>
                                </div>
                                <a href="<?php echo e(route('bookings.create', ['destination' => $dest->id])); ?>" class="btn btn-outline-gold">
                                    Pesan Sekarang <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <i class="ri-map-pin-line"></i>
                        <h3>Tidak ada destinasi ditemukan</h3>
                        <p>Coba ubah kata kunci pencarian Anda.</p>
                        <a href="<?php echo e(route('destinations.index')); ?>" class="btn btn-gold">Atur Ulang Filter</a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($destinations->hasPages()): ?>
                <div class="pagination-wrapper" data-aos="fade-up">
                    <?php echo e($destinations->withQueryString()->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({ duration: 800, once: true });</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\raven-travel\resources\views/destinations/index.blade.php ENDPATH**/ ?>