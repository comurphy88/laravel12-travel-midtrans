# Audit Raven Travel

Tanggal: 2026-11-18 · Branch: `main` (HEAD `14d3e6b`)

> **Status: diperbarui.** Temuan #1–#4, #7–#11, #13, #16 sudah diperbaiki. Lihat bagian "Status perbaikan" di akhir.

## Bukti yang dijalankan

| Cek | Hasil |
|---|---|
| `php artisan test` | 6 lulus, 13 assertion. **5 dari 6 tes tidak bermakna** (2 `true is true` / welcome page, 4 tes galeri) |
| `php artisan route:list` | 100 route ter-resolve. OK |
| `vendor/bin/phpstan` | **Tidak terpasang** — `phpstan.neon` ada tapi `larastan` tidak ada di `composer.json` |
| `git status` | 67 file modified + 25 untracked (belum di-commit) |

---

## CRITICAL

### 1. Webhook Midtrans bisa dipakai menandai booking "paid" tanpa pembayaran
`PaymentController::notification` memanggil `new \Midtrans\Notification()`. Library ini memverifikasi `signature_key`, **tetapi hanya jika `Config::$serverKey` terisi**. Jika `MIDTRANS_SERVER_KEY` kosong (default di `.env.example` → `''`), signature tidak bisa divalidasi, dan route ini `withoutMiddleware(VerifyCsrfToken)` + publik.

Tambahan: endpoint mengembalikan **500 vs 200** sehingga penyerang bisa membedakan "booking code valid" dari "tidak" (enumerasi booking code).

Cek juga: `updateBookingStatus` **tidak membandingkan `gross_amount` dengan `$booking->total_price`**. Payload palsu dengan `gross_amount` kecil tetap menandai lunas.

**Fix:** abort 500 saat startup jika server key kosong; bandingkan `(int) $notification->gross_amount === (int) $booking->total_price`; return 200 untuk semua kasus yang sudah diproses.

### 2. Race condition kursi kosong → overbooking
`BookingController::store` mengecek `getAvailableSeats()` di luar transaksi (baris 73), lalu di dalam `DB::transaction` mencari kursi bebas dengan loop `while (SeatMap::...->exists())` tanpa lock maupun unique index. Dua request bersamaan bisa mendapat kursi yang sama.

**Fix:** unique index `(schedule_id, travel_date, seat_number)` di `seat_maps` + `lockForUpdate()`, atau `insertOrIgnore` dalam retry loop.

### 3. `is_production` Midtrans tidak di-cast ke boolean
`config/services.php:41` → `env('MIDTRANS_IS_PRODUCTION', false)`. Nilai string `"false"` dari `.env` adalah **truthy** di PHP. Artinya setup produksi bisa diam-diam memakai sandbox, atau sebaliknya — melencengkan verifikasi webhook.

**Fix:** `(bool) env('MIDTRANS_IS_PRODUCTION', false)` — atau lebih baik `filter_var(..., FILTER_VALIDATE_BOOLEAN)`.

### 4. `PromoCode` tidak pernah cek `usage_limit`
`BookingController:88-100` cek `active` dan tanggal, **tidak cek `usage_limit`** dan tidak atomik (`increment` di luar transaksi booking). Promo bisa dipakai unlimited.

**Fix:** `->where(fn($q) => $q->where('usage_limit', 0)->orWhereColumn('used_count', '<', 'usage_limit'))` + `increment` di dalam transaksi.

---

## HIGH

### 5. Test suite hampir tidak ada nilainya
Tidak ada satu test pun untuk booking, payment, auth, atau promo. Ini area yang memindahkan uang. Logic booking rawan regresi setiap refactor.

### 6. PHPStan/Larastan tidak terpasang
`phpstan.neon` mereferensikan `vendor/larastan/larastan/extension.neon` yang tidak ada. CI (`ci.yml`) juga tidak menjalankannya — hanya `artisan test`.

### 7. `PageController` memakai regex HTML sanitizer sendiri
`SanitizeHtmlContent::sanitize()` berbasis `strip_tags` + regex. Regex HTML tidak pernah aman: atribut `style="..."`, `href="jav&#x09;ascript:"` (entity/whitespace bypass), dan `svg`-nested payload bisa lolos. Saat ini konten page hardcoded (aman), **tapi kelas ini akan dipakai untuk input admin**. Jangan bangun sanitizer sendiri — pakai `mews/purifier` (HTMLPurifier) kalau butuh rich text.

### 8. `SanitizeHtmlContent` middleware tidak terdaftar
Middleware ini ada, dipakai statis di `PageController`, tapi **tidak pernah di-append** di `bootstrap/app.php`. `SanitizeInput` dan `SecurityHeaders` terdaftar; yang ini tidak.

