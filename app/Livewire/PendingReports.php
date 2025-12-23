<?php

namespace App\Livewire;

use App\Models\BackToOfficeReport;
use App\Models\GeotagPhoto;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PendingReports extends Component
{
    use WithFileUploads;

    public bool $showEditModal = false;
    public ?int $editingId = null;
    public array $editForm = [
        'travel_order_id' => '',
        'activity_name' => '',
        'date_of_travel' => '',
        'start_date' => '',
        'end_date' => '',
        'purpose' => '',
        'purpose_type' => '',
        'subproject_name' => '',
        'place' => '',
        'accomplishment' => '',
        'monitoring_report' => null,
    ];
    public $newPhotos = [];
    public $newMonitoringReport = null;
    public array $existingPhotos = [];
    public array $photosToDelete = [];
    public ?string $monitoringReportToDelete = null;

    protected $listeners = ['editReport', 'deleteReport'];

    public function editReport($rowId): void
    {
        $report = BackToOfficeReport::with('enrollActivity')->find($rowId);

        if ($report) {
            $this->editingId = $report->id;
            
            // Format date range for display
            $dateRange = '';
            if ($report->start_date && $report->end_date) {
                if ($report->start_date->eq($report->end_date)) {
                    $dateRange = $report->start_date->format('Y-m-d');
                } else {
                    $dateRange = $report->start_date->format('Y-m-d') . ' to ' . $report->end_date->format('Y-m-d');
                }
            }

            // Get activity details from enrolled activity
            $activityName = '';
            $purposeType = '';
            $subprojectName = '';
            if ($report->enrollActivity) {
                $activityName = $report->enrollActivity->activity_name ?? '';
                $purposeType = $report->enrollActivity->purpose_type ?? '';
                $subprojectName = $report->enrollActivity->subproject_name ?? '';
            }
            
            $this->editForm = [
                'travel_order_id' => $report->travel_order_id ?? '',
                'activity_name' => $activityName,
                'date_of_travel' => $dateRange,
                'start_date' => $report->start_date?->format('Y-m-d') ?? '',
                'end_date' => $report->end_date?->format('Y-m-d') ?? '',
                'purpose' => $report->purpose ?? '',
                'purpose_type' => $purposeType,
                'subproject_name' => $subprojectName,
                'place' => $report->place ?? '',
                'accomplishment' => $report->accomplishment ?? '',
                'monitoring_report' => $report->monitoring_report ?? null,
            ];
            $this->existingPhotos = $report->photos ?? [];
            $this->newPhotos = [];
            $this->newMonitoringReport = null;
            $this->photosToDelete = [];
            $this->monitoringReportToDelete = null;
            $this->showEditModal = true;
        }
    }

    public function removeExistingPhoto($index): void
    {
        if (isset($this->existingPhotos[$index])) {
            $this->photosToDelete[] = $this->existingPhotos[$index];
            unset($this->existingPhotos[$index]);
            $this->existingPhotos = array_values($this->existingPhotos);
        }
    }

    public function removeMonitoringReport(): void
    {
        if (!empty($this->editForm['monitoring_report'])) {
            $this->monitoringReportToDelete = $this->editForm['monitoring_report'];
            $this->editForm['monitoring_report'] = null;
        }
    }

    /**
     * Validate that all new photos contain GPS metadata
     */
    protected function validateGpsMetadata()
    {
        if (!empty($this->newPhotos)) {
            foreach ($this->newPhotos as $photoIndex => $photo) {
                $path = $photo->getRealPath();
                
                // Read EXIF data
                $exif = @exif_read_data($path);
                
                // Check if GPS data exists
                if (!$exif || !isset($exif['GPSLatitude']) || !isset($exif['GPSLongitude'])) {
                    $this->addError(
                        "newPhotos.{$photoIndex}",
                        "Photo '{$photo->getClientOriginalName()}' does not contain GPS coordinates. Please upload photos taken with location services enabled."
                    );
                }
            }
        }
        
        // Return true if no GPS errors were added
        return empty($this->getErrorBag()->get('newPhotos.*'));
    }

    public function updateReport(): void
    {
        $this->validate([
            'editForm.place' => 'nullable|string|max:255',
            'editForm.accomplishment' => 'nullable|string',
            'newPhotos' => 'nullable|array',
            'newPhotos.*' => 'image|max:10240',
            'newMonitoringReport' => 'nullable|file|mimes:pdf|max:20480',
        ], [
            'editForm.place.max' => 'Place must not exceed 255 characters',
            'newPhotos.*.image' => 'Each file must be an image',
            'newPhotos.*.max' => 'Each image must not exceed 10MB',
            'newMonitoringReport.file' => 'The monitoring report must be a file',
            'newMonitoringReport.mimes' => 'The monitoring report must be a PDF file',
            'newMonitoringReport.max' => 'The monitoring report must not exceed 20MB',
        ]);

        // Validate GPS metadata in new photos
        if (!$this->validateGpsMetadata()) {
            return; // Stop submission if GPS validation fails
        }

        $report = BackToOfficeReport::find($this->editingId);

        if ($report) {
            // Delete photos marked for deletion
            foreach ($this->photosToDelete as $photoPath) {
                Storage::disk('public')->delete($photoPath);
                
                // Also remove from geotag_photos table
                GeotagPhoto::where('user_id', Auth::id())
                    ->where('photo_path', $photoPath)
                    ->delete();
            }

            // Delete monitoring report if marked for deletion
            if ($this->monitoringReportToDelete) {
                Storage::disk('public')->delete($this->monitoringReportToDelete);
            }

            // Upload new photos with compression
            $photoPaths = $this->existingPhotos;
            if (!empty($this->newPhotos)) {
                foreach ($this->newPhotos as $photo) {
                    // Get original filename and extension
                    $originalName = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $photo->getClientOriginalExtension();

                    // Generate unique filename with original name
                    $filename = $this->generateUniqueFilename($originalName, $extension, 'reports/photos');
                    $path = 'reports/photos/' . $filename;
                    
                    // Compress and store the photo
                    $compressedImage = $this->compressImage($photo);
                    Storage::disk('public')->put($path, $compressedImage);
                    
                    $photoPaths[] = $path;
                    
                    // Store in geotag_photos table
                    GeotagPhoto::create([
                        'user_id' => Auth::id(),
                        'travel_order_id' => $this->editForm['travel_order_id'],
                        'photo_path' => $path,
                    ]);
                }
            }

            // Upload new monitoring report if provided
            $monitoringReportPath = $this->editForm['monitoring_report'];
            if (!empty($this->newMonitoringReport)) {
                $originalName = $this->newMonitoringReport->getClientOriginalName();
                $filename = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $this->newMonitoringReport->getClientOriginalExtension();

                // Check if file exists and add underscore with counter
                $counter = 1;
                $newFileName = $originalName;
                while (Storage::disk('public')->exists('reports/monitoring/' . $newFileName)) {
                    $newFileName = $filename . '_' . $counter . '.' . $extension;
                    $counter++;
                }

                $monitoringReportPath = $this->newMonitoringReport->storeAs('reports/monitoring', $newFileName, 'public');
            }

            $report->update([
                'place' => $this->editForm['place'],
                'accomplishment' => $this->editForm['accomplishment'],
                'photos' => $photoPaths,
                'monitoring_report' => $monitoringReportPath,
            ]);

            $this->closeModal();
            $this->dispatch('pg:eventRefresh-pendingTable');

            session()->flash('success', 'Report updated successfully!');
        }
    }

    public function deleteReport($rowId): void
    {
        $report = BackToOfficeReport::find($rowId);

        if ($report && $report->user_id === Auth::id() && $report->status === 'Pending') {
            // Delete photos from storage
            if (!empty($report->photos)) {
                foreach ($report->photos as $photoPath) {
                    Storage::disk('public')->delete($photoPath);
                }
            }

            // Delete monitoring report from storage
            if (!empty($report->monitoring_report)) {
                Storage::disk('public')->delete($report->monitoring_report);
            }

            // Delete the report
            $report->delete();

            $this->dispatch('pg:eventRefresh-pendingTable');
            session()->flash('success', 'Report deleted successfully!');
        }
    }

    public function closeModal(): void
    {
        $this->showEditModal = false;
        $this->editingId = null;
        $this->editForm = [
            'travel_order_id' => '',
            'activity_name' => '',
            'date_of_travel' => '',
            'start_date' => '',
            'end_date' => '',
            'purpose' => '',
            'purpose_type' => '',
            'subproject_name' => '',
            'place' => '',
            'accomplishment' => '',
            'monitoring_report' => null,
        ];
        $this->newPhotos = [];
        $this->newMonitoringReport = null;
        $this->existingPhotos = [];
        $this->photosToDelete = [];
        $this->monitoringReportToDelete = null;
        $this->resetErrorBag();
    }

    /**
     * Generate a unique filename, adding _1, _2, etc. if duplicates exist
     */
    private function generateUniqueFilename($originalName, $extension, $directory)
    {
        $filename = $originalName . '.' . $extension;
        $counter = 1;
        
        // Check if file exists and increment counter if needed
        while (Storage::disk('public')->exists($directory . '/' . $filename)) {
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

    public function render()
    {
        return view('livewire.pending-reports');
    }
}
