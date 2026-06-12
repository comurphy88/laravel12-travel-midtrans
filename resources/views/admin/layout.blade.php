<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin') - Raven Travel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Lato:wght@300;400;600;700&family=Cinzel:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { 
            font-family: 'Lato', sans-serif; 
            background-color: #faf8f3;
        }

        .admin-main {
            margin-left: 240px;
            min-height: 100vh;
            background-color: #f5f1e8;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }

        .admin-main > * {
            width: 100%;
        }

        .admin-sidebar {
            min-height: 100vh;
            background: #131627;
            color: #fff;
            display: flex;
            flex-direction: column;
            width: 240px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .admin-sidebar .nav-link {
            color: rgba(255,255,255,.75);
            padding: .6rem 1rem;
            border-radius: 10px;
            margin: 2px 6px;
            font-size: .9rem;
            transition: all .2s;
        }

        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            color: #d4af37;
            background: rgba(212,175,55,.12);
        }

        .admin-sidebar .nav-link i { margin-right: .5rem; }

        .admin-sidebar .nav-wrap { flex: 1; }

        .admin-page .card {
            border-radius: 14px;
            border: 1px solid #e7ddd0;
            box-shadow: 0 8px 20px rgba(26, 26, 46, 0.06);
            background-color: #fff;
        }

        .admin-page .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e7ddd0;
            font-weight: 600;
            border-radius: 14px 14px 0 0 !important;
            color: #1a1a2e;
        }

        .admin-page .table {
            font-size: .9rem;
            margin-bottom: 0;
        }

        .admin-page .table thead th {
            background-color: #f5f1e8;
            border-bottom: 1px solid #e7ddd0;
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #9f7c39;
            padding: 0.75rem;
            font-family: 'Cinzel', serif;
            font-weight: 600;
        }

        .admin-page .table td {
            vertical-align: middle;
            font-size: .85rem;
            padding: 0.75rem;
        }

        .admin-page .table-sm td {
            padding: 0.6rem 0.75rem;
        }

        .admin-page .btn-primary {
            background: linear-gradient(135deg, #d4af37, #b8941f);
            border: none;
            color: #1a1a2e;
            font-weight: 700;
        }

        .admin-page .btn-primary:hover {
            color: #1a1a2e;
            box-shadow: 0 8px 16px rgba(212, 175, 55, 0.25);
        }

        .stat-card {
            background-color: #fff;
            border: 1px solid #e7ddd0;
            border-left: 4px solid #d4af37;
            border-radius: 8px;
            transition: all 0.3s ease;
            padding: 1rem !important;
        }

        .stat-card:hover {
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.15);
            transform: translateY(-2px);
        }

        .stat-card h3 {
            color: #1a1a2e;
            font-family: 'Cinzel', serif;
            font-weight: 700;
            margin-bottom: 0.25rem;
            font-size: 1.8rem;
        }

        .stat-card h6 {
            color: #9f7c39;
            font-size: .75rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .admin-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: .75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .admin-badge.success { background: rgba(46,160,67,.12); color: #1a7f37; }
        .admin-badge.danger { background: rgba(220,53,69,.12); color: #cf222e; }
        .admin-badge.warning { background: rgba(255,193,7,.15); color: #856404; }
        .admin-badge.gold { background: rgba(212,175,55,.15); color: #9f7c39; }
        .admin-badge.neutral { background: rgba(100,100,100,.1); color: #666; }

        .admin-topbar {
            border-bottom: 1px solid #e7e0d6;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .admin-topbar h1 {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            color: #1a1a2e;
        }

        .admin-table code {
            background-color: #f5f1e8;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid #e7ddd0;
            font-size: .82rem;
            color: #1a1a2e;
        }

        .modal-content { 
            border-radius: 14px;
            background-color: #fff;
            border: 1px solid #e7ddd0;
        }
        
        .modal-header { 
            border-bottom: 1px solid #e7ddd0;
            background-color: #fff;
        }
        
        .modal-footer { 
            border-top: 1px solid #e7ddd0;
            background-color: transparent;
        }

        .form-label { 
            font-weight: 600; 
            font-size: .85rem; 
            color: #2a241d;
            font-family: 'Cinzel', serif;
        }

        .form-control:focus, .form-select:focus {
            border-color: #d4af37 !important;
            box-shadow: 0 0 0 .2rem rgba(212,175,55,.15) !important;
        }

        .form-control, .form-select {
            border-color: #e7ddd0;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .admin-sidebar { display: none; }
            .admin-main { margin-left: 0; padding: 1rem; }
        }

        .admin-main { max-height: 100vh; overflow-y: auto; }
    </style>
    @yield('styles')
</head>
<body class="admin-page">
    <div class="d-flex">
        <aside class="admin-sidebar">
            <div class="p-3 border-bottom border-secondary">
                <a href="{{ url('/') }}" class="text-white text-decoration-none fw-bold">
                    <i class="bi bi-bus-front"></i> Raven Travel
                </a>
            </div>
            <div class="nav-wrap">
                <nav class="nav flex-column p-2">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dasbor
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.destinations.*') ? 'active' : '' }}" href="{{ route('admin.destinations.index') }}">
                        <i class="bi bi-geo-alt"></i> Destinasi
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}">
                        <i class="bi bi-ticket-perforated"></i> Pesanan
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people"></i> Pengguna
                    </a>

                    <hr class="border-secondary my-2">

                    <a class="nav-link {{ request()->routeIs('admin.galleries.*') ? 'active' : '' }}" href="{{ route('admin.galleries.index') }}">
                        <i class="bi bi-images"></i> Galeri
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}" href="{{ route('admin.testimonials.index') }}">
                        <i class="bi bi-chat-quote"></i> Testimoni
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">
                        <i class="bi bi-star"></i> Review
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.promo-codes.*') ? 'active' : '' }}" href="{{ route('admin.promo-codes.index') }}">
                        <i class="bi bi-tag"></i> Kode Promo
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.cancellation-policies.*') ? 'active' : '' }}" href="{{ route('admin.cancellation-policies.index') }}">
                        <i class="bi bi-x-circle"></i> Kebijakan Batal
                    </a>

                    <hr class="border-secondary my-2">

                    <a class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}">
                        <i class="bi bi-bell"></i> Notifikasi
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.email-logs.*') ? 'active' : '' }}" href="{{ route('admin.email-logs.index') }}">
                        <i class="bi bi-envelope"></i> Log Email
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.logs') ? 'active' : '' }}" href="{{ route('admin.logs') }}">
                        <i class="bi bi-journal-text"></i> Log Aktivitas
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                        <i class="bi bi-gear"></i> Pengaturan
                    </a>
                </nav>
            </div>
            <div class="p-3 border-top border-secondary">
                <a href="{{ url('/') }}" class="text-white-50 small text-decoration-none"><i class="bi bi-box-arrow-left"></i> Kembali ke Situs</a>
            </div>
        </aside>

        <main class="admin-main grow p-4">
            <div class="d-flex justify-content-between align-items-center admin-topbar">
                <h1 class="h4 mb-0">@yield('heading', 'Panel Admin')</h1>
                <span class="text-muted">{{ auth()->user()->name }} <i class="bi bi-person-badge"></i></span>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
