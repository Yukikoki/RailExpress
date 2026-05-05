<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'booking_code',
        'user_id',
        'schedule_id',
        'total_price',
        'status',
    ];

    public function passengers(): HasMany
    {
        // Relasi ke tabel booking_passengers
        return $this->hasMany(BookingPassenger::class, 'booking_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
