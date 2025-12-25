<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Travel Accomplishment Report - {{ $reportNum }}</title>
    <style>
        @page {
            margin: 80px 50px 100px 50px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            position: relative;
        }

        .document-header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            position: relative;
        }

        .header-wrapper {
            position: relative;
        }

        .logo-section {
            display: table-cell;
            width: 120px;
            vertical-align: middle;
            text-align: center;
            padding-right: 15px;
        }

        .logo-placeholder {
            width: 100px;
            height: 100px;
            border: 2px solid #ccc;
            display: inline-block;
            text-align: center;
            line-height: 100px;
            color: #666;
            font-size: 9px;
        }

        .header-info {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            font-family: 'Bookman Old Style', serif;
        }

        .header-info p {
            margin: 2px 0;
            font-size: 12px;
        }

        .header-main {
            font-weight: bold;
            font-size: 10px;
        }

        .employee-info {
            margin: 20px 0;
            font-size: 11px;
        }

        .employee-info p {
            margin: 3px 0;
        }

        .report-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin: 20px 0;
        }

        .accomplishment-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .accomplishment-table th {
            border: 1px solid #000;
            padding: 8px;
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
        }

        .accomplishment-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
            font-size: 11px;
        }

        .date-column {
            width: 20%;
            text-align: center;
            vertical-align: middle;
        }

        .place-column {
            width: 25%;
            text-align: center;
            vertical-align: middle;
        }

        .accomplishment-column {
            width: 55%;
        }

        .accomplishment-text {
            text-align: justify;
            line-height: 1.5;
        }

        .travel-order-info {
            font-size: 10px;
            color: #333;
            margin-bottom: 5px;
        }

        .photos-grid {
            margin: 20px 0;
        }

        .photo-item {
            border: 1px solid #000;
            overflow: hidden;
            page-break-inside: avoid;
            position: relative;
            margin-bottom: 15px;
            max-width: 100%;
        }

        .photo-item img {
            width: 100%;
            height: auto;
            max-height: 350px;
            display: block;
            object-fit: contain;
        }

        .photo-qr {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 50px;
            height: 50px;
            background: white;
            border: 1px solid #000;
            padding: 2px;
        }

        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-row {
            display: table;
            width: 100%;
            margin-top: 30px;
        }

        .signature-cell {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-label {
            font-size: 10px;
            margin-bottom: 40px;
        }

        .signature-name {
            font-weight: bold;
            font-size: 11px;
            text-decoration: underline;
        }

        .signature-title {
            font-size: 10px;
        }

        .page-number:after {
            content: counter(page);
        }

        .report-separator {
            margin: 30px 0;
            border-top: 2px solid #000;
            page-break-after: always;
        }

        .qr-code-container {
            position: absolute;
            top: -70px;
            right: 0;
            width: 80px;
            padding: 5px;
            background: white;
            text-align: center;
        }

        .qr-code-container img {
            width: 100%;
            height: auto;
        }

        .qr-code-container p {
            font-size: 7px;
            margin: 3px 0 0 0;
            word-wrap: break-word;
        }

        .logo-container {
            position: absolute;
            top: -40px;
            left: 0;
            width: 140px;
            padding: 5px;
            background: white;
            text-align: center;
        }

        .logo-container img {
            width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="header-wrapper">
        <div class="logo-container">
            <img src="{{ public_path('prdp-logo.png') }}" alt="PRDP Logo">
        </div>

        @if ($approvalId && $qrCode)
            <div class="qr-code-container">
                <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
            </div>
        @endif
    </div>

    <div class="document-header">
        <div class="header-info">
            <p style="font-size: 12px;">Republic of the Philippines</p>
            <p class="header-main">DEPARTMENT OF AGRICULTURE RFO 1</p>
            <p class="header-main">Regional Project Coordination Office 1</p>
            <p style="font-size: 12px;">San Fernando City, La Union</p>
        </div>
    </div>

    <div class="employee-info">
        <p><strong>Name: {{ strtoupper($reports->first()->user->name ?? 'UNKNOWN') }}</strong></p>
        <p><strong>Division: PHILIPPINE RURAL DEVELOPMENT PROJECT (GEOMAPPING AND GOVERNANCE UNIT)</strong></p>
    </div>

    <div class="report-title">TRAVEL ACCOMPLISHMENT REPORT</div>

    <table class="accomplishment-table">
        <thead>
            <tr>
                <th class="date-column">Date</th>
                <th class="place-column">Particulars/Place</th>
                <th class="accomplishment-column">Accomplishments/Highlight of the Activity</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $index => $report)
                <tr>
                    <td class="date-column">
                        {{ $report->start_date ? $report->start_date->format('F j, Y') : 'N/A' }}
                        @if ($report->end_date && !$report->start_date->isSameDay($report->end_date))
                            <br>to<br>
                            {{ $report->end_date->format('F j, Y') }}
                        @endif
                    </td>
                    <td class="place-column">
                        <strong>{{ $report->place }}</strong>
                    </td>
                    <td class="accomplishment-column">
                        <div class="accomplishment-text">
                            {{ $report->accomplishment }}
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Photos Section - Display all photos from all reports --}}
    @php
        $allPhotos = [];
        foreach ($reports as $report) {
            if (!empty($report->photos) && count($report->photos) > 0) {
                $allPhotos = array_merge($allPhotos, $report->photos);
            }
        }
    @endphp

    @if (count($allPhotos) > 0)
        <div class="photos-grid">
            @foreach ($allPhotos as $photo)
                <div class="photo-item">
                    <img src="{{ public_path('storage/' . $photo) }}" alt="Report photo">
                </div>
            @endforeach
        </div>
    @endif

    <div class="signature-section">
        <div class="signature-row">
            <div class="signature-cell">
                <p class="signature-label">Prepared by:</p>
                <p class="signature-name">{{ strtoupper($reports->first()->user->name ?? 'UNKNOWN') }}</p>
                <p class="signature-title">ADMINISTRATIVE OFFICER II</p>
            </div>
            <div class="signature-cell">
                <p class="signature-label">Noted by:</p>
                @if ($reports->first()->approver && $reports->first()->approver->e_signature)
                    <img src="{{ public_path('storage/' . $reports->first()->approver->e_signature) }}" alt="Signature"
                        style="max-width: 200px; height: auto; margin: 0 auto 5px; display: block;">
                @endif
                <p class="signature-name">{{ strtoupper($reports->first()->approver->name ?? 'DEO G. RIVERA') }}</p>
                <p class="signature-title">I-SUPPORT COMPONENT HEAD</p>
            </div>
        </div>
    </div>
</body>

</html>
