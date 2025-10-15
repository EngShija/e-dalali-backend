<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StorageController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\OwnerController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\DebtController;
Route::post('/register', [AuthController::class, 'register']);
// Handle CORS preflight requests
Route::options('/{any}', function () {
    return response()->json([], 204);
})->where('any', '.*');
Route::post('/login', [AuthController::class, 'login']);

// Serve storage files with CORS headers (fallback if webserver doesn't add CORS)
Route::match(['get', 'options'], '/storage/{path}', [StorageController::class, 'show'])->where('path', '.*');
Route::get('/listings', [ListingController::class, 'index']);
Route::get('/listings/{listing}', [ListingController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user()->load('owner'); // Eager load owner for convenience
    });

    Route::middleware([\App\Http\Middleware\CheckUserRole::class . ':customer'])->group(function () {
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::post('/favorites/{listing}', [CustomerController::class, 'addFavorite']);
        Route::get('/favorites', [CustomerController::class, 'getFavorites']);
    });

    Route::middleware([\App\Http\Middleware\CheckUserRole::class . ':owner'])->group(function () {
        Route::post('/listings', [ListingController::class, 'store']);
        Route::put('/listings/{listing}', [ListingController::class, 'update']);
        Route::delete('/listings/{listing}', [ListingController::class, 'destroy']);
        Route::get('/my-listings', [OwnerController::class, 'myListings']);
        // Backwards-compatible endpoint expected by Flutter client
        Route::get('/owner/listings', [OwnerController::class, 'myListings']);
    // Listing images (owner-only)
    Route::get('/listings/{listing}/images', [\App\Http\Controllers\Api\ListingImageController::class, 'index']);
    Route::post('/listings/{listing}/images', [\App\Http\Controllers\Api\ListingImageController::class, 'store']);
    Route::delete('/listings/{listing}/images/{image}', [\App\Http\Controllers\Api\ListingImageController::class, 'destroy']);
        Route::post('/bookings/{booking}/mark-paid', [OwnerController::class, 'markBookingAsPaid']);
        Route::get('/owner/debt-status', [OwnerController::class, 'getDebtStatus']);
    });

    Route::middleware([\App\Http\Middleware\CheckUserRole::class . ':admin'])->group(function () {
        // ... Admin routes
        Route::get('/admin/users', [AdminController::class, 'listUsers']);
        Route::get('/admin/bookings', [AdminController::class, 'listBookings']);
        Route::get('/admin/owners', [AdminController::class, 'listOwners']);
        Route::get('/admin/debts', [AdminController::class, 'listDebts']);
  
    });
});