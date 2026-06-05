<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| ARUNIKA RYOKA — Web Routes
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

    Route::get('/pembayaran', function () {
        return Inertia::render('Booking/Payment');
    })->name('payment');
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
        return Inertia::render('User/Reservations');
    })->name('reservations');

    Route::get('/ulasan', function () {
        return Inertia::render('User/Reviews');
    })->name('reviews');
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
