<?php       

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import Controller yang akan digunakan
use App\Http\Controllers\Api\AuthController; // Tambahkan ini
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReviewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- Rute Autentikasi ---
// Anda bisa memilih prefix sendiri, misal 'auth' atau langsung di root API
// Saya akan gunakan prefix 'v1/auth' agar konsisten dengan resource Anda yang lain
Route::prefix('v1/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');

    // Rute yang memerlukan autentikasi Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
        Route::get('/user', [AuthController::class, 'user'])->name('api.auth.user'); // Menggantikan closure lama
        Route::put('/user/profile', [AuthController::class, 'updateProfile'])->name('api.auth.profile.update');
        Route::put('/user/password', [AuthController::class, 'changePassword'])->name('api.auth.password.change');
            
    });
});


// --- Rute Resource (API v1) ---
Route::prefix('v1')->group(function () {
    // UserController: Untuk manajemen user oleh admin (jika diperlukan).
    // Jika CRUD User hanya untuk admin, pastikan route ini juga dilindungi (misal dengan middleware admin).
    // Untuk sekarang, saya asumsikan ini dilindungi oleh Sanctum secara umum jika dipanggil oleh admin yang login.
    Route::apiResource('users', UserController::class)->middleware('auth:sanctum'); // Melindungi CRUD User

    // Resource lain, bisa saja ada yang publik dan ada yang perlu login
    Route::apiResource('menus', MenuController::class); // Menu bisa dilihat publik

    // Resource yang biasanya memerlukan autentikasi user
    Route::middleware('auth:sanctum')->group(function() {
        Route::apiResource('orders', OrderController::class);
        Route::apiResource('order-items', OrderItemController::class);
        Route::apiResource('payments', PaymentController::class);
        Route::apiResource('drivers', DriverController::class); // Mungkin hanya admin
        Route::apiResource('deliveries', DeliveryController::class); // Mungkin hanya admin
        Route::apiResource('notifications', NotificationController::class);
        Route::apiResource('reviews', ReviewController::class);

        // Contoh route custom jika diperlukan, misal untuk Order
        // Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus']);

        // Anda mungkin perlu memisahkan route mana yang untuk user biasa dan mana yang untuk admin
        // Contoh:
        // Route::prefix('admin')->middleware('is_admin')->group(function() {
        //     Route::apiResource('drivers', DriverController::class);
        //     Route::apiResource('deliveries', DeliveryController::class);
        // });
    });
});

// Hapus atau komentari closure lama untuk /api/user jika sudah digantikan AuthController@user
/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/