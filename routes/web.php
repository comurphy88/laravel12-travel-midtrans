<?php

use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminBusController;
use App\Http\Controllers\Admin\AdminCancellationPolicyController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminEmailLogController;
use App\Http\Controllers\Admin\AdminGalleryController;
use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminPromoCodeController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminRouteController;
use App\Http\Controllers\Admin\AdminScheduleController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminTestimonialController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────────
Route::get('/', WelcomeController::class)->name('home');
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

// Midtrans webhook (no auth, no CSRF)
Route::post('/payment/notification', [PaymentController::class, 'notification'])
    ->name('payment.notification')
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->middleware('throttle:100,1');

// ── Guest ─────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:3,1');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:3,60')->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->middleware('throttle:3,60')->name('password.update');
});

// ── Auth ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Schedules (AJAX)
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/schedules/available', [ScheduleController::class, 'available'])->name('schedules.available');

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->middleware('throttle:10,60')->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->middleware('throttle:5,60')->name('bookings.cancel');
    Route::get('/bookings/{booking}/ticket', [BookingController::class, 'ticket'])->name('bookings.ticket');

    // AJAX helpers
    Route::post('/calculate-price', [BookingController::class, 'calculatePrice'])->name('booking.calculate-price');
    Route::get('/check-seats', [BookingController::class, 'checkSeats'])->name('booking.check-seats');

    // Payments
    Route::get('/bookings/{booking}/pay', [PaymentController::class, 'show'])->name('payment.show');
    Route::get('/bookings/{booking}/pay/finish', [PaymentController::class, 'finish'])->middleware('throttle:10,60')->name('payment.finish');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->middleware('throttle:5,60')->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->middleware('throttle:3,60')->name('profile.password');
});

// ── Admin ─────────────────────────────────────────────────────
Route::middleware(['auth', 'admin', 'throttle:60,1'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Buses
    Route::resource('buses', AdminBusController::class)->except('show');

    // Routes
    Route::resource('routes', AdminRouteController::class)->except('show');
    Route::patch('/routes/{route}/toggle', [AdminRouteController::class, 'toggle'])->name('routes.toggle');

    // Schedules
    Route::resource('schedules', AdminScheduleController::class)->except('show');
    Route::patch('/schedules/{schedule}/toggle', [AdminScheduleController::class, 'toggle'])->name('schedules.toggle');

    // Bookings
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');

    // Users
    Route::resource('users', AdminUserController::class)->except('show');

    // Promo codes
    Route::resource('promo-codes', AdminPromoCodeController::class)->except('show');

    // Cancellation policies
    Route::resource('cancellation-policies', AdminCancellationPolicyController::class)->except('show');

    // Reviews
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{review}', [AdminReviewController::class, 'show'])->name('reviews.show');
    Route::patch('/reviews/{review}/status', [AdminReviewController::class, 'updateStatus'])->name('reviews.status');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // Galleries & Testimonials
    Route::resource('galleries', AdminGalleryController::class)->except('show');
    Route::resource('testimonials', AdminTestimonialController::class)->except('show');

    // Settings
    Route::resource('settings', AdminSettingController::class)->except('show');

    // Logs
    Route::get('/logs', [AdminLogController::class, 'index'])->name('logs');
    Route::get('/email-logs', [AdminEmailLogController::class, 'index'])->name('email-logs.index');

    // Notifications
    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::delete('/notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
});