### 9. `Sandbox` URL Midtrans hardcoded di view
`bookings/pay.blade.php:151` selalu memuat `app.sandbox.midtrans.com/snap/snap.js`, tidak mengikuti `MIDTRANS_IS_PRODUCTION`. Di produksi pembayaran akan mengarah ke sandbox.

### 10. HSTS dikirim di semua environment
`SecurityHeaders` selalu mengirim `Strict-Transport-Security` max-age 1 tahun + `preload`. Di dev via HTTP ini membuat browser memaksa HTTPS ke `localhost`, menyulitkan development. Seharusnya hanya saat `$request->secure()` / production.

---

## MEDIUM

### 11. `ProfileController::update` — user bisa ganti `role`?
Tidak: `$validated` hanya berisi `name/phone/address/city`, dan `role` tidak ada di rules. **Aman.** Tapi `User::$fillable` memuat `role`; satu `update($request->all())` di masa depan = privilege escalation. Pertimbangkan `$guarded` eksplisit atau `role` di luar `$fillable`.

### 12. `AdminUserController::update` memungkinkan admin menurunkan role admin terakhir
Tidak ada cek "jangan hapus admin terakhir". Lockout.

### 13. `finish` endpoint menerima `order_id` dari query
`PaymentController::finish:82` — `$request->input('order_id', $booking->booking_code)`. User bisa mengirim order_id booking lain dan statusnya dicek ke Midtrans. Verifikasi `$booking->user_id` ada, tetapi `order_id` yang berbeda memungkinkan membaca status transaksi milik user lain (info leak kecil). Cukup hapus parameter `order_id` dan selalu pakai `$booking->booking_code`.

### 14. `PromoCode::used_count` di-increment sebelum booking tersimpan
Jika `DB::transaction` gagal, `used_count` tetap naik (counter drift). Pindahkan increment ke dalam transaksi.

### 15. `BookingController::store` - validasi `schedule_id` dan `route_id` terpisah
Sudah dicek (`abort_if($schedule->route_id !== $route->id)`), bagus. Tapi `travel_date` hanya `after:today` — tidak dicek apakah jadwal beroperasi pada hari itu (mis. hari libur / route tidak aktif di tanggal itu). Kalau ini aturan bisnis, validasi belum ada.

### 16. `SecurityLogger` tidak dipakai
`app/Services/SecurityLogger.php` (69 baris) ada, tapi **tidak di-instantiate/dipanggil di mana pun**. Kode mati. Ganti `ActivityLog` dengan ini atau hapus.

### 17. `GalleryRepository` juga kemungkinan tidak dipakai
Perlu dicek; jika ya, hapus.

### 18. Dead scaffold
- `docker-compose.yml` punya `db` dan `redis` tapi `.env.example` default `DB_CONNECTION=sqlite` dan `CACHE_STORE=database`. Docker Compose tidak akan jalan tanpa `.env` custom.
- `Dockerfile` menjalankan `composer install` **dan `npm install` setelah `USER www`** — permission gagal di beberapa base image; juga memasang dev dependencies? Tidak (`--no-dev` ok), tapi `npm` di image produksi = ukuran besar.
- `.env.ci` berisi `APP_KEY` statis yang di-commit. Tidak berbahaya untuk test, tapi jadi kebiasaan buruk.

---

## LOW / Kualitas

### 19. 67 file modified + 25 untracked belum di-commit
Working tree kotor. Kesulitan audit reproduksi. Commit atau stash sebelum melanjutkan.

### 20. Migrasi SQLite compatibility
Commit `92723d7` menyebut "Add SQLite compatibility to view migration" — anggap sudah ditangani.

### 21. Query tanpa index
`BookingController::checkSeats` dan `getAvailableSeats` query `seat_maps` by `(schedule_id, travel_date, status)`. Ada migrasi indeks (`2026_04_20_000000_add_database_indices.php`), tapi tidak ada constraint unique — lihat finding #2.

### 22. `SanitizeInput` men-trim semua input
Sengaja, tapi password dengan spasi awal/akhir jadi tidak bisa. Biasa di Laravel, catat saja.

### 23. Tidak ada rate-limit pada webhook (`throttle:100,1`) — cukup wajar, tapi endpoint `payment/notification` adalah target DoS ringan.

### 24. `SanitizeHtmlContent::sanitize()` dideklarasikan `public static` tapi dipakai di middleware sebagai instance — konsisten, tapi membingungkan. Pilih satu.

---

## Ringkasan prioritas

