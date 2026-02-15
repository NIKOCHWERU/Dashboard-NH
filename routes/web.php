<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/login', function () {
    return view('welcome'); // Or a custom login view
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // File Routes (accessible by all auth users, scoped by logic)
    Route::resource('files', FileController::class);
    Route::get('files/{file}/view', [FileController::class, 'view'])->name('files.view');
    Route::get('files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::post('files/bulk-download', [FileController::class, 'bulkDownload'])->name('files.bulk-download');
    Route::delete('files/folder', [FileController::class, 'destroyFolder'])->name('files.destroyFolder')->middleware(['role:admin']);

    // Clients - Read access for all, write for admin only
    Route::get('clients', [ClientController::class, 'index'])->name('clients.index');

    // Events/Calendar - Read access for all
    Route::get('events', [App\Http\Controllers\EventController::class, 'index'])->name('events.index');

    // Event CRUD - for users with permission (checked in controller)
    Route::post('events/from-date', [App\Http\Controllers\EventController::class, 'storeFromDate'])->name('events.store-from-date');
    Route::post('events', [App\Http\Controllers\EventController::class, 'store'])->name('events.store');
    Route::put('events/{event}', [App\Http\Controllers\EventController::class, 'update'])->name('events.update');
    Route::delete('events/{event}', [App\Http\Controllers\EventController::class, 'destroy'])->name('events.destroy');

    // Admin Only Routes (Create, Update, Delete)
    Route::middleware(['role:admin'])->group(function () {
        Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
        Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

        Route::resource('users', UserController::class);
        Route::post('users/{user}/assign-client', [UserController::class, 'assignClient'])->name('users.assign-client');
        Route::post('users/{user}/toggle-event-permission', [UserController::class, 'toggleEventPermission'])->name('users.toggle-event-permission');

        // Info Management
        Route::resource('infos', App\Http\Controllers\InfoController::class);
    });
});
