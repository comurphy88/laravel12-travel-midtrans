@extends('layouts.app')

@section('title', 'Pesan Perjalanan | Raven Travel')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endpush

@section('body')
    @include('partials.navbar')

    <section class="page-header page-header--sm">
        <div class="page-header__overlay"></div>
        <div class="page-header__content" data-aos="fade-up">
            <span class="hero__badge"><i class="ri-ticket-2-line"></i> Pesan Sekarang</span>
            <h1>Pesan <span class="text-gold">Perjalanan</span></h1>
        </div>
    </section>

    <section class="section-elegant">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success" data-aos="fade-up">
                    <i class="ri-check-line"></i> {{ session('success') }}
                </div>
            @endif

            <div class="booking-form-grid" data-aos="fade-up">
                <div class="booking-form-main">
                    <div class="detail-card">
                        <h2><i class="ri-file-list-3-line"></i> Detail Pemesanan</h2>

                        <form method="POST" action="{{ route('bookings.store') }}" class="form-elegant">
                            @csrf

                            {{-- Booking Type --}}
                            @if(!$selectedDestination)
                            <div class="form-group">
                                <!-- DEPRECATED: Rute Bus option removed 2026-05-26, using destination mode only -->
                                <input type="hidden" name="booking_type" value="destination">
                            </div>
                            @endif

                            {{-- Hidden booking type for destination flow --}}
                            @if($selectedDestination)
                            <input type="hidden" name="booking_type" value="destination">
                            @endif

                            {{-- Route Selection (DEPRECATED) --}}
                            @if(!$selectedDestination)
                            <!-- Bus routes disabled 2026-05-26 - routing form removed -->
                            @endif

                            {{-- Destination Selection --}}
                            <div class="form-group" id="destGroup" @if($selectedDestination) @else style="display:none;" @endif>
                                <label class="form-label">{{ $selectedDestination ? 'Konfirmasi Destinasi' : 'Pilih Destinasi' }}</label>
                                <select name="destination_id" class="form-select" id="destSelect" @if($selectedDestination) disabled @endif>
                                    @if(!$selectedDestination)
                                    <option value="">-- Pilih Destinasi --</option>
                                    @endif
                                    @foreach($destinations as $dest)
                                        <option value="{{ $dest->id }}"
                                            data-price="{{ $dest->price }}"
                                            {{ old('destination_id', $selectedDestination?->id) == $dest->id ? 'selected' : '' }}>
                                            {{ $dest->name }} ({{ $dest->location }}) — IDR {{ number_format($dest->price, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($selectedDestination)
                                <input type="hidden" name="destination_id" value="{{ $selectedDestination->id }}">
                                @endif
                                @error('destination_id') <small class="form-error">{{ $message }}</small> @enderror
                            </div>

                            {{-- Travel Date --}}
                            <div class="form-group">
                                <label class="form-label">Tanggal Perjalanan</label>
                                <input type="date" name="travel_date" class="form-input"
                                    value="{{ old('travel_date') }}"
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                @error('travel_date') <small class="form-error">{{ $message }}</small> @enderror
                            </div>

                            {{-- Number of Passengers --}}
                            <div class="form-group">
                                <label class="form-label">Jumlah Penumpang</label>
                                <input type="number" name="num_passengers" class="form-input"
                                    value="{{ old('num_passengers', 1) }}" min="1" max="45"
                                    id="numPassengers" oninput="updatePrice()">
                                @error('num_passengers') <small class="form-error">{{ $message }}</small> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-input"
                                    value="{{ old('email', Auth::user()->email) }}" placeholder="example@email.com">
                                @error('email') <small class="form-error">{{ $message }}</small> @enderror
                            </div>

                            {{-- Phone --}}
                            <div class="form-group">
                                <label class="form-label">Nomor Telepon</label>
                                <input type="tel" name="phone" class="form-input"
                                    value="{{ old('phone') }}" placeholder="+62812345678 atau 081234567890">
                                @error('phone') <small class="form-error">{{ $message }}</small> @enderror
                            </div>

                            {{-- Notes --}}
                            <div class="form-group">
                                <label class="form-label">Catatan <span class="form-optional">(opsional)</span></label>
                                <textarea name="notes" class="form-textarea" rows="3" placeholder="Permintaan khusus...">{{ old('notes') }}</textarea>
                                @error('notes') <small class="form-error">{{ $message }}</small> @enderror
                            </div>

                            <button type="submit" class="btn btn-gold btn-full btn-lg">
                                <i class="ri-check-line"></i> Konfirmasi Booking
                            </button>
                        </form>
                    </div>
                </div>

                <div class="booking-form-sidebar">
                    <div class="detail-card booking-card">
                        <h3><i class="ri-bill-line"></i> Ringkasan</h3>
                        <div class="summary-row">
                            <span>Harga per orang</span>
                            <strong id="unitPrice">IDR 0</strong>
                        </div>
                        <div class="summary-row">
                            <span>Jumlah penumpang</span>
                            <strong id="paxCount">1</strong>
                        </div>
                        <div class="summary-divider"></div>
                        <div class="summary-row summary-total">
                            <span>Total</span>
                            <strong id="totalPrice">IDR 0</strong>
                        </div>
                        <ul class="booking-features" style="margin-top: 1.5rem;">
                            <li><i class="ri-shield-check-line"></i> Pembayaran aman</li>
                            <li><i class="ri-refund-line"></i> Bisa dibatalkan</li>
                            <li><i class="ri-customer-service-2-line"></i> Support 24/7</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection

@push('scripts')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });

        function toggleBookingType() {
            const type = document.querySelector('input[name="booking_type"]:checked')?.value;
            const routeGroup = document.getElementById('routeGroup');
            const destGroup = document.getElementById('destGroup');
            
            // Update visibility only if elements exist (not in destination-specific booking)
            if (routeGroup !== null && destGroup !== null) {
                document.querySelectorAll('.radio-card').forEach(c => c.classList.remove('radio-card--active'));
                document.querySelector('input[name="booking_type"]:checked')?.closest('.radio-card')?.classList.add('radio-card--active');

                if (type === 'route') {
                    routeGroup.style.display = '';
                    destGroup.style.display = 'none';
                } else {
                    routeGroup.style.display = 'none';
                    destGroup.style.display = '';
                }
            }
            updatePrice();
        }

        function updatePrice() {
            const type = document.querySelector('input[name="booking_type"]:checked')?.value || 'destination';
            let price = 0;
            
            if (type === 'route') {
                const routeSelect = document.getElementById('routeSelect');
                const opt = routeSelect?.selectedOptions[0];
                price = parseFloat(opt?.dataset.price || 0);
            } else {
                const destSelect = document.getElementById('destSelect');
                const opt = destSelect?.selectedOptions[0];
                price = parseFloat(opt?.dataset.price || 0);
            }
            
            const pax = parseInt(document.getElementById('numPassengers').value) || 1;
            const unitPrice = document.getElementById('unitPrice');
            const paxCount = document.getElementById('paxCount');
            const totalPrice = document.getElementById('totalPrice');
            
            if (unitPrice) unitPrice.textContent = 'IDR ' + price.toLocaleString('id-ID');
            if (paxCount) paxCount.textContent = pax;
            if (totalPrice) totalPrice.textContent = 'IDR ' + (price * pax).toLocaleString('id-ID');
        }

        document.getElementById('routeSelect')?.addEventListener('change', updatePrice);
        document.getElementById('destSelect')?.addEventListener('change', updatePrice);

        // Init
        toggleBookingType();
    </script>
@endpush
