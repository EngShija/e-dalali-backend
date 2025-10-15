<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Store a new booking for an authenticated customer.
     *
     * Expected payload: listing_id, start_date (Y-m-d), end_date (Y-m-d)
     */
    public function store(StoreBookingRequest $request)
    {
        $user = Auth::user();

        // Listing must exist
        $listing = Listing::find($request->listing_id);
        if (! $listing) {
            return response()->json(['message' => 'Listing not found'], 404);
        }

        // Prevent owner booking their own listing
        if ($listing->owner && $listing->owner->user_id === $user->id) {
            return response()->json(['message' => 'Owners cannot book their own listing'], 403);
        }

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->startOfDay();

        if ($end->lt($start)) {
            return response()->json(['message' => 'end_date must be after start_date'], 422);
        }

        // Check for overlapping bookings on this listing
        $overlap = Booking::where('listing_id', $listing->id)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start->toDateString(), $end->toDateString()])
                  ->orWhereBetween('end_date', [$start->toDateString(), $end->toDateString()])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('start_date', '<=', $start->toDateString())
                         ->where('end_date', '>=', $end->toDateString());
                  });
            })->exists();

        if ($overlap) {
            return response()->json(['message' => 'Listing is already booked for the selected dates'], 409);
        }

        // Calculate nights and total rent
        $nights = $start->diffInDays($end);
        if ($nights <= 0) {
            return response()->json(['message' => 'Booking must be at least one night'], 422);
        }

        $total = $nights * (float) $listing->price;

        $booking = null;
        DB::transaction(function () use ($user, $listing, $request, $start, $end, $total, &$booking) {
            $booking = Booking::create([
                'listing_id' => $listing->id,
                'customer_id' => $user->id,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'total_rent' => $total,
                'payment_method' => 'cash', // default until payment occurs
                'commission_paid' => false,
                'is_completed' => false,
            ]);
        });

        return response()->json(['message' => 'Booking created', 'booking' => $booking], 201);
    }
}

