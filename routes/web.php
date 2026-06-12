<?php

use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminBusController;
use App\Http\Controllers\Admin\AdminBusRouteController;
use App\Http\Controllers\Admin\AdminCancellationPolicyController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminDestinationController;
use App\Http\Controllers\Admin\AdminEmailLogController;
use App\Http\Controllers\Admin\AdminGalleryController;
use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminPromoCodeController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminTestimonialController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomeController::class)->name('home');

// Public pages
Route::get('/destinations', [DestinationController::class, 'index'])->name('destinations.index');
Route::get('/destinations/{destination}', [DestinationController::class, 'show'])->name('destinations.show');
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

// Midtrans webhook (no auth, no CSRF) - Rate limit webhook calls
Route::post('/payment/notification', [PaymentController::class, 'notification'])
    ->name('payment.notification')
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->middleware('throttle:100,1');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    // Login attempts: max 5 requests per 1 minute
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    // Registration attempts: max 3 requests per 1 minute
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:3,1');

    // Password reset routes
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:3,60')->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->middleware('throttle:3,60')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Bookings - Rate limit: 10 bookings per 1 hour
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->middleware('throttle:10,60')->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->middleware('throttle:5,60')->name('bookings.cancel');

    // Payments - Rate limit: 10 payment attempts per hour
    Route::get('/bookings/{booking}/pay', [PaymentController::class, 'show'])->name('payment.show');
    Route::get('/bookings/{booking}/pay/finish', [PaymentController::class, 'finish'])->middleware('throttle:10,60')->name('payment.finish');

    // Profile - Rate limit: 5 updates per hour
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->middleware('throttle:5,60')->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->middleware('throttle:3,60')->name('profile.password');
});

// Admin panel
Route::middleware(['auth', 'admin', 'throttle:60,1'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::resource('destinations', AdminDestinationController::class)->except('show');
    Route::resource('buses', AdminBusController::class)->except('show');
    Route::resource('bus-routes', AdminBusRouteController::class)->except('show');
    Route::resource('users', AdminUserController::class)->except('show');
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');
    Route::get('/logs', [AdminLogController::class, 'index'])->name('logs');

    // New CRUD resources
    Route::resource('galleries', AdminGalleryController::class)->except('show');
    Route::resource('testimonials', AdminTestimonialController::class)->except('show');
    Route::resource('promo-codes', AdminPromoCodeController::class)->except('show');
    Route::resource('cancellation-policies', AdminCancellationPolicyController::class)->except('show');
    Route::resource('settings', AdminSettingController::class)->except('show');

    // Reviews (read-only + status)
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{review}', [AdminReviewController::class, 'show'])->name('reviews.show');
    Route::patch('/reviews/{review}/status', [AdminReviewController::class, 'updateStatus'])->name('reviews.status');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // Email logs (read-only)
    Route::get('/email-logs', [AdminEmailLogController::class, 'index'])->name('email-logs.index');

    // Notifications (read + delete)
    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::delete('/notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
});
