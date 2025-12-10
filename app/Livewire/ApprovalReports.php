<?php

namespace App\Livewire;

use App\Models\BackToOfficeReport;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ApprovalReports extends Component
{
    public bool $showViewModal = false;
    public string $currentReportNum = '';
    public $reports = [];

    protected $listeners = ['viewReports'];

    public function viewReports($reportNum): void
    {
        $this->currentReportNum = $reportNum;
        
        // Get all reports with this report number for the supervisor's unit
        $this->reports = BackToOfficeReport::query()
            ->where('report_num', $reportNum)
            ->whereHas('user', function ($query) {
                $query->where('unit_component', Auth::user()->superior_role);
            })
            ->with('user')
            ->get();
        
        $this->showViewModal = true;
    }

    public function closeModal(): void
    {
        $this->showViewModal = false;
        $this->currentReportNum = '';
        $this->reports = [];
    }

    public function render()
    {
        return view('livewire.approval-reports');
    }
}
