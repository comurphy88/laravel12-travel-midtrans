## Ringkasan

Branch `audit-fixes` berisi perbaikan hasil audit keamanan & kualitas pada Raven Travel.

⚠️ **Catatan penting:** commit ini adalah *snapshot* yang mencampur dua hal:
1. **Refactor `Route/Schedule/SeatMap`** yang sudah ada di working tree sebelum audit (menggantikan `Destination`/`BusRoute`/`SeatBooking`).
2. **Perbaikan audit** yang ditumpuk di atasnya.

Keduanya tidak dipisah per-commit karena banyak file memuat keduanya sekaligus. Review sebaiknya dilakukan per-file, bukan per-commit.

## Perbaikan keamanan

| Area | Masalah | Perbaikan |
|---|---|---|
| **Webhook Midtrans** | Signature tidak pernah diverifikasi jika `MIDTRANS_SERVER_KEY` kosong; `gross_amount` tidak dicek | Verifikasi `SHA512(order_id+status_code+gross_amount+server_key)` dengan `hash_equals`; tolak 500 jika key kosong; cek nominal vs `total_price`; selalu balas 200 agar tidak bisa dipakai enumerasi booking |
| **Overbooking kursi** | Cek-lalu-insert di luar transaksi | Tangkap `UniqueConstraintViolationException` + retry 3x; unique index menjadi penentu |
| **Promo code** | `usage_limit` tidak pernah dicek; increment di luar transaksi | Dicek di query + increment bersyarat atomik; kuota dikembalikan saat booking batal |
| **Sanitizer HTML** | Berbasis regex — bypass `jav&#x09;ascript:` | Ditulis ulang dengan DOM parsing + allowlist tag/atribut |
| **`is_production`** | `"false"` dari `.env` truthy di PHP | `filter_var(..., FILTER_VALIDATE_BOOLEAN)` |
| **HSTS** | Dikirim di semua environment | Hanya saat request secure |
| **Admin terakhir** | Bisa diturunkan/dihapus → lockout | Guard di `update` & `destroy` |
| **`MIDTRANS` URL** | Snap.js hardcoded sandbox | Mengikuti `is_production` |

## Bug lain yang ditemukan

- **`SanitizeInput` menghapus seluruh body JSON** — `$request->merge($request->all())` menimpa bag `json` dengan array kosong. Berdampak ke semua endpoint `application/json`.
- **`BookingController::store` crash** jika `notes` tidak dikirim (field opsional).
- **`App\Exceptions\Handler` (249 baris) tidak pernah aktif** — deteksi injeksi & pencatatan auth tidak pernah jalan. Sekarang di-bind.
- **Validasi nama admin lebih ketat dari registrasi** — "O'Brien" tidak bisa diedit admin.
- **Cache Blade ter-track di git** — 6 file hasil compile di-`rm --cached`.

## Infrastruktur

- `Dockerfile`: build sebagai root lalu `chown` ke `www` (sebelumnya `composer`/`npm` gagal permission); `node_modules` dibuang dari image runtime.
- `.dockerignore` baru.
- `.env.example`: tambah konfigurasi Docker (MySQL/Redis).
- `GalleryRepository` dihapus (kode mati).

## Test

```
php artisan test  →  23 passed (47 assertions)
```

Baru: `PaymentAndBookingTest`, `AdminGuardAndReleaseTest`, `SanitizeHtmlContentTest`.
Guard admin diverifikasi dengan mematikan guard sementara — test gagal, lalu lulus setelah dihidupkan.

## Yang perlu diperhatikan reviewer

- **Webhook**: `SanitizeInput` sekarang hanya merge query+body; payload JSON dibaca dari `$request->getContent()`.
- **`Booking::release()`** dipanggil di 3 jalur pembatalan dan bersifat idempotent.
- **`SanitizeHtmlContent` sengaja TIDAK didaftarkan sebagai middleware global** — akan merusak markup Blade.

## Belum dikerjakan

- Larastan/phpstan belum terpasang (composer tidak tersedia di mesin audit).
- Test alur booking end-to-end (create → pay → ticket).
- Validasi jadwal per tanggal butuh kolom hari operasi — keputusan bisnis.
- `docker compose build` belum dijalankan.

## ⚠️ Tindakan wajib sebelum merge

`.env` lokal berisi **`MIDTRANS_SERVER_KEY` produksi asli**. Rotasi key di dashboard Midtrans.
