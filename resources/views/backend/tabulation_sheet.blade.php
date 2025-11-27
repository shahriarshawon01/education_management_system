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
                                            $subject = $student['subjects'][$subjectName] ?? null;
                                            $totalMark = 0;
                                        @endphp

                                        @if ($subject)
                                            @if (!empty($subject['components']))
                                                @php
                                                    $subjectWiseTotal = 0;
                                                @endphp
                                                @foreach ($subject['components'] as $component)
                                                    @php
                                                        $isAbsent = $component['mark'] === 'AB';
                                                        $isFail =
                                                            strtolower($component['grade']) === 'f' ||
                                                            strtolower($component['result_status']) === 'fail';

                                                        $cellStyle = '';
                                                        if ($isAbsent) {
                                                            $cellStyle = 'background-color: red; color: white;';
                                                        } elseif ($isFail) {
                                                            $cellStyle = 'background-color: #ffc107; color: black;';
                                                        }
                                                    @endphp

                                                    <td style="text-align: center; {{ $cellStyle }}">
                                                        {{ $isAbsent ? 'AB' : $component['mark'] }}
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
                                            // $student['percentage'] == 0 &&
                                            ($student['merit_position'] == '-' || is_null($student['merit_position']));
                                        $absentStyle = 'background-color: red; color: white;';
                                    @endphp

                                    {{-- <td style="text-align: center; {{ $isAbsent ? $absentStyle : '' }}">
                                        {{ number_format($student['percentage'], 2) ?? 'N/A' }}
                                    </td> --}}
                                    <td style="text-align: center;">
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

                            @php
                                $firstStudent = $sortedStudents->first();
                            @endphp

                            {{-- Summary Row Generator --}}
                            @foreach (['pass' => 'Total Pass', 'fail' => 'Total Fail', 'ab' => 'Total Absent'] as $status => $label)
                                <tr style="background-color: #cecece; border: 1px solid #4c4c4c;">
                                    <td colspan="3" style="text-align: center;">{{ $label }}</td>

                                    @foreach ($firstStudent['subjects'] as $subjectName => $subjectData)
                                        @if (!empty($subjectData['components']))
                                            @foreach ($subjectData['components'] as $component)
                                                @php
                                                    $key = $subjectName . '-' . $component['name'];
                                                    $count = $summaryData[$key][$status] ?? 0;
                                                @endphp
                                                <td>{{ $count }}</td>
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
                            @endforeach

                        </tbody>
                    </table>

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
                                        <td>{{ $resultAnalysis['total_students'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>2</th>
                                        <th>All Subject Pass</th>
                                        <td>{{ $resultAnalysis['all_subject_pass'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>3</th>
                                        <th>Failed one subject</th>
                                        <td>{{ $resultAnalysis['failed_one_subject'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>4</th>
                                        <th>Failed two subject</th>
                                        <td>{{ $resultAnalysis['failed_two_subjects'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>5</th>
                                        <th>Failed in more than two subjects</th>
                                        <td>{{ $resultAnalysis['failed_more_than_two_subjects'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>6</th>
                                        <th>Absent</th>
                                        <td>{{ $resultAnalysis['fail'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Mark analysis table --}}
                        <div class="table-responsive col-md-6">
                            <table class="table table-bordered mark-analysis-table">
                                <tbody>
                                    <tr>
                                        <th colspan="2" class="text-center" style="width: 10%;">Mark Analysis</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">Marks Obtained (%)</th>
                                        <th class="text-center">Number of Students</th>
                                    </tr>
                                    @foreach ($markAnalysis as $range => $count)
                                        <tr>
                                            <td class="text-center">{{ $range }}</td>
                                            <td class="text-center">{{ $count }}</td>
                                        </tr>
                                    @endforeach
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
