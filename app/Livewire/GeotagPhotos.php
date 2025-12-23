<?php

namespace App\Livewire;

use App\Models\EnrollActivity;
use App\Models\GeotagPhoto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class GeotagPhotos extends Component
{
    use WithFileUploads, WithPagination;

    public $showModal = false;
    public $travel_order_id = '';
    public $photos = [];
    public $search = '';

    protected $rules = [
        'travel_order_id' => 'required|string',
        'photos.*' => 'required|image|max:10240', // 10MB Max
    ];

    protected $messages = [
        'travel_order_id.required' => 'The Travel Order ID field is required.',
        'photos.*.required' => 'Please select at least one photo.',
        'photos.*.image' => 'The file must be an image.',
        'photos.*.max' => 'Each photo must not exceed 10MB.',
    ];

    public function openUploadModal()
    {
        $this->showModal = true;
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['travel_order_id', 'photos']);
        $this->resetValidation();
    }

    /**
     * Validate that all photos contain GPS metadata
     */
    protected function validateGpsMetadata()
    {
        if (!empty($this->photos)) {
            foreach ($this->photos as $photoIndex => $photo) {
                $path = $photo->getRealPath();
                
                // Read EXIF data
                $exif = @exif_read_data($path);
                
                // Check if GPS data exists
                if (!$exif || !isset($exif['GPSLatitude']) || !isset($exif['GPSLongitude'])) {
                    $this->addError(
                        "photos.{$photoIndex}",
                        "Photo '{$photo->getClientOriginalName()}' does not contain GPS coordinates. Please upload photos taken with location services enabled."
                    );
                }
            }
        }
        
        // Return true if no GPS errors were added
        return empty($this->getErrorBag()->get('photos.*'));
    }

    public function uploadPhotos()
    {
        $this->validate();

        // Validate GPS metadata in photos
        if (!$this->validateGpsMetadata()) {
            return; // Stop submission if GPS validation fails
        }

        // Check if Travel Order ID exists in enrolled_activities table
        $enrolledActivity = EnrollActivity::where('to_num', $this->travel_order_id)->first();

        if (!$enrolledActivity) {
            $this->addError('travel_order_id', 'The Travel Order ID does not exist in the system.');
            return;
        }

        try {
            foreach ($this->photos as $photo) {
                // Get original filename and extension
                $originalName = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $photo->getClientOriginalExtension();
                
                // Generate unique filename with original name
                $filename = $this->generateUniqueFilename($originalName, $extension);
                $path = 'geotag-photos/' . $filename;
                
                // Compress and store the photo
                $compressedImage = $this->compressImage($photo);
                Storage::disk('public')->put($path, $compressedImage);

                // Create record in database
                GeotagPhoto::create([
                    'user_id' => Auth::id(),
                    'travel_order_id' => $this->travel_order_id,
                    'photo_path' => $path,
                ]);
            }

            session()->flash('success', 'Photos uploaded successfully!');
            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to upload photos: ' . $e->getMessage());
        }
    }

    /**
     * Generate a unique filename, adding _1, _2, etc. if duplicates exist
     */
    private function generateUniqueFilename($originalName, $extension)
    {
        $filename = $originalName . '.' . $extension;
        $counter = 1;
        
        // Check if file exists and increment counter if needed
        while (Storage::disk('public')->exists('geotag-photos/' . $filename)) {
            $filename = $originalName . '_' . $counter . '.' . $extension;
            $counter++;
        }
        
        return $filename;
    }

    /**
     * Compress image while preserving EXIF data
     */
    private function compressImage($photo)
    {
        $originalSize = $photo->getSize(); // Size in bytes
        $targetSize = 600 * 1024; // 600 KB in bytes
        
        // If file is already small enough, return as-is
        if ($originalSize <= $targetSize) {
            return file_get_contents($photo->getRealPath());
        }
        
        // Read and process image with Intervention Image
        $manager = new ImageManager(new Driver());
        $image = $manager->read($photo->getRealPath());
        
        $extension = strtolower($photo->getClientOriginalExtension());
        
        // Calculate compression ratio needed
        $ratio = $targetSize / $originalSize;
        
        // Estimate quality needed (more aggressive for larger files)
        if ($ratio > 0.7) {
            $quality = 85; // Light compression needed
        } elseif ($ratio > 0.5) {
            $quality = 75; // Medium compression
        } elseif ($ratio > 0.3) {
            $quality = 65; // Heavy compression
        } else {
            $quality = 55; // Very heavy compression
        }
        
        // For very large files (> 3x target), resize first
        if ($originalSize > ($targetSize * 3)) {
            $scaleFactor = sqrt($ratio); // Scale to roughly target size
            $newWidth = (int)($image->width() * $scaleFactor);
            $newHeight = (int)($image->height() * $scaleFactor);
            $image->scale(width: $newWidth, height: $newHeight);
        }
        
        // Compress with calculated quality
        if (in_array($extension, ['jpg', 'jpeg'])) {
            $compressed = $image->toJpeg(quality: $quality)->toString();
        } else {
            // Convert other formats to JPEG
            $compressed = $image->toJpeg(quality: $quality)->toString();
        }
        
        // If still too large, do one more pass with lower quality
        if (strlen($compressed) > $targetSize) {
            $compressed = $image->toJpeg(quality: 50)->toString();
        }
        
        return $compressed;
    }

    public function deletePhoto($photoId)
    {
        $photo = GeotagPhoto::find($photoId);

        if ($photo && $photo->user_id == Auth::id()) {
            $photoPath = $photo->photo_path;
            
            // Delete file from storage
            Storage::disk('public')->delete($photoPath);

            // Delete database record
            $photo->delete();

            // Remove photo reference from back_to_office_reports
            $reports = \App\Models\BackToOfficeReport::where('user_id', Auth::id())
                ->whereJsonContains('photos', $photoPath)
                ->get();

            foreach ($reports as $report) {
                $photos = $report->photos;
                if (is_array($photos)) {
                    // Remove the deleted photo path from the array
                    $photos = array_values(array_filter($photos, function($path) use ($photoPath) {
                        return $path !== $photoPath;
                    }));
                    $report->photos = $photos;
                    $report->save();
                }
            }

            session()->flash('success', 'Photo deleted successfully!');
        } else {
            session()->flash('error', 'Photo not found or you do not have permission to delete it.');
        }
    }

    public function downloadPhoto($photoId)
    {
        $photo = GeotagPhoto::find($photoId);

        if ($photo) {
            $filePath = storage_path('app/public/' . $photo->photo_path);

            if (file_exists($filePath)) {
                return response()->download($filePath);
            }
        }

        session()->flash('error', 'Photo not found.');
    }

    public function render()
    {
        // Apply search filter to find matching travel_order_ids
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';

            // Find travel_order_ids that match the search criteria
            $matchingTravelOrderIds = GeotagPhoto::with(['enrolledActivity', 'user'])
                ->where(function ($q) use ($searchTerm) {
                    $q->where('travel_order_id', 'like', $searchTerm)
                        ->orWhereHas('enrolledActivity', function ($q) use ($searchTerm) {
                            $q->where('activity_name', 'like', $searchTerm);
                        })
                        ->orWhereHas('user', function ($q) use ($searchTerm) {
                            $q->where('name', 'like', $searchTerm);
                        });
                })
                ->pluck('travel_order_id')
                ->unique();

            // Get ALL photos for those matching travel_order_ids
            $allPhotos = GeotagPhoto::with(['enrolledActivity', 'user'])
                ->whereIn('travel_order_id', $matchingTravelOrderIds)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // No search, get all photos
            $allPhotos = GeotagPhoto::with(['enrolledActivity', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Group photos by travel_order_id
        $groupedPhotos = $allPhotos->groupBy('travel_order_id')->map(function ($photos, $travelOrderId) {
            return [
                'travel_order_id' => $travelOrderId,
                'photos' => $photos,
                'enrolled_activity' => $photos->first()->enrolledActivity,
                'photo_count' => $photos->count(),
                'latest_upload' => $photos->first()->created_at,
            ];
        })->sortByDesc('latest_upload');

        return view('livewire.geotag-photos', [
            'groupedPhotos' => $groupedPhotos,
        ]);
    }
}
