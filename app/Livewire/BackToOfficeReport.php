<?php

namespace App\Livewire;

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
            $rules["reports.{$index}.date_of_travel"] = 'required|string';
            $rules["reports.{$index}.purpose"] = 'required|string';
            $rules["reports.{$index}.place"] = 'required|string|max:255';
            $rules["reports.{$index}.accomplishment"] = 'required|string';
            $rules["reports.{$index}.geotagged_photos"] = 'required|array|min:1';
            $rules["reports.{$index}.geotagged_photos.*"] = 'required|image|max:10240';
        }
        return $rules;
    }

    protected function messages()
    {
        $messages = [];
        foreach ($this->reports as $index => $report) {
            $reportNumber = $index + 1;
            $messages["reports.{$index}.date_of_travel.required"] = "Please select a date of travel";
            $messages["reports.{$index}.purpose.required"] = "Please select a purpose of travel";
            $messages["reports.{$index}.place.required"] = "Please enter a place of travel";
            $messages["reports.{$index}.accomplishment.required"] = "Please enter an accomplishment";
            $messages["reports.{$index}.geotagged_photos.required"] = 'Please upload at least one geotagged photo';
            $messages["reports.{$index}.geotagged_photos.min"] = 'Please upload at least one geotagged photo';
            $messages["reports.{$index}.geotagged_photos.*.required"] = 'Please upload a valid image file';
            $messages["reports.{$index}.geotagged_photos.*.image"] = 'The file must be an image';
            $messages["reports.{$index}.geotagged_photos.*.max"] = 'Each image must not exceed 10MB';
        }
        return $messages;
    }

    public function submit()
    {
        $this->validate();

        // Generate a unique report number for this submission
        $reportNum = 'RPT-' . date('Ymd') . '-' . strtoupper(uniqid());

        // Process each report
        foreach ($this->reports as $report) {
            // Save photos
            $photoPaths = [];
            if (!empty($report['geotagged_photos'])) {
                foreach ($report['geotagged_photos'] as $photo) {
                    $originalName = $photo->getClientOriginalName();
                    $filename = pathinfo($originalName, PATHINFO_FILENAME);
                    $extension = $photo->getClientOriginalExtension();

                    // Check if file exists and add underscore with counter
                    $counter = 1;
                    $newFileName = $originalName;
                    while (file_exists(storage_path('app/public/reports/photos/' . $newFileName))) {
                        $newFileName = $filename . '_' . $counter . '.' . $extension;
                        $counter++;
                    }

                    $photoPaths[] = $photo->storeAs('reports/photos', $newFileName, 'public');
                }
            }

            // Parse date of travel
            $dateOfTravel = $report['date_of_travel'];
            if (strpos($dateOfTravel, ' to ') !== false) {
                $dates = explode(' to ', $dateOfTravel);
                $startDate = trim($dates[0]);
                $endDate = trim($dates[1]);
            } else {
                $startDate = $dateOfTravel;
                $endDate = $dateOfTravel;
            }

            // Save to database
            BackToOfficeReportModel::create([
                'user_id' => Auth::id(),
                'report_num' => $reportNum,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'purpose' => $report['purpose'],
                'place' => $report['place'],
                'accomplishment' => $report['accomplishment'],
                'photos' => $photoPaths,
                'status' => 'Pending',
            ]);
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

    public function render()
    {
        return view('livewire.back-to-office-report');
    }
}
