<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Gallery;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@raventravel.com'],
            [
                'name'              => 'Admin Raven',
                'password'          => bcrypt('password'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Regular user
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'     => 'Test User',
                'password' => bcrypt('password'),
                'role'     => 'user',
            ]
        );

        // Bus - 1 armada ekonomi
        $bus = Bus::create([
            'bus_name'     => 'Raven Travel',
            'bus_number'   => 'RVN-001',
            'capacity'     => 40,
            'price_per_km' => 900,
            'status'       => 'active',
        ]);

        // Routes
        $routes = [];
        $routeData = [
            ['origin' => 'Jakarta',    'destination' => 'Bandung',    'distance_km' => 150],
            ['origin' => 'Jakarta',    'destination' => 'Yogyakarta', 'distance_km' => 530],
            ['origin' => 'Jakarta',    'destination' => 'Surabaya',   'distance_km' => 780],
            ['origin' => 'Jakarta',    'destination' => 'Semarang',   'distance_km' => 450],
            ['origin' => 'Surabaya',   'destination' => 'Malang',     'distance_km' => 90],
            ['origin' => 'Yogyakarta', 'destination' => 'Surabaya',   'distance_km' => 320],
        ];
        foreach ($routeData as $r) {
            $routes[] = Route::create(array_merge($r, ['active' => true]));
        }

        // Schedules - 1 bus, beberapa jam keberangkatan per rute
        Schedule::insert([
            ['route_id' => $routes[0]->id, 'bus_id' => $bus->id, 'departure_time' => '07:00', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['route_id' => $routes[0]->id, 'bus_id' => $bus->id, 'departure_time' => '13:00', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['route_id' => $routes[0]->id, 'bus_id' => $bus->id, 'departure_time' => '20:00', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['route_id' => $routes[1]->id, 'bus_id' => $bus->id, 'departure_time' => '08:00', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['route_id' => $routes[1]->id, 'bus_id' => $bus->id, 'departure_time' => '20:00', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['route_id' => $routes[2]->id, 'bus_id' => $bus->id, 'departure_time' => '07:00', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['route_id' => $routes[2]->id, 'bus_id' => $bus->id, 'departure_time' => '19:00', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['route_id' => $routes[3]->id, 'bus_id' => $bus->id, 'departure_time' => '06:00', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['route_id' => $routes[3]->id, 'bus_id' => $bus->id, 'departure_time' => '21:00', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['route_id' => $routes[4]->id, 'bus_id' => $bus->id, 'departure_time' => '07:00', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['route_id' => $routes[4]->id, 'bus_id' => $bus->id, 'departure_time' => '14:00', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['route_id' => $routes[5]->id, 'bus_id' => $bus->id, 'departure_time' => '08:00', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['route_id' => $routes[5]->id, 'bus_id' => $bus->id, 'departure_time' => '20:00', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Testimonials
        Testimonial::insert([
            [
                'name'       => 'Sarah Johnson',
                'role'       => 'Business Traveler',
                'rating'     => 5,
                'text'       => 'Pelayanan terasa jauh lebih matang dari bus premium biasa. Kabin tenang, crew rapi, dan seluruh perjalanan terasa effortless.',
                'image'      => 'https://i.pravatar.cc/150?img=1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Ahmad Rizki',
                'role'       => 'Family Vacation',
                'rating'     => 5,
                'text'       => 'Anak-anak nyaman, proses boarding tertib, dan kualitas kursinya memang premium. Sangat cocok untuk perjalanan keluarga jarak jauh.',
                'image'      => 'https://i.pravatar.cc/150?img=12',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Lisa Chen',
                'role'       => 'Solo Traveler',
                'rating'     => 5,
                'text'       => 'Aman, elegan, dan tepat waktu. Saya suka bagaimana brand ini terasa refined tanpa berusaha terlalu keras.',
                'image'      => 'https://i.pravatar.cc/150?img=5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Gallery
        Gallery::insert([
            ['title' => 'Sunrise Escape', 'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Mountain Route', 'image' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=900&q=80', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'City Lights',   'image' => 'https://images.unsplash.com/photo-1491553895911-0055eca6402d?auto=format&fit=crop&w=900&q=80', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Coastal Drive', 'image' => 'https://images.unsplash.com/photo-1489515217757-5fd1be406fef?auto=format&fit=crop&w=900&q=80', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Forest Road',   'image' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=900&q=80', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Ocean View',    'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=900&q=80', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
