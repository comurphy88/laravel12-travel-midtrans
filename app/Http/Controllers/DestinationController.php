<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DestinationController extends Controller
{
    /** Maximum number of related destinations shown on the detail page. */
    private const MAX_RELATED = 3;

    public function index(Request $request): View
    {
        $query = Destination::where('active', true);

        if ($request->filled('search')) {
            $term = $request->string('search')->trim()->toString();

            $query->where(function ($q) use ($term): void {
                $q->where('name',        'like', "%{$term}%")
                  ->orWhere('location',  'like', "%{$term}%")
                  ->orWhere('description','like', "%{$term}%");
            });
        }

        match ($request->input('sort')) {
            'price_low'  => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            default      => $query->orderByDesc('rating'),
        };

        $destinations = $query->paginate(12)->withQueryString();

        return view('destinations.index', compact('destinations'));
    }

    public function show(Destination $destination): View
    {
        abort_if($destination->trashed(),      404);
        abort_if(! $destination->active, 404, 'Destinasi tidak tersedia.');

        // Fetch related destinations in one query — no pre-count needed.
        $related = Destination::where('active', true)
            ->whereKeyNot($destination->id)
            ->inRandomOrder()
            ->limit(self::MAX_RELATED)
            ->get();

        return view('destinations.show', compact('destination', 'related'));
    }
}