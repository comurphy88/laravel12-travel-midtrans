<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Destination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDestinationController extends Controller
{
    /** @return array<string, string> */
    private function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'required|url|max:500',
            'price'       => 'required|numeric|min:0',
            'rating'      => 'required|numeric|min:0|max:5',
            'active'      => 'boolean',
        ];
    }

    public function index(Request $request): View
    {
        $query = Destination::query();

        if ($request->filled('search')) {
            $term = $request->string('search')->trim()->toString();
            $query->where(function ($q) use ($term): void {
                $q->where('name',     'like', "%{$term}%")
                  ->orWhere('location', 'like', "%{$term}%");
            });
        }

        $destinations = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.destinations.index', compact('destinations'));
    }

    public function create(): View
    {
        return view('admin.destinations.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['description'] = strip_tags($validated['description']);
        $validated['active']      = $request->boolean('active');

        $destination = Destination::create($validated);

        ActivityLog::log('create', "Menambah destinasi: {$destination->name}", 'destinations', $destination->id);

        return redirect()->route('admin.destinations.index')->with('success', 'Destinasi berhasil ditambahkan.');
    }

    public function edit(Destination $destination): View
    {
        return view('admin.destinations.form', compact('destination'));
    }

    public function update(Request $request, Destination $destination): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['description'] = strip_tags($validated['description']);
        $validated['active']      = $request->boolean('active');

        $destination->update($validated);

        ActivityLog::log('update', "Mengubah destinasi: {$destination->name}", 'destinations', $destination->id);

        return redirect()->route('admin.destinations.index')->with('success', 'Destinasi berhasil diperbarui.');
    }

    public function destroy(Destination $destination): RedirectResponse
    {
        if ($destination->bookings()->exists()) {
            return redirect()
                ->route('admin.destinations.index')
                ->with('error', 'Destinasi tidak bisa dihapus karena masih memiliki pesanan terkait.');
        }

        ActivityLog::log('delete', "Menghapus destinasi: {$destination->name}", 'destinations', $destination->id);
        $destination->delete();

        return redirect()->route('admin.destinations.index')->with('success', 'Destinasi berhasil dihapus.');
    }
}