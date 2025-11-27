<div class="page-content">
    <div class="container-fluid" id="academicTranscriptId">
        <div style="width: 100% !important;" class="transcript-header">
            <div class="row info-table">
                @foreach ($sortedStudents as $studentInfo)
                    <div class="col-md-2">
                        <div class="student-details-card">
                            <div class="student-detail">
                                <strong>Name:</strong>
                                <span>{{ $studentInfo['student_name'] ?? '' }}</span>
                            </div>
                            <div class="student-detail">
                                <strong>Student ID:</strong>
                                <span>{{ $studentInfo['student_roll'] ?? '' }}</span>
                            </div>
                            <div class="student-detail">
                                <strong>Class Roll:</strong>
                                <span>{{ $studentInfo['merit_roll'] ?? '' }}</span>
                            </div>
                            <div class="student-detail">
                                <strong>Class:</strong>
                                <span>{{ $studentInfo['class_name'] ?? '' }}</span>
                            </div>
                            <div class="student-detail">
                                <strong>Session:</strong>
                                <span>{{ $studentInfo['session_title'] ?? '' }}</span>
                            </div>
                            <div class="student-detail">
                                <strong>Section:</strong>
                                <span>{{ $studentInfo['section_name'] ?? '' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 text-center">
                        <div class="student-image-wrapper">
                            <img src="{{ asset('storage/app/public/' . $studentInfo['student_photo']) }}" alt="Photo"
                                class="student-image">
                        </div>
                    </div>
                @endforeach

                <div class="col-md-4">
                    <div class="main-header">
                        <div class="row mb-3">
                            <div class="report-header">
                                <img src="{{ asset('storage/app/public/uploads/logotpsc.png') }}" alt="Logo"
                                    style="width: 70px">
                                <h3 class="school-name">{{ $schoolData['title'] }}</h3>
                                <p class="school-address">{{ $schoolData['address'] }}</p>
                                <h4 class="report-title">Academic Transcript</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="col-md-2 text-center qr-code">
                    <h5>QR Code</h5>
                    {!! $qrCode !!}
                </div>

                <!-- Grading Table -->
                <div class="col-md-2">
                    <div class="table-responsive">
                        <table class=" table-bordered text-center shadow-sm grade-table">
                            <thead class="text-black">
                                <tr>
                                    <th>Marks</th>
                                    <th>GPA</th>
                                    <th>LG</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>80-100</td>
                                    <td>5.00</td>
                                    <td>A+</td>
                                </tr>
                                <tr>
                                    <td>70-79</td>
                                    <td>4.00</td>
                                    <td>A</td>
                                </tr>
                                <tr>
                                    <td>60-69</td>
                                    <td>3.50</td>
                                    <td>A-</td>
                                </tr>
                                <tr>
                                    <td>50-59</td>
                                    <td>3.00</td>
                                    <td>B</td>
                                </tr>
                                <tr>
                                    <td>40-49</td>
                                    <td>2.00</td>
                                    <td>C</td>
                                </tr>
                                <tr>
                                    <td>0-39</td>
                                    <td>0.00</td>
                                    <td>F</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Mark Table --}}
            <table class="table table-bordered mark-table">
                <thead>
                    <tr>
                        <th rowspan="3" style="width: 5% !important;" class="sub-name">Name of Subjects</th>
                        <th rowspan="3" style="width: 1%; text-align: center;">Full Marks</th>
                        @php
                            $examNames = [];
                            $subExamNames = [];
                            foreach ($sortedStudents[0]['subjects'] as $GroupSubject) {
                                foreach ($GroupSubject['group_data'] as $subject) {
                                    foreach ($subject['data'] as $examName => $examData) {
                                        if (!in_array($examName, $examNames)) {
                                            $examNames[] = $examName;
                                        }
                                        foreach ($examData as $subExamName => $subExamData) {
                                            if (!in_array($subExamName, $subExamNames)) {
                                                $subExamNames[] = $subExamName;
                                            }
                                        }
                                    }
                                }
                            }
                            $filteredExamNames = [];
                            $filteredSubExamNames = [];
                            foreach ($sortedStudents[0]['subjects'] as $GroupSubject) {
                                foreach ($GroupSubject['group_data'] as $subject) {
                                    foreach ($examNames as $examName) {
                                        if (isset($subject['data'][$examName])) {
                                            if (!in_array($examName, $filteredExamNames)) {
                                                $filteredExamNames[] = $examName;
                                            }
                                            foreach ($subExamNames as $subExamName) {
                                                if (
                                                    isset($subject['data'][$examName][$subExamName]) &&
                                                    !in_array($subExamName, $filteredSubExamNames[$examName] ?? [])
                                                ) {
                                                    $filteredSubExamNames[$examName][] = $subExamName;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        @endphp
                        @foreach ($filteredExamNames as $examName)
                            @if (isset($filteredSubExamNames[$examName]) && count($filteredSubExamNames[$examName]) > 0)
                                <th colspan="{{ count($filteredSubExamNames[$examName]) * 6 }}" class="text-center"
                                    style="width: 12% !important;">{{ $examName }}</th>
                            @endif
                        @endforeach
                        {{-- final exam --}}
                        @if (empty($examID) && empty($examTypeId))
                            <th style="width: 6% !important;" rowspan="2" colspan="3">Final Exam</th>
                        @elseif(empty($examTypeId))
                            <th style="width: 6% !important;" rowspan="2" colspan="3">{{ $examination }}</th>
                        @endif
                    </tr>
                    <tr>
                        @foreach ($filteredExamNames as $examName)
                            @if (isset($filteredSubExamNames[$examName]))
                                @foreach ($filteredSubExamNames[$examName] as $subExamName)
                                    <th colspan="6" class="text-center" style="width: 12% !important;">
                                        {{ $subExamName }}</th>
                                @endforeach
                            @endif
                        @endforeach
                    </tr>
                    <tr>
                        @foreach ($filteredExamNames as $examName)
                            @if (isset($filteredSubExamNames[$examName]))
                                @foreach ($filteredSubExamNames[$examName] as $subExamName)
                                    <th style="width: 1%; text-align: center;">CQ</th>
                                    <th style="width: 1%; text-align: center;">MCQ</th>
                                    <th style="width: 1%; text-align: center;">PR</th>
                                    <th style="width: 1%; text-align: center;">Total</th>
                                    <th style="width: 1%; text-align: center;">GPA</th>
                                    <th style="width: 1%; text-align: center;">LG</th>
                                @endforeach
                            @endif
                        @endforeach
                        {{-- final exam --}}
                        @if (empty($examID) && empty($examTypeId))
                            <th style="width: 1%; text-align: center;">Total</th>
                            <th style="width: 1%; text-align: center;">GPA</th>
                            <th style="width: 1%; text-align: center;">LG</th>
                        @elseif(empty($examTypeId))
                            <th style="width: 1%; text-align: center;">Total</th>
                            <th style="width: 1%; text-align: center;">GPA</th>
                            <th style="width: 1%; text-align: center;">LG</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @foreach ($sortedStudents[0]['subjects'] as $subjectGrouped)
                        @php
                            $rowSpan = count($subjectGrouped['group_data']);
                        @endphp
                        @php
                            $sGrades = [];
                        @endphp
                        @foreach ($subjectGrouped['group_data'] as $subject)
                            @php
                                $sindex = $loop->index;
                            @endphp

                            <tr data-sl="{{ $sindex }}">
                                <td style="width: 10%;">{{ $subject['subject_name'] }}</td>
                                <td style="width: 1%; text-align: center;">{{ $subject['subject_mark'] }}</td>
                                @php
                                    $finalCQ = $finalMCQ = $finalPR = 0;
                                    $examCount = count($filteredExamNames);
                                    $totalSubjectLG = 0;
                                    $totalLGCount = 0;
                                @endphp

                                @foreach ($filteredExamNames as $examName)
                                    @if (isset($filteredSubExamNames[$examName]))
                                        @foreach ($filteredSubExamNames[$examName] as $subExamName)
                                            @if (isset($subject['data'][$examName][$subExamName]['components'][0]))
                                                @php
                                                    $subExamData = $subject['data'][$examName][$subExamName];
                                                    $components = $subExamData['components'];
                                                    $cq = $components[0]['mark'] ?? '-';
                                                    $mcq = $components[1]['mark'] ?? '-';
                                                    $pr = $components[2]['mark'] ?? '-';
                                                    $total =
                                                        (is_numeric($cq) ? $cq : 0) +
                                                        (is_numeric($mcq) ? $mcq : 0) +
                                                        (is_numeric($pr) ? $pr : 0);

                                                    if (is_numeric($cq)) {
                                                        $finalCQ += $cq;
                                                    }
                                                    if (is_numeric($mcq)) {
                                                        $finalMCQ += $mcq;
                                                    }
                                                    if (is_numeric($pr)) {
                                                        $finalPR += $pr;
                                                    }

                                                    // LG Collection
                                                    foreach ($components as $component) {
                                                        if (isset($component['lg']) && is_numeric($component['lg'])) {
                                                            $totalSubjectLG += $component['lg'];
                                                            $totalLGCount++;
                                                        }
                                                    }
                                                @endphp
                                                <td style="width: 1%; text-align: center;">{{ $cq }}</td>
                                                <td style="width: 1%; text-align: center;">{{ $mcq }}</td>
                                                <td style="width: 1%; text-align: center;">{{ $pr }}</td>

                                                @php
                                                    $keyFormat = preg_replace(
                                                        '/[\s\-]+/',
                                                        '',
                                                        $examName . $subExamName,
                                                    );
                                                    $totalMark = $keyFormat . '_total';
                                                    $totalGPA = $keyFormat . '_gpa';
                                                    $totalLg = $keyFormat . '_lg';
                                                @endphp
                                                @if ($subjectGrouped['has_group'] == false)
                                                    @php
                                                        $sGrades[] = $components[0]['gpa'];
                                                    @endphp
                                                    <td style="width: 1%; text-align: center;">
                                                        {{ $total }}
                                                    </td>

                                                    <td style="width: 1%; text-align: center;">
                                                        {{ number_format($components[0]['lg'], 2) ?? '-' }}
                                                    </td>
                                                    <td style="width: 1%; text-align: center;">
                                                        {{ $components[0]['gpa'] ?? '-' }}</td>
                                                @elseif($sindex == 0)
                                                    @php
                                                        $sGrades[] = $subjectGrouped['group_totals'][$totalLg];
                                                    @endphp
                                                    <td style="width: 1%; text-align: center; vertical-align: middle;"
                                                        rowspan="{{ $rowSpan }}">
                                                        {{ $subjectGrouped['group_totals'][$totalMark] ?? '' }}
                                                    </td>

                                                    <td style="width: 1%; text-align: center; vertical-align: middle;"
                                                        rowspan="{{ $rowSpan }}">
                                                        {{ $subjectGrouped['group_totals'][$totalGPA] }}
                                                    </td>
                                                    <td style="width: 1%; text-align: center; vertical-align: middle;"
                                                        rowspan="{{ $rowSpan }}">
                                                        {{ $subjectGrouped['group_totals'][$totalLg] }}
                                                    </td>
                                                @endif
                                            @else
                                                <td style="width: 1%;">-</td>
                                                <td style="width: 1%;">-</td>
                                                <td style="width: 1%;">-</td>
                                                <td style="width: 1%;">-</td>
                                                <td style="width: 1%;">-</td>
                                                <td style="width: 1%;">-</td>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach

                                @php
                                    $validExamCount = count($filteredExamNames);
                                    $convertedMarkSum = 0;

                                    // sum subject converted_mark
                                    if (isset($subject['data']['Half Yearly'])) {
                                        foreach ($subject['data']['Half Yearly'] as $subExam) {
                                            foreach ($subExam['components'] as $component) {
                                                if (
                                                    isset($component['converted_mark']) &&
                                                    is_numeric($component['converted_mark'])
                                                ) {
                                                    $convertedMarkSum += $component['converted_mark'];
                                                }
                                            }
                                        }
                                    }

                                    $averageCQ = $validExamCount > 0 ? $finalCQ / $validExamCount : 0;
                                    $averageMCQ = $validExamCount > 0 ? $finalMCQ / $validExamCount : 0;
                                    $averagePR = $validExamCount > 0 ? $finalPR / $validExamCount : 0;
                                    $finalTotal = round($averageCQ + $averageMCQ + $averagePR, 2);

                                    $finalGPA = 0;
                                    $finalLG = '-';

                                    if ($convertedMarkSum > 0) {
                                        $percentage = round(($finalTotal * 100) / $convertedMarkSum);

                                        // Letter Grade
                                        if ($percentage >= 80) {
                                            $finalLG = 'A+';
                                        } elseif ($percentage >= 70) {
                                            $finalLG = 'A';
                                        } elseif ($percentage >= 60) {
                                            $finalLG = 'A-';
                                        } elseif ($percentage >= 50) {
                                            $finalLG = 'B';
                                        } elseif ($percentage >= 40) {
                                            $finalLG = 'C';
                                        } else {
                                            $finalLG = 'F';
                                        }

                                        // GPA mapping
                                        switch ($finalLG) {
                                            case 'A+':
                                                $finalGPA = 5.0;
                                                break;
                                            case 'A':
                                                $finalGPA = 4.0;
                                                break;
                                            case 'A-':
                                                $finalGPA = 3.5;
                                                break;
                                            case 'B':
                                                $finalGPA = 3.0;
                                                break;
                                            case 'C':
                                                $finalGPA = 2.0;
                                                break;
                                            default:
                                                $finalGPA = 0.0;
                                        }
                                    }

                                @endphp

                                @php
                                    $groupFinalTotal = $subjectGrouped['final_exam_totals']['final_total'] ?? 0;
                                    $groupFinalGPA = $subjectGrouped['final_exam_totals']['final_gpa'] ?? '-';
                                    $groupFinalLG = $subjectGrouped['final_exam_totals']['final_lg'] ?? '-';
                                @endphp
                                {{-- final exam --}}
                                @if (empty($examID) && empty($examTypeId))
                                    {{-- {{ dd("if") }} --}}
                                    @if ($subjectGrouped['has_group'] == false)
                                        <td style="text-align: center;">{{ round($finalTotal) }}</td>
                                        <td style="text-align: center;">{{ number_format($finalGPA, 2) }}</td>
                                        <td style="text-align: center;">{{ $finalLG }}</td>
                                    @elseif($sindex == 0)
                                        <td style="text-align: center; vertical-align: middle;"
                                            rowspan="{{ $rowSpan }}">
                                            {{ round($groupFinalTotal) }}
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;"
                                            rowspan="{{ $rowSpan }}">
                                            {{ number_format($groupFinalGPA, 2) }}
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;"
                                            rowspan="{{ $rowSpan }}">
                                            {{ $groupFinalLG }}
                                        </td>
                                    @endif
                                    {{-- single exam --}}
                                @elseif(empty($examTypeId))
                                    {{-- {{ dd('else') }} --}}
                                    @if ($subjectGrouped['has_group'] == false)
                                        <td style="width: 1%; text-align: center;">{{ round($finalTotal) }}</td>
                                        <td style="width: 1%; text-align: center;">{{ number_format($finalGPA, 2) }}
                                        </td>
                                        <td style="width: 1%; text-align: center;">{{ $finalLG }}</td>
                                    @elseif($sindex == 0)
                                        <td style="width: 1%; text-align: center; vertical-align: middle;"
                                            rowspan="{{ $rowSpan }}">
                                            {{ round($groupFinalTotal) }}
                                        </td>
                                        <td style="width: 1%; text-align: center; vertical-align: middle;"
                                            rowspan="{{ $rowSpan }}">
                                            {{ number_format($groupFinalGPA, 2) }}
                                        </td>
                                        <td style="width: 1%; text-align: center; vertical-align: middle;"
                                            rowspan="{{ $rowSpan }}">
                                            {{ $groupFinalLG }}
                                        </td>
                                    @endif
                                @endif

                            </tr>
                        @endforeach
                    @endforeach
                </tbody>

                <tfoot>
                    <tr class="table-success">
                        @php
                            $totalSubjectMarks = 0;
                            foreach ($sortedStudents[0]['subjects'] as $subject) {
                                foreach ($subject['group_data'] as $paper) {
                                    $totalSubjectMarks += $paper['subject_mark'] ?? 0;
                                }
                            }
                            $grandTotalMarks = 0;
                            $totalGPA = 0;
                            $gpaSubjectCount = 0;
                            $examWiseTotals = [];
                        @endphp

                        <th style="text-align: center;">Grand Total / GPA</th>
                        <th style="text-align: center;">{{ $totalSubjectMarks }}</th>
                        @foreach ($filteredExamNames as $examName)
                            @if (isset($filteredSubExamNames[$examName]))
                                @php
                                    $examTotal = 0;
                                    $examGpaTotal = 0;
                                    $examSubjects = 0;
                                @endphp
                                @foreach ($filteredSubExamNames[$examName] as $subExamName)
                                    @php
                                        $subTotal = 0;
                                        $subGpaTotal = 0;
                                        $subjectCount = $sortedStudents[0]['total_subjects'];
                                        $hasFail = false;
                                    @endphp
                                    @foreach ($sortedStudents[0]['subjects'] as $subjectGrouped)
                                        @foreach ($subjectGrouped['group_data'] as $subject)
                                            @php
                                                $keyFormat = preg_replace('/[\s\-]+/', '', $examName . $subExamName);
                                                $total = $keyFormat . '_total';
                                                $totalGPA = $keyFormat . '_gpa';
                                                $totalLG = $keyFormat . '_lg';
                                            @endphp
                                        @endforeach
                                    @endforeach
                                    <th style="text-align: center;">-</th>
                                    <th style="text-align: center;">-</th>
                                    <th style="text-align: center;">-</th>
                                    <th style="text-align: center;">
                                        {{ round($sortedStudents[0]['total_info'][$total]) }}</th>
                                    <th style="text-align: center;">
                                        {{ number_format($sortedStudents[0]['total_info'][$totalGPA], 2) }}</th>
                                    <th style="text-align: center;">{{ @$sortedStudents[0]['total_info'][$totalLG] }}
                                    </th>
                                @endforeach
                                @php
                                    $examWiseTotals[] = [
                                        'total' => $examTotal,
                                        'gpa' => $examGpaTotal,
                                        'subject_count' => $examSubjects,
                                    ];
                                @endphp
                            @endif
                        @endforeach

                        @if (empty($examID) && empty($examTypeId))
                            @php
                                $gpaSubjectCount = $sortedStudents[0]['total_subjects'];
                                $totalGPA = 0;
                                foreach ($sortedStudents[0]['subjects'] as $subjectGrouped) {
                                    foreach ($subjectGrouped['group_data'] as $subject) {
                                        $subjectTotal = 0;
                                        $hasComponent = false;
                                        $totalSubExamCount = 0;
                                        $totalSubjectLG = 0;
                                        $totalLGCount = 0;
                                        foreach ($filteredExamNames as $examName) {
                                            foreach ($filteredSubExamNames[$examName] ?? [] as $subExamName) {
                                                if (isset($subject['data'][$examName][$subExamName]['components'])) {
                                                    $components =
                                                        $subject['data'][$examName][$subExamName]['components'];
                                                    $cq = is_numeric($components[0]['mark'] ?? null)
                                                        ? $components[0]['mark']
                                                        : 0;
                                                    $mcq = is_numeric($components[1]['mark'] ?? null)
                                                        ? $components[1]['mark']
                                                        : 0;
                                                    $pr = is_numeric($components[2]['mark'] ?? null)
                                                        ? $components[2]['mark']
                                                        : 0;

                                                    $subjectTotal += $cq + $mcq + $pr;
                                                    $hasComponent = true;
                                                    $totalSubExamCount++;

                                                    // LG Collection
                                                    foreach ($components as $component) {
                                                        if (isset($component['lg']) && is_numeric($component['lg'])) {
                                                            $totalSubjectLG += $component['lg'];
                                                            $totalLGCount++;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        if ($hasComponent && $totalLGCount > 0) {
                                            $subjectGPA = round($totalSubjectLG / $totalLGCount, 2);
                                            // dd($subjectGPA);
                                            $totalGPA += $subjectGPA;
                                            $grandTotalMarks += $subjectTotal;
                                        }
                                    }
                                }
                                // dd($totalGPA);
                                $finalGPA = $gpaSubjectCount > 0 ? round($totalGPA / $gpaSubjectCount, 2) : 0;
                                $finalLG = match (true) {
                                    $finalGPA == 5 => 'A+',
                                    $finalGPA >= 4 => 'A',
                                    $finalGPA >= 3.5 => 'A-',
                                    $finalGPA >= 3 => 'B',
                                    $finalGPA >= 2 => 'C',
                                    default => 'F',
                                };
                            @endphp
                            <th style="text-align: center;">{{ round($grandTotalMarks) }}</th>
                            <th style="text-align: center;">{{ $finalGPA }}</th>
                            <th style="text-align: center;">{{ $finalLG }}</th>
                        @elseif(empty($examTypeId))
                            {{-- @php
                                $gpaSubjectCount = $sortedStudents[0]['total_subjects'];
                                $totalGPA = 0;
                                $grandTotalMarks = 0;

                                foreach ($sortedStudents[0]['subjects'] as $subjectGrouped) {
                                    foreach ($subjectGrouped['group_data'] as $subject) {
                                        $subjectTotal = 0;
                                        $hasComponent = false;
                                        $totalSubjectLG = 0;
                                        $totalLGCount = 0;
                                        // dd($subject);

                                        foreach ($filteredExamNames as $examName) {
                                            foreach ($filteredSubExamNames[$examName] ?? [] as $subExamName) {
                                                if (isset($subject['data'][$examName][$subExamName]['components'])) {
                                                    $components =
                                                        $subject['data'][$examName][$subExamName]['components'];

                                                    // Marks
                                                    $cq = is_numeric($components[0]['mark'] ?? null)
                                                        ? $components[0]['mark']
                                                        : 0;
                                                    $mcq = is_numeric($components[1]['mark'] ?? null)
                                                        ? $components[1]['mark']
                                                        : 0;
                                                    $pr = is_numeric($components[2]['mark'] ?? null)
                                                        ? $components[2]['mark']
                                                        : 0;

                                                    $subjectTotal += $cq + $mcq + $pr;
                                                    $hasComponent = true;

                                                    // LG Collection
                                                    foreach ($components as $component) {
                                                        // dd($component);
                                                        if (isset($component['lg']) && is_numeric($component['lg'])) {
                                                            $totalSubjectLG += $component['lg'];
                                                            $totalLGCount++;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        dd($totalSubjectLG, $totalLGCount);

                                        if ($hasComponent && $totalLGCount > 0) {
                                            $subjectGPA = round($totalSubjectLG / $totalLGCount, 2);
                                            $totalGPA += $subjectGPA;
                                            $grandTotalMarks += $subjectTotal;
                                        }
                                    }
                                }
                                 dd($totalGPA); 
                                $finalGPA = $gpaSubjectCount > 0 ? round($totalGPA / $gpaSubjectCount, 2) : 0;

                                $finalLG = match (true) {
                                    $finalGPA == 5 => 'A+',
                                    $finalGPA >= 4 => 'A',
                                    $finalGPA >= 3.5 => 'A-',
                                    $finalGPA >= 3 => 'B',
                                    $finalGPA >= 2 => 'C',
                                    default => 'F',
                                };
                            @endphp --}}

                            @php
                                $gpaSubjectCount = $sortedStudents[0]['total_subjects'];
                                $totalFinalGpaSum = 0;
                                $grandTotalMarks = 0;

                                foreach ($sortedStudents[0]['subjects'] as $subjectGrouped) {
                                    foreach ($subjectGrouped['group_data'] as $subject) {
                                        $convertedMarkSum = 0;

                                        // Sum converted marks
                                        foreach ($filteredExamNames as $examName) {
                                            foreach ($filteredSubExamNames[$examName] ?? [] as $subExamName) {
                                                if (isset($subject['data'][$examName][$subExamName]['components'])) {
                                                    foreach (
                                                        $subject['data'][$examName][$subExamName]['components']
                                                        as $component
                                                    ) {
                                                        if (
                                                            isset($component['converted_mark']) &&
                                                            is_numeric($component['converted_mark'])
                                                        ) {
                                                            $convertedMarkSum += $component['converted_mark'];
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        // Total mark from CQ + MCQ + PR
                                        $finalCQ = $finalMCQ = $finalPR = 0;
                                        $validExamCount = count($filteredExamNames);

                                        foreach ($filteredExamNames as $examName) {
                                            foreach ($filteredSubExamNames[$examName] ?? [] as $subExamName) {
                                                $components =
                                                    $subject['data'][$examName][$subExamName]['components'] ?? [];
                                                $finalCQ += is_numeric($components[0]['mark'] ?? null)
                                                    ? $components[0]['mark']
                                                    : 0;
                                                $finalMCQ += is_numeric($components[1]['mark'] ?? null)
                                                    ? $components[1]['mark']
                                                    : 0;
                                                $finalPR += is_numeric($components[2]['mark'] ?? null)
                                                    ? $components[2]['mark']
                                                    : 0;
                                            }
                                        }

                                        $averageCQ = $validExamCount > 0 ? $finalCQ / $validExamCount : 0;
                                        $averageMCQ = $validExamCount > 0 ? $finalMCQ / $validExamCount : 0;
                                        $averagePR = $validExamCount > 0 ? $finalPR / $validExamCount : 0;

                                        $finalTotal = round($averageCQ + $averageMCQ + $averagePR, 2);
                                        $grandTotalMarks += $finalTotal;

                                        if ($convertedMarkSum > 0) {
                                            $percentage = round(($finalTotal * 100) / $convertedMarkSum);

                                            // GPA from percentage
                                            $subjectGPA = match (true) {
                                                $percentage >= 80 => 5.0,
                                                $percentage >= 70 => 4.0,
                                                $percentage >= 60 => 3.5,
                                                $percentage >= 50 => 3.0,
                                                $percentage >= 40 => 2.0,
                                                default => 0.0,
                                            };

                                            $totalFinalGpaSum += $subjectGPA;
                                        }
                                    }
                                }

                                $finalGPA = $gpaSubjectCount > 0 ? round($totalFinalGpaSum / $gpaSubjectCount, 2) : 0;

                                $finalLG = match (true) {
                                    $finalGPA == 5 => 'A+',
                                    $finalGPA >= 4 => 'A',
                                    $finalGPA >= 3.5 => 'A-',
                                    $finalGPA >= 3 => 'B',
                                    $finalGPA >= 2 => 'C',
                                    default => 'F',
                                };
                            @endphp


                            <th style="text-align: center;">{{ round($grandTotalMarks) }}</th>
                            <th style="text-align: center;">{{ $finalGPA }}</th>
                            <th style="text-align: center;">{{ $finalLG }}</th>
                        @endif

                    </tr>
                </tfoot>


            </table>

            {{-- Summary & behavior Table --}}
            <div class="row table-container">
                <div class="table-responsive col-md-6">
                    <table class="table table-bordered shadow-sm summary-table">
                        <tbody>
                            <tr>
                                <th style="width: 10%; text-align: center;">Exam Name</th>
                                <th style="width: 15%; text-align: center;">Total Students</th>
                                <th style="width: 15%; text-align: center;">Merit Position</th>
                                <th style="width: 15%; text-align: center;">Highest Marks</th>
                                <th style="width: 30%; text-align: center;">Class Attendance</th>
                            </tr>
                            @foreach ($sortedStudents as $student)
                                <tr>
                                    <td style="text-align: center;">{{ $student['exam_name'] ?? 'N/A' }}</td>
                                    <td style="text-align: center;">{{ $student['total_student'] ?? 'N/A' }}</td>
                                    <td style="text-align: center;">
                                        {{ $student['merit_position'] }}
                                        @if ($student['merit_position'] !== '-' && is_numeric($student['merit_position']))
                                            {{ getOrdinalSuffix($student['merit_position']) }}
                                        @endif
                                    </td>
                                    {{-- for final exam  --}}
                                    @if (empty($examID) && empty($examTypeId))
                                        <td style="text-align: center;">
                                            {{ round($student['highest_marks'] / 2) ?? 'N/A' }}
                                        </td>
                                    @elseif(empty($examTypeId))
                                        <td style="text-align: center;">
                                            {{ round($student['highest_marks']) ?? 'N/A' }}</td>
                                    @elseif(!empty($examID) || !empty($examTypeId))
                                        <td style="text-align: center;">
                                            {{ round($student['highest_marks']) ?? 'N/A' }}
                                        </td>
                                    @endif
                                    <td style="text-align: center;"></td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                @php
                    function getOrdinalSuffix($num)
                    {
                        if (!is_numeric($num)) {
                            return '';
                        }

                        $num = (int) $num;
                        if (!in_array($num % 100, [11, 12, 13])) {
                            switch ($num % 10) {
                                case 1:
                                    return 'st';
                                case 2:
                                    return 'nd';
                                case 3:
                                    return 'rd';
                            }
                        }
                        return 'th';
                    }
                @endphp


                <div class="table-responsive col-md-6">
                    <table class="table table-bordered behavior-table">
                        <tbody>
                            <tr>
                                <th style="width: 10%; text-align: center;" colspan="5">Student's Behavior</th>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" style="transform: scale(1);"> <span>Excellent</span>
                                </td>
                                <td style="text-align: center;">
                                    <input type="checkbox" style="transform: scale(1);"> <span>Good</span>
                                </td>
                                <td style="text-align: center;">
                                    <input type="checkbox" style="transform: scale(1);"> <span>Very Good</span>
                                </td>
                                <td style="text-align: center;">
                                    <input type="checkbox" style="transform: scale(1);"> <span>Not Satisfactory</span>
                                </td>
                                <td style="text-align: center;">
                                    <input type="checkbox" style="transform: scale(1);"> <span>Improvement
                                        Needed</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- student Remark table --}}
            <div class="table-responsive mt-2">
                <table class="table table-bordered remark-table">
                    <tbody>
                        <tr>
                            <th style="width: 10%; text-align: center;" colspan="7">Remarks</th>
                        </tr>
                        <tr>
                            <td style="text-align: center;"><input type="checkbox" style="transform: scale(1);">
                                Excellent</td>
                            <td style="text-align: center;"><input type="checkbox" style="transform: scale(1);">
                                Very Good</td>
                            <td style="text-align: center;"><input type="checkbox" style="transform: scale(1);">
                                Good</td>
                            <td style="text-align: center;"><input type="checkbox" style="transform: scale(1);">
                                Fair</td>
                            <td style="text-align: center;"><input type="checkbox" style="transform: scale(1);">
                                Poor</td>
                            <td style="text-align: center;"><input type="checkbox" style="transform: scale(1);">
                                Very Poor</td>
                            <td style="text-align: center;"><input type="checkbox" style="transform: scale(1);">
                                Fail</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Signature --}}
    @include('backend.signature')
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

    .grade-table {
        margin-top: 0px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0px;
        border: 1px solid #000;
    }

    th,
    td {
        padding: 1px;
        font-size: 12px;
        border: 1px solid #000 !important;
    }

    .table thead tr th {
        background-color: #cccbcb;
        font-weight: bold;
        border: 1px solid #000 !important;
    }

    .summary-table tbody tr th,
    .behavior-table tbody tr th,
    .remark-table tbody tr th {
        background-color: #cccbcb;
        font-weight: bold;
        border: 1px solid #000 !important;
    }

    thead th[rowspan] {
        vertical-align: middle;
    }

    thead th[colspan] {
        text-align: center;
    }

    .table tfoot tr th {
        background-color: #cccbcb;
        font-weight: bold;
        border: 1px solid #000 !important;
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

    .student-image-wrapper {
        width: 100px;
        height: 110px;
        overflow: hidden;
        border-radius: 10px;
        border: 2px solid #ddd;
        display: inline-block;
    }

    .student-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    table.table tr th:first-child,
    table.table tr td:first-child {
        width: auto !important;
    }

    .summary-table .behavior-table {
        border: 1px solid #000;
        border-radius: 8px;
        background-color: #f9f9f9;
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
        float: inline-start;
        margin-top: 0px;
    }

    .summary-table th,
    .summary-table td {
        border: 1px solid #000 !important;
    }

    .grad-show {
        border: 1px solid #000;
    }

    .transcript-header {
        overflow-x: scroll;
    }

    @media print {

        .info-table {
            margin-top: 5px;
        }

        .page-content {
            padding: none !important;
        }

        .transcript-header {
            overflow-x: hidden;
        }

        th,
        td {
            font-size: 13px;
            padding: 0px;
        }

        .summary-table,
        .grade-table,
        table {
            box-shadow: none !important;
            border: 1px solid #000 !important;
        }

        table {
            page-break-inside: avoid;
        }

        .signature_box {
            margin-bottom: 1px;
        }
    }
</style>
