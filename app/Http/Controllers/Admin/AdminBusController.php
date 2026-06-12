<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Bus;
use App\Models\BusRoute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBusRouteController extends Controller
{
    /** @return array<string, string> */
    private function rules(): array
    {
        return [
            'route_name'     => 'required|string|max:255',
            'origin'         => 'required|string|max:255',
            'destination'    => 'required|string|max:255',
            'distance'       => 'nullable|string|max:50',
            'duration'       => 'nullable|string|max:50',
            'price'          => 'required|numeric|min:0',
            'bus_id'         => 'required|exists:buses,id',
            'departure_time' => 'nullable|date_format:H:i',
            'arrival_time'   => 'nullable|date_format:H:i|after:departure_time',
            'active'         => 'boolean',
        ];
    }

    private function activeBuses()
    {
        return Bus::where('status', 'active')->orderBy('bus_name')->get();
    }

    public function index(Request $request): View
    {
        $query = BusRoute::with('bus');

        if ($request->filled('search')) {
            $term = $request->string('search')->trim()->toString();
            $query->where(function ($q) use ($term): void {
                $q->where('route_name',   'like', "%{$term}%")
                  ->orWhere('origin',      'like', "%{$term}%")
                  ->orWhere('destination', 'like', "%{$term}%");
            });
        }

        $routes = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.bus-routes.index', compact('routes'));
    }

    public function create(): View
    {
        $buses = $this->activeBuses();

        return view('admin.bus-routes.form', compact('buses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['active'] = $request->boolean('active');

        $route = BusRoute::create($validated);

        ActivityLog::log('create', "Menambah rute: {$route->route_name}", 'bus_routes', $route->id);

        return redirect()->route('admin.bus-routes.index')->with('success', 'Rute berhasil ditambahkan.');
    }

    public function edit(BusRoute $busRoute): View
    {
        $buses = $this->activeBuses();

        return view('admin.bus-routes.form', compact('busRoute', 'buses'));
    }

    public function update(Request $request, BusRoute $busRoute): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['active'] = $request->boolean('active');

        $busRoute->update($validated);

        ActivityLog::log('update', "Mengubah rute: {$busRoute->route_name}", 'bus_routes', $busRoute->id);

        return redirect()->route('admin.bus-routes.index')->with('success', 'Rute berhasil diperbarui.');
    }

    public function destroy(BusRoute $busRoute): RedirectResponse
    {
        if ($busRoute->bookings()->exists()) {
            return redirect()
                ->route('admin.bus-routes.index')
                ->with('error', 'Rute tidak bisa dihapus karena masih memiliki pesanan terkait.');
        }

        ActivityLog::log('delete', "Menghapus rute: {$busRoute->route_name}", 'bus_routes', $busRoute->id);
        $busRoute->delete();

        return redirect()->route('admin.bus-routes.index')->with('success', 'Rute berhasil dihapus.');
    }
}