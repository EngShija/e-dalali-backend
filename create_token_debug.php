<?php
require __DIR__ . '/vendor/autoload.php';
// Boot the application to use Eloquent
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use App\Models\User;

$user = User::find(11);
if (! $user) {
    echo "USER_NOT_FOUND";
    exit(1);
}

echo $user->createToken('debug-token')->plainTextToken;

$kernel->terminate($request, $response);
