<?php

namespace App\Livewire;

use App\Models\BackToOfficeReport;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

#[Title('Approved Reports')]
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

        // Get data from the first report
        $firstReport = $reports->first();
        $approvalId = $firstReport->approval_id;

        // Generate QR Code with detailed information
        $qrCode = null;
        if ($approvalId) {
            // Format QR code data
            $qrData = "ID: " . $firstReport->id . "\n";
            $qrData .= "Approval ID: " . $approvalId . "\n";
            $qrData .= "Travel Order ID: " . $firstReport->travel_order_id . "\n";
            $qrData .= "Submitted By: " . ($firstReport->user->name ?? 'Unknown') . "\n";
            $qrData .= "Approved At: " . $firstReport->updated_at->format('F j, Y');

            // Use GDLib renderer directly (faster for PDF embedding)
            try {
                $renderer = new GDLibRenderer(
                    size: 200, // Increased size to accommodate more data
                    margin: 2,
                    imageFormat: 'png',
                    compressionQuality: 9
                );
            } catch (\Exception $e) {
                // Fallback to Imagick if GD not available
                $renderer = new ImageRenderer(
                    new RendererStyle(200),
                    new ImagickImageBackEnd()
                );
            }

            $writer = new Writer($renderer);
            $qrCodePng = $writer->writeString($qrData);

            // Convert PNG to base64 for embedding
            $qrCode = base64_encode($qrCodePng);
        }

        // Optimize photos for PDF embedding
        $optimizedPhotos = $this->optimizePhotosForPdf($reports);

        // Generate PDF
        $pdf = Pdf::loadView('pdf.back-to-office-report', [
            'reports' => $reports,
            'reportNum' => $this->currentReportNum,
            'approvalId' => $approvalId,
            'qrCode' => $qrCode,
            'optimizedPhotos' => $optimizedPhotos,
        ])->setPaper('a4', 'portrait');

        // Download the PDF
        return response()->streamDownload(function () use ($pdf, $firstReport) {
            echo $pdf->output();
        }, 'Report_for_' . $firstReport->travel_order_id . '.pdf');
    }

    public function closeModal(): void
    {
        $this->showViewModal = false;
        $this->currentReportNum = '';
        $this->reports = [];
    }

    /**
     * Optimize photos for PDF embedding - resize and compress for faster generation
     */
    private function optimizePhotosForPdf($reports): array
    {
        $optimizedPhotos = [];
        $manager = new ImageManager(new Driver());

        foreach ($reports as $report) {
            if (!empty($report->photos) && is_array($report->photos)) {
                foreach ($report->photos as $photoPath) {
                    try {
                        $fullPath = storage_path('app/public/' . $photoPath);

                        if (!file_exists($fullPath)) {
                            continue;
                        }

                        // Load and optimize image
                        $image = $manager->read($fullPath);

                        // Resize to max width of 800px for PDF (maintains aspect ratio)
                        if ($image->width() > 800) {
                            $image->scale(width: 800);
                        }

                        // Compress to JPEG with 70% quality for faster PDF embedding
                        $optimized = $image->toJpeg(quality: 70)->toDataUri();

                        $optimizedPhotos[$photoPath] = $optimized;
                    } catch (\Exception $e) {
                        // If optimization fails, skip this photo
                        continue;
                    }
                }
            }
        }

        return $optimizedPhotos;
    }

    public function render()
    {
        return view('livewire.approved-reports');
    }
}
