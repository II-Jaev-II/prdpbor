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

class VerifyReport extends Component
{
    protected $listeners = ['generateVerifyReport'];

    public function generateVerifyReport($reportNum)
    {
        // Get all approved reports with this report number for the superior's unit
        $reports = BackToOfficeReport::query()
            ->where('report_num', $reportNum)
            ->where('status', 'Approved')
            ->whereHas('user', function ($query) {
                $query->where('unit_component', Auth::user()->superior_role);
            })
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
            'reportNum' => $reportNum,
            'approvalId' => $approvalId,
            'qrCode' => $qrCode,
            'optimizedPhotos' => $optimizedPhotos,
        ])->setPaper('a4', 'portrait');

        // Download the PDF
        return response()->streamDownload(function () use ($pdf, $firstReport) {
            echo $pdf->output();
        }, 'Report_for_' . $firstReport->travel_order_id . '.pdf');
    }

    /**
     * Optimize photos for PDF embedding - resize and compress for faster generation
     */
    private function optimizePhotosForPdf($reports): array
    {
        $optimizedPhotos = [];

        foreach ($reports as $report) {
            $reportPhotos = [];

            if ($report->photos && is_array($report->photos)) {
                $manager = new ImageManager(new Driver());

                foreach ($report->photos as $photoPath) {
                    if (Storage::exists($photoPath)) {
                        try {
                            // Read and optimize the image
                            $image = $manager->read(Storage::path($photoPath));

                            // Resize to max 600px width (good balance of quality and file size)
                            $image->scale(width: 600);

                            // Convert to JPEG with 80% quality
                            $encodedImage = $image->toJpeg(quality: 80);

                            // Convert to base64
                            $reportPhotos[] = base64_encode($encodedImage);
                        } catch (\Exception $e) {
                            // If optimization fails, try to get original as base64
                            try {
                                $reportPhotos[] = base64_encode(Storage::get($photoPath));
                            } catch (\Exception $e) {
                                // Skip this photo if we can't process it
                                continue;
                            }
                        }
                    }
                }
            }

            $optimizedPhotos[$report->id] = $reportPhotos;
        }

        return $optimizedPhotos;
    }

    public function render()
    {
        return view('livewire.verify-report');
    }
}
