@extends('layouts.app')

@section('title', 'Edit Profil | Raven Travel')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endpush

@section('body')
    @include('partials.navbar')

    <section class="page-header page-header--sm">
        <div class="page-header__overlay"></div>
        <div class="page-header__content" data-aos="fade-up">
            <span class="hero__badge"><i class="ri-user-settings-line"></i> Akun</span>
            <h1>Profil <span class="text-gold">Saya</span></h1>
        </div>
    </section>

    <section class="section-elegant">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success" data-aos="fade-up">
                    <i class="ri-check-line"></i> {{ session('success') }}
                </div>
            @endif

            <div class="profile-grid" data-aos="fade-up">
                {{-- Profile Info --}}
                <div class="detail-card">
                    <h2><i class="ri-user-3-line"></i> Informasi Profil</h2>
                    <form method="POST" action="{{ route('profile.update') }}" class="form-elegant">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label class="form-label" for="name">Nama Lengkap</label>
                            <input type="text" id="name" name="name" class="form-input" autocomplete="name" value="{{ old('name', $user->name) }}">
                            @error('name') <small class="form-error">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-input form-input--disabled" autocomplete="email" value="{{ $user->email }}" disabled>
                            <small class="form-hint">Email tidak dapat diubah</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="phone">Telepon</label>
                                <input type="text" id="phone" name="phone" class="form-input" autocomplete="tel" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                                @error('phone') <small class="form-error">{{ $message }}</small> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="city">Kota</label>
                                <input type="text" id="city" name="city" class="form-input" autocomplete="address-level2" value="{{ old('city', $user->city) }}" placeholder="Kota Anda">
                                @error('city') <small class="form-error">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="address">Alamat</label>
                            <textarea id="address" name="address" class="form-textarea" autocomplete="street-address" rows="3" placeholder="Alamat lengkap...">{{ old('address', $user->address) }}</textarea>
                            @error('address') <small class="form-error">{{ $message }}</small> @enderror
                        </div>

                        <button type="submit" class="btn btn-gold btn-lg">
                            <i class="ri-save-line"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>

                {{-- Change Password --}}
                <div class="detail-card">
                    <h2><i class="ri-lock-line"></i> Ubah Password</h2>
                    <form method="POST" action="{{ route('profile.password') }}" class="form-elegant">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label class="form-label" for="current_password">Password Saat Ini</label>
                            <input type="password" id="current_password" name="current_password" class="form-input" autocomplete="current-password" placeholder="••••••••">
                            @error('current_password') <small class="form-error">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">Password Baru</label>
                            <input type="password" id="password" name="password" class="form-input" autocomplete="new-password" placeholder="••••••••">
                            @error('password') <small class="form-error">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" autocomplete="new-password" placeholder="••••••••">
                        </div>

                        <button type="submit" class="btn btn-gold btn-lg">
                            <i class="ri-lock-password-line"></i> Perbarui Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection

@push('scripts')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({ duration: 800, once: true });</script>
@endpush