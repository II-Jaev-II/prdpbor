<?php

namespace App\Livewire;

use App\Models\BackToOfficeReport;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ApprovalReports extends Component
{
    public bool $showViewModal = false;
    public bool $showApprovalModal = false;
    public string $currentReportNum = '';
    public int $selectedReportId = 0;
    public string $approvalId = '';
    public $reports = [];

    protected $listeners = ['viewReports'];

    protected $rules = [
        'approvalId' => 'required|string|size:4|regex:/^[A-Za-z0-9]+$/',
    ];

    protected $messages = [
        'approvalId.required' => 'Approval ID is required.',
        'approvalId.size' => 'Approval ID must be exactly 4 characters.',
        'approvalId.regex' => 'Approval ID must contain only letters and numbers.',
    ];

    public function viewReports($reportNum): void
    {
        $this->currentReportNum = $reportNum;

        // Get all reports with this report number for the supervisor's unit
        $this->reports = BackToOfficeReport::query()
            ->where('report_num', $reportNum)
            ->whereHas('user', function ($query) {
                $query->where('unit_component', Auth::user()->superior_role);
            })
            ->with(['user', 'enrollActivity'])
            ->get();

        $this->showViewModal = true;
    }

    public function openApprovalModal(): void
    {
        $this->approvalId = '';
        $this->showApprovalModal = true;
    }

    public function closeApprovalModal(): void
    {
        $this->showApprovalModal = false;
        $this->approvalId = '';
        $this->resetValidation();
    }

    public function approveReport()
    {
        $this->validate();

        // Approve all pending reports with this report number
        $pendingReports = BackToOfficeReport::query()
            ->where('report_num', $this->currentReportNum)
            ->where('status', 'Pending')
            ->whereHas('user', function ($query) {
                $query->where('unit_component', Auth::user()->superior_role);
            })
            ->get();

        foreach ($pendingReports as $report) {
            $report->update([
                'status' => 'Approved',
                'approval_id' => strtoupper($this->approvalId),
            ]);
        }

        $this->closeApprovalModal();
        $this->closeModal();

        // Dispatch event to refresh the table
        $this->dispatch('reportApproved');

        // Redirect to refresh the page and update sidebar count
        return $this->redirect(route('superior.approval-reports'), navigate: true);
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
