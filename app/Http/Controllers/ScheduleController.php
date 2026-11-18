<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'route_id' => 'required|exists:routes,id',
        ]);

        $schedules = Schedule::with('bus')
            ->where('route_id', $request->route_id)
            ->active()
            ->orderBy('departure_time')
            ->get()
            ->map(fn($s) => [
                'id'             => $s->id,
                'departure_time' => $s->formatted_departure_time,
                'bus_name'       => $s->bus->bus_name,
                'capacity'       => $s->bus->capacity,
                'price_per_km'   => $s->bus->price_per_km,
            ]);

        return response()->json($schedules);
    }

    public function available(Request $request): JsonResponse
    {
        $request->validate([
            'route_id'    => 'required|exists:routes,id',
            'travel_date' => 'required|date|after:today',
        ]);

        $schedules = Schedule::with('bus')
            ->where('route_id', $request->route_id)
            ->active()
            ->orderBy('departure_time')
            ->get()
            ->map(fn($s) => [
                'id'              => $s->id,
                'departure_time'  => $s->formatted_departure_time,
                'bus_name'        => $s->bus->bus_name,
                'capacity'        => $s->bus->capacity,
                'available_seats' => $s->getAvailableSeats($request->travel_date),
                'price_per_km'    => $s->bus->price_per_km,
            ]);

        return response()->json($schedules);
    }
}
