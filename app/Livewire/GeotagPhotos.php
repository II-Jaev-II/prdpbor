<?php

namespace App\Livewire;

use App\Models\EnrollActivity;
use App\Models\GeotagPhoto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

    public function uploadPhotos()
    {
        $this->validate();

        // Check if Travel Order ID exists in enrolled_activities table
        $enrolledActivity = EnrollActivity::where('to_num', $this->travel_order_id)->first();

        if (!$enrolledActivity) {
            $this->addError('travel_order_id', 'The Travel Order ID does not exist in the system.');
            return;
        }

        try {
            foreach ($this->photos as $photo) {
                // Store photo in storage
                $path = $photo->store('geotag-photos', 'public');

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

    public function deletePhoto($photoId)
    {
        $photo = GeotagPhoto::find($photoId);

        if ($photo && $photo->user_id == Auth::id()) {
            // Delete file from storage
            Storage::disk('public')->delete($photo->photo_path);

            // Delete database record
            $photo->delete();

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
