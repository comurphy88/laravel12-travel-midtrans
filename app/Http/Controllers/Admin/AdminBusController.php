<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Bus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBusController extends Controller
{
    private function rules(): array
    {
        return [
            'bus_name'     => 'required|string|max:255',
            'bus_number'   => 'required|string|max:255',
            'capacity'     => 'required|integer|min:1',
            'price_per_km' => 'required|numeric|min:0',
            'status'       => 'required|in:active,maintenance,retired',
        ];
    }

    public function index(Request $request): View
    {
        $query = Bus::withCount('schedules');

        if ($request->filled('search')) {
            $term = $request->string('search')->trim()->toString();
            $query->where(function ($q) use ($term): void {
                $q->where('bus_name',   'like', "%{$term}%")
                    ->orWhere('bus_number', 'like', "%{$term}%");
            });
        }

        $buses = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.buses.index', compact('buses'));
    }

    public function create(): View
    {
        return view('admin.buses.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $bus = Bus::create($validated);

        ActivityLog::log('create', "Menambah bus: {$bus->bus_name}", 'buses', $bus->id);

        return redirect()->route('admin.buses.index')->with('success', 'Bus berhasil ditambahkan.');
    }

    public function edit(Bus $bus): View
    {
        return view('admin.buses.form', compact('bus'));
    }

    public function update(Request $request, Bus $bus): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $bus->update($validated);

        ActivityLog::log('update', "Mengubah bus: {$bus->bus_name}", 'buses', $bus->id);

        return redirect()->route('admin.buses.index')->with('success', 'Bus berhasil diperbarui.');
    }

    public function destroy(Bus $bus): RedirectResponse
    {
        if ($bus->schedules()->exists()) {
            return redirect()
                ->route('admin.buses.index')
                ->with('error', 'Bus tidak bisa dihapus karena masih memiliki jadwal terkait.');
        }

        $busId   = $bus->id;
        $busName = $bus->bus_name;
        $bus->delete();

        ActivityLog::log('delete', "Menghapus bus: {$busName}", 'buses', $busId);

        return redirect()->route('admin.buses.index')->with('success', 'Bus berhasil dihapus.');
    }
}
