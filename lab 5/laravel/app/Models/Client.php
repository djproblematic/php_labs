<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['full_name', 'email', 'phone'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
