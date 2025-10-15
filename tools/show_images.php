<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ListingImage;

foreach (ListingImage::limit(5)->get() as $i) {
    echo $i->image_url . PHP_EOL;
}
