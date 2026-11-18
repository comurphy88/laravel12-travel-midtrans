<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket {{ $booking->booking_code }} | Raven Travel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f5f5;
            color: #1a1a1a;
        }

        .print-btn-bar {
            background: #1a1a2e;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .print-btn-bar span { color: #fff; font-size: 14px; }
        .print-btn {
            background: #d4a017;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .print-btn:hover { background: #b8890f; }

        .ticket-wrapper {
            max-width: 700px;
            margin: 30px auto;
            padding: 0 16px 40px;
        }

        .ticket {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.1);
        }

        /* Header */
        .ticket-header {
            background: #1a1a2e;
            padding: 24px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ticket-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .ticket-brand .crest {
            width: 44px; height: 44px;
            background: #d4a017;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 20px; color: #fff;
            font-family: 'Georgia', serif;
        }
        .ticket-brand .brand-text strong {
            display: block; color: #fff; font-size: 18px; letter-spacing: 1px;
        }
        .ticket-brand .brand-text small {
            color: #d4a017; font-size: 11px; text-transform: uppercase; letter-spacing: 2px;
        }
        .ticket-code {
            text-align: right;
        }
        .ticket-code .label { color: rgba(255,255,255,0.5); font-size: 11px; text-transform: uppercase; }
        .ticket-code .code { color: #d4a017; font-size: 20px; font-weight: 700; font-family: monospace; }

        /* Status bar */
        .ticket-status {
            padding: 10px 28px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
        }
        .ticket-status.confirmed { background: #dcfce7; color: #166534; }
        .ticket-status.pending   { background: #fef9c3; color: #854d0e; }
        .ticket-status.completed { background: #dbeafe; color: #1e40af; }
        .ticket-status.cancelled { background: #fee2e2; color: #991b1b; }

        /* Route section */
        .ticket-route {
            padding: 24px 28px;
            border-bottom: 2px dashed #e5e7eb;
        }
        .route-row {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }
        .city { flex: 1; }
        .city .city-name { font-size: 24px; font-weight: 700; color: #1a1a2e; }
        .city .city-label { font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: 1px; }
        .city.right { text-align: right; }
        .route-arrow {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
        }
        .route-arrow .arrow-line {
            width: 100px; height: 2px;
            background: linear-gradient(90deg, #d4a017, #f0c040);
            position: relative;
        }
        .route-arrow .arrow-line::after {
            content: '▶';
            position: absolute;
            right: -8px; top: -7px;
            color: #d4a017; font-size: 12px;
        }
        .route-arrow .bus-icon { font-size: 18px; color: #d4a017; }
        .route-arrow .distance { font-size: 11px; color: #999; }

        .route-meta {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-top: 4px;
        }
        .meta-item .meta-label {
            font-size: 11px; color: #999;
            text-transform: uppercase; letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .meta-item .meta-value { font-size: 14px; font-weight: 600; color: #1a1a2e; }

        /* Perforation */
        .perforation {
            display: flex;
            align-items: center;
            margin: 0 -1px;
        }
        .perf-circle {
            width: 24px; height: 24px;
            background: #f5f5f5;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .perf-line { flex: 1; border-top: 2px dashed #e5e7eb; }

        /* Passengers */
        .ticket-passengers {
            padding: 20px 28px;
            border-bottom: 2px dashed #e5e7eb;
        }
        .section-title {
            font-size: 11px; color: #999;
            text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 12px;
        }
        .passenger-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .passenger-row:last-child { border-bottom: none; }
        .passenger-name { font-weight: 600; font-size: 14px; }
        .passenger-meta { font-size: 12px; color: #999; margin-top: 2px; }
        .seat-badge {
            background: #1a1a2e;
            color: #d4a017;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            font-family: monospace;
        }

        /* Payment */
        .ticket-payment {
            padding: 20px 28px;
            border-bottom: 2px dashed #e5e7eb;
        }
        .payment-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            font-size: 14px;
        }
        .payment-row .label { color: #666; }
        .payment-row .value { font-weight: 500; }
        .payment-divider { border-top: 1px solid #e5e7eb; margin: 8px 0; }
        .payment-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0 0;
        }
        .payment-total .label { font-weight: 600; font-size: 15px; }
        .payment-total .value { font-size: 20px; font-weight: 700; color: #d4a017; }

        /* Notes */
        .ticket-notes {
            padding: 16px 28px;
            background: #fffdf0;
            border-bottom: 2px dashed #e5e7eb;
            font-size: 13px;
            color: #666;
        }
        .ticket-notes strong { color: #1a1a2e; display: block; margin-bottom: 4px; }

        /* Contact */
        .ticket-contact {
            padding: 16px 28px;
            border-bottom: 1px solid #e5e7eb;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }
        .contact-item .contact-label {
            font-size: 10px; color: #999;
            text-transform: uppercase; letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .contact-item .contact-value { font-size: 13px; font-weight: 500; color: #1a1a2e; }

        /* Footer */
        .ticket-footer {
            background: #f9f9f9;
            padding: 16px 28px;
            text-align: center;
            font-size: 11px;
            color: #999;
        }
        .ticket-footer strong { color: #d4a017; }

        /* Print */
        @media print {
            .print-btn-bar { display: none !important; }
            body { background: #fff; }
            .ticket-wrapper { margin: 0; padding: 0; max-width: 100%; }
            .ticket { box-shadow: none; border-radius: 0; }
        }
    </style>
</head>
<body>

    <div class="print-btn-bar">
        <span>Tiket Perjalanan Raven Travel</span>
        <button class="print-btn" onclick="window.print()">
            🖨️ Cetak Tiket
        </button>
    </div>

    @php $bus = $booking->schedule?->bus; @endphp

    <div class="ticket-wrapper">
        <div class="ticket">

            {{-- Header --}}
            <div class="ticket-header">
                <div class="ticket-brand">
                    <div class="crest">R</div>
                    <div class="brand-text">
                        <strong>RAVEN TRAVEL</strong>
                        <small>E-Tiket Perjalanan</small>
                    </div>
                </div>
                <div class="ticket-code">
                    <div class="label">Kode Booking</div>
                    <div class="code">{{ $booking->booking_code }}</div>
                </div>
            </div>

            {{-- Status --}}
            <div class="ticket-status {{ $booking->status }}">
                {{ strtoupper($booking->status) }} —
                {{ $booking->payment_status === 'paid' ? 'LUNAS' : 'BELUM DIBAYAR' }}
            </div>

            {{-- Rute --}}
            <div class="ticket-route">
                <div class="route-row">
                    <div class="city">
                        <div class="city-name">{{ $booking->origin ?? '-' }}</div>
                        <div class="city-label">Asal</div>
                    </div>
                    <div class="route-arrow">
                        <div class="bus-icon">🚌</div>
                        <div class="arrow-line"></div>
                        <div class="distance">{{ $booking->distance_km }} km</div>
                    </div>
                    <div class="city right">
                        <div class="city-name">{{ $booking->destination ?? '-' }}</div>
                        <div class="city-label">Tujuan</div>
                    </div>
                </div>

                <div class="route-meta">
                    <div class="meta-item">
                        <div class="meta-label">Tanggal</div>
                        <div class="meta-value">{{ $booking->travel_date->format('d M Y') }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Jam Berangkat</div>
                        <div class="meta-value">{{ $booking->departure_time ? substr($booking->departure_time, 0, 5) . ' WIB' : '-' }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Bus</div>
                        <div class="meta-value">{{ $bus?->bus_name ?? '-' }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">No. Armada</div>
                        <div class="meta-value">{{ $bus?->bus_number ?? '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- Perforation --}}
            <div class="perforation">
                <div class="perf-circle"></div>
                <div class="perf-line"></div>
                <div class="perf-circle"></div>
            </div>

            {{-- Penumpang --}}
            <div class="ticket-passengers">
                <div class="section-title">Data Penumpang ({{ $booking->passengers->count() }} orang)</div>
                @foreach($booking->passengers as $p)
                    <div class="passenger-row">
                        <div>
                            <div class="passenger-name">{{ $p->name }}</div>
                            <div class="passenger-meta">
                                @if($p->id_number) ID: {{ $p->id_number }} @endif
                                @if($p->id_number && ($p->phone || $p->email)) &nbsp;·&nbsp; @endif
                                @if($p->phone) {{ $p->phone }} @endif
                                @if($p->phone && $p->email) &nbsp;·&nbsp; @endif
                                @if($p->email) {{ $p->email }} @endif
                            </div>
                        </div>
                        <div class="seat-badge">{{ $p->seat_number ?? 'AUTO' }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Perforation --}}
            <div class="perforation">
                <div class="perf-circle"></div>
                <div class="perf-line"></div>
                <div class="perf-circle"></div>
            </div>

            {{-- Pembayaran --}}
            <div class="ticket-payment">
                <div class="section-title">Rincian Pembayaran</div>
                @php $unitPrice = $booking->num_passengers > 0 ? $booking->subtotal / $booking->num_passengers : 0; @endphp
                <div class="payment-row">
                    <span class="label">Harga/penumpang</span>
                    <span class="value">Rp {{ number_format($unitPrice, 0, ',', '.') }}</span>
                </div>
                <div class="payment-row">
                    <span class="label">Jumlah penumpang</span>
                    <span class="value">× {{ $booking->num_passengers }}</span>
                </div>
                @if($booking->discount_amount > 0)
                    <div class="payment-row">
                        <span class="label">Diskon</span>
                        <span class="value" style="color:#16a34a;">- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="payment-divider"></div>
                <div class="payment-total">
                    <span class="label">Total Pembayaran</span>
                    <span class="value">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Catatan --}}
            @if($booking->notes)
                <div class="ticket-notes">
                    <strong>Catatan:</strong>
                    {{ $booking->notes }}
                </div>
            @endif

            {{-- Kontak --}}
            <div class="ticket-contact">
                <div class="contact-item">
                    <div class="contact-label">Telepon</div>
                    <div class="contact-value">+62 812-3456-7890</div>
                </div>
                <div class="contact-item">
                    <div class="contact-label">Email</div>
                    <div class="contact-value">info@raventravel.com</div>
                </div>
                <div class="contact-item">
                    <div class="contact-label">Website</div>
                    <div class="contact-value">www.raventravel.com</div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="ticket-footer">
                Dicetak pada {{ now()->format('d M Y, H:i') }} WIB &nbsp;·&nbsp;
                <strong>Raven Travel</strong> &nbsp;·&nbsp;
                Simpan tiket ini sebagai bukti perjalanan Anda
            </div>

        </div>
    </div>

</body>
</html>
