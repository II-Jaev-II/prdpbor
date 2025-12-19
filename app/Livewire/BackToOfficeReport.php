<?php

namespace App\Livewire;

use App\Models\BackToOfficeReport as BackToOfficeReportModel;
use App\Models\EnrollActivity;
use App\Models\SubprojectList;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class BackToOfficeReport extends Component
{
    use WithFileUploads;

    public $reports = [];
    public $tracking_code = '';
    public $loadAttempted = false;

    public function mount()
    {
        // Initialize with one report form
        $this->addReport();
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

        // Generate reports based on enrolled activities
        $this->reports = [];
        foreach ($activities as $activity) {
            // Format date range
            if ($activity->start_date === $activity->end_date) {
                $dateOfTravel = $activity->start_date;
            } else {
                $dateOfTravel = $activity->start_date . ' to ' . $activity->end_date;
            }

            // Get subproject name if subproject_id exists
            $subprojectName = '';
            if ($activity->subproject_id) {
                $subproject = SubprojectList::find($activity->subproject_id);
                $subprojectName = $subproject ? $subproject->subproject_name : '';
            } elseif ($activity->subproject_name) {
                $subprojectName = $activity->subproject_name;
            }

            $this->reports[] = [
                'activity_name' => $activity->activity_name,
                'date_of_travel' => $dateOfTravel,
                'purpose' => $activity->purpose,
                'purpose_type' => $activity->purpose_type,
                'subproject_name' => $subprojectName,
                'place' => '',
                'accomplishment' => '',
                'geotagged_photos' => [],
            ];
        }
    }

    public function addReport()
    {
        $this->reports[] = [
            'activity_name' => '',
            'date_of_travel' => '',
            'purpose' => '',
            'purpose_type' => '',
            'subproject_name' => '',
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
            $rules["reports.{$index}.activity_name"] = 'required|string|max:255';
            $rules["reports.{$index}.date_of_travel"] = 'required|string';
            $rules["reports.{$index}.purpose"] = 'required|string';
            $rules["reports.{$index}.purpose_type"] = 'required|string';
            
            // Subproject name is required if purpose is Site Specific
            if (isset($report['purpose']) && $report['purpose'] === 'Site Specific') {
                $rules["reports.{$index}.subproject_name"] = 'required|string|max:255';
            }
            
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
            $messages["reports.{$index}.activity_name.required"] = "Please enter an activity name";
            $messages["reports.{$index}.date_of_travel.required"] = "Please select a date of travel";
            $messages["reports.{$index}.purpose.required"] = "Please select a purpose of travel";
            $messages["reports.{$index}.purpose_type.required"] = "Please select a purpose type";
            $messages["reports.{$index}.subproject_name.required"] = "Please enter the subproject name";
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
        foreach ($this->reports as $index => $report) {
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

            // Find the matching enrolled activity
            $enrolledActivity = EnrollActivity::where('to_num', $this->tracking_code)
                ->where('activity_name', $report['activity_name'])
                ->where('purpose', $report['purpose'])
                ->first();

            // Save to database
            BackToOfficeReportModel::create([
                'user_id' => Auth::id(),
                'report_num' => $reportNum,
                'travel_order_id' => $this->tracking_code,
                'enrolled_activity_id' => $enrolledActivity ? $enrolledActivity->id : null,
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
