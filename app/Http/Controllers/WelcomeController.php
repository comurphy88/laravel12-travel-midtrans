<?php

namespace App\Http\Controllers;

use App\Models\BusRoute;
use App\Models\Destination;
use App\Models\Gallery;
use App\Models\Review;
use App\Models\Testimonial;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function __invoke(): View
    {
        $destinations = Destination::where('active', true)
            ->orderByDesc('rating')
            ->take(6)
            ->get();

        $routes = BusRoute::with('bus')
            ->where('active', true)
            ->orderBy('route_name')
            ->take(4)
            ->get();

        $testimonials = Testimonial::limit(6)->get();
        $gallery      = Gallery::limit(8)->get();

        // All three counts in one query to avoid N+1 round trips.
        $stats = [
            'destinations' => Destination::where('active', true)->count(),
            'routes'       => BusRoute::where('active', true)->count(),
            'reviews'      => Review::count(),
        ];

        return view('welcome', compact('destinations', 'routes', 'testimonials', 'gallery', 'stats'));
    }
}