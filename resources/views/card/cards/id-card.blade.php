<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: #fff;
        }

        .card {
            width: 54mm;
            height: 85.6mm;
            position: relative;
            overflow: hidden;
            border: 0.5px solid #ddd;
        }

        .card-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .content-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
        }

        .photo-container {
            position: absolute;
            top: 18mm;
            left: 17.5mm;
            width: 22mm;
            height: 27mm;
            border: 1px solid #ccc;
            background: #fff;
        }

        .profile-pic {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }


        .valid-till-bar {
            position: absolute;
            top: 16mm;
            right: 2mm;
            width: 8mm;
            height: 30mm;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3;
        }

        .valid-till-bar span {
            color: #055b0a;
            font-size: 4.5pt;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            white-space: nowrap;
            text-align: center;
        }

        .student-header {
            position: absolute;
            top: 47mm;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .student-name {
            margin-top: 3mm;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #000;
            margin-bottom: 0.5mm;
        }

        .student-roll {
            font-size: 7pt;
            color: #333;
            margin-bottom: 0.5mm;
        }

        .student-program {
            font-size: 8pt;
            font-weight: bold;
            color: #0073e6;
            /* text-transform: uppercase; */
            margin-bottom: 1mm;
        }

        .student-address {
            font-size: 5.8pt;
            color: #444;
            margin-top: 0.5mm;
            padding: 0 3mm;
            text-align: center;
            line-height: 1.3;
        }

        .info-grid {
            position: absolute;
            top: 61mm;
            left: 3mm;
            width: 48mm;
            font-size: 6.5pt;
            line-height: 1.4;
            color: #000;
        }

        .info-grid table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-grid td {
            vertical-align: top;
        }

        .info-grid td:first-child {
            font-weight: bold;
            color: #055b0a;
            white-space: nowrap;
            width: 10mm;
        }

        .stamp-container {
            position: absolute;
            bottom: 4.5mm;
            right: 2mm;
            width: 10mm;
            height: auto;
            opacity: 0.85;
            z-index: 5;
        }

        .signature-container {
            position: absolute;
            top: 35mm;
            left: 15mm;
            width: 15mm;
            height: auto;
            z-index: 5;
            opacity: 0.9;
        }

        .signature-container img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        .barcode-container {
            position: absolute;
            right: -5mm;
            bottom: 15mm;
            width: 20mm;
            height: 20mm;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(-90deg);
            transform-origin: center;
            z-index: 3;
        }

        .barcode-svg {
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body>

    @php
        // 1. Load org + department records once
        $orgRecord = $student->organizationRecord;
        $deptRecord = $student->departmentRecord;

        // 2. Resolve card background — DB-managed with hardcoded fallbacks
        $isSchool = $student->organization === 'school';
        $orgType  = $isSchool ? 'school' : 'college';
        $mtKey    = strtolower($student->member_type);

        $dbBg = \Illuminate\Support\Facades\Schema::hasTable('card_backgrounds')
            ? \App\Models\Card\CardBackground::activeFor($orgType, $mtKey)
            : null;

        if ($dbBg) {
            $bgImage = $dbBg->file_path;
        } else {
            $fallbackMap = $isSchool
                ? ['student' => 'erp/card/img/school_student.png', 'staff' => 'erp/card/img/school_staff.png', 'teacher' => 'erp/card/img/school_faculty.png']
                : ['student' => 'erp/card/img/clz_student.png',    'staff' => 'erp/card/img/clz_staff.png',    'teacher' => 'erp/card/img/clz_faculty.png'];
            $bgImage = $fallbackMap[$mtKey] ?? 'erp/card/img/school_student.png';
        }

        // 3. Resolve header text: department overrides org which overrides hardcoded defaults
        if ($isSchool) {
            $orgTitle1 = $orgRecord->name ?? 'Barchhain Secondary School';
            $orgTitle2 = '';
            $orgTitle3 = 'Barchhain - 2, Doti';
            $logoImage = $orgRecord->logo_path ?? 'img/school_logo.jpg';
        } else {
            // Start with org-level defaults
            $orgTitle1 = 'TRIBHUVAN UNIVERSITY';
            $orgTitle2 = $orgRecord->name ?? 'Barchhain Secondary School';
            $orgTitle3 = 'Barchhain - 2, Doti';
            $logoImage = $orgRecord->logo_path ?? 'assets/image/logo.png';

            // Department overrides university name + logo (for multi-university orgs)
            if ($deptRecord && $deptRecord->university) {
                $orgTitle1 = $deptRecord->university;
                $orgTitle2 = $deptRecord->university_college ?? $orgTitle2;
                $logoImage = $deptRecord->university_logo ?? $logoImage;
            }
        }

        // 4. Signature & stamp — from shared asset library, with hardcoded fallbacks
        $signatureImage = $orgRecord?->signatureAsset?->path ?? 'img/omSirLarge.png';
        $stampImage = $orgRecord?->stampAsset?->path ?? 'img/stamp_ioepc.png';

        // Also override org logo from asset if set (for non-department-specific logo)
        if ($orgRecord?->logoAsset?->path && !$deptRecord?->university_logo) {
            $logoImage = $orgRecord->logoAsset->path;
        }

        // 2. Resolve Profile Photo
        $photoSrc = $student->photo_url;

        // 3. Inline Code39 barcode — all non-school orgs, all member types
        $barcodeSvg = null;

        if ($student->organization !== 'school' && !empty($student->roll_number)) {
            $code39Map = [
                '0' => 'nnnwwnwnn',
                '1' => 'wnnwnnnnw',
                '2' => 'nnwwnnnnw',
                '3' => 'wnwwnnnnn',
                '4' => 'nnnwwnnnw',
                '5' => 'wnnwwnnnn',
                '6' => 'nnwwwnnnn',
                '7' => 'nnnwnnwnw',
                '8' => 'wnnwnnwnn',
                '9' => 'nnwwnnwnn',
                'A' => 'wnnnnwnnw',
                'B' => 'nnwnnwnnw',
                'C' => 'wnwnnwnnn',
                'D' => 'nnnnwwnnw',
                'E' => 'wnnnwwnnn',
                'F' => 'nnwnwwnnn',
                'G' => 'nnnnnwwnw',
                'H' => 'wnnnnwwnn',
                'I' => 'nnwnnwwnn',
                'J' => 'nnnnwwwnn',
                'K' => 'wnnnnnnww',
                'L' => 'nnwnnnnww',
                'M' => 'wnwnnnnwn',
                'N' => 'nnnnwnnww',
                'O' => 'wnnnwnnwn',
                'P' => 'nnwnwnnwn',
                'Q' => 'nnnnnnwww',
                'R' => 'wnnnnnwwn',
                'S' => 'nnwnnnwwn',
                'T' => 'nnnnwnwwn',
                'U' => 'wwnnnnnnw',
                'V' => 'nwwnnnnnw',
                'W' => 'wwwnnnnnn',
                'X' => 'nwnnwnnnw',
                'Y' => 'wwnnwnnnn',
                'Z' => 'nwwnwnnnn',
                '-' => 'nwnnnnwnw',
                '.' => 'wwnnnnwnn',
                ' ' => 'nwwnnnwnn',
                '$' => 'nwnwnwnnn',
                '/' => 'nwnwnnnwn',
                '+' => 'nwnnnwnwn',
                '%' => 'nnnwnwnwn',
                '*' => 'nwnnwnwnn',
            ];

            $barcodeValue = '*' . strtoupper($student->roll_number) . '*';
            $units = 0;
            $segments = [];

            foreach (str_split($barcodeValue) as $character) {
                if (!isset($code39Map[$character])) {
                    continue;
                }

                foreach (str_split($code39Map[$character]) as $index => $widthType) {
                    $widthUnits = $widthType === 'w' ? 3 : 1;
                    $segments[] = [
                        'is_bar' => $index % 2 === 0,
                        'width' => $widthUnits,
                        'x' => $units,
                    ];
                    $units += $widthUnits;
                }

                $units += 1;
            }

            $targetWidth = 90;
            $barScale = $units > 0 ? $targetWidth / $units : 1;
            $elements = [];

            foreach ($segments as $segment) {
                if (!$segment['is_bar']) {
                    continue;
                }

                $elements[] =
                    '<rect x="' .
                    number_format($segment['x'] * $barScale, 2, '.', '') .
                    '" y="0" width="' .
                    number_format($segment['width'] * $barScale, 2, '.', '') .
                    '" height="28" fill="#111"/>';
            }

            $barcodeSvg =
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90 28" preserveAspectRatio="none">' .
                implode('', $elements) .
                '</svg>';
        }
    @endphp

    <div class="card" @if ($flip ?? false) style="transform: scaleX(-1);" @endif>
        <img src="{{ asset($bgImage) }}" class="card-background" alt="Background">

        <div class="content-layer">

            <div class="header"
                style="display: flex; align-items: center; padding: 1mm; border-bottom: 0.5px solid #055b0a;">
                <img src="{{ asset($logoImage) }}" class="logo" alt="Logo" style="width: 8mm; height: 8mm;">
                <div class="header-text" style="text-align: center; flex: 1; color: #023014;">
                    <h2 style="font-size: 8pt; font-weight: bold; margin: 0; text-transform: uppercase;">
                        {{ $orgTitle1 }}</h2>
                    @if (!empty($orgTitle2))
                        <h2 style="font-size: 8pt; font-weight: bold; margin: 0; color: #055b0a;">{{ $orgTitle2 }}
                        </h2>
                    @endif
                    <h3 style="font-size: 7pt; font-weight: bold; margin: 0; color: #cb910b;">{{ $orgTitle3 }}</h3>
                </div>
            </div>

            <div class="photo-container">
                <img src="{{ $photoSrc }}" class="profile-pic" alt="Profile Photo">
            </div>

            @if ($student->valid_till)
                <div class="valid-till-bar">
                    <span style="font-size:12px">Valid Till <br> {{ $student->valid_till->format('M Y') }}</span>
                </div>
            @endif

            <div class="student-header">
                <div class="student-name">{{ $student->full_name }}</div>
                <div class="student-roll">Roll No : {{ $student->roll_number }}</div>
                <div class="student-program">
                     @if ($student->member_type === 'Faculty' ||    $student->member_type === 'Staff')
                        ({{ $student->designation}})
                    @endif
                    @if ( $student->member_type === 'Student' )
                        ({{ $student->program_label ?? 'N/A' }})
                    @endif


                   
                    @if(in_array(strtolower($student->member_type), ['staff', 'teacher']))
                        {{ $student->designation ?? 'N/A' }}
                    @else
                        {{ $student->department_label ?? 'N/A' }}
                        @if ($isSchool && $student->section)
                            ({{ $student->section }})
                        @endif
                    @endif
                </div>

            </div>

            <div class="info-grid" style=" margin-top: 8px;">
                <table>
                    @if ($student->registration_no)
                        <tr>
                            <td>Reg No:</td>
                            <td>{{ $student->registration_no }}</td>
                        </tr>
                    @endif
                    @if ($student->dob)
                        <tr>
                            <td>DOB:</td>
                            <td>{{ $student->dob->format('d M Y') }}</td>
                        </tr>
                    @endif
                    @if ($student->citizenship_no)
                        <tr>
                            <td>CTZ No:</td>
                            <td>{{ $student->citizenship_no }}</td>
                        </tr>
                    @endif
                    @if ($student->guardian_name)
                        <tr>
                            <td>Guardian:</td>
                            <td>{{ $student->guardian_name }}</td>
                        </tr>
                    @endif
                    @if ($student->mobile)
                        <tr>
                            <td>Mobile:</td>
                            <td>{{ $student->mobile }}</td>
                        </tr>
                    @endif
                    @if ($student->email)
                        <tr>
                            <td>Email:</td>
                            <td>{{ $student->email }}</td>
                        </tr>
                    @endif
                    @if ($student->address_label)
                        <tr>
                            <td>Address:</td>
                            <td>{{ $student->address_label }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            {{-- Signature --}}
            <div class="signature-container">
                <img src="{{ asset($signatureImage) }}" alt="Signature">
            </div>

            {{-- Stamp --}}
            <div class="stamp-container">
                <img src="{{ asset($stampImage) }}" alt="Stamp" style="width:100%; height:auto;">
            </div>

            @if ($barcodeSvg)
                <div class="barcode-container">
                    {!! $barcodeSvg !!}
                </div>
            @endif

        </div>
    </div>
</body>

</html>
