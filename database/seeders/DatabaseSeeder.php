<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Destination;
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
        User::create([
            'name' => 'Admin Raven',
            'email' => 'admin@raventravel.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Regular user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        // Destinations (Tempat Wisata)
        Destination::insert([
            [
                'name' => 'Candi Borobudur',
                'location' => 'Magelang, Jawa Tengah',
                'rating' => 4.9,
                'price' => 285000,
                'description' => 'Candi Buddha terbesar di dunia dan warisan budaya UNESCO. Nikmati keindahan relief bersejarah dan pemandangan matahari terbit yang memukau dari puncak candi.',
                'image' => 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?auto=format&fit=crop&w=900&q=80',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gunung Bromo',
                'location' => 'Probolinggo, Jawa Timur',
                'rating' => 4.8,
                'price' => 350000,
                'description' => 'Gunung berapi aktif dengan pemandangan kawah yang dramatis dan lautan pasir luas. Spot favorit untuk menikmati sunrise spektakuler di Jawa Timur.',
                'image' => 'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=900&q=80',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pantai Kuta',
                'location' => 'Badung, Bali',
                'rating' => 4.7,
                'price' => 450000,
                'description' => 'Pantai ikonik Bali dengan pasir putih lembut dan ombak sempurna untuk surfing. Nikmati sunset memukau, kuliner khas, dan kehidupan malam yang meriah.',
                'image' => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=900&q=80',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Malioboro',
                'location' => 'Yogyakarta, DI Yogyakarta',
                'rating' => 5.0,
                'price' => 310000,
                'description' => 'Jantung kota Yogyakarta dengan suasana belanja tradisional, kuliner gudeg legendaris, dan seni budaya Jawa yang kental. Destinasi wajib bagi setiap wisatawan.',
                'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=900&q=80',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kawah Putih',
                'location' => 'Bandung, Jawa Barat',
                'rating' => 4.8,
                'price' => 185000,
                'description' => 'Danau kawah vulkanik dengan air berwarna putih kehijauan yang eksotis. Dikelilingi hutan pinus dan udara sejuk pegunungan, cocok untuk wisata alam dan fotografi.',
                'image' => 'https://images.unsplash.com/photo-1598880940080-ff9a29891b85?auto=format&fit=crop&w=900&q=80',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Danau Toba',
                'location' => 'Samosir, Sumatera Utara',
                'rating' => 4.9,
                'price' => 520000,
                'description' => 'Danau vulkanik terbesar di dunia dengan Pulau Samosir di tengahnya. Keindahan alam luar biasa, budaya Batak yang kaya, dan kuliner khas yang menggugah selera.',
                'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Buses
        $buses = [];
        $busData = [
            ['bus_name' => 'Raven Executive I', 'bus_number' => 'RVN-001', 'capacity' => 32, 'bus_type' => 'executive', 'facilities' => 'AC, WiFi, Leg Rest, USB Charger, Toilet'],
            ['bus_name' => 'Raven Executive II', 'bus_number' => 'RVN-002', 'capacity' => 32, 'bus_type' => 'executive', 'facilities' => 'AC, WiFi, Leg Rest, USB Charger, Toilet'],
            ['bus_name' => 'Raven Suite I', 'bus_number' => 'RVN-003', 'capacity' => 20, 'bus_type' => 'suite', 'facilities' => 'AC, WiFi, Full Recline, USB Charger, Toilet, Snack Box'],
            ['bus_name' => 'Raven Standard I', 'bus_number' => 'RVN-004', 'capacity' => 40, 'bus_type' => 'standard', 'facilities' => 'AC, USB Charger'],
        ];
        foreach ($busData as $b) {
            $buses[] = Bus::create(array_merge($b, ['status' => 'active']));
        }

        // Bus Routes
        BusRoute::insert([
            [
                'route_name' => 'Jakarta - Bandung Express',
                'origin' => 'Jakarta',
                'destination' => 'Bandung',
                'distance' => '150 km',
                'duration' => '3 jam',
                'price' => 185000,
                'bus_id' => $buses[0]->id,
                'departure_time' => '06:00',
                'arrival_time' => '09:00',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_name' => 'Jakarta - Yogyakarta Premium',
                'origin' => 'Jakarta',
                'destination' => 'Yogyakarta',
                'distance' => '530 km',
                'duration' => '9 jam',
                'price' => 350000,
                'bus_id' => $buses[2]->id,
                'departure_time' => '20:00',
                'arrival_time' => '05:00',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_name' => 'Surabaya - Malang Shuttle',
                'origin' => 'Surabaya',
                'destination' => 'Malang',
                'distance' => '90 km',
                'duration' => '2 jam',
                'price' => 120000,
                'bus_id' => $buses[3]->id,
                'departure_time' => '07:00',
                'arrival_time' => '09:00',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_name' => 'Bandung - Semarang Night',
                'origin' => 'Bandung',
                'destination' => 'Semarang',
                'distance' => '380 km',
                'duration' => '7 jam',
                'price' => 285000,
                'bus_id' => $buses[1]->id,
                'departure_time' => '21:00',
                'arrival_time' => '04:00',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_name' => 'Yogyakarta - Surabaya Scenic',
                'origin' => 'Yogyakarta',
                'destination' => 'Surabaya',
                'distance' => '320 km',
                'duration' => '6 jam',
                'price' => 245000,
                'bus_id' => $buses[0]->id,
                'departure_time' => '08:00',
                'arrival_time' => '14:00',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_name' => 'Jakarta - Semarang Direct',
                'origin' => 'Jakarta',
                'destination' => 'Semarang',
                'distance' => '450 km',
                'duration' => '8 jam',
                'price' => 310000,
                'bus_id' => $buses[2]->id,
                'departure_time' => '19:00',
                'arrival_time' => '03:00',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Testimonials
        Testimonial::insert([
            [
                'name' => 'Sarah Johnson',
                'role' => 'Business Traveler',
                'rating' => 5,
                'text' => 'Pelayanan terasa jauh lebih matang dari bus premium biasa. Kabin tenang, crew rapi, dan seluruh perjalanan terasa effortless.',
                'image' => 'https://i.pravatar.cc/150?img=1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ahmad Rizki',
                'role' => 'Family Vacation',
                'rating' => 5,
                'text' => 'Anak-anak nyaman, proses boarding tertib, dan kualitas kursinya memang premium. Sangat cocok untuk perjalanan keluarga jarak jauh.',
                'image' => 'https://i.pravatar.cc/150?img=12',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lisa Chen',
                'role' => 'Solo Traveler',
                'rating' => 5,
                'text' => 'Aman, elegan, dan tepat waktu. Saya suka bagaimana brand ini terasa refined tanpa berusaha terlalu keras.',
                'image' => 'https://i.pravatar.cc/150?img=5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Gallery
        Gallery::insert([
            ['title' => 'Sunrise Escape', 'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Mountain Route', 'image' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=900&q=80', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'City Lights', 'image' => 'https://images.unsplash.com/photo-1491553895911-0055eca6402d?auto=format&fit=crop&w=900&q=80', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Coastal Drive', 'image' => 'https://images.unsplash.com/photo-1489515217757-5fd1be406fef?auto=format&fit=crop&w=900&q=80', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Forest Road', 'image' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=900&q=80', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Ocean View', 'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=900&q=80', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
