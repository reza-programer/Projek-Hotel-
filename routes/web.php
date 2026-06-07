<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| DARMA MIZUKI — Web Routes
| アルニカ 旅館 ルーティング
|--------------------------------------------------------------------------
*/

// ============================================
// PUBLIC ROUTES — User Facing
// ============================================

// Home
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// About
Route::get('/tentang-kami', function () {
    return Inertia::render('About');
})->name('about');

// Rooms
Route::prefix('kamar')->name('rooms.')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Rooms/Index');
    })->name('index');

    Route::get('/{id}', function ($id) {
        return Inertia::render('Rooms/Show', ['roomId' => $id]);
    })->name('show');
});

// Booking
Route::prefix('booking')->name('booking.')->group(function () {
    Route::get('/buat', function () {
        return Inertia::render('Booking/Create');
    })->name('create');

    Route::post('/simpan', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'room_id' => 'required|integer',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'nights' => 'required|integer',
            'guests' => 'required|integer',
            'total_price' => 'required|numeric',
            'email' => 'required|email',
            'guest_name' => 'required|string',
        ]);

        // Cek Overlap (Ketersediaan)
        // Logika overlap: Reservasi bertabrakan jika (existing.check_in < new.check_out) AND (existing.check_out > new.check_in)
        $overlap = \App\Models\Reservation::where('room_id', $validated['room_id'])
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->where(function ($query) use ($validated) {
                $query->where('check_in', '<', $validated['check_out'])
                      ->where('check_out', '>', $validated['check_in']);
            })->exists();

        if ($overlap) {
            return back()->withErrors(['room' => 'Mohon maaf, kamar ini sudah dipesan/dibayar pada tanggal tersebut. Silakan pilih tanggal atau kamar lain.']);
        }

        $user = null;
        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
        } else {
            $user = \App\Models\User::firstOrCreate(
                ['email' => $validated['email']],
                [
                    'name' => $validated['guest_name'],
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16))
                ]
            );
        }
        $user_id = $user->id;

        $reservation = \App\Models\Reservation::create([
            'booking_code' => 'MYB-' . date('Y') . '-' . rand(1000, 9999),
            'user_id' => $user_id,
            'room_id' => $validated['room_id'],
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'nights' => $validated['nights'],
            'guests' => $validated['guests'],
            'total_price' => $validated['total_price'],
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        return redirect()->route('booking.payment');
    })->name('store');

    Route::get('/pembayaran', function () {
        $reservation = \App\Models\Reservation::with('room')->where('payment_status', 'pending')->latest()->first();
        if (!$reservation) {
            // Fallback jika tidak ada pending, ambil yg terakhir
            $reservation = \App\Models\Reservation::with('room')->latest()->first();
        }
        return Inertia::render('Booking/Payment', [
            'reservation' => $reservation
        ]);
    })->name('payment');
});

use App\Http\Controllers\PaymentController;
Route::post('/api/midtrans/snap-token', [PaymentController::class, 'getSnapToken']);
Route::post('/api/midtrans/webhook', [PaymentController::class, 'webhook']);

// Local fallback untuk mengirim email karena webhook tidak jalan di localhost tanpa ngrok
Route::post('/api/midtrans/local-success', function (\Illuminate\Http\Request $request) {
    $reservation = \App\Models\Reservation::with(['user', 'room'])->find($request->reservation_id);
    if ($reservation && $reservation->payment_status !== 'paid') {
        $reservation->update(['payment_status' => 'paid', 'status' => 'confirmed']);
        
        \App\Models\Payment::updateOrCreate(
            ['reservation_id' => $reservation->id],
            ['method' => 'midtrans', 'amount' => $request->gross_amount ?? $reservation->total_price, 'verified_at' => now(), 'notes' => 'Local Success']
        );

        try {
            \Illuminate\Support\Facades\Mail::to($reservation->user->email)->send(new \App\Mail\InvoiceMail($reservation));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Local email failed: ' . $e->getMessage());
        }
    }
    return response()->json(['success' => true]);
});

// Authentication
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::get('/register', function () {
    return Inertia::render('Auth/Register');
})->name('register');

// Google Authentication Routes
use App\Http\Controllers\Auth\GoogleAuthController;
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// User Area
Route::prefix('akun')->name('user.')->group(function () {
    Route::get('/profil', function () {
        return Inertia::render('User/Profile');
    })->name('profile');

    Route::get('/reservasi', function () {
        $user_id = \Illuminate\Support\Facades\Auth::id() ?? 1;
        $reservations = \App\Models\Reservation::with('room')->where('user_id', $user_id)->orderBy('created_at', 'desc')->get();

        $formatted = $reservations->map(function($res) {
            return [
                'id' => $res->booking_code,
                'roomId' => $res->room_id,
                'roomName' => $res->room->name ?? 'Room',
                'checkIn' => $res->check_in->format('Y-m-d'),
                'checkOut' => $res->check_out->format('Y-m-d'),
                'nights' => $res->nights,
                'guests' => $res->guests,
                'totalPrice' => $res->total_price,
                'status' => $res->status,
                'paymentStatus' => $res->payment_status,
                'bookingDate' => $res->created_at->format('Y-m-d'),
            ];
        });

        return Inertia::render('User/Reservations', [
            'reservations' => $formatted
        ]);
    })->name('reservations');

    Route::get('/ulasan', function () {
        return Inertia::render('User/Reviews');
    })->name('reviews');
});

// ============================================
// API ROUTES
// ============================================
Route::get('/api/kamar/{id}/tanggal-penuh', function ($id) {
    $reservations = \App\Models\Reservation::where('room_id', $id)
        ->whereNotIn('status', ['cancelled', 'rejected'])
        ->get(['check_in', 'check_out']);

    $unavailableDates = [];
    foreach ($reservations as $res) {
        $start = \Carbon\Carbon::parse($res->check_in);
        $end = \Carbon\Carbon::parse($res->check_out);
        // Tanggal check-out tidak di-block agar user lain bisa check-in di hari tersebut (pakai lt bukan lte)
        for ($date = $start; $date->lt($end); $date->addDay()) {
            $unavailableDates[] = $date->format('Y-m-d');
        }
    }

    return response()->json(array_values(array_unique($unavailableDates)));
});

// ============================================
// ADMIN ROUTES
// ============================================

Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('dashboard');

    // Chat Concierge
    Route::get('/chat', function () {
        return Inertia::render('Admin/Chat');
    })->name('chat');

    // Room Management
    Route::prefix('kamar')->name('rooms.')->group(function () {
        Route::get('/', function () {
            return Inertia::render('Admin/Rooms/Index');
        })->name('index');
    });

    // Facilities Management
    Route::get('/fasilitas', function () {
        return Inertia::render('Admin/Facilities/Index');
    })->name('facilities');

    // Reservation Management
    Route::get('/reservasi', function () {
        return Inertia::render('Admin/Reservations/Index');
    })->name('reservations');

    // Payment Management
    Route::get('/pembayaran', function () {
        return Inertia::render('Admin/Payments/Index');
    })->name('payments');

    // Income Report
    Route::get('/laporan/pendapatan', function () {
        return Inertia::render('Admin/Reports/Income');
    })->name('reports.income');

    // User Management
    Route::get('/pengguna', function () {
        return Inertia::render('Admin/Users/Index');
    })->name('users');
});
