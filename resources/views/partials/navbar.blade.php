<nav class="navbar-elegant">
    <div class="nav__container">
        <div class="nav__header">
            <div class="nav__logo">
                <a href="{{ url('/') }}" class="logo-elegant">
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
            <li><a href="{{ url('/') }}#home">Beranda</a></li>
            <li><a href="{{ route('destinations.index') }}">Destinasi</a></li>
            <li><a href="{{ url('/') }}#gallery">Galeri</a></li>
            <li><a href="{{ url('/') }}#testimonials">Testimoni</a></li>
            <li><a href="{{ url('/') }}#contact">Kontak</a></li>
        </ul>
        <div class="nav__actions">
            @auth
                <div class="user-menu">
                    <button class="btn-user" id="userMenuBtn">
                        <i class="ri-user-line"></i>
                        <span>{{ Auth::user()->name ?? 'User' }}</span>
                        <i class="ri-arrow-down-s-line"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="{{ route('dashboard') }}"><i class="ri-dashboard-line"></i> Dasbor</a>
                        <a href="{{ route('bookings.index') }}"><i class="ri-ticket-2-line"></i> Pesanan Saya</a>
                        <a href="{{ route('profile.edit') }}"><i class="ri-user-settings-line"></i> Profil</a>
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}"><i class="ri-admin-line"></i> Admin Panel</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                            @csrf
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"><i class="ri-logout-box-line"></i> Keluar</a>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn-link">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-gold">Daftar</a>
            @endauth
        </div>
    </div>
</nav>
