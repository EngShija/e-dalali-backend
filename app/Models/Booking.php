<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'customer_id',
        'start_date',
        'end_date',
        'total_rent',
        'payment_method',
        'commission_paid',
        'is_completed',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_rent' => 'decimal:2',
        'commission_paid' => 'boolean',
        'is_completed' => 'boolean',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Number of nights for the booking
     */
    public function nights()
    {
        if (! $this->start_date || ! $this->end_date) {
            return 0;
        }
        $start = \Carbon\Carbon::parse($this->start_date);
        $end = \Carbon\Carbon::parse($this->end_date);
        return $start->diffInDays($end);
    }
}