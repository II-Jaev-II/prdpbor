<?php

namespace App\Livewire;

use App\Models\BackToOfficeReport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class PendingReports extends Component
{
    use WithFileUploads;

    public bool $showEditModal = false;
    public ?int $editingId = null;
    public array $editForm = [
        'date_of_travel' => '',
        'start_date' => '',
        'end_date' => '',
        'purpose' => '',
        'place' => '',
        'accomplishment' => '',
    ];
    public $newPhotos = [];
    public array $existingPhotos = [];
    public array $photosToDelete = [];

    protected $listeners = ['editReport'];

    public function editReport($rowId): void
    {
        $report = BackToOfficeReport::find($rowId);

        if ($report) {
            $this->editingId = $report->id;
            
            // Format date range for flatpickr
            $dateRange = '';
            if ($report->start_date && $report->end_date) {
                if ($report->start_date->eq($report->end_date)) {
                    $dateRange = $report->start_date->format('Y-m-d');
                } else {
                    $dateRange = $report->start_date->format('Y-m-d') . ' to ' . $report->end_date->format('Y-m-d');
                }
            }
            
            $this->editForm = [
                'date_of_travel' => $dateRange,
                'start_date' => $report->start_date?->format('Y-m-d') ?? '',
                'end_date' => $report->end_date?->format('Y-m-d') ?? '',
                'purpose' => $report->purpose ?? '',
                'place' => $report->place ?? '',
                'accomplishment' => $report->accomplishment ?? '',
            ];
            $this->existingPhotos = $report->photos ?? [];
            $this->newPhotos = [];
            $this->photosToDelete = [];
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

    public function updateReport(): void
    {
        $this->validate([
            'editForm.date_of_travel' => 'required|string',
            'editForm.purpose' => 'required|string|max:255',
            'editForm.place' => 'required|string|max:255',
            'editForm.accomplishment' => 'required|string',
            'newPhotos' => 'nullable|array',
            'newPhotos.*' => 'image|max:10240',
        ], [
            'editForm.date_of_travel.required' => 'Please select a date of travel',
            'editForm.purpose.required' => 'Please select a purpose',
            'editForm.purpose.max' => 'Purpose must not exceed 255 characters',
            'editForm.place.required' => 'Please enter a place',
            'editForm.place.max' => 'Place must not exceed 255 characters',
            'editForm.accomplishment.required' => 'Please enter an accomplishment',
            'newPhotos.*.image' => 'Each file must be an image',
            'newPhotos.*.max' => 'Each image must not exceed 10MB',
        ]);

        // Parse date range from flatpickr
        $dateOfTravel = $this->editForm['date_of_travel'];
        if (strpos($dateOfTravel, ' to ') !== false) {
            $dates = explode(' to ', $dateOfTravel);
            $startDate = trim($dates[0]);
            $endDate = trim($dates[1]);
        } else {
            $startDate = $dateOfTravel;
            $endDate = $dateOfTravel;
        }

        $report = BackToOfficeReport::find($this->editingId);

        if ($report) {
            // Delete photos marked for deletion
            foreach ($this->photosToDelete as $photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            // Upload new photos
            $photoPaths = $this->existingPhotos;
            if (!empty($this->newPhotos)) {
                foreach ($this->newPhotos as $photo) {
                    $originalName = $photo->getClientOriginalName();
                    $filename = pathinfo($originalName, PATHINFO_FILENAME);
                    $extension = $photo->getClientOriginalExtension();

                    // Check if file exists and add underscore with counter
                    $counter = 1;
                    $newFileName = $originalName;
                    while (Storage::disk('public')->exists('reports/photos/' . $newFileName)) {
                        $newFileName = $filename . '_' . $counter . '.' . $extension;
                        $counter++;
                    }

                    $photoPaths[] = $photo->storeAs('reports/photos', $newFileName, 'public');
                }
            }

            $report->update([
                'start_date' => $startDate,
                'end_date' => $endDate,
                'purpose' => $this->editForm['purpose'],
                'place' => $this->editForm['place'],
                'accomplishment' => $this->editForm['accomplishment'],
                'photos' => $photoPaths,
            ]);

            $this->closeModal();
            $this->dispatch('pg:eventRefresh-pendingTable');

            session()->flash('success', 'Report updated successfully!');
        }
    }

    public function closeModal(): void
    {
        $this->showEditModal = false;
        $this->editingId = null;
        $this->editForm = [
            'date_of_travel' => '',
            'start_date' => '',
            'end_date' => '',
            'purpose' => '',
            'place' => '',
            'accomplishment' => '',
        ];
        $this->newPhotos = [];
        $this->existingPhotos = [];
        $this->photosToDelete = [];
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.pending-reports');
    }
}
