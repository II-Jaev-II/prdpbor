<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\GeotagPhoto;
use Illuminate\Support\Facades\Storage;

// Find all photos with spaces in their paths
$photos = GeotagPhoto::where('photo_path', 'LIKE', '% %')->get();

echo "Found {$photos->count()} photos with spaces in paths\n\n";

foreach ($photos as $photo) {
    $oldPath = $photo->photo_path;

    // Skip if file doesn't exist
    if (!Storage::disk('public')->exists($oldPath)) {
        echo "⚠️  File not found: {$oldPath}\n";
        continue;
    }

    // Generate new path by replacing spaces and special characters
    $pathInfo = pathinfo($oldPath);
    $directory = $pathInfo['dirname'];
    $filename = $pathInfo['filename'];
    $extension = $pathInfo['extension'];

    // Sanitize filename
    $newFilename = preg_replace('/[^A-Za-z0-9_-]/', '_', $filename);
    $newPath = $directory . '/' . $newFilename . '.' . $extension;

    // Check if new path already exists
    $counter = 1;
    while (Storage::disk('public')->exists($newPath) && $newPath !== $oldPath) {
        $newPath = $directory . '/' . $newFilename . '_' . $counter . '.' . $extension;
        $counter++;
    }

    // Move the file
    try {
        if ($oldPath !== $newPath) {
            Storage::disk('public')->move($oldPath, $newPath);
            $photo->photo_path = $newPath;
            $photo->save();
            echo "✓ Renamed: {$oldPath} → {$newPath}\n";
        }
    } catch (\Exception $e) {
        echo "✗ Error renaming {$oldPath}: {$e->getMessage()}\n";
    }
}

echo "\nDone!\n";
