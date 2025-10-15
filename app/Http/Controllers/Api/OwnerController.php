<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Debt;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OwnerController extends Controller
{
    public function markBookingAsPaid(Request $request, Booking $booking)
    {
        if (Auth::user()->id !== $booking->listing->owner->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        if ($booking->is_completed) {
            return response()->json(['message' => 'Booking is already marked as paid'], 400);
        }
    
        $request->validate([
            'payment_method' => 'required|in:cash,online',
        ]);
    
        $booking->update([
            'is_completed' => true,
            'payment_method' => $request->payment_method,
        ]);
    
        if ($request->payment_method === 'cash') {
            DB::transaction(function () use ($booking) {
                $commissionAmount = $booking->total_rent * 0.10;
                $owner = Auth::user()->owner;
    
                Debt::create([
                    'owner_id' => $owner->id,
                    'booking_id' => $booking->id,
                    'amount' => $commissionAmount,
                ]);
    
                $owner->debt_count += 1;
                $owner->save();
    
                if ($owner->debt_count >= 5) {
                    $owner->user->is_blocked = true;
                    $owner->user->save();
                }
            });
    
            return response()->json(['message' => 'Booking marked as paid in cash. Commission debt added.']);
        }
    
        if ($request->payment_method === 'online') {
            // Placeholder for Payment Gateway logic
            $commissionAmount = $booking->total_rent * 0.10;
            $ownerShare = $booking->total_rent - $commissionAmount;
    
            Transaction::create([
                'user_id' => Auth::id(),
                'amount' => $ownerShare,
                'type' => 'deposit_to_owner',
                'status' => 'completed',
            ]);
    
            Transaction::create([
                'user_id' => Auth::id(),
                'amount' => $commissionAmount,
                'type' => 'commission_payment',
                'status' => 'completed',
            ]);
    
            $booking->commission_paid = true;
    
            return response()->json(['message' => 'Booking paid via app. Funds transferred.']);
        }
    }
    
    public function getDebtStatus()
    {
        $owner = Auth::user()->owner;
        return response()->json([
            'debt_count' => $owner->debt_count,
            'unpaid_debts' => Debt::where('owner_id', $owner->id)->where('is_paid', false)->get()
        ]);
    }
    
    /**
     * Return paginated listings belonging to the authenticated owner.
     */
    public function myListings(Request $request)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'owner' || ! $user->owner) {
            return response()->json(['message' => 'Only owners can view their listings.'], 403);
        }

        $perPage = (int) $request->query('per_page', 10);
        $listings = \App\Models\Listing::where('owner_id', $user->owner->id)->paginate($perPage);
        return response()->json($listings);
    }

    // ... (other owner-specific methods)
}
