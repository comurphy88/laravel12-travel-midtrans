@extends('layouts.app')

@section('title', 'Pesan Tiket | Raven Travel')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
        .schedule-card {
            border: 2px solid rgba(212,175,55,0.2);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 10px;
            cursor: pointer;
            transition: border-color .2s, background .2s;
            background: rgba(255,255,255,0.03);
        }
        .schedule-card:hover:not(.schedule-card--full) {
            border-color: #d4af37;
            background: rgba(212,175,55,0.07);
        }
        .schedule-card--selected {
            border-color: #d4af37 !important;
            background: rgba(212,175,55,0.1) !important;
        }
        .schedule-card--full { opacity: .4; cursor: not-allowed; }
        .schedule-card .bus-name { font-weight: 600; font-size: 15px; color: #fff; }
        .schedule-card .bus-type { font-size: 12px; color: #d4af37; }
        .schedule-card .dep-time { font-size: 22px; font-weight: 700; color: #d4af37; }
        .schedule-card .seat-info { font-size: 11px; color: rgba(255,255,255,0.5); }
        .passenger-block {
            border: 1px solid rgba(212,175,55,0.2);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            background: rgba(255,255,255,0.02);
        }
        .passenger-block-title { font-weight: 600; margin-bottom: 0.75rem; color: #d4af37; font-size: 14px; }
        #schedule-loading i { animation: spin 1s linear infinite; font-size: 24px; color: #d4af37; }
    </style>
@endpush

@section('body')
    @include('partials.navbar')

    <section class="page-header page-header--sm">
        <div class="page-header__overlay"></div>
        <div class="page-header__content" data-aos="fade-up">
            <span class="hero__badge"><i class="ri-ticket-2-line"></i> Pemesanan Tiket</span>
            <h1>Pesan <span class="text-gold">Tiket Bus</span></h1>
        </div>
    </section>

    <section class="section-elegant">
        <div class="container">

            @if($errors->any())
                <div class="alert alert-danger" data-aos="fade-up">
                    <i class="ri-error-warning-line"></i>
                    <ul style="margin:0; padding-left:1.25rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('bookings.store') }}" id="booking-form">
                @csrf

                <div class="detail-grid" data-aos="fade-up">
                    <div class="detail-main">

                        {{-- Step 1: Pilih Rute --}}
                        <div class="detail-card">
                            <h2><i class="ri-route-line"></i> Pilih Rute</h2>

                            <div class="form-group">
                                <label class="form-label">Rute Perjalanan <span style="color:#ef4444;">*</span></label>
                                <select name="route_id" id="route_id" class="form-select" required>
                                    <option value="">-- Pilih Rute --</option>
                                    @foreach($routes as $route)
                                        <option value="{{ $route->id }}"
                                            data-distance="{{ $route->distance_km }}"
                                            data-origin="{{ $route->origin }}"
                                            data-destination="{{ $route->destination }}"
                                            {{ old('route_id', $selectedRouteId) == $route->id ? 'selected' : '' }}>
                                            {{ $route->origin }} → {{ $route->destination }}
                                            ({{ $route->distance_km }} km)
                                        </option>
                                    @endforeach
                                </select>
                                @error('route_id')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tanggal Perjalanan <span style="color:#ef4444;">*</span></label>
                                <input type="date" name="travel_date" id="travel_date" class="form-input"
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                    max="{{ date('Y-m-d', strtotime('+1 year')) }}"
                                    value="{{ old('travel_date', $selectedDate) }}" required>
                                @error('travel_date')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Jumlah Penumpang <span style="color:#ef4444;">*</span></label>
                                <input type="number" name="num_passengers" id="num_passengers" class="form-input"
                                    min="1" max="45" value="{{ old('num_passengers', 1) }}" required>
                                @error('num_passengers')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Step 2: Pilih Jadwal --}}
                        <div class="detail-card" id="schedule-section" style="{{ $selectedRouteId ? '' : 'display:none' }}">
                            <h2><i class="ri-bus-line"></i> Pilih Jadwal & Bus</h2>
                            <p style="color:rgba(255,255,255,0.5); font-size:13px; margin-bottom:1rem;">
                                Pilih jadwal keberangkatan dan tipe bus sesuai preferensi Anda.
                            </p>

                            <div id="schedule-loading" style="display:none; text-align:center; padding:1.5rem 0;">
                                <i class="ri-loader-4-line"></i>
                                <p style="color:rgba(255,255,255,0.4); margin-top:0.5rem; font-size:13px;">Memuat jadwal...</p>
                            </div>

                            <div id="schedule-list"></div>

                            <input type="hidden" name="schedule_id" id="schedule_id" value="{{ old('schedule_id') }}">
                            @error('schedule_id')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Step 3: Data Penumpang --}}
                        <div class="detail-card" id="passengers-section" style="display:none">
                            <h2><i class="ri-group-line"></i> Data Penumpang</h2>
                            <div id="passenger-fields"></div>
                        </div>

                        {{-- Promo & Catatan --}}
                        <div class="detail-card">
                            <h2><i class="ri-coupon-line"></i> Promo & Catatan</h2>

                            <div class="form-group">
                                <label class="form-label">Kode Promo</label>
                                <div style="display:flex; gap:8px;">
                                    <input type="text" name="promo_code" id="promo_code" class="form-input"
                                        placeholder="Masukkan kode promo"
                                        value="{{ old('promo_code') }}"
                                        style="text-transform:uppercase;">
                                    <button type="button" id="apply-promo" class="btn btn-outline-gold" style="white-space:nowrap; flex-shrink:0;">
                                        Terapkan
                                    </button>
                                </div>
                                <div id="promo-result" style="margin-top:6px; font-size:13px;"></div>
                                @error('promo_code')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Catatan</label>
                                <textarea name="notes" class="form-textarea" rows="3"
                                    placeholder="Catatan tambahan (opsional)" maxlength="500">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>

                    {{-- Sidebar --}}
                    <div class="detail-sidebar">
                        <div class="detail-card booking-card">
                            <h3><i class="ri-bill-line"></i> Ringkasan Pesanan</h3>

                            <div id="summary-empty" style="color:rgba(255,255,255,0.4); font-size:13px; text-align:center; padding:1rem 0;">
                                Pilih rute dan jadwal untuk melihat estimasi harga.
                            </div>

                            <div id="summary-detail" style="display:none;">
                                <div class="summary-row">
                                    <span>Rute</span>
                                    <strong id="sum-route" style="text-align:right; max-width:60%;">-</strong>
                                </div>
                                <div class="summary-row">
                                    <span>Jarak</span>
                                    <strong id="sum-distance">-</strong>
                                </div>
                                <div class="summary-row">
                                    <span>Bus</span>
                                    <strong id="sum-bus">-</strong>
                                </div>
                                <div class="summary-row">
                                    <span>Jam Berangkat</span>
                                    <strong id="sum-time">-</strong>
                                </div>
                                <div class="summary-row">
                                    <span>Penumpang</span>
                                    <strong id="sum-pax">-</strong>
                                </div>
                                <div class="summary-divider"></div>
                                <div class="summary-row">
                                    <span>Harga/orang</span>
                                    <strong id="sum-price-per-pax">-</strong>
                                </div>
                                <div class="summary-divider"></div>
                                <div class="summary-row summary-total">
                                    <span>Total</span>
                                    <strong class="price-big" id="sum-total">-</strong>
                                </div>
                            </div>

                            <div id="seat-info" style="display:none; margin-top:0.75rem; font-size:12px; color:rgba(255,255,255,0.4); text-align:center;">
                                <i class="ri-seat-line"></i> <span id="seat-count"></span> kursi tersedia
                            </div>

                            <button type="submit" class="btn btn-gold btn-full" style="margin-top:1.25rem;" id="submit-btn" disabled>
                                <i class="ri-secure-payment-line"></i> Lanjut ke Pembayaran
                            </button>

                            <ul class="booking-features" style="margin-top:1rem;">
                                <li><i class="ri-shield-check-line"></i> Pembayaran aman via Midtrans</li>
                                <li><i class="ri-bank-card-line"></i> Transfer, QRIS, e-wallet</li>
                                <li><i class="ri-customer-service-2-line"></i> Support 24/7</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    @include('partials.footer')
