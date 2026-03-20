<?php

use App\Http\Controllers\Admin\BookingManagementController as AdminBookingManagementController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Staff\BookingController as StaffBookingController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->middleware('storefront')->name('home');

Route::get('/events', [EventController::class, 'index'])->middleware('storefront')->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->middleware('storefront')->name('events.show');
Route::get('/tickets/verify/{token}', [TicketController::class, 'verify'])->name('tickets.verify');

Route::get('/dashboard', [HomeController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:user')->group(function () {
        Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
        Route::post('/events/{event}/book', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('/checkout/{booking}', [CheckoutController::class, 'show'])->name('checkout.show');
        Route::post('/checkout/{booking}', [CheckoutController::class, 'process'])->name('checkout.process');
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
        Route::post('/events/{event}/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
        Route::delete('/events/{event}/wishlist', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
        Route::post('/events/{event}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
        Route::get('/events/create', [AdminEventController::class, 'create'])->name('events.create');
        Route::post('/events', [AdminEventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}/edit', [AdminEventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [AdminEventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');

        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/toggle-block', [UserManagementController::class, 'toggleBlock'])->name('users.toggle-block');

        Route::get('/bookings', [AdminBookingManagementController::class, 'index'])->name('bookings.index');
        Route::patch('/bookings/{booking}/assign-staff', [AdminBookingManagementController::class, 'assignStaff'])->name('bookings.assign-staff');
        Route::patch('/bookings/{booking}/status', [AdminBookingManagementController::class, 'updateStatus'])->name('bookings.update-status');

        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    });

    Route::prefix('staff')->name('staff.')->middleware('role:staff')->group(function () {
        Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
        Route::patch('/bookings/{booking}/status', [StaffBookingController::class, 'updateStatus'])->name('bookings.update-status');
    });
});

require __DIR__ . '/auth.php';
