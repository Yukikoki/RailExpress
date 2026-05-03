<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Train extends Model
{
    protected $fillable = [
        'name',
        'class',
        'total_seats',
    ];

    public function carriages()
    {
        return $this->hasMany(Carriage::class);
    }
}
