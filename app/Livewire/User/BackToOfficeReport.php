<?php

namespace App\Livewire\User;

use App\Models\BackToOfficeReport as BackToOfficeReportModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class BackToOfficeReport extends Component
{
    use WithFileUploads;

    public $reports = [];

    public function mount()
    {
        // Initialize with one report form
        $this->addReport();
    }

    public function addReport()
    {
        $this->reports[] = [
            'date_of_travel' => '',
            'purpose' => '',
            'place' => '',
            'accomplishment' => '',
            'geotagged_photos' => [],
        ];
    }

    public function removeReport($index)
    {
        unset($this->reports[$index]);
        $this->reports = array_values($this->reports); // Re-index array
    }

    protected function rules()
    {
        $rules = [];
        foreach ($this->reports as $index => $report) {
            $rules["reports.{$index}.date_of_travel"] = 'required|date';
            $rules["reports.{$index}.purpose"] = 'required|string';
            $rules["reports.{$index}.place"] = 'required|string|max:255';
            $rules["reports.{$index}.accomplishment"] = 'required|string';
            $rules["reports.{$index}.geotagged_photos.*"] = 'required|image|max:10240';
        }
        return $rules;
    }

    protected function messages()
    {
        $messages = [];
        foreach ($this->reports as $index => $report) {
            $reportNumber = $index + 1;
            $messages["reports.{$index}.date_of_travel.required"] = "The date of travel field is required for Report #{$reportNumber}.";
            $messages["reports.{$index}.purpose.required"] = "Please select a purpose for Report #{$reportNumber}.";
            $messages["reports.{$index}.place.required"] = "The place field is required for Report #{$reportNumber}.";
            $messages["reports.{$index}.accomplishment.required"] = "The accomplishment field is required for Report #{$reportNumber}.";
            $messages["reports.{$index}.geotagged_photos.*.required"] = "At least one geotagged photo is required for Report #{$reportNumber}.";
            $messages["reports.{$index}.geotagged_photos.*.image"] = "Each file must be an image for Report #{$reportNumber}.";
            $messages["reports.{$index}.geotagged_photos.*.max"] = "Each image must not exceed 10MB for Report #{$reportNumber}.";
        }
        return $messages;
    }

    public function submit()
    {
        $this->validate();

        // Process each report
        foreach ($this->reports as $report) {
            // Save photos
            $photoPaths = [];
            if (!empty($report['geotagged_photos'])) {
                foreach ($report['geotagged_photos'] as $photo) {
                    $photoPaths[] = $photo->store('reports/photos', 'public');
                }
            }

            // Save to database
            BackToOfficeReportModel::create([
                'user_id' => Auth::id(),
                'date_of_travel' => $report['date_of_travel'],
                'purpose' => $report['purpose'],
                'place' => $report['place'],
                'accomplishment' => $report['accomplishment'],
                'photos' => $photoPaths,
                'status' => 'pending',
            ]);
        }

        $reportCount = count($this->reports);
        $message = $reportCount === 1 
            ? 'Back to Office Report submitted successfully!' 
            : "{$reportCount} Back to Office Reports submitted successfully!";
        
        session()->flash('success', $message);

        // Reset form
        $this->reset();
        $this->addReport(); // Add one form after reset
    }

    public function cancel()
    {
        $this->reset();
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.user.back-to-office-report');
    }
}
