<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\DigitalIdController;
use App\Http\Controllers\DownlinesController;
use App\Http\Controllers\IDVerificationController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\PhoneVerificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\RaffleEntryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/invite', [InviteController::class, 'index'])->name('invite.index');

Route::get('/dashboard', [ProfileController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/user', [ProfileController::class, 'getUser'])->name('user');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::post('/profile/signature', [ProfileController::class, 'updateSignature'])->name('profile.signature');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/qrcode', [QRCodeController::class, 'index'])->name('qrcode.index');

    Route::get('/verify', [PhoneVerificationController::class, 'index'])->name('verify.index');
    Route::get('/verify/send', [PhoneVerificationController::class, 'send'])->name('verify.send');
    Route::get('/verify/phone', [PhoneVerificationController::class, 'verify'])->name('profile.verifyPhone');

    Route::get('/verify/id', [IDVerificationController::class, 'index'])->name('verify.id');
    Route::post('/verify/store', [IDVerificationController::class, 'store'])->name('verify.store');

    Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');
    Route::get('/activity/location', [ActivityController::class, 'checkLocation'])->name('activity.location');
    Route::post('/activity/store', [ActivityController::class, 'storeActivityAttendees'])->name('activity.store');

    Route::get('/provinces', [AddressController::class, 'getProvinces'])->name('address.getProvinces');
    Route::get('/municipalities', [AddressController::class, 'getMunicipalities'])->name('address.getMunicipalities');
    Route::get('/barangays', [AddressController::class, 'getBarangays'])->name('address.getBarangays');

    Route::get('/raffle', [RaffleEntryController::class, 'index'])->name('raffle.index');
    Route::post('/raffle', [RaffleEntryController::class, 'store'])->name('raffle.store');

    Route::get('/digital-id', [DigitalIdController::class, 'index'])->name('digital-id.index');

    Route::get('/downlines', [DownlinesController::class, 'index'])->name('downlines');

});

require __DIR__ . '/auth.php';
