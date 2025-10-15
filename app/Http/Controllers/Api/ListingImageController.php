<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use App\Models\Listing;
use App\Models\ListingImage;

class ListingImageController extends Controller
{
    public function index(Listing $listing)
    {
        $user = Auth::user();
        // Only owner of the listing can view/manage images
        if (! $user || $user->role !== 'owner' || $user->owner->id !== $listing->owner_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($listing->images()->get());
    }

    public function store(Request $request, Listing $listing)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'owner' || $user->owner->id !== $listing->owner_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $saved = [];

        $files = [];
        if ($request->hasFile('images')) {
            $files = $request->file('images');
        } elseif ($request->hasFile('image')) {
            $files = [$request->file('image')];
        }

        foreach ($files as $file) {
            $path = $file->store("listings/{$listing->id}", 'public');
            // Build a stable API URL that the frontend can fetch without CORS issues
            $appUrl = config('app.url') ?: 'http://localhost:8000';
            $url = rtrim($appUrl, '/') . '/api/storage/' . ltrim($path, '/');
            $img = ListingImage::create([
                'listing_id' => $listing->id,
                'image_url' => $url,
            ]);
            $saved[] = $img;
        }

        return response()->json(['images' => $saved], 201);
    }

    public function destroy(Listing $listing, ListingImage $image)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'owner' || $user->owner->id !== $listing->owner_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Ensure image belongs to listing
        if ($image->listing_id !== $listing->id) {
            return response()->json(['message' => 'Image does not belong to this listing'], 400);
        }

        // Delete file from storage if possible
        try {
            $path = parse_url($image->image_url, PHP_URL_PATH);
            // remove leading /storage if present
            $path = preg_replace('#^/storage/#', '', $path);
            /** @var FilesystemAdapter $disk */
            $disk = Storage::disk('public');
            $disk->delete($path);
        } catch (\Throwable $e) {
            // continue even if delete fails
        }

        $image->delete();
        return response()->json(null, 204);
    }
}
