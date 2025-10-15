<?php
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap the framework like other tools in this repo
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Listing;
use App\Models\Booking;

// Find a customer user
$user = User::where('role', 'customer')->first();
if (! $user) {
    echo "No customer user found. Create a user with role=customer first.\n";
    exit(1);
}

// Pick a listing that is not owned by this user (if possible)
$listing = Listing::whereHas('owner', function ($q) use ($user) {
    $q->where('user_id', '!=', $user->id);
})->first();

// Fallback: any listing
if (! $listing) {
    $listing = Listing::first();
}

if (! $listing) {
    echo "No listing found in DB.\n";
    exit(1);
}

echo "Creating booking for user={$user->id} listing={$listing->id}\n";

$start = new DateTimeImmutable('tomorrow');
$end = $start->modify('+2 days');
$nights = (int) $start->diff($end)->format('%a');
$total = $nights * (float) $listing->price;

try {
    $booking = Booking::create([
        'listing_id' => $listing->id,
        'customer_id' => $user->id,
        'start_date' => $start->format('Y-m-d'),
        'end_date' => $end->format('Y-m-d'),
        'total_rent' => $total,
        'payment_method' => 'cash',
        'commission_paid' => false,
        'is_completed' => false,
    ]);

    echo "Booking created: id={$booking->id} start={$booking->start_date} end={$booking->end_date} total={$booking->total_rent}\n";
} catch (Throwable $e) {
    echo "Booking creation failed: " . $e->getMessage() . "\n";
    exit(1);
}

