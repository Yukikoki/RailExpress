<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TicketController::class, 'index'])->name('home');

// Route bawaan Breeze
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- ROUTE RAILEXPRESS ---

// Pencarian tiket bisa diakses publik
Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
Route::get('/tickets/search', [TicketController::class, 'search'])->name('tickets.search');

// Prosedur booking & pembayaran wajib login
Route::middleware('auth')->group(function () {
    Route::get('/tickets/{schedule}/select-seat', [TicketController::class, 'selectSeats'])->name('tickets.selectSeat');
    Route::post('/tickets/book', [TicketController::class, 'store'])->name('tickets.store');

    Route::get('/booking/{id}', [TicketController::class, 'show'])->name('booking.show');
    Route::post('/booking/{id}/pay', [TicketController::class, 'pay'])->name('booking.pay');
});

require __DIR__.'/auth.php';
