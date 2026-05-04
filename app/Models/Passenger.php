<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    protected $fillable = [
        'booking_id',
        'name',
        'nik',
        'seat_number'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
