<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Travel Accomplishment Report - {{ $reportNum }}</title>
    <style>
        @page {
            margin: 200px 50px 100px 50px;
        }

        body {
            font-family: 'Cambria', 'Times New Roman', serif;
            font-size: 13px;
            line-height: 1;
            color: #000;
            position: relative;
        }

        .page-header {
            position: fixed;
            top: -200px;
            left: 0;
            right: 0;
            height: 170px;
        }

        .document-header {
            display: table;
            width: auto;
            margin: 20px auto 0 auto;
        }

        .header-wrapper {
            position: relative;
        }

        .logo-cell {
            display: table-cell;
            width: 120px;
            vertical-align: middle;
            text-align: center;
            padding: 0;
        }

        .logo-cell img {
            width: 150px;
            height: auto;
        }

        .logo-placeholder {
            width: 100px;
            height: 100px;
            border: 2px solid #ccc;
            display: inline-block;
            text-align: center;
            line-height: 100px;
            color: #666;
            font-size: 13px;
        }

        .header-info {
            display: table-cell;
            vertical-align: middle;
            text-align: left;
            font-family: 'Bookman Old Style', serif;
            padding-left: 10px;
        }

        .header-info p {
            margin: 0;
            line-height: 1;
            font-size: 13px;
        }

        .header-main {
            font-weight: bold;
            font-size: 13px;
        }

        .employee-info {
            margin: 20px 0;
            font-size: 13px;
            color: rgb(68, 114, 196);
        }

        .employee-info p {
            margin: 3px 0;
        }

        .employee-info .label {
            display: inline-block;
            width: 80px;
        }

        .employee-info .value {
            display: inline-block;
            margin-left: 10px;
        }

        .report-title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
            color: rgb(68, 114, 196);
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
            font-size: 13px;
            text-align: center;
        }

        .accomplishment-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
            font-size: 13px;
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
            font-size: 13px;
            color: #333;
            margin-bottom: 5px;
        }

        .photos-grid {
            margin: 20px 0;
            width: 100%;
            padding-top: 15px;
        }

        .photo-item {
            border: 1px solid #000;
            overflow: hidden;
            page-break-inside: avoid;
            position: relative;
            margin-bottom: 15px;
            width: 48%;
            display: inline-block;
            vertical-align: top;
            margin-right: 2%;
            box-sizing: border-box;
        }

        .photo-item:nth-child(2n) {
            margin-right: 0;
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
            position: relative;
        }

        .signature-label {
            font-size: 13px;
            margin-bottom: 40px;
        }

        .signature-name {
            font-weight: bold;
            font-size: 13px;
            text-decoration: underline;
            position: relative;
            margin-bottom: 2px;
        }

        .signature-title {
            font-size: 13px;
            font-style: italic;
            margin-top: 2px;
        }

        .e-signature-image {
            position: absolute;
            left: 50%;
            top: -50px;
            transform: translateX(-50%);
            max-width: 250px;
            height: auto;
            z-index: 10;
        }

        .page-number:after {
            content: counter(page);
        }

        .report-separator {
            margin: 5px 0px;
            border-bottom: 2px solid #000;
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
            font-size: 13px;
            margin: 3px 0 0 0;
            word-wrap: break-word;
        }

        .logo-container {
            position: absolute;
            top: 10px;
            left: 150px;
            width: 140px;
            padding: 5px;
            text-align: center;
        }

        .logo-container img {
            width: 100%;
            height: auto;
        }

        .qr-code-container {
            position: absolute;
            top: 10px;
            right: 50px;
            width: 80px;
            padding: 5px;
            background: white;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="page-header">
        <div class="header-wrapper">
            @if ($approvalId && $qrCode)
            <div class="qr-code-container">
                <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
            </div>
            @endif
        </div>

        <div class="document-header">
            <div class="logo-cell">
                <img src="{{ public_path('prdp-logo.png') }}" alt="PRDP Logo">
            </div>
            <div class="header-info">
                <p style="font-size: 12px;">Republic of the Philippines</p>
                <p class="header-main">Department of Agriculture</p>
                <p class="header-main">Philippine Rural Development Project</p>
                <p class="header-main">Regional Project Coordination Office 1</p>
                <p style="font-size: 12px;">City of San Fernando, La Union</p>
            </div>
        </div>

        <div class="report-title">TRAVEL ACCOMPLISHMENT REPORT</div>
        <div class="report-separator"></div>
    </div>

    <div class="employee-info">
        <p><span class="label">Name:</span><span class="value"><strong>{{ $reports->first()->user->name ?? 'UNKNOWN' }}</strong></span></p>
        <p><span class="label">Position:</span><span class="value"><strong>{{ $reports->first()->user->designation ?? 'UNKNOWN' }}</strong></span></p>
        <p><span class="label">Division:</span><span class="value"><strong>Philippine Rural Development Project ({{ $reports->first()->user->unit_component ?? 'UNKNOWN' }})</strong></span></p>
    </div>

    <table class="accomplishment-table">
        <thead>
            <tr>
                <th class="date-column">Date</th>
                <th class="place-column">Location</th>
                <th class="accomplishment-column">Accomplishments or Highlights of the Activity</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $index => $report)
            <tr>
                <td class="date-column">
                    {{ $report->start_date ? $report->start_date->format('j F, Y') : 'N/A' }}
                    @if ($report->end_date && !$report->start_date->isSameDay($report->end_date))
                    <br>to<br>
                    {{ $report->end_date->format('j F, Y') }}
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
                <p class="signature-name">
                    @if ($reports->first()->user && $reports->first()->user->e_signature)
                    <img src="{{ public_path('storage/' . $reports->first()->user->e_signature) }}" alt="Signature"
                        class="e-signature-image">
                    @endif
                    {{ strtoupper($reports->first()->user->name ?? 'UNKNOWN') }}
                </p>
                <p class="signature-title">{{ $reports->first()->user->designation ?? 'UNKNOWN' }}</p>
            </div>
            <div class="signature-cell">
                <p class="signature-label">Noted by:</p>
                <p class="signature-name">
                    @if ($reports->first()->approver && $reports->first()->approver->e_signature)
                    <img src="{{ public_path('storage/' . $reports->first()->approver->e_signature) }}" alt="Signature"
                        class="e-signature-image">
                    @endif
                    {{ strtoupper($reports->first()->approver->name ?? 'UNKNOWN') }}
                </p>
                <p class="signature-title">{{ $reports->first()->approver->designation ?? 'UNKNOWN' }}</p>
            </div>
        </div>
    </div>
</body>

</html>