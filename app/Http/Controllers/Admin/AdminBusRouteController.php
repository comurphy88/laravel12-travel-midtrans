<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Bus;
use App\Models\BusRoute;
use Illuminate\Http\Request;

class AdminBusRouteController extends Controller
{
    public function index(Request $request)
    {
        $query = BusRoute::with('bus');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('route_name', 'like', "%{$search}%")
                    ->orWhere('origin', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        $routes = $query->orderByDesc('created_at')->paginate(15);

        return view('admin.bus-routes.index', compact('routes'));
    }

    public function create()
    {
        $buses = Bus::where('status', 'active')->orderBy('bus_name')->get();

        return view('admin.bus-routes.form', compact('buses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_name' => 'required|string|max:255',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'distance' => 'nullable|string|max:50',
            'duration' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'bus_id' => 'required|exists:buses,id',
            'departure_time' => 'nullable|date_format:H:i',
            'arrival_time' => 'nullable|date_format:H:i',
            'active' => 'boolean',
        ]);

        // Validate arrival time is after departure time
        if ($validated['departure_time'] && $validated['arrival_time'] && $validated['arrival_time'] <= $validated['departure_time']) {
            return back()->withErrors(['arrival_time' => 'Waktu tiba harus lebih besar dari waktu keberangkatan'])->withInput();
        }

        $validated['active'] = $request->boolean('active');
        $route = BusRoute::create($validated);
        ActivityLog::log('create', "Menambah rute: {$route->route_name}", 'bus_routes', $route->id);

        return redirect()->route('admin.bus-routes.index')->with('success', 'Rute berhasil ditambahkan.');
    }

    public function edit(BusRoute $busRoute)
    {
        $buses = Bus::where('status', 'active')->orderBy('bus_name')->get();

        return view('admin.bus-routes.form', compact('busRoute', 'buses'));
    }

    public function update(Request $request, BusRoute $busRoute)
    {
        $validated = $request->validate([
            'route_name' => 'required|string|max:255',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'distance' => 'nullable|string|max:50',
            'duration' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'bus_id' => 'required|exists:buses,id',
            'departure_time' => 'nullable|date_format:H:i',
            'arrival_time' => 'nullable|date_format:H:i',
            'active' => 'boolean',
        ]);

        // Validate arrival time is after departure time
        if ($validated['departure_time'] && $validated['arrival_time'] && $validated['arrival_time'] <= $validated['departure_time']) {
            return back()->withErrors(['arrival_time' => 'Waktu tiba harus lebih besar dari waktu keberangkatan'])->withInput();
        }

        $validated['active'] = $request->boolean('active');
        $busRoute->update($validated);
        ActivityLog::log('update', "Mengubah rute: {$busRoute->route_name}", 'bus_routes', $busRoute->id);

        return redirect()->route('admin.bus-routes.index')->with('success', 'Rute berhasil diperbarui.');
    }

    public function destroy(BusRoute $busRoute)
    {
        if ($busRoute->bookings()->exists()) {
            return redirect()->route('admin.bus-routes.index')->with('error', 'Rute tidak bisa dihapus karena masih memiliki pesanan terkait.');
        }

        ActivityLog::log('delete', "Menghapus rute: {$busRoute->route_name}", 'bus_routes', $busRoute->id);
        $busRoute->delete();

        return redirect()->route('admin.bus-routes.index')->with('success', 'Rute berhasil dihapus.');
    }
}
