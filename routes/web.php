<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/verify/send', [ProfileController::class, 'sendVerification'])->name('verify.send');
    Route::get('/verify/phone', [ProfileController::class, 'verify'])->name('profile.verifyPhone');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');
    Route::post('/activity', [ActivityController::class, 'storeActivityAttendees'])->name('activity.store');

    Route::get('/provinces', [AddressController::class, 'getProvinces'])->name('address.getProvinces');
    Route::get('/municipalities', [AddressController::class, 'getMunicipalities'])->name('address.getMunicipalities');
    Route::get('/barangays', [AddressController::class, 'getBarangays'])->name('address.getBarangays');

});

require __DIR__ . '/auth.php';
