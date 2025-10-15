<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'booking_id',
        'amount',
        'is_paid',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}