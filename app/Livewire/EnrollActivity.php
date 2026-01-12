<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EnrollActivity as EnrollActivityModel;
use App\Models\SubprojectList;
use App\Models\User;
use Livewire\Attributes\Title;

#[Title('Enroll Activity')]
class EnrollActivity extends Component
{
    public $activities = [];
    public $subprojects = [];
    public $users = [];

    public function mount()
    {
        // Fetch all subprojects
        $this->subprojects = SubprojectList::all();
        
        // Fetch all registered users
        $this->users = User::orderBy('name', 'asc')->get(['id', 'name']);

        // Initialize with one empty activity
        $this->addActivity();
    }

    public function addActivity()
    {
        // Get the to_num from the last activity if exists
        $lastToNum = '';
        if (count($this->activities) > 0) {
            $lastActivity = end($this->activities);
            $lastToNum = $lastActivity['to_num'] ?? '';
        }

        $this->activities[] = [
            'to_num' => $lastToNum,
            'employee_name' => [],
            'activity_name' => '',
            'unit_component' => [],
            'purpose' => '',
            'purpose_type' => '',
            'subproject_id' => '',
            'subproject_name' => '',
            'travel_duration' => '',
        ];
    }

    public function removeActivity($index)
    {
        unset($this->activities[$index]);
        $this->activities = array_values($this->activities);
    }

    protected function rules()
    {
        $rules = [];
        foreach ($this->activities as $index => $activity) {
            $rules["activities.{$index}.to_num"] = 'required|string|max:255';
            $rules["activities.{$index}.activity_name"] = 'required|string|max:255';
            $rules["activities.{$index}.unit_component"] = 'required|array|min:1';
            $rules["activities.{$index}.unit_component.*"] = 'in:IBUILD,IREAP,IPLAN,ISUPPORT';
            $rules["activities.{$index}.purpose"] = 'required|in:Site Specific,Non Site Specific';
            $rules["activities.{$index}.purpose_type"] = 'required|string|max:255';

            // Subproject ID is required when purpose is Site Specific and purpose_type is not Validation
            if (isset($activity['purpose']) && $activity['purpose'] === 'Site Specific' && isset($activity['purpose_type']) && $activity['purpose_type'] !== 'Validation') {
                $rules["activities.{$index}.subproject_id"] = 'required|exists:subproject_lists,id';
            }

            // Subproject name is required when purpose_type is Validation
            if (isset($activity['purpose_type']) && $activity['purpose_type'] === 'Validation') {
                $rules["activities.{$index}.subproject_name"] = 'required|string|max:255';
            }

            $rules["activities.{$index}.travel_duration"] = 'required|string';
        }
        return $rules;
    }

    protected function messages()
    {
        $messages = [];
        foreach ($this->activities as $index => $activity) {
            $messages["activities.{$index}.to_num.required"] = "Please enter the Travel Order ID";
            $messages["activities.{$index}.activity_name.required"] = "Please enter the activity name";
            $messages["activities.{$index}.unit_component.required"] = "Please select a component";
            $messages["activities.{$index}.purpose.required"] = "Please select a purpose";
            $messages["activities.{$index}.purpose_type.required"] = "Please select a purpose type";
            $messages["activities.{$index}.subproject_id.required"] = "Please select a subproject";
            $messages["activities.{$index}.subproject_name.required"] = "Please enter the subproject name";
            $messages["activities.{$index}.travel_duration.required"] = "Please select the duration of travel";
        }
        return $messages;
    }

    public function submit()
    {
        $this->validate();

        foreach ($this->activities as $activity) {
            // Parse date range
            $dateOfTravel = $activity['travel_duration'];
            if (strpos($dateOfTravel, ' to ') !== false) {
                $dates = explode(' to ', $dateOfTravel);
                $startDate = trim($dates[0]);
                $endDate = trim($dates[1]);
            } else {
                $startDate = $dateOfTravel;
                $endDate = $dateOfTravel;
            }

            $data = [
                'to_num' => $activity['to_num'],
                'activity_name' => $activity['activity_name'],
                'employee_name' => $activity['employee_name'] ?? [],
                'unit_component' => $activity['unit_component'],
                'purpose' => $activity['purpose'],
                'purpose_type' => $activity['purpose_type'],
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];

            // Add subproject_id when purpose is Site Specific and purpose_type is not Validation
            if ($activity['purpose'] === 'Site Specific' && $activity['purpose_type'] !== 'Validation' && !empty($activity['subproject_id'])) {
                $data['subproject_id'] = $activity['subproject_id'];
            }

            // Add subproject_name when purpose_type is Validation
            if ($activity['purpose_type'] === 'Validation' && !empty($activity['subproject_name'])) {
                $data['subproject_name'] = $activity['subproject_name'];
            }

            EnrollActivityModel::create($data);
        }

        session()->flash('success', 'Activities enrolled successfully.');

        // Reset the form
        $this->activities = [];
        $this->addActivity();
    }

    public function cancel()
    {
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.enroll-activity');
    }
}
