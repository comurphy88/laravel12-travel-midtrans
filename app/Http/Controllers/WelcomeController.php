<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\Review;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Testimonial;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function __invoke(): View
    {
        $schedules = Schedule::with(['bus', 'route'])
            ->active()
            ->orderBy('departure_time')
            ->take(6)
            ->get();

        $testimonials = Testimonial::limit(6)->get();
        $gallery      = Gallery::limit(8)->get();

        $stats = [
            'routes'  => Route::where('active', true)->count(),
            'reviews' => Review::where('status', 'approved')->count(),
        ];

        return view('welcome', compact('schedules', 'testimonials', 'gallery', 'stats'));
    }
}
