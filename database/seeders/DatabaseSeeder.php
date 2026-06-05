<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPreference;
use App\Models\Room;
use App\Models\Facility;
use App\Models\Testimonial;
use App\Models\Reservation;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users & Preferences
        $admin = User::create([
            'name' => 'Ahmad Admin',
            'email' => 'admin@arunikaryoka.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'created_at' => '2023-12-01 10:00:00',
        ]);

        $user1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@email.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'active',
            'created_at' => '2024-01-15 10:00:00',
        ]);

        UserPreference::create([
            'user_id' => $user1->id,
            'welcome_tea' => 'Matcha Premium (抹茶)',
            'yukata_size' => 'L (Large)',
            'pillow_type' => 'Soba Hukura (Bantal Kulit Gandum)',
            'food_allergies' => 'Tidak ada',
            'breakfast_time' => '07:30',
        ]);

        // 2. Rooms
        $room1 = Room::create([
            'name' => 'Sakura Standard',
            'name_jp' => '桜スタンダード',
            'type' => 'Standard',
            'price' => 850000,
            'capacity' => 2,
            'size' => 28,
            'floor' => '3-5',
            'description' => 'Kamar nyaman dengan nuansa sakura yang elegan. Dilengkapi dengan pemandangan taman Jepang yang menenangkan.',
            'long_description' => 'Sakura Standard hadir sebagai pengalaman menginap sempurna bagi Anda yang mendambakan ketenangan. Interior kamar dirancang dengan motif bunga sakura yang lembut, paduan warna krem dan merah muda yang memanjakan mata. Nikmati istirahat berkualitas di atas futon premium dengan selimut sutra terbaik.',
            'amenities' => ['Wi-Fi Gratis', 'AC', 'TV 43"', 'Minibar', 'Safe Box', 'Shower & Bathtub', 'Yukata', 'Teh Matcha Gratis'],
            'images' => ['/images/rooms/sakura-1.png', '/images/rooms/generic-bathroom.png', '/images/rooms/generic-balcony.png'],
            'features' => ['Garden View', 'Futon Option'],
            'color_theme' => 'sakura',
            'is_available' => true,
            'rating' => 4.8,
            'review_count' => 124,
        ]);

        $room2 = Room::create([
            'name' => 'Bamboo Deluxe',
            'name_jp' => '竹デラックス',
            'type' => 'Deluxe',
            'price' => 1350000,
            'capacity' => 2,
            'size' => 38,
            'floor' => '6-9',
            'description' => 'Kamar deluxe terinspirasi dari ketenangan hutan bambu. Menawarkan pemandangan kota yang menakjubkan.',
            'long_description' => 'Bamboo Deluxe adalah perpaduan sempurna antara modernitas dan tradisi Jepang. Motif bambu vertikal menghiasi dinding kamar, menciptakan suasana tenang dan sejuk. Dilengkapi dengan sudut baca (reading nook) bergaya Jepang dan koleksi buku pilihan tentang budaya dan filosofi Timur.',
            'amenities' => ['Wi-Fi Gratis', 'AC', 'TV 55"', 'Minibar Premium', 'Safe Box', 'Bathtub Mewah', 'Yukata & Slippers', 'Teh Matcha & Sake', 'Reading Nook', 'Coffee Maker'],
            'images' => ['/images/rooms/bamboo-1.png', '/images/rooms/generic-bathroom.png', '/images/rooms/generic-balcony.png'],
            'features' => ['City View', 'Reading Nook', 'Premium Minibar'],
            'color_theme' => 'matcha',
            'is_available' => true,
            'rating' => 4.9,
            'review_count' => 89,
        ]);

        $room3 = Room::create([
            'name' => 'Zen Garden Suite',
            'name_jp' => '禅ガーデンスイート',
            'type' => 'Suite',
            'price' => 2800000,
            'capacity' => 4,
            'size' => 68,
            'floor' => '10-14',
            'description' => 'Suite mewah dengan taman zen pribadi. Pengalaman menginap yang tak terlupakan dengan layanan butler 24 jam.',
            'long_description' => 'Zen Garden Suite adalah puncak kemewahan Omotenashi. Suite dua lantai ini memiliki taman zen miniatur pribadi yang dapat Anda rawat selama menginap. Ruang makan Jepang terpisah, kamar mandi dengan ofuro (bathtub tradisional Jepang), dan akses ke spa privat tersedia untuk kenyamanan Anda.',
            'amenities' => ['Wi-Fi Gratis', 'AC Multi-zone', 'TV 65" + Proyektor', 'Bar Sake Premium', 'Butler 24 Jam', 'Ofuro (Bathtub Jepang)', 'Yukata Sutra', 'Taman Zen Privat', 'Ruang Makan Privat', 'Spa Akses', 'Limousine Transfer'],
            'images' => ['/images/rooms/zen-1.png', '/images/rooms/generic-bathroom.png', '/images/rooms/generic-balcony.png'],
            'features' => ['Private Garden', 'Butler Service', 'Ofuro Bathtub', 'Spa Access'],
            'color_theme' => 'gold',
            'is_available' => false,
            'rating' => 5.0,
            'review_count' => 47,
        ]);

        // 3. Facilities
        $facilities = [
            ['name' => 'Onsen & Spa', 'name_jp' => '温泉・スパ', 'icon' => 'onsen', 'description' => 'Pemandian air panas alami gaya Jepang', 'is_active' => true, 'category' => 'Wellness'],
            ['name' => 'Kaiseki Restaurant', 'name_jp' => '懐石レストラン', 'icon' => 'dining', 'description' => 'Restoran fine dining masakan Jepang multi-course', 'is_active' => true, 'category' => 'Dining'],
            ['name' => 'Zen Garden', 'name_jp' => '禅の庭', 'icon' => 'garden', 'description' => 'Taman batu zen untuk meditasi dan relaksasi', 'is_active' => true, 'category' => 'Leisure'],
            ['name' => 'Tea Ceremony Room', 'name_jp' => '茶室', 'icon' => 'tea', 'description' => 'Ruang upacara teh tradisional Jepang', 'is_active' => true, 'category' => 'Culture'],
        ];

        foreach ($facilities as $facility) {
            Facility::create($facility);
        }

        // 4. Testimonials
        Testimonial::create([
            'name' => 'Hiroshi Tanaka',
            'origin' => 'Tokyo, Japan',
            'stay_type' => 'Zen Garden Suite',
            'rating' => 5,
            'comment' => 'Sungguh luar biasa! Pelayanan di sini benar-benar mencerminkan semangat Omotenashi yang sesungguhnya. Staf begitu perhatian hingga detail terkecil. Saya merasa seperti sedang berada di Kyoto.',
            'date' => '2025-03-15',
        ]);

        Testimonial::create([
            'name' => 'Sarah Mitchell',
            'origin' => 'Melbourne, Australia',
            'stay_type' => 'Bamboo Deluxe',
            'rating' => 5,
            'comment' => 'The onsen experience was absolutely transcendent. The bamboo room had such a calming energy, and the kaiseki dinner was a culinary journey I will never forget. This is not just a hotel — it\'s an experience.',
            'date' => '2025-04-02',
        ]);

        // 5. Reservations & Payments
        $res1 = Reservation::create([
            'booking_code' => 'MYB-2025-001',
            'user_id' => $user1->id,
            'room_id' => $room2->id,
            'check_in' => '2025-06-10',
            'check_out' => '2025-06-13',
            'nights' => 3,
            'guests' => 2,
            'total_price' => 4050000,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'created_at' => '2025-05-28 10:00:00',
        ]);

        Payment::create([
            'reservation_id' => $res1->id,
            'method' => 'bca',
            'account_number' => '1234567890',
            'account_name' => 'Budi Santoso',
            'amount' => 4050000,
            'verified_at' => '2025-05-28 12:00:00',
            'verified_by' => $admin->id,
        ]);
        
        $res2 = Reservation::create([
            'booking_code' => 'MYB-2025-002',
            'user_id' => $user1->id,
            'room_id' => $room1->id,
            'check_in' => '2025-07-01',
            'check_out' => '2025-07-03',
            'nights' => 2,
            'guests' => 2,
            'total_price' => 1700000,
            'status' => 'pending',
            'payment_status' => 'pending',
            'created_at' => '2025-06-01 10:00:00',
        ]);
    }
}
