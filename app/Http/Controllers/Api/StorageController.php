<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageController extends Controller
{
    /**
     * Serve a storage file and set CORS headers so web clients on other ports can fetch it.
     */
    public function show(Request $request, string $path)
    {
        // Always respond to OPTIONS preflight with CORS headers
        if ($request->getMethod() === 'OPTIONS') {
            $origin = $request->headers->get('Origin') ?: '*';
            return response('', 204)
                ->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '1728000');
        }

        // Prevent directory traversal
        $path = ltrim($path, '/');

        $origin = $request->headers->get('Origin') ?: '*';

        if (! Storage::disk('public')->exists($path)) {
            return response()->json(['message' => 'Not found'], 404)
                ->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '1728000');
        }

        $fullPath = Storage::disk('public')->path($path);

        /** @var BinaryFileResponse $response */
        $response = response()->file($fullPath);

        // Add CORS headers so the browser can load the image from a different origin/port
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Max-Age', '1728000');
        $response->headers->set('Vary', 'Origin');

        return $response;
    }
}
