<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carriage extends Model
{
    protected $fillable = [
        'train_id',
        'name',
        'type',
    ];

    protected $guarded = [];

    public function train()
    {
        return $this->belongsTo(Train::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
}
