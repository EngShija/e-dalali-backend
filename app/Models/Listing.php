<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'title',
        'description',
        'location_address',
        'latitude',
        'longitude',
        'price',
        'property_type',
        'status',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function images()
    {
        return $this->hasMany(ListingImage::class);
    }
    
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}