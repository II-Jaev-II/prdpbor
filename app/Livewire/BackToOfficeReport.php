<?php

namespace App\Livewire;

use App\Models\BackToOfficeReport as BackToOfficeReportModel;
use App\Models\EnrollActivity;
use App\Models\GeotagPhoto;
use App\Models\SubprojectList;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Livewire\Component;
use Livewire\WithFileUploads;

class BackToOfficeReport extends Component
{
    use WithFileUploads;

    public $reports = [];
    public $tracking_code = '';
    public $loadAttempted = false;
    public $userActivities = [];
    public $existingPhotos;
    public $photoSelectionMode = []; // 'upload' or 'select' per report index
    public $photoSearchTerm = [];
    public $photoFilterUser = [];
    public $photosPerPage = 12;
    public $currentPhotoPage = [];

    public function mount()
    {
        // Initialize with one report form
        $this->existingPhotos = collect([]);
        $this->loadUserActivities();
        $this->addReport();
    }

    public function loadUserActivities()
    {
        // Get current user's name
        $userName = Auth::user()->name;
        
        // Find all enrolled activities where user's name appears
        $this->userActivities = EnrollActivity::whereRaw(
            'LOWER(employee_name) LIKE ?', 
            ['%"' . strtolower($userName) . '"%']
        )
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($activity) {
            return [
                'id' => $activity->id,
                'to_num' => $activity->to_num,
                'activity_name' => $activity->activity_name,
                'start_date' => $activity->start_date,
                'end_date' => $activity->end_date,
                'employee_names' => $activity->employee_name ?? [],
            ];
        })
        ->toArray();
    }

    public function loadActivities()
    {
        $this->loadAttempted = true;

        if (empty($this->tracking_code)) {
            $this->reports = [];
            $this->addReport();
            return;
        }

        // Fetch enrolled activities by tracking code
        $activities = EnrollActivity::where('to_num', $this->tracking_code)->get();

        if ($activities->isEmpty()) {
            $this->reports = [];
            return;
        }

        // Load existing geotagged photos for this tracking code
        $this->existingPhotos = GeotagPhoto::where('travel_order_id', $this->tracking_code)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Initialize photo library state for each report
        $this->photoSearchTerm = [];
        $this->photoFilterUser = [];
        $this->currentPhotoPage = [];

        // Generate reports based on enrolled activities
        $this->reports = [];
        foreach ($activities as $activity) {
            // Format date range
            if ($activity->start_date === $activity->end_date) {
                $dateOfTravel = Carbon::parse($activity->start_date)->format('F j, Y');
            } else {
                $dateOfTravel = Carbon::parse($activity->start_date)->format('F j, Y') . ' to ' . Carbon::parse($activity->end_date)->format('F j, Y');
            }

            // Get subproject name if subproject_id exists
            $subprojectName = '';
            if ($activity->subproject_id) {
                $subproject = SubprojectList::find($activity->subproject_id);
                $subprojectName = $subproject ? $subproject->subproject_name : '';
            } elseif ($activity->subproject_name) {
                $subprojectName = $activity->subproject_name;
            }

            // Get travel dates array
            $travelDates = $this->getTravelDates($dateOfTravel);
            
            // Initialize date-keyed photo structure
            $geotaggedPhotos = [];
            $selectedPhotoIds = [];
            foreach ($travelDates as $date) {
                $geotaggedPhotos[$date] = null;
                $selectedPhotoIds[$date] = [];
            }
            
            $this->reports[] = [
                'enrolled_activity_id' => $activity->id,
                'tracking_code' => $activity->to_num,
                'employee_names' => $activity->employee_name ?? [],
                'activity_name' => $activity->activity_name,
                'date_of_travel' => $dateOfTravel,
                'travel_dates' => $travelDates,
                'purpose' => $activity->purpose,
                'purpose_type' => $activity->purpose_type,
                'subproject_name' => $subprojectName,
                'place' => '',
                'accomplishment' => '',
                'geotagged_photos' => $geotaggedPhotos,
                'selected_photo_ids' => $selectedPhotoIds,
                'monitoring_report' => null,
            ];
            $this->photoSelectionMode[] = 'upload';
            $this->photoSearchTerm[] = '';
            $this->photoFilterUser[] = '';
            $this->currentPhotoPage[] = 1;
        }
    }

    public function addReport()
    {
        $this->reports[] = [
            'activity_name' => '',
            'date_of_travel' => '',
            'travel_dates' => [],
            'purpose' => '',
            'purpose_type' => '',
            'subproject_name' => '',
            'place' => '',
            'accomplishment' => '',
            'geotagged_photos' => [],
            'selected_photo_ids' => [],
            'monitoring_report' => null,
        ];
        $this->photoSelectionMode[] = 'upload';
        $this->photoSearchTerm[] = '';
        $this->photoFilterUser[] = '';
        $this->currentPhotoPage[] = 1;
    }

    public function removeReport($index)
    {
        unset($this->reports[$index]);
        unset($this->photoSelectionMode[$index]);
        unset($this->photoSearchTerm[$index]);
        unset($this->photoFilterUser[$index]);
        unset($this->currentPhotoPage[$index]);
        $this->reports = array_values($this->reports); // Re-index array
        $this->photoSelectionMode = array_values($this->photoSelectionMode);
        $this->photoSearchTerm = array_values($this->photoSearchTerm);
        $this->photoFilterUser = array_values($this->photoFilterUser);
        $this->currentPhotoPage = array_values($this->currentPhotoPage);
    }

    public function togglePhotoMode($index, $mode)
    {
        $this->photoSelectionMode[$index] = $mode;
        // Reset to first page when switching modes
        $this->currentPhotoPage[$index] = 1;
    }

    /**
     * Parse date_of_travel string and return array of dates
     */
    public function getTravelDates($dateOfTravel)
    {
        $dates = [];
        
        if (strpos($dateOfTravel, ' to ') !== false) {
            // Date range
            $parts = explode(' to ', $dateOfTravel);
            $startDate = Carbon::parse($parts[0]);
            $endDate = Carbon::parse($parts[1]);
            
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $dates[] = $currentDate->format('Y-m-d');
                $currentDate->addDay();
            }
        } else {
            // Single date
            $dates[] = Carbon::parse($dateOfTravel)->format('Y-m-d');
        }
        
        return $dates;
    }

    public function togglePhotoSelection($reportIndex, $photoId, $date = null)
    {
        if ($date) {
            // Date-specific selection
            if (!isset($this->reports[$reportIndex]['selected_photo_ids'])) {
                $this->reports[$reportIndex]['selected_photo_ids'] = [];
            }
            if (!isset($this->reports[$reportIndex]['selected_photo_ids'][$date])) {
                $this->reports[$reportIndex]['selected_photo_ids'][$date] = [];
            }

            $key = array_search($photoId, $this->reports[$reportIndex]['selected_photo_ids'][$date]);
            if ($key !== false) {
                // Deselect
                unset($this->reports[$reportIndex]['selected_photo_ids'][$date][$key]);
                $this->reports[$reportIndex]['selected_photo_ids'][$date] = array_values($this->reports[$reportIndex]['selected_photo_ids'][$date]);
            } else {
                // Select - replace any existing selection for this date
                $this->reports[$reportIndex]['selected_photo_ids'][$date] = [$photoId];
            }
        }
    }

    public function changePhotoPage($index, $page)
    {
        $this->currentPhotoPage[$index] = $page;
    }

    public function updatedPhotoSearchTerm($value, $index)
    {
        // Reset to first page when searching
        $this->currentPhotoPage[$index] = 1;
    }

    public function updatedPhotoFilterUser($value, $index)
    {
        // Reset to first page when filtering
        $this->currentPhotoPage[$index] = 1;
    }

    public function getFilteredPhotos($reportIndex)
    {
        $photos = $this->existingPhotos ?? collect([]);

        // Apply search filter
        if (!empty($this->photoSearchTerm[$reportIndex])) {
            $searchTerm = strtolower($this->photoSearchTerm[$reportIndex]);
            $photos = $photos->filter(function ($photo) use ($searchTerm) {
                return str_contains(strtolower($photo->user->name ?? ''), $searchTerm) ||
                    str_contains(strtolower($photo->created_at->format('M d, Y')), $searchTerm);
            });
        }

        // Apply user filter
        if (!empty($this->photoFilterUser[$reportIndex])) {
            $photos = $photos->filter(function ($photo) use ($reportIndex) {
                return $photo->user_id == $this->photoFilterUser[$reportIndex];
            });
        }

        return $photos;
    }

    public function getPaginatedPhotos($reportIndex)
    {
        $filteredPhotos = $this->getFilteredPhotos($reportIndex);
        $currentPage = $this->currentPhotoPage[$reportIndex] ?? 1;
        $perPage = $this->photosPerPage;

        $offset = ($currentPage - 1) * $perPage;
        return $filteredPhotos->slice($offset, $perPage);
    }

    public function getTotalPhotoPages($reportIndex)
    {
        $filteredPhotos = $this->getFilteredPhotos($reportIndex);
        return (int) ceil($filteredPhotos->count() / $this->photosPerPage);
    }

    public function getUniqueUsers()
    {
        return ($this->existingPhotos ?? collect([]))->pluck('user')->unique('id')->sortBy('name');
    }

    protected function rules()
    {
        $rules = [];
        foreach ($this->reports as $index => $report) {
            $rules["reports.{$index}.activity_name"] = 'required|string|max:255';
            $rules["reports.{$index}.date_of_travel"] = 'required|string';
            $rules["reports.{$index}.purpose"] = 'required|string';
            $rules["reports.{$index}.purpose_type"] = 'required|string';

            // Subproject name is required if purpose is Site Specific
            if (isset($report['purpose']) && $report['purpose'] === 'Site Specific') {
                $rules["reports.{$index}.subproject_name"] = 'required|string|max:255';
                // Monitoring report is optional for Site Specific
                $rules["reports.{$index}.monitoring_report"] = 'nullable|file|mimes:pdf|max:20480';
            }

            $rules["reports.{$index}.place"] = 'nullable|string|max:255';
            $rules["reports.{$index}.accomplishment"] = 'nullable|string';
            $rules["reports.{$index}.geotagged_photos"] = 'nullable|array';
            $rules["reports.{$index}.selected_photo_ids"] = 'nullable|array';
            
            // Validate each date has a photo (either uploaded or selected)
            if (!empty($report['travel_dates'])) {
                foreach ($report['travel_dates'] as $date) {
                    $rules["reports.{$index}.geotagged_photos.{$date}"] = 'nullable|image|max:10240';
                }
            }
        }
        return $rules;
    }

    protected function messages()
    {
        $messages = [];
        foreach ($this->reports as $index => $report) {
            $reportNumber = $index + 1;
            $messages["reports.{$index}.activity_name.required"] = "Please enter an activity name";
            $messages["reports.{$index}.date_of_travel.required"] = "Please select a date of travel";
            $messages["reports.{$index}.purpose.required"] = "Please select a purpose of travel";
            $messages["reports.{$index}.purpose_type.required"] = "Please select a purpose type";
            $messages["reports.{$index}.subproject_name.required"] = "Please enter the subproject name";
            $messages["reports.{$index}.monitoring_report.file"] = "The monitoring report must be a file";
            $messages["reports.{$index}.monitoring_report.mimes"] = "The monitoring report must be a PDF file";
            $messages["reports.{$index}.monitoring_report.max"] = "The monitoring report must not exceed 20MB";
            $messages["reports.{$index}.geotagged_photos.*.required"] = 'Please upload a valid image file';
            $messages["reports.{$index}.geotagged_photos.*.image"] = 'The file must be an image';
            $messages["reports.{$index}.geotagged_photos.*.max"] = 'Each image must not exceed 10MB';
        }
        return $messages;
    }

    /**
     * Validate that all photos contain GPS metadata
     */
    protected function validateGpsMetadata()
    {
        $hasErrors = false;
        
        foreach ($this->reports as $index => $report) {
            if (!empty($report['travel_dates'])) {
                foreach ($report['travel_dates'] as $date) {
                    $photo = null;
                    
                    // Check if there's an uploaded photo for this date
                    if (!empty($report['geotagged_photos'][$date])) {
                        $photo = $report['geotagged_photos'][$date];
                    }
                    
                    // Validate GPS metadata for uploaded photos (if provided)
                    if ($photo && is_object($photo)) {
                        $path = $photo->getRealPath();
                        $exif = @exif_read_data($path);
                        
                        if (!$exif || !isset($exif['GPSLatitude']) || !isset($exif['GPSLongitude'])) {
                            $formattedDate = Carbon::parse($date)->format('F j, Y');
                            $this->addError(
                                "reports.{$index}.geotagged_photos.{$date}",
                                "Photo for {$formattedDate} does not contain GPS coordinates. Please upload photos taken with location services enabled."
                            );
                            $hasErrors = true;
                        }
                    }
                }
            }
        }

        return !$hasErrors;
    }

    public function submit()
    {
        $this->validate();

        // Validate GPS metadata in photos
        if (!$this->validateGpsMetadata()) {
            return; // Stop submission if GPS validation fails
        }

        // Generate a unique report number for this submission
        $reportNum = 'RPT-' . date('Ymd') . '-' . strtoupper(uniqid());

        // Process each report
        foreach ($this->reports as $index => $report) {
            // Save photos
            $photoPaths = [];
            $newlyUploadedPaths = []; // Track newly uploaded photo paths

            // Handle newly uploaded photos (date-keyed)
            if (!empty($report['geotagged_photos'])) {
                foreach ($report['geotagged_photos'] as $date => $photo) {
                    if ($photo && is_object($photo)) {
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
                        $newlyUploadedPaths[] = $path; // Store for later use
                    }
                }
            }

            // Handle selected existing photos (date-keyed)
            if (!empty($report['selected_photo_ids'])) {
                foreach ($report['selected_photo_ids'] as $date => $photoIds) {
                    if (!empty($photoIds)) {
                        $selectedPhotos = GeotagPhoto::whereIn('id', $photoIds)->get();
                        foreach ($selectedPhotos as $selectedPhoto) {
                            $photoPaths[] = $selectedPhoto->photo_path;
                        }
                    }
                }
            }

            // Save monitoring report PDF if provided
            $monitoringReportPath = null;
            if (!empty($report['monitoring_report'])) {
                $originalName = $report['monitoring_report']->getClientOriginalName();
                $filename = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $report['monitoring_report']->getClientOriginalExtension();

                // Check if file exists and add underscore with counter
                $counter = 1;
                $newFileName = $originalName;
                while (file_exists(storage_path('app/public/reports/monitoring/' . $newFileName))) {
                    $newFileName = $filename . '_' . $counter . '.' . $extension;
                    $counter++;
                }

                $monitoringReportPath = $report['monitoring_report']->storeAs('reports/monitoring', $newFileName, 'public');
            }

            // Parse date of travel - convert from formatted string back to Y-m-d
            $dateOfTravel = $report['date_of_travel'];
            if (strpos($dateOfTravel, ' to ') !== false) {
                $dates = explode(' to ', $dateOfTravel);
                $startDate = Carbon::parse(trim($dates[0]))->format('Y-m-d');
                $endDate = Carbon::parse(trim($dates[1]))->format('Y-m-d');
            } else {
                $startDate = Carbon::parse($dateOfTravel)->format('Y-m-d');
                $endDate = $startDate;
            }

            // Find the matching enrolled activity (fallback if enrolled_activity_id not in report)
            $enrolledActivityId = $report['enrolled_activity_id'] ?? null;
            
            if (!$enrolledActivityId) {
                // Fallback: try to find by tracking code and activity name
                $enrolledActivity = EnrollActivity::where('to_num', $this->tracking_code)
                    ->where('activity_name', $report['activity_name'])
                    ->where('purpose', $report['purpose'])
                    ->first();
                $enrolledActivityId = $enrolledActivity ? $enrolledActivity->id : null;
            }

            // Save to database
            BackToOfficeReportModel::create([
                'user_id' => Auth::id(),
                'report_num' => $reportNum,
                'travel_order_id' => $report['tracking_code'] ?? $this->tracking_code,
                'enrolled_activity_id' => $enrolledActivityId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'purpose' => $report['purpose'],
                'place' => $report['place'],
                'accomplishment' => $report['accomplishment'],
                'photos' => $photoPaths,
                'monitoring_report' => $monitoringReportPath,
                'status' => 'Pending',
            ]);

            // Store only newly uploaded photos in geotag_photos table (not selected ones)
            if (!empty($newlyUploadedPaths)) {
                $trackingCodeToUse = $report['tracking_code'] ?? $this->tracking_code;
                foreach ($newlyUploadedPaths as $path) {
                    GeotagPhoto::create([
                        'user_id' => Auth::id(),
                        'travel_order_id' => $trackingCodeToUse,
                        'photo_path' => $path,
                    ]);
                }
            }
        }

        $reportCount = count($this->reports);
        $message = $reportCount === 1
            ? 'Back to Office Report submitted successfully!'
            : "{$reportCount} Back to Office Reports submitted successfully!";

        session()->flash('success', $message);

        // Reset form
        $this->reset();
        $this->addReport();
    }

    public function cancel()
    {
        $this->reset();
        return redirect()->route('dashboard');
    }

    /**
     * Generate a unique filename, adding _1, _2, etc. if duplicates exist
     */
    private function generateUniqueFilename($originalName, $extension, $directory)
    {
        // Sanitize filename: replace spaces and special characters
        $originalName = preg_replace('/[^A-Za-z0-9_-]/', '_', $originalName);
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
        return view('livewire.back-to-office-report');
    }
}
