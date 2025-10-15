<?php
require __DIR__ . '/../vendor/autoload.php';
// Bootstrap the framework
\$app = require_once __DIR__ . '/../bootstrap/app.php';
\Illuminate\Foundation\Console\Kernel::class;

use App\Models\User;

\$users = User::select('id','email','role')->get();
foreach (\$users as \$u) {
    echo \"ID: \" . \$u->id . \" email: \" . (\$u->email ?? 'n/a') . \" role: \" . (\$u->role ?? 'n/a') . "\n";
}
