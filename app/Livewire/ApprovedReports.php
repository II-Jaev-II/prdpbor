<?php

namespace App\Livewire;

use App\Models\BackToOfficeReport;
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

        // Generate QR Code as PNG (faster than SVG for PDF rendering)
        $qrCode = null;
        if ($approvalId) {
            // Use GDLib renderer directly (faster for PDF embedding)
            try {
                $renderer = new GDLibRenderer(
                    size: 128, // Reduced size for faster generation
                    margin: 2,
                    imageFormat: 'png',
                    compressionQuality: 9
                );
            } catch (\Exception $e) {
                // Fallback to Imagick if GD not available
                $renderer = new ImageRenderer(
                    new RendererStyle(128),
                    new ImagickImageBackEnd()
                );
            }

            $writer = new Writer($renderer);
            $qrCodePng = $writer->writeString($approvalId);

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
