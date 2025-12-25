<?php

namespace App\Livewire;

use App\Models\BackToOfficeReport;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class ApprovedReports extends Component
{
    public bool $showViewModal = false;
    public string $currentReportNum = '';
    public $reports = [];

    protected $listeners = ['viewReports'];

    public function viewReports($reportNum): void
    {
        $this->currentReportNum = $reportNum;

        // Get all reports with this report number for the authenticated user
        $this->reports = BackToOfficeReport::query()
            ->where('report_num', $reportNum)
            ->where('user_id', Auth::id())
            ->where('status', 'Approved')
            ->with(['user', 'enrollActivity', 'approver'])
            ->get();

        $this->showViewModal = true;
    }

    public function generateReport()
    {
        // Get all approved reports with this report number
        $reports = BackToOfficeReport::query()
            ->where('report_num', $this->currentReportNum)
            ->where('user_id', Auth::id())
            ->where('status', 'Approved')
            ->with(['user', 'enrollActivity', 'approver'])
            ->get();

        if ($reports->isEmpty()) {
            session()->flash('error', 'No reports found.');
            return;
        }

        // Get the approval ID from the first report (all should have the same approval_id)
        $approvalId = $reports->first()->approval_id;

        // Generate QR Code
        $qrCode = null;
        if ($approvalId) {
            $renderer = new ImageRenderer(
                new RendererStyle(256),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCodeSvg = $writer->writeString($approvalId);

            // Convert SVG to base64 for embedding
            $qrCode = base64_encode($qrCodeSvg);
        }

        // Generate PDF
        $pdf = Pdf::loadView('pdf.back-to-office-report', [
            'reports' => $reports,
            'reportNum' => $this->currentReportNum,
            'approvalId' => $approvalId,
            'qrCode' => $qrCode,
        ])->setPaper('a4', 'portrait');

        // Download the PDF
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Report_' . $this->currentReportNum . '_' . now()->format('Y-m-d') . '.pdf');
    }

    public function closeModal(): void
    {
        $this->showViewModal = false;
        $this->currentReportNum = '';
        $this->reports = [];
    }

    public function render()
    {
        return view('livewire.approved-reports');
    }
}
