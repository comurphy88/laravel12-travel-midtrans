<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SanitizeHtmlContent;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Static page definitions.
     * For a larger app these would live in the database or a dedicated config file.
     *
     * @var array<string, array<string, mixed>>
     */
    private const PAGES = [
        'faq' => [
            'title'    => 'Pertanyaan yang Sering Diajukan',
            'sections' => [
                [
                    'q' => 'Bagaimana cara memesan tiket?',
                    'a' => 'Anda dapat memesan tiket melalui halaman Booking setelah login. Pilih rute atau destinasi, tentukan tanggal perjalanan dan jumlah penumpang, lalu konfirmasi pemesanan.',
                ],
                [
                    'q' => 'Apakah bisa membatalkan pemesanan?',
                    'a' => 'Ya, pemesanan dengan status pending atau confirmed dapat dibatalkan melalui halaman detail booking Anda.',
                ],
                [
                    'q' => 'Metode pembayaran apa yang tersedia?',
                    'a' => 'Kami menerima transfer bank, e-wallet, dan kartu kredit/debit (Visa, Mastercard).',
                ],
                [
                    'q' => 'Bagaimana cara menghubungi customer service?',
                    'a' => 'Anda dapat menghubungi kami melalui email di info@raventravel.com atau telepon di +62 21 5555 8888.',
                ],
                [
                    'q' => 'Apakah ada fasilitas di dalam bus?',
                    'a' => 'Semua armada kami dilengkapi AC, USB charger, dan kursi ergonomis. Tipe Executive dan Suite memiliki WiFi, leg rest, dan toilet.',
                ],
            ],
        ],
        'terms' => [
            'title'   => 'Syarat & Ketentuan',
            'content' => '<h3>1. Ketentuan Umum</h3><p>Dengan menggunakan layanan Raven Travel, Anda menyetujui syarat dan ketentuan berikut. Layanan ini hanya tersedia untuk pengguna yang berusia minimal 17 tahun atau didampingi orang tua/wali.</p><h3>2. Pemesanan & Pembayaran</h3><p>Pemesanan dianggap sah setelah pembayaran dikonfirmasi. Harga yang tertera sudah termasuk pajak. Raven Travel berhak mengubah harga tanpa pemberitahuan sebelumnya.</p><h3>3. Pembatalan & Refund</h3><p>Pembatalan dapat dilakukan maksimal 24 jam sebelum keberangkatan. Refund akan diproses dalam waktu 3-7 hari kerja sesuai kebijakan pembatalan yang berlaku.</p><h3>4. Tanggung Jawab</h3><p>Raven Travel bertanggung jawab atas keselamatan penumpang selama perjalanan. Kami tidak bertanggung jawab atas kehilangan barang pribadi penumpang.</p>',
        ],
        'privacy' => [
            'title'   => 'Kebijakan Privasi',
            'content' => '<h3>1. Pengumpulan Data</h3><p>Kami mengumpulkan informasi pribadi seperti nama, email, nomor telepon saat Anda mendaftar atau melakukan pemesanan. Data ini digunakan untuk memproses layanan kami.</p><h3>2. Penggunaan Data</h3><p>Data Anda digunakan untuk memproses pemesanan, mengirim konfirmasi dan notifikasi, serta meningkatkan layanan kami. Kami tidak menjual data pribadi kepada pihak ketiga.</p><h3>3. Keamanan Data</h3><p>Kami menggunakan enkripsi dan langkah keamanan standar industri untuk melindungi data pribadi Anda. Akses terhadap data dibatasi hanya untuk karyawan yang memerlukan.</p><h3>4. Hak Pengguna</h3><p>Anda berhak mengakses, memperbarui, atau menghapus data pribadi kapan saja melalui halaman profil atau menghubungi customer service kami.</p>',
        ],
    ];

    public function show(string $slug): View
    {
        abort_unless(isset(self::PAGES[$slug]), 404);

        $page = self::PAGES[$slug];

        if (isset($page['content'])) {
            $page['content'] = SanitizeHtmlContent::sanitize($page['content']);
        }

        return view('page', compact('page', 'slug'));
    }
}