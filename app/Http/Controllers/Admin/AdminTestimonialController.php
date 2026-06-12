<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTestimonialController extends Controller
{
    /** @return array<string, string> */
    private function rules(): array
    {
        return [
            'name'   => 'required|string|max:255',
            'role'   => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'text'   => 'required|string',
            'image'  => 'required|url|max:500',
        ];
    }

    public function index(): View
    {
        $testimonials = Testimonial::orderByDesc('created_at')->paginate(15);

        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create(): View
    {
        return view('admin.testimonials.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['text'] = strip_tags($validated['text']);

        $testimonial = Testimonial::create($validated);

        ActivityLog::log('create', "Menambah testimoni: {$testimonial->name}", 'testimonials', $testimonial->id);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function edit(Testimonial $testimonial): View
    {
        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['text'] = strip_tags($validated['text']);

        $testimonial->update($validated);

        ActivityLog::log('update', "Mengubah testimoni: {$testimonial->name}", 'testimonials', $testimonial->id);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimoni berhasil diperbarui.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        ActivityLog::log('delete', "Menghapus testimoni: {$testimonial->name}", 'testimonials', $testimonial->id);
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimoni berhasil dihapus.');
    }
}