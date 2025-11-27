<div class="page-content" style="margin-top: 10px">
    @if ($sortedStudents->isNotEmpty())
        <div class="container-fluid" id="tabulationSheetTableId">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="report-header">
                            <img src="{{ asset('storage/app/public/uploads/logotpsc.png') }}" alt="Logo"
                                style="width: 70px">
                            <h3 class="school-name">{{ $schoolData['title'] }}</h3>
                            <p class="school-address">{{ $schoolData['address'] }}</p>
                            <h4 class="report-title">Tabulation Sheet</h4>
                        </div>

                        <div class="col-md-6">
                            <div class="student-details-card">
                                <div class="student-detail">
                                    <strong>Exam:</strong>
                                    <span>{{ $examNames['name'] }} => {{ $examTypes['type_name'] }}</span>
                                </div>
                                <div class="student-detail">
                                    <strong>Class:</strong>
                                    <span>{{ $info['class_name'] ?? '' }}</span>
                                </div>
                                <div class="student-detail">
                                    <strong>Section:</strong>
                                    <span>{{ $info['section_name'] ?? '' }}</span>
                                </div>
                                <div class="student-detail">
                                    <strong>Session:</strong>
                                    <span>{{ $info['session_name'] ?? '' }}</span>
                                </div>
                                <div class="student-detail">
                                    <strong>Department:</strong>
                                    <span>{{ $info['department_name'] ?? '' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        $subjectColumnCount = 0;
                        if (!empty($sortedStudents->first()['subjects'])) {
                            foreach ($sortedStudents->first()['subjects'] as $subjectData) {
                                $subjectColumnCount +=
                                    count($subjectData['components']) > 0 ? count($subjectData['components']) + 1 : 1;
                            }
                        }
                        $totalColspan = 4 + $subjectColumnCount + 4;
                    @endphp

                    <table class="table table-bordered">
                        <thead class="text-center">
                            <tr style="background-color: #cecece; border: 1px solid #4c4c4c;">
                                <th rowspan="3" style="vertical-align: middle;">SL</th>
                                <th rowspan="3" style="vertical-align: middle;">Name/ID</th>
                                <th rowspan="3" style="vertical-align: middle;">Class Roll</th>

                                @if (!empty($sortedStudents->first()['subjects']))
                                    @foreach ($sortedStudents->first()['subjects'] as $subjectName => $subjectData)
                                        <th
                                            colspan="{{ count($subjectData['components']) > 0 ? count($subjectData['components']) + 1 : 1 }}">
                                            {{ $subjectName }}
                                        </th>
                                    @endforeach
                                @endif

                                <th rowspan="2" style="vertical-align: middle;">Sub Total</th>
                                <th rowspan="3" style="vertical-align: middle;">Percentage(%)</th>
                                <th rowspan="3" style="vertical-align: middle;">GPA</th>
                                <th rowspan="3" style="vertical-align: middle;">Position</th>
                            </tr>

                            <tr style="background-color: #cecece; border: 1px solid #4c4c4c;">
                                @if (!empty($sortedStudents->first()['subjects']))
                                    @foreach ($sortedStudents->first()['subjects'] as $subjectData)
                                        @if (!empty($subjectData['components']))
                                            @foreach ($subjectData['components'] as $component)
                                                <th>{{ $component['name'] }}</th>
                                            @endforeach
                                            <th>Total</th>
                                        @else
                                            <th>Total</th>
                                        @endif
                                    @endforeach
                                @endif
                            </tr>

                            <tr style="background-color: #cecece; border: 1px solid #4c4c4c;">
                                @php $overallTotalMark = 0; @endphp

                                @foreach ($subjectList as $subjectName)
                                    @php
                                        $matchedStudent = collect($sortedStudents)->first(function ($student) use (
                                            $subjectName,
                                        ) {
                                            return !empty($student['subjects'][$subjectName]['components']) &&
                                                collect($student['subjects'][$subjectName]['components'])
                                                    ->pluck('convert_mark')
                                                    ->sum() > 0;
                                        });

                                        $subjectData = $matchedStudent['subjects'][$subjectName] ?? null;
                                        $overallTotalMark = $matchedStudent['sub_total'] ?? 0;
                                    @endphp


                                    @if (!empty($subjectData))
                                        @foreach ($subjectData['components'] as $component)
                                            <th>{{ $component['convert_mark'] }}</th>
                                        @endforeach
                                        <th>{{ $subjectData['total'] }}</th>
                                    @else
                                        <th colspan="2">-</th>
                                    @endif
                                @endforeach

                                <th>{{ $overallTotalMark }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php $sl = 1; @endphp
                            @foreach ($sortedStudents as $key => $student)
                                <tr style="border: 1px solid #4c4c4c;">
                                    <td>{{ $sl++ }}</td>
                                    <td>{{ $student['student_name'] }} -
                                        <strong>[{{ $student['student_roll'] }}]</strong>
                                    </td>
                                    <td style="text-align: center;">{{ $student['merit_roll'] ?? '' }}</td>

                                    @php
                                        $subTotal = 0;
                                        $totalPossibleMarks = 0;
                                    @endphp

                                    @foreach ($sortedStudents->first()['subjects'] as $subjectName => $subjectData)
                                        @php
                                            // dd($student['sub_total']);
                                            $subject = $student['subjects'][$subjectName] ?? null;
                                            $totalMark = 0;
                                        @endphp

                                        @if ($subject)
                                            {{-- @if (!empty($subject['components']))
                                                @php
                                                    $subjectWiseTotal = 0;
                                                @endphp
                                                @foreach ($subject['components'] as $component)
                                                {{ dd($component) }}
                                                    <td
                                                        style="text-align: center; {{ $component['mark'] === 'AB' ? 'background-color: red; color: white;' : '' }}">
                                                        {{ $component['mark'] === 'AB' ? 'AB' : $component['mark'] }}
                                                    </td>
                                                    @php
                                                        $totalMark += is_numeric($component['mark'])
                                                            ? $component['mark']
                                                            : 0;

                                                        $subjectWiseTotal += is_numeric($component['convert_mark'])
                                                            ? $component['convert_mark']
                                                            : 0;

                                                    @endphp
                                                @endforeach
                                            @endif --}}

                                            @if (!empty($subject['components']))
    @php
        $subjectWiseTotal = 0;
    @endphp
    @foreach ($subject['components'] as $component)
        <td
            style="text-align: center; 
                {{ 
                    $component['mark'] === 'AB' || 
                    strtolower($component['grade']) === 'f' || 
                    strtolower($component['result_status']) === 'fail' 
                        ? 'background-color: red; color: white;' 
                        : '' 
                }}">
            {{ $component['mark'] === 'AB' ? 'AB' : $component['mark'] }}
        </td>
        @php
            $totalMark += is_numeric($component['mark']) ? $component['mark'] : 0;
            $subjectWiseTotal += is_numeric($component['convert_mark']) ? $component['convert_mark'] : 0;
        @endphp
    @endforeach
@endif


                                            <td style="text-align: center;">{{ $totalMark }}</td>
                                        @else
                                            @foreach ($subjectData['components'] as $component)
                                                <td style="text-align: center;">0</td>
                                            @endforeach
                                            <td style="text-align: center;">0</td>
                                        @endif

                                        @php
                                            $subTotal += $totalMark;
                                            $totalPossibleMarks =
                                                $subjectWiseTotal > 0 ? ($totalMark / $subjectWiseTotal) * 100 : 0;
                                        @endphp
                                    @endforeach

                                    <td style="text-align: center;">{{ round($subTotal) }} </td>

                                    @php
                                        $isAbsent =
                                            $student['cgpa'] == 0.0 &&
                                            $student['percentage'] == 0 &&
                                            ($student['merit_position'] == '-' || is_null($student['merit_position']));
                                        $absentStyle = 'background-color: red; color: white;';
                                    @endphp

                                    <td style="text-align: center; {{ $isAbsent ? $absentStyle : '' }}">
                                        {{ number_format($student['percentage'], 2) ?? 'N/A' }}
                                    </td>

                                    <td style="text-align: center; {{ $isAbsent ? $absentStyle : '' }}">
                                        {{ number_format($student['cgpa'], 2) ?? 'N/A' }}
                                    </td>

                                    <td style="text-align: center; {{ $isAbsent ? $absentStyle : '' }}">
                                        {{ $student['merit_position'] ?? '-' }}
                                    </td>

                                </tr>
                            @endforeach

                            {{-- pass/fail summary --}}
                            <tr style="background-color: #cecece; border: 1px solid #4c4c4c;">
                                <td colspan="3" style="text-align: center;">Total Pass</td>
                                @foreach ($sortedStudents->first()['subjects'] as $subjectName => $subjectData)
                                    @if (!empty($subjectData['components']))
                                        @foreach ($subjectData['components'] as $component)
                                            @php
                                                $passCount = 0;
                                                foreach ($sortedStudents as $student) {
                                                    if (
                                                        isset($student['subjects'][$subjectName]['components']) &&
                                                        is_array($student['subjects'][$subjectName]['components'])
                                                    ) {
                                                        foreach (
                                                            $student['subjects'][$subjectName]['components']
                                                            as $comp
                                                        ) {
                                                            if (
                                                                $comp['name'] === $component['name'] &&
                                                                strtolower($comp['result_status']) === 'pass'
                                                            ) {
                                                                $passCount++;
                                                            }
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <td>{{ $passCount }}</td>
                                        @endforeach
                                        <td></td>
                                    @else
                                        <td></td>
                                    @endif
                                @endforeach
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            {{-- Total Fail --}}
                            <tr style="background-color: #cecece; border: 1px solid #4c4c4c;">
                                <td colspan="3" style="text-align: center;">Total Fail</td>
                                @foreach ($sortedStudents->first()['subjects'] as $subjectName => $subjectData)
                                    @if (!empty($subjectData['components']))
                                        @foreach ($subjectData['components'] as $component)
                                            @php
                                                $failCount = 0;
                                                foreach ($sortedStudents as $student) {
                                                    if (
                                                        isset($student['subjects'][$subjectName]['components']) &&
                                                        is_array($student['subjects'][$subjectName]['components'])
                                                    ) {
                                                        foreach (
                                                            $student['subjects'][$subjectName]['components']
                                                            as $comp
                                                        ) {
                                                            if (
                                                                $comp['name'] === $component['name'] &&
                                                                strtolower($comp['result_status']) === 'fail'
                                                            ) {
                                                                $failCount++;
                                                            }
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <td>{{ $failCount }}</td>
                                        @endforeach
                                        <td></td>
                                    @else
                                        <td></td>
                                    @endif
                                @endforeach
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            {{-- Total Absent --}}
                            <tr style="background-color: #cecece; border: 1px solid #4c4c4c;">
                                <td colspan="3" style="text-align: center;">Total Absent</td>
                                @foreach ($sortedStudents->first()['subjects'] as $subjectName => $subjectData)
                                    @if (!empty($subjectData['components']))
                                        @foreach ($subjectData['components'] as $component)
                                            @php
                                                $absentCount = 0;
                                                foreach ($sortedStudents as $student) {
                                                    if (
                                                        isset($student['subjects'][$subjectName]['components']) &&
                                                        is_array($student['subjects'][$subjectName]['components'])
                                                    ) {
                                                        foreach (
                                                            $student['subjects'][$subjectName]['components']
                                                            as $comp
                                                        ) {
                                                            if (
                                                                $comp['name'] === $component['name'] &&
                                                                strtolower($comp['result_status']) === 'absent'
                                                            ) {
                                                                $absentCount++;
                                                            }
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <td>{{ $absentCount }}</td>
                                        @endforeach
                                        <td></td>
                                    @else
                                        <td></td>
                                    @endif
                                @endforeach
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                    @php
                        $analysis = [
                            'total_students' => $sortedStudents->count(),
                            'all_subject_pass' => 0,
                            'failed_one_subject' => 0,
                            'failed_two_subjects' => 0,
                            'failed_more_than_two_subjects' => 0,
                            'absent' => 0,
                        ];

                        foreach ($sortedStudents as $student) {
                            $failCount = $student['fail_count'] ?? 0;
                            $isAbsent = false;

                            foreach ($student['subjects'] as $subject) {
                                foreach ($subject['components'] ?? [] as $component) {
                                    if (strtolower($component['result_status']) === 'absent') {
                                        $isAbsent = true;
                                        break 2;
                                    }
                                }
                            }

                            if ($isAbsent) {
                                $analysis['absent']++;
                            } elseif ($failCount === 0) {
                                $analysis['all_subject_pass']++;
                            } elseif ($failCount === 1) {
                                $analysis['failed_one_subject']++;
                            } elseif ($failCount === 2) {
                                $analysis['failed_two_subjects']++;
                            } elseif ($failCount > 2) {
                                $analysis['failed_more_than_two_subjects']++;
                            }
                        }
                    @endphp

                    <div class="row table-container">
                        {{-- Result analysis Table --}}
                        <div class="table-responsive col-md-6">
                            <table class="table table-bordered result-analysis-table">
                                <tbody>
                                    <tr>
                                        <th style="text-align: center;" colspan="5">Result Analysis</th>
                                    </tr>
                                    <tr>
                                        <th>1</th>
                                        <th>Total Students</th>
                                        <td>{{ $analysis['total_students'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>2</th>
                                        <th>All Subject Pass</th>
                                        <td>{{ $analysis['all_subject_pass'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>3</th>
                                        <th>Failed one subject</th>
                                        <td>{{ $analysis['failed_one_subject'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>4</th>
                                        <th>Failed two subject</th>
                                        <td>{{ $analysis['failed_two_subjects'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>5</th>
                                        <th>Failed in more then two subjects</th>
                                        <td>{{ $analysis['failed_more_than_two_subjects'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>6</th>
                                        <th>Absent</th>
                                        <td>{{ $analysis['absent'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Mark analysis table --}}
                        <div class="table-responsive col-md-6">
                            @php
                                $markAnalysis = [
                                    '80-100' => 0,
                                    '70-79' => 0,
                                    '60-69' => 0,
                                    '50-59' => 0,
                                    '40-49' => 0,
                                    '0-39' => 0,
                                ];

                                foreach ($sortedStudents as $student) {
                                    $subTotal = 0;
                                    $totalPossibleMarks = 0;

                                    foreach ($student['subjects'] as $subject) {
                                        if (!empty($subject['components'])) {
                                            $subjectMark = 0;
                                            $subjectMaxMark = 0;

                                            foreach ($subject['components'] as $component) {
                                                $subjectMark += is_numeric($component['mark']) ? $component['mark'] : 0;
                                                $subjectMaxMark += is_numeric($component['convert_mark'])
                                                    ? $component['convert_mark']
                                                    : 0;
                                            }

                                            $subTotal += $subjectMark;
                                            $totalPossibleMarks += $subjectMaxMark;
                                        }
                                    }

                                    if ($totalPossibleMarks > 0) {
                                        $percentage = ($subTotal / $totalPossibleMarks) * 100;

                                        if ($percentage >= 80) {
                                            $markAnalysis['80-100']++;
                                        } elseif ($percentage >= 70) {
                                            $markAnalysis['70-79']++;
                                        } elseif ($percentage >= 60) {
                                            $markAnalysis['60-69']++;
                                        } elseif ($percentage >= 50) {
                                            $markAnalysis['50-59']++;
                                        } elseif ($percentage >= 40) {
                                            $markAnalysis['40-49']++;
                                        } else {
                                            $markAnalysis['0-39']++;
                                        }
                                    }
                                }
                            @endphp

                            <table class="table table-bordered mark-analysis-table">
                                <tbody>
                                    <tr>
                                        <th style="width: 10%; text-align: center;" colspan="2">Mark Analysis</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: center;">Marks Obtained (%)</th>
                                        <th style="text-align: center;">Number of Students</th>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;">80-100</td>
                                        <td style="text-align: center;">{{ $markAnalysis['80-100'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;">70-79</td>
                                        <td style="text-align: center;">{{ $markAnalysis['70-79'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;">60-69</td>
                                        <td style="text-align: center;">{{ $markAnalysis['60-69'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;">50-59</td>
                                        <td style="text-align: center;">{{ $markAnalysis['50-59'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;">40-49</td>
                                        <td style="text-align: center;">{{ $markAnalysis['40-49'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;">0-39</td>
                                        <td style="text-align: center;">{{ $markAnalysis['0-39'] }}</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
            {{-- Signature --}}
            {{-- @include('backend.signature') --}}
            @include('backend.signature', ['guardianSignatureNeeded' => false])
        </div>
    @else
        <div class="alert alert-warning text-center">
            <strong>No data available.</strong>
        </div>
    @endif
</div>

<style>
    .report-header {
        text-align: center;
        margin-bottom: 0px;
    }

    .report-header .school-name {
        font-size: 18px;
        font-weight: bold;
        color: #2c3e50;
        margin: 0;
    }

    .report-header .school-address {
        font-size: 14px;
        color: #7f8c8d;
        margin: 2px 0;
    }

    .report-header .report-title {
        font-size: 15px;
        font-weight: 600;
        color: #34495e;
        text-transform: uppercase;
        border-top: 1px solid #bdc3c7;
        border-bottom: 1px solid #bdc3c7;
        padding: 4px 0;
        display: inline-block;
        margin-top: 1px;
    }

    .student-details-card {
        margin-bottom: 0px !important;
    }

    .student-detail {
        margin-bottom: 2px;
        font-size: 14px;
        color: #34495e;
    }

    .student-detail strong {
        color: #2c3e50;
    }

    .student-detail span {
        margin-left: 2px;
        font-weight: 600;
        color: #7f8c8d;
    }

    .table-custom {
        width: 100% !important;
        overflow-x: scroll;
    }

    .card-body {
        width: 100% !important;
        overflow-x: auto;
    }

    .result-analysis-table tbody tr th,
    .mark-analysis-table tbody tr th {
        background-color: #cccbcb;
        font-weight: bold;
        border: 1px solid #000 !important;
    }

    .result-analysis-table .mark-analysis-table {
        border: 1px solid #000;
        border-radius: 8px;
        background-color: #f9f9f9;
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
        float: inline-start;
        margin-top: 0px;
    }

    .result-analysis-table th,
    .result-analysis-table td {
        border: 1px solid #000 !important;
    }

    .mark-analysis-table th,
    .mark-analysis-table td {
        border: 1px solid #000 !important;
    }

    @media print {
        .table-custom {
            overflow-x: hidden;
        }
        
        /* table {
            page-break-inside: avoid;
        } */

        .card-body {
            overflow-x: hidden;
        }
    }
</style>
