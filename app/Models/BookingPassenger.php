<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPassenger extends Model
{
    protected $guarded = [];

    // Relasi ke Kursi (Seat)
    public function seat()
    {
        return $this->belongsTo(Seat::class, 'seat_id');
    }
}
