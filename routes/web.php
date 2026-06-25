<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FnbController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\LaundryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FnbMenuController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth', 'prevent-back'])->group(function () {

    // Root Redirector based on User Role
    Route::get('/', function () {
        $user = Auth::user();
        if ($user->hasRole('Administrator')) {
            return redirect('/admin/dashboard');
        } elseif ($user->hasRole('Front Office')) {
            return redirect('/fo/dashboard');
        } elseif ($user->hasRole('Housekeeping')) {
            return redirect('/hk/dashboard');
        } elseif ($user->hasRole('Food & Beverage')) {
            return redirect('/fnb/dashboard');
        }

        return redirect('/login');
    });

    // Administrator Routes
    Route::middleware(['role:Administrator'])->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
        Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports');
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    });

    // Front Office Routes (and Admin helper)
    Route::middleware(['role:Front Office|Administrator'])->group(function () {
        Route::get('/fo/dashboard', [DashboardController::class, 'foDashboard'])->name('fo.dashboard');

        // Reservations Management
        Route::resource('reservations', ReservationController::class);
        Route::post('/reservations/{reservation}/checkin', [ReservationController::class, 'checkin'])->name('reservations.checkin');
        Route::post('/reservations/{reservation}/extend', [ReservationController::class, 'extend'])->name('reservations.extend');
        Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

        // Guests CRUD
        Route::resource('guests', GuestController::class);

        // Billing & Settlements
        Route::post('/reservations/{reservation}/checkout', [PaymentController::class, 'checkout'])->name('reservations.checkout.process');
        Route::post('/reservations/{reservation}/request-inspection', [ReservationController::class, 'requestInspection'])->name('reservations.request-inspection');
        Route::get('/reservations/{reservation}/invoice', [PaymentController::class, 'printInvoice'])->name('reservations.invoice');
        Route::get('/reservations/{reservation}/registration-form', [ReservationController::class, 'printRegistrationForm'])->name('reservations.registration-form');

        // Creating service requests
        Route::get('/laundry/create', [LaundryController::class, 'create'])->name('laundry.create');
        Route::post('/laundry', [LaundryController::class, 'store'])->name('laundry.store');
        Route::get('/fnb/create', [FnbController::class, 'create'])->name('fnb.create');
        Route::post('/fnb', [FnbController::class, 'store'])->name('fnb.store');

        // Reports
        Route::get('/fo/reports', [ReportController::class, 'foReports'])->name('fo.reports');
    });

    // Housekeeping Routes (and Admin helper)
    Route::middleware(['role:Housekeeping|Administrator'])->group(function () {
        Route::get('/hk/dashboard', [DashboardController::class, 'hkDashboard'])->name('hk.dashboard');
        Route::post('/rooms/{room}/status', [DashboardController::class, 'updateRoomStatus'])->name('rooms.status');

        // Inspections
        Route::get('/reservations/{reservation}/rooms/{room}/inspect', [InspectionController::class, 'create'])->name('inspections.create');
        Route::post('/reservations/{reservation}/rooms/{room}/inspect', [InspectionController::class, 'store'])->name('inspections.store');
        Route::get('/inspections', [InspectionController::class, 'index'])->name('inspections.index');

        // Laundry Management
        Route::get('/laundry', [LaundryController::class, 'index'])->name('laundry.index');
        Route::post('/laundry/{laundryRequest}/status', [LaundryController::class, 'updateStatus'])->name('laundry.status');

        // Reports
        Route::get('/hk/reports', [ReportController::class, 'hkReports'])->name('hk.reports');
    });

    // F&B Routes (and Admin helper)
    Route::middleware(['role:Food & Beverage|Administrator'])->group(function () {
        Route::get('/fnb/dashboard', [DashboardController::class, 'fnbDashboard'])->name('fnb.dashboard');
        Route::get('/fnb', [FnbController::class, 'index'])->name('fnb.index');
        Route::post('/fnb/{fnbOrder}/status', [FnbController::class, 'updateStatus'])->name('fnb.status');
        Route::get('/fnb/reports', [FnbController::class, 'reports'])->name('fnb.reports');
        Route::resource('fnb/menus', FnbMenuController::class)->names('fnb.menus');
    });
});
