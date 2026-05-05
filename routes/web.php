<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TicketController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return redirect()->route('tickets.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- ROUTE RAILEXPRESS ---
Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
Route::get('/tickets/search', [TicketController::class, 'index'])->name('tickets.search');

Route::middleware('auth')->group(function () {
    // Gunakan prefix jika ingin URL lebih terstruktur, misal: /tickets/1/passengers
    Route::controller(TicketController::class)->group(function () {
        Route::get('/tickets/{schedule}/passengers', 'inputPassengers')->name('tickets.passengers');
        Route::post('/tickets/{schedule}/passengers', 'processPassengers')->name('tickets.processPassengers');

        Route::get('/tickets/{schedule}/select-seat', 'selectSeats')->name('tickets.selectSeat');
        Route::post('/tickets/book', 'store')->name('tickets.store');

        Route::get('/booking/{id}', 'show')->name('booking.show');
        Route::post('/booking/{id}/pay', [TicketController::class, 'pay'])->name('booking.pay');
        Route::post('/booking/{id}/cancel', [TicketController::class, 'cancel'])->name('booking.cancel');
    });
});

require __DIR__.'/auth.php';
