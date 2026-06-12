<nav class="navbar-elegant">
    <div class="nav__container">
        <div class="nav__header">
            <div class="nav__logo">
                <a href="<?php echo e(url('/')); ?>" class="logo-elegant">
                    <i class="ri-bus-line"></i>
                    <span>Raven</span>
                    <span class="logo-travel">Travel</span>
                </a>
            </div>
            <div class="nav__menu__btn" id="menu-btn">
                <i class="ri-menu-line"></i>
            </div>
        </div>
        <ul class="nav__links" id="nav-links">
            <li><a href="<?php echo e(url('/')); ?>#home">Beranda</a></li>
            <li><a href="<?php echo e(route('destinations.index')); ?>">Destinasi</a></li>
            <li><a href="<?php echo e(url('/')); ?>#gallery">Galeri</a></li>
            <li><a href="<?php echo e(url('/')); ?>#testimonials">Testimoni</a></li>
            <li><a href="<?php echo e(url('/')); ?>#contact">Kontak</a></li>
        </ul>
        <div class="nav__actions">
            <?php if(auth()->guard()->check()): ?>
                <div class="user-menu">
                    <button class="btn-user" id="userMenuBtn">
                        <i class="ri-user-line"></i>
                        <span><?php echo e(Auth::user()->name ?? 'User'); ?></span>
                        <i class="ri-arrow-down-s-line"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="<?php echo e(route('dashboard')); ?>"><i class="ri-dashboard-line"></i> Dasbor</a>
                        <a href="<?php echo e(route('bookings.index')); ?>"><i class="ri-ticket-2-line"></i> Pesanan Saya</a>
                        <a href="<?php echo e(route('profile.edit')); ?>"><i class="ri-user-settings-line"></i> Profil</a>
                        <?php if(Auth::user()->role === 'admin'): ?>
                            <a href="<?php echo e(route('admin.dashboard')); ?>"><i class="ri-admin-line"></i> Admin Panel</a>
                        <?php endif; ?>
                        <form method="POST" action="<?php echo e(route('logout')); ?>" style="margin:0;">
                            <?php echo csrf_field(); ?>
                            <a href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); this.closest('form').submit();"><i class="ri-logout-box-line"></i> Keluar</a>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="btn-link">Masuk</a>
                <a href="<?php echo e(route('register')); ?>" class="btn btn-gold">Daftar</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<?php /**PATH C:\xampp\htdocs\raven-travel\resources\views/partials/navbar.blade.php ENDPATH**/ ?>