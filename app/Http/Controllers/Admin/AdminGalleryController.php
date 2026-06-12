<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Gallery;
use App\Repositories\GalleryRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdminGalleryController extends Controller
{
    /**
     * Trusted image hostnames (exact host match, not substring).
     *
     * @var array<string>
     */
    private const ALLOWED_IMAGE_HOSTS = [
        'images.unsplash.com',
        'plus.unsplash.com',
        'source.unsplash.com',
    ];

    public function __construct(
        private readonly GalleryRepository $galleryRepository,
    ) {}

    public function index(): View
    {
        $galleries = $this->galleryRepository->getAllPaginated();

        return view('admin.galleries.index', compact('galleries'));
    }

    public function create(): View
    {
        return view('admin.galleries.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateGallery($request);

        if (! $validated) {
            return back()->withInput();
        }

        try {
            $gallery = $this->galleryRepository->create($validated);
            ActivityLog::log('create', "Menambah galeri: {$gallery->title}", 'galleries', $gallery->id);

            return redirect()->route('admin.galleries.index')->with('success', 'Galeri berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating gallery: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menambah galeri.')->withInput();
        }
    }

    public function edit(Gallery $gallery): View
    {
        return view('admin.galleries.form', compact('gallery'));
    }

    public function update(Request $request, Gallery $gallery): RedirectResponse
    {
        $validated = $this->validateGallery($request);

        if (! $validated) {
            return back()->withInput();
        }

        try {
            $this->galleryRepository->update($gallery, $validated);
            ActivityLog::log('update', "Mengubah galeri: {$gallery->title}", 'galleries', $gallery->id);

            return redirect()->route('admin.galleries.index')->with('success', 'Galeri berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating gallery: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat memperbarui galeri.')->withInput();
        }
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        try {
            ActivityLog::log('delete', "Menghapus galeri: {$gallery->title}", 'galleries', $gallery->id);
            $this->galleryRepository->delete($gallery);

            return redirect()->route('admin.galleries.index')->with('success', 'Galeri berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting gallery: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menghapus galeri.');
        }
    }

    // -------------------------------------------------------------------------

    /**
     * Validate the request and return the cleaned data array, or null on failure.
     * Centralises the identical store/update validation block.
     *
     * @return array<string, string>|null
     */
    private function validateGallery(Request $request): ?array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|url|max:500',
        ]);

        if (! $this->isAllowedImageHost($validated['image'])) {
            session()->flash('error', 'URL gambar harus dari domain terpercaya (Unsplash).');

            return null;
        }

        $validated['title'] = strip_tags($validated['title']);

        return $validated;
    }

    /**
     * Check the URL's hostname against the exact allowlist.
     * Uses parse_url() instead of stripos() to prevent bypass via crafted paths.
     */
    private function isAllowedImageHost(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return in_array($host, self::ALLOWED_IMAGE_HOSTS, strict: true);
    }
}