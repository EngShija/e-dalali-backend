<?php
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap the framework
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ListingImage;

$appUrl = config('app.url') ?: 'http://localhost:8000';
$count = 0;
$images = ListingImage::all();
foreach ($images as $img) {
    $url = $img->image_url;
    // If it's already an api/storage URL, skip
    if (strpos($url, '/api/storage/') !== false || strpos($url, 'http') === 0) {
        // But if it is http and not api/storage, convert if it points to /storage/
        if (strpos($url, $appUrl . '/storage/') === 0) {
            $path = substr($url, strlen($appUrl . '/storage/'));
            $new = rtrim($appUrl, '/') . '/api/storage/' . ltrim($path, '/');
            $img->image_url = $new;
            $img->save();
            $count++;
        }
        continue;
    }

    // If it's a storage path like /storage/..., convert
    if (strpos($url, '/storage/') === 0) {
        $path = preg_replace('#^/storage/#', '', $url);
        $new = rtrim($appUrl, '/') . '/api/storage/' . ltrim($path, '/');
        $img->image_url = $new;
        $img->save();
        $count++;
    }
}

echo "Updated $count listing image(s)\n";
