<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Route;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRouteController extends Controller
{
    private function rules(): array
    {
        return [
            'origin'      => 'required|string|max:255',
            'destination' => 'required|string|max:255|different:origin',
            'distance_km' => 'required|integer|min:1',
            'active'      => 'boolean',
        ];
    }

    public function index(Request $request): View
    {
        $query = Route::withCount('schedules');

        if ($request->filled('search')) {
            $term = $request->string('search')->trim()->toString();
            $query->where(function ($q) use ($term): void {
                $q->where('origin',      'like', "%{$term}%")
                    ->orWhere('destination', 'like', "%{$term}%");
            });
        }

        if ($request->filled('active')) {
            $query->where('active', $request->boolean('active'));
        }

        $routes = $query->orderBy('origin')->paginate(20)->withQueryString();

        return view('admin.routes.index', compact('routes'));
    }

    public function create(): View
    {
        return view('admin.routes.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated          = $request->validate($this->rules());
        $validated['active'] = $request->boolean('active', true);

        // Cek duplikat rute
        $exists = Route::where('origin', $validated['origin'])
            ->where('destination', $validated['destination'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'destination' => "Rute {$validated['origin']} → {$validated['destination']} sudah ada.",
            ]);
        }

        $route = Route::create($validated);

        ActivityLog::log('create', "Menambah rute: {$route->route_name}", 'routes', $route->id);

        return redirect()->route('admin.routes.index')->with('success', 'Rute berhasil ditambahkan.');
    }

    public function edit(Route $route): View
    {
        return view('admin.routes.form', compact('route'));
    }

    public function update(Request $request, Route $route): RedirectResponse
    {
        $validated          = $request->validate($this->rules());
        $validated['active'] = $request->boolean('active', true);

        // Cek duplikat kecuali diri sendiri
        $exists = Route::where('origin', $validated['origin'])
            ->where('destination', $validated['destination'])
            ->where('id', '!=', $route->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'destination' => "Rute {$validated['origin']} → {$validated['destination']} sudah ada.",
            ]);
        }

        $route->update($validated);

        ActivityLog::log('update', "Mengubah rute: {$route->route_name}", 'routes', $route->id);

        return redirect()->route('admin.routes.index')->with('success', 'Rute berhasil diperbarui.');
    }

    public function destroy(Route $route): RedirectResponse
    {
        if ($route->schedules()->exists()) {
            return redirect()
                ->route('admin.routes.index')
                ->with('error', 'Rute tidak bisa dihapus karena masih memiliki jadwal terkait.');
        }

        ActivityLog::log('delete', "Menghapus rute: {$route->route_name}", 'routes', $route->id);
        $route->delete();

        return redirect()->route('admin.routes.index')->with('success', 'Rute berhasil dihapus.');
    }

    public function toggle(Route $route): RedirectResponse
    {
        $route->update(['active' => ! $route->active]);

        $status = $route->active ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLog::log('update', "Rute {$route->route_name} {$status}", 'routes', $route->id);

        return back()->with('success', "Rute berhasil {$status}.");
    }
}
