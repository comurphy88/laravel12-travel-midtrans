

<?php $__env->startSection('title', 'Raven Travel - Perjalanan Elegan & Nyaman'); ?>

<?php $__env->startSection('meta'); ?>
    <meta name="description" content="Layanan bus premium dengan sentuhan elegan. Jelajahi berbagai destinasi wisata terbaik di seluruh Indonesia.">
    <meta name="keywords" content="bus wisata, bus premium, tur candi, perjalanan spiritual, wisata Indonesia">
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    
    <?php echo $__env->make('partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <section id="home" class="hero-classic">
        <div class="ornament ornament-top-left">
            <i class="ri-leaf-line"></i>
        </div>
        <div class="ornament ornament-bottom-right">
            <i class="ri-leaf-line"></i>
        </div>

        <div class="hero__container">
            <div class="hero__content" data-aos="fade-up">
                <span class="hero__badge">
                    <i class="ri-vip-crown-line"></i> Pengalaman Perjalanan Premium
                </span>
                <h1 class="hero__title">
                    Perjalanan<br>
                    <span class="text-gold">Elegan & Nyaman</span>
                </h1>
                <p class="hero__description">
                    Mulailah petualangan di mana kemewahan klasik bertemu kenyamanan modern.
                    Nikmati seni perjalanan yang elegan dengan layanan bus premium kami ke berbagai destinasi wisata di seluruh Indonesia.
                </p>

                <div class="hero__stats">
                    <div class="stat-item">
                        <h3 class="counter" data-target="<?php echo e($stats['destinations']); ?>">0</h3>
                        <p>Destinasi</p>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <h3>4.9<span class="small">/5</span></h3>
                        <p>Rating Pelanggan</p>
                    </div>
                </div>

                <div class="hero__cta">
                    <a href="<?php echo e(route('destinations.index')); ?>" class="btn btn-gold btn-lg">
                        Jelajahi Destinasi
                        <i class="ri-arrow-right-line"></i>
                    </a>
                    <button class="btn-play" data-video="https://www.youtube.com/embed/dQw4w9WgXcQ">
                        <i class="ri-play-circle-fill"></i>
                        <span>Tonton Cerita Kami</span>
                    </button>
                </div>
            </div>

            <div class="hero__image" data-aos="fade-left" data-aos-delay="200">
                <div class="image-frame">
                    <img src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=800" alt="Bus Premium" loading="eager">
                    <div class="image-badge">
                        <i class="ri-shield-check-line"></i>
                        <span>Keamanan<br>Tersertifikasi</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="scroll-indicator">
            <span>Gulir untuk Jelajahi</span>
            <i class="ri-arrow-down-line"></i>
        </div>
    </section>

    
    <section class="booking-section">
        <div class="section__container">
            <div class="booking-card-elegant" data-aos="zoom-in" style="text-align: center; background: linear-gradient(135deg, #f5f1e8 0%, #f9f7f4 100%);">
                <div class="booking-header" style="justify-content: center; flex-direction: column; border: none;">
                    <i class="ri-map-journey-line" style="font-size: 40px; color: #c9a469; margin-bottom: 15px;"></i>
                    <h3 style="margin: 0;">Siap untuk Perjalanan Anda?</h3>
                    <p style="color: #999; margin-top: 10px; margin-bottom: 20px;">Pilih destinasi wisata Anda dan pesan perjalanan sekarang</p>
                </div>
                <a href="<?php echo e(route('destinations.index')); ?>" class="btn btn-gold" style="display: inline-block;">
                    <i class="ri-compass-3-line"></i>
                    Jelajahi Destinasi
                </a>
            </div>
        </div>
    </section>

    
    <section class="features-section">
        <div class="section__container">
            <div class="section-header-classic" data-aos="fade-up">
                <span class="section-subtitle">Mengapa Memilih Kami</span>
                <h2 class="section__title">Seni Perjalanan Premium</h2>
                <div class="title-ornament">
                    <span></span>
                    <i class="ri-sparkling-line"></i>
                    <span></span>
                </div>
            </div>

            <div class="features-grid">
                <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon-wrapper">
                        <i class="ri-vip-crown-fill"></i>
                    </div>
                    <h4>Kenyamanan VIP</h4>
                    <p>Kursi reclining mewah dengan ruang kaki ekstra, sistem hiburan pribadi, dan pengaturan suhu.</p>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon-wrapper">
                        <i class="ri-shield-check-fill"></i>
                    </div>
                    <h4>Utamakan Keselamatan</h4>
                    <p>Pengemudi tersertifikasi, pelacakan GPS, pemantauan CCTV, dan perawatan kendaraan rutin untuk ketenangan pikiran Anda.</p>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon-wrapper">
                        <i class="ri-wifi-fill"></i>
                    </div>
                    <h4>Tetap Terhubung</h4>
                    <p>WiFi berkecepatan tinggi, port USB, dan stopkontak di setiap kursi untuk konektivitas tanpa hambatan.</p>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-icon-wrapper">
                        <i class="ri-restaurant-fill"></i>
                    </div>
                    <h4>Layanan Kuliner</h4>
                    <p>Minuman, camilan, dan makanan gratis yang disiapkan oleh tim hospitality di dalam bus.</p>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-icon-wrapper">
                        <i class="ri-customer-service-2-fill"></i>
                    </div>
                    <h4>Dukungan 24/7</h4>
                    <p>Layanan pelanggan sepanjang waktu siap membantu Anda sebelum, selama, dan setelah perjalanan.</p>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="600">
                    <div class="feature-icon-wrapper">
                        <i class="ri-price-tag-3-fill"></i>
                    </div>
                    <h4>Harga Terbaik</h4>
                    <p>Tarif kompetitif tanpa biaya tersembunyi. Diskon pemesanan awal dan reward loyalitas tersedia.</p>
                </div>
            </div>
        </div>
    </section>

    
    <section id="destinations" class="destinations-section">
        <div class="section__container">
            <div class="section-header-classic" data-aos="fade-up">
                <span class="section-subtitle">Destinasi Wisata</span>
                <h2 class="section__title">Destinasi Wisata Populer</h2>
                <div class="title-ornament">
                    <span></span>
                    <i class="ri-sparkling-line"></i>
                    <span></span>
                </div>
                <p class="section__description">
                    Jelajahi berbagai destinasi wisata terbaik kami di seluruh Indonesia
                </p>
            </div>

            <div class="destinations-grid">
                <?php $__empty_1 = true; $__currentLoopData = $destinations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $dest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="destination-card-elegant" data-aos="fade-up" data-aos-delay="<?php echo e($index * 100); ?>">
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
                                    Pesan Sekarang
                                    <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center" style="grid-column: 1/-1; padding: 3rem;">
                        <p>Belum ada destinasi tersedia saat ini.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="text-center mt-5" data-aos="fade-up">
                <a href="<?php echo e(route('destinations.index')); ?>" class="btn btn-outline-dark btn-lg">
                    Lihat Semua <?php echo e($stats['destinations']); ?> Destinasi
                    <i class="ri-arrow-right-line"></i>
                </a>
            </div>
        </div>
    </section>

    
    <section id="services" class="services-section">
        <div class="section__container">
            <div class="section-header-classic" data-aos="fade-up">
                <span class="section-subtitle">Layanan Kami</span>
                <h2 class="section__title">Layanan Premium, Perjalanan Tenang</h2>
                <div class="title-ornament">
                    <span></span>
                    <i class="ri-sparkling-line"></i>
                    <span></span>
                </div>
                <p class="section__description">
                    Fasilitas kelas atas untuk memastikan perjalanan Anda aman, nyaman, dan berkesan.
                </p>
            </div>

            <div class="services-grid">
                <div class="service-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-icon"><i class="ri-bus-2-fill"></i></div>
                    <h4>Armada Premium</h4>
                    <p>Bus modern dengan kursi reclining, cabin ergonomis, dan ruang kaki ekstra.</p>
                </div>
                <div class="service-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-icon"><i class="ri-timer-flash-line"></i></div>
                    <h4>Jadwal Tepat</h4>
                    <p>Keberangkatan tepat waktu dengan monitoring perjalanan real-time.</p>
                </div>
                <div class="service-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="service-icon"><i class="ri-shield-star-line"></i></div>
                    <h4>Keamanan Terjaga</h4>
                    <p>Pengemudi tersertifikasi, CCTV, dan protokol keselamatan ketat.</p>
                </div>
                <div class="service-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="service-icon"><i class="ri-headphone-line"></i></div>
                    <h4>Concierge 24/7</h4>
                    <p>Tim support siap membantu sebelum, saat, dan setelah perjalanan.</p>
                </div>
            </div>
        </div>
    </section>


    
    <section id="gallery" class="gallery-section">
        <div class="section__container">
            <div class="section-header-classic" data-aos="fade-up">
                <span class="section-subtitle">Galeri</span>
                <h2 class="section__title">Momen dalam Perjalanan</h2>
                <div class="title-ornament">
                    <span></span>
                    <i class="ri-sparkling-line"></i>
                    <span></span>
                </div>
                <p class="section__description">
                    Sekilas pengalaman perjalanan Raven Travel dari berbagai destinasi pilihan.
                </p>
            </div>

            <div class="gallery-grid">
                <?php $__currentLoopData = $gallery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="gallery-item" data-aos="zoom-in" data-aos-delay="<?php echo e($loop->iteration * 100); ?>">
                        <img src="<?php echo e($item->image); ?>" alt="<?php echo e($item->title); ?>" loading="lazy">
                        <div class="gallery-overlay"><?php echo e($item->title); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    
    <section id="testimonials" class="testimonials-section">
        <div class="section__container">
            <div class="section-header-classic" data-aos="fade-up">
                <span class="section-subtitle">Testimoni</span>
                <h2 class="section__title">Kata Para Pelancong Kami</h2>
                <div class="title-ornament">
                    <span></span>
                    <i class="ri-sparkling-line"></i>
                    <span></span>
                </div>
            </div>

            <div class="swiper testimonials-swiper" data-aos="fade-up" data-aos-delay="200">
                <div class="swiper-wrapper">
                    <?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <div class="quote-mark">&ldquo;</div>
                                <div class="testimonial-content">
                                    <div class="stars">
                                        <?php for($i = 0; $i < $testimonial->rating; $i++): ?>
                                            <i class="ri-star-fill"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="testimonial-text"><?php echo e($testimonial->text); ?></p>
                                    <div class="testimonial-author">
                                        <img src="<?php echo e($testimonial->image); ?>" alt="<?php echo e($testimonial->name); ?>">
                                        <div>
                                            <h5><?php echo e($testimonial->name); ?></h5>
                                            <span><?php echo e($testimonial->role); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </section>

    
    <div class="video-modal" id="videoModal" aria-hidden="true">
        <div class="video-modal__content">
            <button class="video-modal__close" id="closeVideo" aria-label="Close video">
                <i class="ri-close-line"></i>
            </button>
            <div class="video-modal__frame">
                <iframe id="videoFrame" title="Raven Travel Story" src="" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
            </div>
        </div>
    </div>

    
    <section id="newsletter" class="newsletter-classic">
        <div class="section__container">
            <div class="newsletter-content" data-aos="fade-up">
                <div class="newsletter-icon">
                    <i class="ri-mail-line"></i>
                </div>
                <h3>Berlangganan Newsletter Kami</h3>
                <p>Dapatkan penawaran eksklusif, tips perjalanan, dan info destinasi langsung ke email Anda</p>

                <form class="newsletter-form" id="newsletterForm" action="<?php echo e(route('newsletter.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="form-group-newsletter">
                        <input type="email" name="email" placeholder="Masukkan alamat email Anda" autocomplete="email" required>
                        <button type="submit" class="btn btn-gold">
                            Berlangganan
                            <i class="ri-send-plane-fill"></i>
                        </button>
                    </div>
                    <small>Kami menghormati privasi Anda. Berhenti berlangganan kapan saja.</small>
                    <div id="newsletterMessage" style="display:none; margin-top: 10px; font-size: 0.9rem;"></div>
                </form>
            </div>
        </div>
    </section>

    
    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\raven-travel\resources\views/welcome.blade.php ENDPATH**/ ?>