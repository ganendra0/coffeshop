<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DeliveryController;
use App\Models\User; // Import model User untuk registrasi
use Illuminate\Support\Facades\Hash; // Import Hash untuk registrasi

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Rute Halaman Publik (Tidak Perlu Login) ---
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/menu', [PageController::class, 'menu'])->name('menu');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/blog', [PageController::class, 'blog'])->name('blog');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// --- Rute Autentikasi ---

// Grup ini untuk route yang hanya bisa diakses oleh 'tamu' (user yang belum login)
Route::middleware('guest')->group(function () {
    // Menampilkan form login
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    // Memproses login
    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            // Arahkan ke dashboard setelah login berhasil
            // Jika Anda punya route 'dashboard', gunakan itu. Jika tidak, users.index juga boleh.
            return redirect()->intended(route('users.index'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    });

    // Menampilkan form registrasi
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    // Memproses registrasi
    Route::post('/register', function (Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'address' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'address' => $request->address,
        ]);

        Auth::login($user);

        return redirect(route('home'))->with('success', 'Registrasi berhasil! Selamat datang.');
    })->name('register.store');
});

// --- Rute yang Membutuhkan Autentikasi ---

// Grup ini untuk semua route yang hanya bisa diakses oleh user yang sudah login
Route::middleware('auth')->group(function () {
    // Memproses logout
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    // Route untuk fitur CRUD aplikasi Anda
    Route::resource('users', UserController::class)->parameters(['users' => 'user_id']);
    Route::resource('menus', MenuController::class)->parameters(['menus' => 'menu_id']);
    Route::resource('orders', OrderController::class)->parameters(['orders' => 'order_id']);
    Route::resource('order_items', OrderItemController::class)->parameters(['order_items' => 'order_item']);
    Route::resource('payments', PaymentController::class)->parameters(['payments' => 'payment']);
    Route::resource('reviews', ReviewController::class)->parameters(['reviews' => 'review']);
    Route::resource('notifications', NotificationController::class)->parameters(['notifications' => 'notification']);
    Route::resource('drivers', DriverController::class)->parameters(['drivers' => 'driver']);
    Route::resource('deliveries', DeliveryController::class)->parameters(['deliveries' => 'delivery']);

    // Contoh route dashboard jika ada
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});