1. **Webhook Midtrans**: validasi signature wajib + cek `gross_amount`.
2. **Overbooking kursi**: unique index + lock.
3. **`is_production` boolean cast**.
4. **Promo `usage_limit`**.
5. **Test untuk booking + payment** (sekarang nol).
6. **Pasang Larastan + jalankan di CI**.
7. Ganti sanitizer regex dengan HTMLPurifier sebelum dipakai di input admin.
8. Daftarkan/tidak-daftarkan `SanitizeHtmlContent` secara eksplisit.
9. Perbaiki URL Midtrans di view mengikuti env.

Tidak ada perubahan kode dilakukan oleh audit ini.

---

## Status perbaikan (putaran 2)

| # | Temuan | Status |
|---|---|---|
| 1 | Webhook Midtrans bisa dipalsukan | ✅ Verifikasi `SHA512(order_id+status_code+gross_amount+server_key)` via `hash_equals`, tolak server key kosong, cek `gross_amount` vs `total_price`, selalu balas 200 |
| 2 | Overbooking kursi | ✅ Tangkap `UniqueConstraintViolationException` + retry 3x (unique index sudah ada) |
| 3 | `is_production` tidak di-cast | ✅ `filter_var(..., FILTER_VALIDATE_BOOLEAN)` |
| 4 | `usage_limit` promo tidak dicek | ✅ Dicek di query + increment bersyarat atomik |
| 7 | Sanitizer HTML berbasis regex | ✅ Ditulis ulang: DOM parsing + allowlist tag/atribut. Bypass entity (`jav&#x09;ascript:`) ditutup. 7 test |
| 8 | `SanitizeHtmlContent` tidak terdaftar | ✅ Diselesaikan berbeda — **tidak** dijadikan middleware global (akan merusak markup Blade). Tetap dipakai eksplisit; alasan didokumentasikan di kelas |
| 9 | URL Snap sandbox hardcoded | ✅ Mengikuti `is_production` |
| 10 | HSTS di semua environment | ✅ Hanya dikirim saat request secure |
| 12 | Admin bisa menurunkan/menghapus admin terakhir | ✅ Guard di `update` & `destroy`; test membuktikan gagal bila guard dilepas |
| 14 | `used_count` drift saat booking batal | ✅ `Booking::release()` — idempotent, dipakai di cancel user, cancel admin, dan webhook expire |
| 15 | Validasi jadwal per tanggal | ❌ Dilewati — butuh kolom hari operasi (fitur baru, bukan bug) |
| 17 | `GalleryRepository` tidak dipakai | ✅ Dihapus |
| 13 | `order_id` dari query di `finish` | ✅ Dihapus dari controller dan view |
| 16 | `SecurityLogger` tidak dipakai | ⚠️ **Koreksi: laporan awal salah.** `SecurityLogger` dipakai `App\Exceptions\Handler` — tapi Handler-nya sendiri tidak pernah di-bind, jadi tetap mati. Sekarang di-bind di `AppServiceProvider::register()` |

### Bug tambahan yang ditemukan saat menulis test

- **`SanitizeInput` menghapus seluruh body JSON** (`$request->merge($request->all())`). Diperbaiki: hanya merge query+body.
- **`BookingController::store` crash** jika `notes` tidak dikirim (`Undefined array key`). Diperbaiki.
- **`App\Exceptions\Handler` (249 baris) tidak pernah aktif** — deteksi injeksi & pencatatan auth tidak pernah jalan. Sekarang aktif.
- **Validasi nama admin lebih ketat daripada registrasi** — admin tidak bisa mengedit user bernama "O'Brien" (`/^[a-zA-Z\s]+$/` vs `/^[a-zA-Z\s\-\']*$/`). Diseragamkan.

### Masih terbuka

| # | Temuan | Catatan |
|---|---|---|
| 5 | Test booking flow penuh | `PaymentAndBookingTest` menutup webhook + promo cap; `AdminGuardAndReleaseTest` menutup cancel/promo-return. Alur booking end-to-end (create → pay → ticket) belum |
| 6 | Larastan belum terpasang | **Composer tidak ada di mesin ini.** Perlu `composer require --dev larastan/larastan`, lalu tambah langkah phpstan ke `ci.yml` |
| 15 | Validasi jadwal per tanggal | Butuh kolom hari operasi di `schedules` — fitur, perlu keputusan bisnis |
| 18 | Scaffold Docker tidak konsisten dengan `.env` | Belum |
| 19 | 67 file modified + 25 untracked belum di-commit | Belum |
| — | **`.env` berisi `MIDTRANS_SERVER_KEY` produksi asli** | Rotasi key di dashboard Midtrans |

Test suite: **23 lulus, 47 assertion** (`php artisan test`).
