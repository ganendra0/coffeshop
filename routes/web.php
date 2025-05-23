<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UserController; // Pastikan path ini benar ke UserController Anda
use App\Http\Controllers\Auth\LoginController; // Kita akan buat controller ini (opsional, bisa juga closure)
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\MenuController; 
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DeliveryController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route untuk halaman utama, bisa langsung ke daftar user jika sudah login, atau ke login jika belum
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('users.index'); // Jika sudah login, arahkan ke daftar user
    }
    return redirect()->route('login'); // Jika belum, arahkan ke halaman login
})->name('home');


// --- Rute Autentikasi Sederhana ---
// Menampilkan form login
Route::get('/login', function () {
    return view('auth.login'); // Anda perlu membuat view ini: resources/views/auth/login.blade.php
})->middleware('guest')->name('login'); // 'guest' middleware agar user yg sudah login tdk bisa akses halaman login lagi

// Memproses login
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->filled('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended(route('users.index')); // Arahkan ke daftar user setelah login
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->middleware('guest');

// Menampilkan form registrasi (BARU)
Route::get('/register', function () { // Atau [RegisteredUserController::class, 'create']
    return view('auth.register');
})->middleware('guest')->name('register');

// Memproses registrasi (BARU)
Route::post('/register', function (Request $request) { // Atau [RegisteredUserController::class, 'store'] dan beri nama 'register.store' atau 'register' saja
    // Validasi Data
    $request->validate([
        'name' => ['required', 'string', 'max:191'],
        'email' => ['required', 'string', 'email', 'max:191', 'unique:users,email'], // 'unique:users,email' untuk memastikan email unik di tabel users
        'phone' => ['nullable', 'string', 'max:20'],
        'password' => ['required', 'string', 'min:8', 'confirmed'], // 'confirmed' akan mencocokkan dengan 'password_confirmation'
        'address' => ['nullable', 'string'],
    ]);

    // Buat User Baru
    $user = \App\Models\User::create([ // Pastikan model User Anda di-import atau gunakan namespace lengkap
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'password' => Illuminate\Support\Facades\Hash::make($request->password), // Selalu hash password
        'address' => $request->address,
        // email_verified_at bisa diisi null atau dihandle terpisah jika ada verifikasi email
    ]);

    // Opsional: Loginkan user setelah registrasi
    Illuminate\Support\Facades\Auth::login($user);

    // Redirect ke halaman setelah registrasi berhasil (misalnya dashboard atau halaman utama)
    return redirect(route('home'))->with('success', 'Registrasi berhasil! Selamat datang.');

})->middleware('guest')->name('register.store'); // Atau beri nama 'register' jika route GET dan POST sama URLnya

// Memproses logout (sudah ada sebelumnya)
Route::post('/logout', function (Request $request) { // Atau [AuthenticatedSessionController::class, 'destroy']
    // ... logika logout ...
})->middleware('auth')->name('logout');

// --- Rute untuk Fitur Aplikasi ---
Route::middleware(['auth'])->group(function () {
    // ... (Route users, menus, orders, dll.) ...
});

// Memproses logout
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login'); // Arahkan ke halaman login setelah logout
})->middleware('auth')->name('logout');


// --- Rute untuk Fitur Aplikasi (dilindungi middleware auth) ---
Route::middleware(['auth'])->group(function () {
    // Route untuk users (yang sudah Anda buat sebelumnya)
    Route::resource('users', UserController::class)->parameters(['users' => 'user_id']);
    Route::resource('menus', MenuController::class)->parameters(['menus' => 'menu_id']);
    Route::resource('orders', OrderController::class)->parameters(['orders' => 'order_id']);
    Route::resource('order_items', OrderItemController::class)->parameters(['order_items' => 'order_item']);
    Route::resource('payments', PaymentController::class)->parameters(['payments' => 'payment']);
    Route::resource('reviews', ReviewController::class)->parameters(['reviews' => 'review']);
    Route::resource('notifications', NotificationController::class)->parameters(['notifications' => 'notification']);
    Route::resource('drivers', DriverController::class)->parameters(['drivers' => 'driver']);
    Route::resource('deliveries', DeliveryController::class)->parameters(['deliveries' => 'delivery']);

    // ... route lain yang butuh login bisa ditambahkan di sini
    // Contoh:
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Catatan:
// Route::resource('users', UserController::class)->parameters(['users' => 'user_id']);
// akan membuat route-route berikut (dengan user_id sebagai parameter):
// GET       /users                    | users.index
// GET       /users/create             | users.create
// POST      /users                    | users.store
// GET       /users/{user_id}          | users.show
// GET       /users/{user_id}/edit     | users.edit
// PUT/PATCH /users/{user_id}          | users.update
// DELETE    /users/{user_id}          | users.destroy