<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    // Isi dengan kolom-kolom yang ada di migration kamu
    protected $fillable = [
        'booking_code',
        'user_id',
        'schedule_id',
        'seat_id',
        'status',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Tambahkan relasi ke Seat agar bisa tahu kursi mana yang dipesan
    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }
}