@endsection

@push('scripts')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
    AOS.init({ duration: 800, once: true });

    const routeSelect       = document.getElementById('route_id');
    const travelDate        = document.getElementById('travel_date');
    const numPassengers     = document.getElementById('num_passengers');
    const scheduleSection   = document.getElementById('schedule-section');
    const scheduleList      = document.getElementById('schedule-list');
    const scheduleLoading   = document.getElementById('schedule-loading');
    const scheduleIdInput   = document.getElementById('schedule_id');
    const passengersSection = document.getElementById('passengers-section');
    const passengerFields   = document.getElementById('passenger-fields');
    const submitBtn         = document.getElementById('submit-btn');
    const summaryEmpty      = document.getElementById('summary-empty');
    const summaryDetail     = document.getElementById('summary-detail');
    const seatInfo          = document.getElementById('seat-info');
    const seatCount         = document.getElementById('seat-count');

    let selectedSchedule = null;
    let selectedRoute    = null;

    function formatRp(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    async function loadSchedules() {
        const routeId = routeSelect.value;
        const date    = travelDate.value;

        if (!routeId) { scheduleSection.style.display = 'none'; return; }

        scheduleSection.style.display = '';
        scheduleLoading.style.display = 'block';
        scheduleList.innerHTML = '';
        scheduleIdInput.value  = '';
        selectedSchedule = null;
        updateSummary();

        try {
            const url = date
                ? `/schedules/available?route_id=${routeId}&travel_date=${date}`
                : `/schedules?route_id=${routeId}`;

            const res  = await fetch(url);
            const data = await res.json();

            scheduleLoading.style.display = 'none';

            if (!data.length) {
                scheduleList.innerHTML = '<p style="color:rgba(255,255,255,0.4); text-align:center; padding:1rem;">Tidak ada jadwal tersedia untuk rute ini.</p>';
                return;
            }

            const opt = routeSelect.options[routeSelect.selectedIndex];
            selectedRoute = {
                id: routeId,
                name: opt.dataset.origin + ' → ' + opt.dataset.destination,
                distance: opt.dataset.distance,
            };

            scheduleList.innerHTML = data.map(s => `
                <div class="schedule-card ${s.available_seats === 0 ? 'schedule-card--full' : ''}"
                     data-id="${s.id}"
                     data-bus="${s.bus_name}"
                     data-time="${s.departure_time}"
                     data-price="${s.price_per_km}"
                     data-seats="${s.available_seats ?? s.capacity}"
                     onclick="selectSchedule(this)">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:44px; height:44px; border-radius:10px; background:rgba(212,175,55,0.1); border:1px solid rgba(212,175,55,0.3); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="ri-bus-line" style="color:#d4af37; font-size:20px;"></i>
                        </div>
                        <div style="flex:1;">
                            <div class="bus-name">${s.bus_name}</div>
                        </div>
                        <div style="text-align:right;">
                            <div class="dep-time">${s.departure_time}</div>
                            <div class="seat-info">${s.available_seats !== undefined ? s.available_seats + ' kursi' : s.capacity + ' kapasitas'}</div>
                        </div>
                    </div>
                </div>
            `).join('');

        } catch(e) {
            scheduleLoading.style.display = 'none';
            scheduleList.innerHTML = '<p style="color:#ef4444; text-align:center;">Gagal memuat jadwal. Silakan coba lagi.</p>';
        }
    }

    function selectSchedule(el) {
        if (el.dataset.seats === '0') return;
        document.querySelectorAll('.schedule-card').forEach(c => c.classList.remove('schedule-card--selected'));
        el.classList.add('schedule-card--selected');
        scheduleIdInput.value = el.dataset.id;
        selectedSchedule = {
            id: el.dataset.id,
            bus: el.dataset.bus,
            type: el.dataset.type,
            time: el.dataset.time,
            pricePerKm: parseFloat(el.dataset.price),
            seats: parseInt(el.dataset.seats),
        };
        updateSummary();
        buildPassengerFields();
    }

    function updateSummary() {
        const pax = parseInt(numPassengers.value) || 1;
        if (!selectedSchedule || !selectedRoute) {
            summaryEmpty.style.display  = '';
            summaryDetail.style.display = 'none';
            seatInfo.style.display      = 'none';
            submitBtn.disabled          = true;
            passengersSection.style.display = 'none';
            return;
        }
        const distance    = parseInt(selectedRoute.distance);
        const pricePerPax = Math.round(selectedSchedule.pricePerKm * distance);
        const total       = pricePerPax * pax;

        document.getElementById('sum-route').textContent       = selectedRoute.name;
        document.getElementById('sum-distance').textContent    = distance + ' km';
        document.getElementById('sum-bus').textContent         = selectedSchedule.bus + ' (' + selectedSchedule.type + ')';
        document.getElementById('sum-time').textContent        = selectedSchedule.time;
        document.getElementById('sum-pax').textContent         = pax + ' orang';
        document.getElementById('sum-price-per-pax').textContent = formatRp(pricePerPax);
        document.getElementById('sum-total').textContent       = formatRp(total);

        summaryEmpty.style.display  = 'none';
        summaryDetail.style.display = '';
        seatInfo.style.display      = '';
        seatCount.textContent       = selectedSchedule.seats;
        submitBtn.disabled          = false;
    }

    function buildPassengerFields() {
        const pax = parseInt(numPassengers.value) || 1;
        passengersSection.style.display = '';
        let html = '';
        for (let i = 0; i < pax; i++) {
            html += `
                <div class="passenger-block">
                    <div class="passenger-block-title"><i class="ri-user-line"></i> Penumpang ${i + 1}</div>
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="passengers[${i}][name]" class="form-input"
                            placeholder="Nama sesuai KTP" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. KTP / ID</label>
                        <input type="text" name="passengers[${i}][id_number]" class="form-input"
                            placeholder="Nomor identitas (opsional)">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="passengers[${i}][phone]" class="form-input" placeholder="08xx">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="passengers[${i}][email]" class="form-input" placeholder="email@...">
                        </div>
                    </div>
                </div>`;
        }
        passengerFields.innerHTML = html;
    }

    routeSelect.addEventListener('change', loadSchedules);
    travelDate.addEventListener('change', loadSchedules);
    numPassengers.addEventListener('change', () => {
        updateSummary();
        if (selectedSchedule) buildPassengerFields();
    });

    @if($selectedRouteId)
        loadSchedules();
    @endif
    </script>
@endpush
