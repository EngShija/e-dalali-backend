<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $listings = Listing::all();
        return response()->json($listings);
    }

    /**
     * Store a newly created listing.
     */
    public function store(Request $request)
    {
        // Debug: log incoming payload to help diagnose client/server mismatch
        Log::info('ListingController@store request', $request->all());
        // Ensure the authenticated user is an owner and use their owner_id instead
        $user = $request->user();
        if (! $user || $user->role !== 'owner' || ! $user->owner) {
            return response()->json(['message' => 'Only owners can create listings.'], 403);
        }

        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'location_address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'price' => 'required|numeric|min:0',
            'property_type' => ['required', Rule::in(['room', 'house', 'apartment'])],
            'status' => ['nullable', Rule::in(['available', 'rented', 'unavailable'])],
        ]);

        $validatedData['owner_id'] = $user->owner->id;

        $listing = Listing::create($validatedData);
        return response()->json($listing, 201);
    }

    /**
     * Display the specified listing.
     */
    public function show(Listing $listing)
    {
        return response()->json($listing);
    }

    /**
     * Update the specified listing.
     */
    public function update(Request $request, Listing $listing)
    {
        $validatedData = $request->validate([
            'owner_id' => 'sometimes|required|exists:owners,id',
            'title' => 'sometimes|required|max:255',
            'description' => 'sometimes|nullable',
            'location_address' => 'sometimes|nullable|string|max:255',
            'latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'longitude' => 'sometimes|nullable|numeric|between:-180,180',
            'price' => 'sometimes|required|numeric|min:0',
            'property_type' => ['sometimes', 'required', Rule::in(['room', 'house', 'apartment'])],
            'status' => ['sometimes', 'nullable', Rule::in(['available', 'rented', 'unavailable'])],
        ]);

        $listing->update($validatedData);
        return response()->json($listing, 200);
    }

    /**
     * Remove the specified listing.
     */
    public function destroy(Listing $listing)
    {
        $listing->delete();
        return response()->json(null, 204);
    }
}
