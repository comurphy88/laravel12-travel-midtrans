<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Bus;
use App\Models\Route;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminScheduleController extends Controller
{
    private function rules(): array
    {
        return [
            'route_id'       => 'required|exists:routes,id',
            'bus_id'         => 'required|exists:buses,id',
            'departure_time' => 'required|date_format:H:i',
            'active'         => 'boolean',
        ];
    }

    public function index(Request $request): View
    {
        $query = Schedule::with(['route', 'bus'])->withCount('bookings');

        if ($request->filled('route_id')) {
            $query->where('route_id', $request->route_id);
        }

        if ($request->filled('bus_id')) {
            $query->where('bus_id', $request->bus_id);
        }

        if ($request->filled('active')) {
            $query->where('active', $request->boolean('active'));
        }

        $schedules = $query->orderBy('departure_time')->paginate(20)->withQueryString();
        $routes    = Route::active()->orderBy('origin')->get();
        $buses     = Bus::active()->orderBy('bus_name')->get();

        return view('admin.schedules.index', compact('schedules', 'routes', 'buses'));
    }

    public function create(): View
    {
        $routes = Route::active()->orderBy('origin')->get();
        $buses  = Bus::active()->orderBy('bus_name')->get();

        return view('admin.schedules.form', compact('routes', 'buses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated          = $request->validate($this->rules());
        $validated['active'] = $request->boolean('active', true);

        // Cek duplikat
        $exists = Schedule::where('route_id', $validated['route_id'])
            ->where('bus_id', $validated['bus_id'])
            ->where('departure_time', $validated['departure_time'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'departure_time' => 'Jadwal dengan rute, bus, dan jam yang sama sudah ada.',
            ]);
        }

        $schedule = Schedule::create($validated);
        $schedule->load(['route', 'bus']);

        ActivityLog::log(
            'create',
            "Menambah jadwal: {$schedule->route->route_name} - {$schedule->bus->bus_name} jam {$schedule->formatted_departure_time}",
            'schedules',
            $schedule->id
        );

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Schedule $schedule): View
    {
        $routes = Route::active()->orderBy('origin')->get();
        $buses  = Bus::active()->orderBy('bus_name')->get();

        return view('admin.schedules.form', compact('schedule', 'routes', 'buses'));
    }

    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        $validated          = $request->validate($this->rules());
        $validated['active'] = $request->boolean('active', true);

        // Cek duplikat kecuali diri sendiri
        $exists = Schedule::where('route_id', $validated['route_id'])
            ->where('bus_id', $validated['bus_id'])
            ->where('departure_time', $validated['departure_time'])
            ->where('id', '!=', $schedule->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'departure_time' => 'Jadwal dengan rute, bus, dan jam yang sama sudah ada.',
            ]);
        }

        $schedule->update($validated);
        $schedule->load(['route', 'bus']);

        ActivityLog::log(
            'update',
            "Mengubah jadwal: {$schedule->route->route_name} - {$schedule->bus->bus_name} jam {$schedule->formatted_departure_time}",
            'schedules',
            $schedule->id
        );

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        if ($schedule->bookings()->exists()) {
            return redirect()
                ->route('admin.schedules.index')
                ->with('error', 'Jadwal tidak bisa dihapus karena masih memiliki booking terkait.');
        }

        $schedule->load(['route', 'bus']);
        ActivityLog::log(
            'delete',
            "Menghapus jadwal: {$schedule->route->route_name} - {$schedule->bus->bus_name} jam {$schedule->formatted_departure_time}",
            'schedules',
            $schedule->id
        );

        $schedule->delete();

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    public function toggle(Schedule $schedule): RedirectResponse
    {
        $schedule->update(['active' => ! $schedule->active]);
        $schedule->load(['route', 'bus']);

        $status = $schedule->active ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLog::log(
            'update',
            "Jadwal {$schedule->route->route_name} jam {$schedule->formatted_departure_time} {$status}",
            'schedules',
            $schedule->id
        );

        return back()->with('success', "Jadwal berhasil {$status}.");
    }
}
