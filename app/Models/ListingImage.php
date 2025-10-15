<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingImage extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'listing_id',
        'image_url',
    ];

    protected $appends = ['resolved_image_url'];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Return a canonical image URL. If image_url is already absolute, return it.
     * If it looks like a storage path (starts with / or storage/), prefix with APP_URL + /api/storage.
     */
    public function getResolvedImageUrlAttribute()
    {
        if (! $this->image_url) {
            return null;
        }

        // Already absolute URL
        if (filter_var($this->image_url, FILTER_VALIDATE_URL)) {
            // Ensure it uses APP_URL host if the stored URL lacks port — normalize only hostless "http://localhost" cases
            return $this->image_url;
        }

        // Otherwise assume it's a storage path and build API storage URL
        $appUrl = rtrim(config('app.url') ?: env('APP_URL', 'http://localhost:8000'), '/');
        $path = ltrim($this->image_url, '/');
        return $appUrl . '/api/storage/' . $path;
    }
}