<?php

namespace App\Http\Controllers\Examination;

use App\Models\School;
use App\Models\Subject;
use App\Models\ExamName;
use App\Models\ManageMark;
use App\Models\Examination;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class TabulationSheetController extends Controller
{
    public function getTabulationSheet(Request $request)
    {
        // Step 1: Extract & Validate Request Input
        $filters = $this->extractFilters($request);

        if (empty($filters['class_id']) || empty($filters['session_year_id']) || empty($filters['exam_id']) || empty($filters['exam_type_id'])) {
            return returnData(3000, null, "Missing required filters for tabulation sheet.");
        }

        $classID = $filters['class_id'];
        $sessionID = $filters['session_year_id'];
        $departmentID = $filters['department_id'] ?? null;
        $examID = $filters['exam_id'];
        $examTypeId = $filters['exam_type_id'];

        // Step 2: Fetch Subject List
        $subjectList = $this->getSubjectList($filters);

        // Step 3: Fetch Raw Student Marks
        $rawMarks = $this->getStudentRawMarks($filters);

        // Step 4: Prepare Header Info (class, session, department, etc.)
        $info = $this->getTabulationInfo($filters);

        // Step 5: Process Students from Raw Marks
        $students = $this->processStudents($rawMarks, $subjectList);

        // Step 6: Calculate Subject Component Count (for dynamic table rendering)
        $subjectComponentCount = $this->calculateSubjectComponentCount($students, $subjectList);

        // Step 7: Calculate Merit Marks and Rank Students (GPA then merit mark)
        $meritMarks = $this->calculateMeritMarks($classID, $sessionID, $departmentID, $examID, $examTypeId);

        // Step 8: Fetch Raw Marks Again Without Section Filter for Ranking
        $markWithoutSection = $this->getStudentRawMarks($filters, true);
        $studentWithoutSection = $this->processStudents($markWithoutSection, $subjectList);

        // $studentsWithId = collect($students)->mapWithKeys(fn($item, $id) => [$id => $item]);
        $studentsWithId = collect($studentWithoutSection)->mapWithKeys(fn($item, $id) => [$id => $item]);

        $meritData = $this->rankStudents($studentsWithId);

        // Step 9: Total Students 
        $totalStudents = count($students);

        // Step 10: Assign Position to Students
        $sortedStudents = collect($students)->map(function ($student, $studentId) use ($totalStudents, $meritData) {
            $meritPosition = $meritData->firstWhere('student_id', $studentId) ?? null;

            $student['total_student'] = $totalStudents;
            if (isset($student['is_fail']) && $student['is_fail']) {
                $student['merit_position'] = '-';
            } else {
                $student['merit_position'] = $meritPosition['position'] ?? '-';
            }

            return $student;
        });

        // Step 11: Subject component wise summary
        $summaryData = $this->getSubjectWiseSummary($sortedStudents);


        // Step 12: Analyze Student Results
        $resultAnalysis = $this->analyzeStudentResults($sortedStudents);

        // Step 13: Analyze Student Marks
        $markAnalysis = $this->analyzeStudentMarks($sortedStudents);

        // Step 14: Fetch Additional Meta Info (exam name, exam type, school info)
        $examNames = ExamName::select('id', 'name')->find($examID);
        $examTypes = Examination::select('id', 'type_name')->find($examTypeId);
        $school = School::first();
        $schoolData = $school ? collect($school->toArray()) : null;

        // Step 15: Render View HTML
        $htmlData = view('backend.tabulation_sheet', compact(
            'sortedStudents',
            'subjectList',
            'subjectComponentCount',
            'info',
            'schoolData',
            'examNames',
            'examTypes',
            'resultAnalysis',
            'markAnalysis',
            'summaryData'
        ))->render();

        // Step 15: Return Response
        return returnData(2000, ['html' => $htmlData]);
    }

    private function processStudents($data, $subjectList)
    {
        $students = $data->groupBy('student_id')->map(function ($marks) use ($subjectList) {
            $student = $marks->first();

            $studentData = [
                'student_name' => $student->student_name_en,
                'student_roll' => $student->student_roll,
                'merit_roll' => $student->merit_roll,
                'exam_mark_data' => $student->exam_mark_data,
                'subjects' => [],
                'sub_total' => 0,
                'percentage' => 0,
                'total_possible_marks' => 0,
                'cgpa' => 0,
                'total_mark' => 0,
                'total_subjects' => 0,
            ];

            $groupedMarks = $marks->groupBy('subject_name');

            $totalGpa = 0;
            $validSubjectCount = 0;

            foreach ($subjectList as $subjectName) {
                $subjectMarks = $groupedMarks[$subjectName] ?? collect();
                $subjectTotal = 0;
                $totalPossibleMarks = 0;
                $components = [];

                if ($subjectMarks->isNotEmpty()) {
                    foreach ($subjectMarks as $subjectMark) {
                        $componentNames = json_decode($subjectMark->component_name, true) ?? [];
                        $resultMarks = json_decode($subjectMark->result_mark, true) ?? [];
                        $passMarks = json_decode($subjectMark->pass_mark, true) ?? [];
                        $examMark = json_decode($subjectMark->exam_mark, true) ?? [];
                        $convertMarks = json_decode($subjectMark->convert_num, true) ?? [];

                        $gpaResult = (float) $subjectMark->cgpa;
                        $gradeResult = $subjectMark->grade;
                        $resultStatus = $subjectMark->result_status;

                        if (is_array($componentNames) && count($componentNames) > 0 && collect($resultMarks)->filter()->count() > 0) {
                            foreach ($componentNames as $index => $component) {
                                $componentMark = $resultMarks[$index] ?? 0;
                                $passMark = $passMarks[$index] ?? 0;
                                $convertMark = $convertMarks[$index] ?? 0;
                                $individualExamMark = $examMark[$index] ?? 0;

                                $components[] = [
                                    'name' => $component,
                                    'mark' => ($componentMark === 0) ? 'AB' : $componentMark,
                                    'pass_mark' => $passMark,
                                    'gpa' => $gpaResult,
                                    'grade' => $gradeResult,
                                    'result_status' => $resultStatus,
                                    'exam_mark' => $individualExamMark,
                                    'convert_mark' => $convertMark,
                                ];

                                $subjectTotal += $convertMark;
                                $totalPossibleMarks += $componentMark;

                                // calculate total_mark
                                if (is_numeric($componentMark)) {
                                    $studentData['total_mark'] += $componentMark;
                                }
                            }

                            if ($gpaResult > 0) {
                                $totalGpa += $gpaResult;
                                $validSubjectCount++;
                            }
                        }
                    }

                    if (!empty($components)) {
                        $studentData['subjects'][$subjectName] = [
                            'components' => $components,
                            'total' => $subjectTotal,
                        ];
                        $studentData['sub_total'] += $subjectTotal;
                        $studentData['total_possible_marks'] += $totalPossibleMarks;
                    } else {
                        $studentData['subjects'][$subjectName] = $this->defaultAbsentSubject();
                    }
                } else {
                    $studentData['subjects'][$subjectName] = [
                        'components' => [
                            [
                                'name' => 'CQ',
                                'mark' => $studentData['exam_mark_data'] == 0 ? 'AB' : '-',
                                'pass_mark' => 0,
                                'gpa' => 0,
                                'grade' => $studentData['exam_mark_data'] == 0 ? 'F' : '-',
                                'result_status' => $studentData['exam_mark_data'] == 0 ? 'fail' : '-',
                                'exam_mark' => 0,
                                'convert_mark' => 0
                            ]
                        ],
                        'total' => 0
                    ];
                }
            }

            if ($studentData['total_possible_marks'] > 0) {
                $studentData['percentage'] = ($studentData['total_possible_marks'] / $studentData['sub_total']) * 100;
            }

            $studentData['cgpa'] = $validSubjectCount > 0 ? round($totalGpa / $validSubjectCount, 2) : 0;
            $studentData['total_subjects'] = $validSubjectCount;

            return $studentData;
        });

        return $students->map(function ($student) {
            $hasIssue = false;

            foreach ($student['subjects'] as $subject) {
                foreach ($subject['components'] as $component) {
                    $isAbsent = $component['mark'] === 'AB';
                    $isFail = strtolower($component['grade']) === 'f' || strtolower($component['result_status']) === 'fail';

                    if ($isAbsent || $isFail) {
                        $hasIssue = true;
                        break 2;
                    }
                }
            }

            if ($hasIssue) {
                $student['is_fail'] = true;
                $student['cgpa'] = 0.00;
                // $student['percentage'] = 0;
                $student['merit_position'] = '-';
            } else {
                $student['is_fail'] = false;
            }

            return $student;
        });

    }

    private function calculateSubjectComponentCount($students, $subjectList)
    {
        $subjectComponentCount = [];

        foreach ($students as $student) {
            foreach ($subjectList as $subjectName) {
                $count = count($student['subjects'][$subjectName]['components'] ?? []);
                if (!isset($subjectComponentCount[$subjectName]) || $subjectComponentCount[$subjectName] < $count) {
                    $subjectComponentCount[$subjectName] = $count;
                }
            }
        }

        return $subjectComponentCount;
    }

    private function calculateMeritMarks($classID, $sessionID, $departmentID, $examID, $examTypeId)
    {
        $subjectPriority = ['Mathematic', 'English 1st Paper', 'English 2nd Paper', 'English'];

        // Get all marks
        $marks = ManageMark::where('class_id', $classID)
            ->where('session_year_id', $sessionID)
            ->when($departmentID, fn($q) => $q->where('department_id', $departmentID))
            ->when($examID, fn($q) => $q->where('exam_id', $examID))
            ->when($examTypeId, fn($q) => $q->where('exam_type_id', $examTypeId))
            ->get()
            ->groupBy('student_id')
            ->map(function ($rows) use ($subjectPriority) {
                $totalMark = $rows->sum(fn($r) => array_sum(json_decode($r->result_mark, true)));
                $avgGpa = round($rows->avg('cgpa'), 2);

                $subjectMarks = [];
                foreach ($subjectPriority as $subName) {
                    $subRow = $rows->firstWhere('subject_name', $subName);
                    if ($subRow) {
                        $convertMarks = json_decode($subRow->convert_num, true);
                        $subjectMarks[$subName] = array_sum($convertMarks);
                    } else {
                        // Or AB handling
                        $subjectMarks[$subName] = 0;
                    }
                }

                return array_merge([
                    'student_id' => $rows->first()->student_id,
                    'cgpa' => $avgGpa,
                    'merit_mark' => $totalMark,
                ], $subjectMarks);
            });

        // Sort with fallback priority
        return $marks->sort(function ($a, $b) use ($subjectPriority) {
            if ($a['cgpa'] != $b['cgpa'])
                return $b['cgpa'] <=> $a['cgpa'];
            if ($a['merit_mark'] != $b['merit_mark'])
                return $b['merit_mark'] <=> $a['merit_mark'];

            foreach ($subjectPriority as $sub) {
                $cmp = ($b[$sub] ?? 0) <=> ($a[$sub] ?? 0);
                if ($cmp !== 0)
                    return $cmp;
            }

            return 0;
        })->values();
    }

    private function rankStudents($students, $subjectPriority = ['Mathematic', 'English 1st Paper', 'English 2nd Paper', 'English'])
    {
        $students = collect($students)->map(function ($student, $id) {
            $student['student_id'] = $id;
            return $student;
        })->values()->all();

        usort($students, function ($a, $b) use ($subjectPriority) {
            if (($a['cgpa'] ?? 0) !== ($b['cgpa'] ?? 0)) {
                return $b['cgpa'] <=> $a['cgpa'];
            }

            if (($a['total_mark'] ?? 0) !== ($b['total_mark'] ?? 0)) {
                return $b['total_mark'] <=> $a['total_mark'];
            }

            foreach ($subjectPriority as $subject) {
                $aMark = $a['subjects'][$subject]['total'] ?? 0;
                $bMark = $b['subjects'][$subject]['total'] ?? 0;

                if ($aMark !== $bMark) {
                    return $bMark <=> $aMark;
                }
            }

            return 0;
        });

        // Assign ranks
        $ranked = collect($students);
        $prev = null;
        $position = 1;

        return $ranked->map(function ($student, $index) use (&$position, &$prev) {
            if (
                $prev &&
                (
                    $student['cgpa'] !== $prev['cgpa'] ||
                    $student['total_mark'] !== $prev['total_mark'] ||
                    json_encode($student['subjects']) !== json_encode($prev['subjects'])
                )
            ) {
                $position = $index + 1;
            }

            $student['position'] = $position;
            $prev = $student;
            return $student;
        });
    }

    private function getSubjectList(array $filters): array
    {
        return Subject::where('class_id', $filters['class_id'])
            ->where('session_year_id', $filters['session_year_id'])
            ->where('department_id', $filters['department_id'])
            ->where('section_id', $filters['section_id'])
            ->orderBy('id')
            ->pluck('id', 'name')
            ->keys()
            ->toArray();
    }

    // private function getStudentRawMarks(array $filters)
    // {
    //     return ManageMark::with(['studentClass:name,id'])
    //         ->select(
    //             'manage_marks.*',
    //             'student_classes.name as class_name',
    //             'session_years.title',
    //             'students.student_name_en',
    //             'students.student_roll',
    //             'students.merit_roll',
    //             'students.id as student_id',
    //             'subjects.name as subject_name',
    //             'grade_numbers.*',
    //             'manage_marks.cgpa'
    //         )
    //         ->leftJoin('student_classes', 'manage_marks.class_id', 'student_classes.id')
    //         ->leftJoin('session_years', 'manage_marks.session_year_id', 'session_years.id')
    //         ->leftJoin('students', 'manage_marks.student_id', 'students.id')
    //         ->leftJoin('subjects', 'manage_marks.subject_id', 'subjects.id')
    //         ->leftJoin('grade_numbers', 'manage_marks.grade_number_id', 'grade_numbers.id')
    //         ->when($filters['session_year_id'], fn($q) => $q->where('manage_marks.session_year_id', $filters['session_year_id']))
    //         ->when($filters['class_id'], fn($q) => $q->where('manage_marks.class_id', $filters['class_id']))
    //         ->when($filters['department_id'], fn($q) => $q->where('manage_marks.department_id', $filters['department_id']))
    //         ->when($filters['exam_id'], fn($q) => $q->where('manage_marks.exam_id', $filters['exam_id']))
    //         ->when($filters['exam_type_id'], fn($q) => $q->where('manage_marks.exam_type_id', $filters['exam_type_id']))
    //         ->when($filters['section_id'], fn($q) => $q->where('students.section_id', $filters['section_id']))
    //         ->orderBy('students.merit_roll', 'asc')
    //         ->get();
    // }

    private function getStudentRawMarks(array $filters, bool $ignoreSection = false)
    {
        return ManageMark::with(['studentClass:name,id'])
            ->select(
                'manage_marks.*',
                'student_classes.name as class_name',
                'session_years.title',
                'students.student_name_en',
                'students.student_roll',
                'students.merit_roll',
                'students.id as student_id',
                'subjects.name as subject_name',
                'grade_numbers.*',
                'manage_marks.cgpa'
            )
            ->leftJoin('student_classes', 'manage_marks.class_id', 'student_classes.id')
            ->leftJoin('session_years', 'manage_marks.session_year_id', 'session_years.id')
            ->leftJoin('students', 'manage_marks.student_id', 'students.id')
            ->leftJoin('subjects', 'manage_marks.subject_id', 'subjects.id')
            ->leftJoin('grade_numbers', 'manage_marks.grade_number_id', 'grade_numbers.id')
            ->when($filters['session_year_id'], fn($q) => $q->where('manage_marks.session_year_id', $filters['session_year_id']))
            ->when($filters['class_id'], fn($q) => $q->where('manage_marks.class_id', $filters['class_id']))
            ->when($filters['department_id'], fn($q) => $q->where('manage_marks.department_id', $filters['department_id']))
            ->when($filters['exam_id'], fn($q) => $q->where('manage_marks.exam_id', $filters['exam_id']))
            ->when($filters['exam_type_id'], fn($q) => $q->where('manage_marks.exam_type_id', $filters['exam_type_id']))
            ->when(!$ignoreSection && !empty($filters['section_id']), fn($q) => $q->where('students.section_id', $filters['section_id']))
            ->orderBy('students.merit_roll', 'asc')
            ->get();
    }

    private function getTabulationInfo(array $filters): array
    {
        return [
            'session_name' => ManageMark::leftJoin('session_years', 'manage_marks.session_year_id', 'session_years.id')
                ->where('manage_marks.session_year_id', $filters['session_year_id'])
                ->value('session_years.title'),

            'class_name' => ManageMark::leftJoin('student_classes', 'manage_marks.class_id', 'student_classes.id')
                ->where('manage_marks.class_id', $filters['class_id'])
                ->value('student_classes.name'),

            'section_name' => ManageMark::leftJoin('students', 'manage_marks.student_id', 'students.id')
                ->leftJoin('sections', 'students.section_id', 'sections.id')
                ->where('students.section_id', $filters['section_id'])
                ->value('sections.name'),

            'department_name' => ManageMark::leftJoin('departments', 'manage_marks.department_id', 'departments.id')
                ->where('manage_marks.department_id', $filters['department_id'])
                ->value('departments.department_name'),

            'exam_name' => ManageMark::leftJoin('exam_names', 'manage_marks.exam_id', 'exam_names.id')
                ->where('manage_marks.exam_id', $filters['exam_id'])
                ->value('exam_names.name'),

            'exam_type_name' => ManageMark::leftJoin('examinations', 'manage_marks.exam_type_id', 'examinations.id')
                ->where('manage_marks.exam_type_id', $filters['exam_type_id'])
                ->value('examinations.type_name'),
        ];
    }

    private function defaultAbsentSubject(): array
    {
        return [
            'components' => [
                [
                    'name' => 'CQ',
                    'mark' => 'AB',
                    'pass_mark' => 0,
                    'gpa' => 0,
                    'grade' => 'F',
                    'result_status' => 'fail',
                    'exam_mark' => 0,
                    'convert_mark' => 0,
                ]
            ],
            'total' => 0
        ];
    }

    private function extractFilters(Request $request): array
    {
        return $request->only([
            'class_id',
            'session_year_id',
            'department_id',
            'section_id',
            'exam_id',
            'exam_type_id'
        ]);
    }

    private function getSubjectWiseSummary($sortedStudents)
    {
        $summary = [];

        foreach ($sortedStudents as $student) {
            foreach ($student['subjects'] as $subjectName => $subject) {
                foreach ($subject['components'] ?? [] as $component) {
                    $compName = $component['name'];
                    $key = $subjectName . '-' . $compName;

                    if (!isset($summary[$key])) {
                        $summary[$key] = [
                            'subject' => $subjectName,
                            'component' => $compName,
                            'pass' => 0,
                            'fail' => 0,
                            'ab' => 0,
                        ];
                    }

                    if (strtolower($component['mark']) === 'ab') {
                        $summary[$key]['ab']++;
                    } elseif (strtolower($component['result_status']) === 'fail') {
                        $summary[$key]['fail']++;
                    } elseif (strtolower($component['result_status']) === 'pass') {
                        $summary[$key]['pass']++;
                    }
                }
            }
        }

        return $summary;
    }

    private function analyzeStudentResults($sortedStudents)
    {
        $analysis = [
            'total_students' => $sortedStudents->count(),
            'all_subject_pass' => 0,
            'failed_one_subject' => 0,
            'failed_two_subjects' => 0,
            'failed_more_than_two_subjects' => 0,
            'fail' => 0,
        ];

        foreach ($sortedStudents as $student) {
            $failCount = 0;
            $isAbsent = false;

            foreach ($student['subjects'] as $subject) {
                foreach ($subject['components'] ?? [] as $component) {
                    $mark = $component['mark'];
                    $grade = strtolower($component['grade'] ?? '');
                    $resultStatus = strtolower($component['result_status'] ?? '');

                    if ($mark === 'AB') {
                        $isAbsent = true;
                        break 2;
                    }

                    if ($grade === 'f' || $resultStatus === 'fail') {
                        $failCount++;
                    }
                }
            }

            if ($isAbsent) {
                $analysis['fail']++;
            } elseif ($failCount === 0) {
                $analysis['all_subject_pass']++;
            } elseif ($failCount === 1) {
                $analysis['failed_one_subject']++;
            } elseif ($failCount === 2) {
                $analysis['failed_two_subjects']++;
            } else {
                $analysis['failed_more_than_two_subjects']++;
            }
        }

        return $analysis;
    }

    private function analyzeStudentMarks(Collection $students): array
    {
        $buckets = [
            '80-100' => 0,
            '70-79' => 0,
            '60-69' => 0,
            '50-59' => 0,
            '40-49' => 0,
            '0-39' => 0,
        ];

        foreach ($students as $student) {
            $subTotal = 0;
            $totalPossibleMarks = 0;

            foreach ($student['subjects'] as $subject) {
                foreach ($subject['components'] ?? [] as $cmp) {
                    $subTotal += is_numeric($cmp['mark']) ? $cmp['mark'] : 0;
                    $totalPossibleMarks += is_numeric($cmp['convert_mark']) ? $cmp['convert_mark'] : 0;
                }
            }

            if ($totalPossibleMarks > 0) {
                $pct = ($subTotal / $totalPossibleMarks) * 100;

                if ($pct >= 80) {
                    $buckets['80-100']++;
                } elseif ($pct >= 70) {
                    $buckets['70-79']++;
                } elseif ($pct >= 60) {
                    $buckets['60-69']++;
                } elseif ($pct >= 50) {
                    $buckets['50-59']++;
                } elseif ($pct >= 40) {
                    $buckets['40-49']++;
                } else {
                    $buckets['0-39']++;
                }
            }
        }
        return $buckets;
    }





    // public function getTabulationSheet(Request $request)
    // {
    //     $classID = $request->input('class_id');
    //     $sessionID = $request->input('session_year_id');
    //     $departmentID = $request->input('department_id');
    //     $sectionID = $request->input('section_id');
    //     $examID = $request->input('exam_id');
    //     $examTypeId = $request->input('exam_type_id');

    //     $subjectIds = Subject::where('class_id', $classID)
    //         ->where('session_year_id', $sessionID)
    //         ->where('department_id', $departmentID)
    //         ->where('section_id', $sectionID)
    //         ->orderBy('id')
    //         ->pluck('id', 'name')
    //         ->toArray();

    //     $subjectList = array_keys($subjectIds);

    //     $data = ManageMark::with(['studentClass:name,id'])
    //         ->select(
    //             'manage_marks.*',
    //             'student_classes.name as class_name',
    //             'session_years.title',
    //             'students.student_name_en',
    //             'students.student_roll',
    //             'students.merit_roll',
    //             'students.id as student_id',
    //             'subjects.name as subject_name',
    //             'grade_numbers.*',
    //             'manage_marks.cgpa'
    //         )
    //         ->leftJoin('student_classes', 'manage_marks.class_id', 'student_classes.id')
    //         ->leftJoin('session_years', 'manage_marks.session_year_id', 'session_years.id')
    //         ->leftJoin('students', 'manage_marks.student_id', 'students.id')
    //         ->leftJoin('subjects', 'manage_marks.subject_id', 'subjects.id')
    //         ->leftJoin('grade_numbers', 'manage_marks.grade_number_id', 'grade_numbers.id')
    //         ->when($sessionID, fn($q) => $q->where('manage_marks.session_year_id', $sessionID))
    //         ->when($classID, fn($q) => $q->where('manage_marks.class_id', $classID))
    //         ->when($departmentID, fn($q) => $q->where('manage_marks.department_id', $departmentID))
    //         ->when($examID, fn($q) => $q->where('manage_marks.exam_id', $examID))
    //         ->when($examTypeId, fn($q) => $q->where('manage_marks.exam_type_id', $examTypeId))
    //         ->when($sectionID, fn($q) => $q->where('students.section_id', $sectionID))
    //         ->orderBy('students.merit_roll', 'asc')
    //         ->get();

    //     $info = [
    //         'session_name' => ManageMark::leftJoin('session_years', 'manage_marks.session_year_id', 'session_years.id')
    //             ->when($sessionID, fn($q) => $q->where('manage_marks.session_year_id', $sessionID))
    //             ->value('session_years.title'),

    //         'class_name' => ManageMark::leftJoin('student_classes', 'manage_marks.class_id', 'student_classes.id')
    //             ->when($classID, fn($q) => $q->where('manage_marks.class_id', $classID))
    //             ->value('student_classes.name'),

    //         'section_name' => ManageMark::leftJoin('students', 'manage_marks.student_id', 'students.id')
    //             ->leftJoin('sections', 'students.section_id', 'sections.id')
    //             ->when($sectionID, fn($q) => $q->where('students.section_id', $sectionID))
    //             ->value('sections.name'),

    //         'department_name' => ManageMark::leftJoin('departments', 'manage_marks.department_id', 'departments.id')
    //             ->when($departmentID, fn($q) => $q->where('manage_marks.department_id', $departmentID))
    //             ->value('departments.department_name'),

    //         'exam_name' => ManageMark::leftJoin('exam_names', 'manage_marks.exam_id', 'exam_names.id')
    //             ->when($examID, fn($q) => $q->where('manage_marks.exam_id', $examID))
    //             ->value('exam_names.name'),

    //         'exam_type_name' => ManageMark::leftJoin('examinations', 'manage_marks.exam_type_id', 'examinations.id')
    //             ->when($examTypeId, fn($q) => $q->where('manage_marks.exam_type_id', $examTypeId))
    //             ->value('examinations.type_name')
    //     ];

    //     $students = $data->groupBy('student_id')->map(function ($marks) use ($subjectList) {
    //         $student = $marks->first();

    //         $studentData = [
    //             'student_name' => $student->student_name_en,
    //             'student_roll' => $student->student_roll,
    //             'merit_roll' => $student->merit_roll,
    //             'exam_mark_data' => $student->exam_mark_data,
    //             'subjects' => [],
    //             'sub_total' => 0,
    //             'percentage' => 0,
    //             'total_possible_marks' => 0,
    //             'cgpa' => 0,
    //             'total_subjects' => 0,
    //         ];

    //         $groupedMarks = $marks->groupBy('subject_name');

    //         $totalGpa = 0;
    //         $validSubjectCount = 0;

    //         foreach ($subjectList as $subjectName) {
    //             $subjectMarks = $groupedMarks[$subjectName] ?? collect();
    //             $subjectTotal = 0;
    //             $totalPossibleMarks = 0;
    //             $components = [];

    //             if ($subjectMarks->count() > 0) {
    //                 foreach ($subjectMarks as $subjectMark) {
    //                     $componentNames = json_decode($subjectMark->component_name, true) ?? [];
    //                     $resultMarks = json_decode($subjectMark->result_mark, true) ?? [];
    //                     $passMarks = json_decode($subjectMark->pass_mark, true) ?? [];
    //                     $examMark = json_decode($subjectMark->exam_mark, true) ?? [];
    //                     $convertMarks = json_decode($subjectMark->convert_num, true) ?? [];

    //                     $gpaResult = (float) $subjectMark->cgpa;
    //                     $gradeResult = $subjectMark->grade;
    //                     $resultStatus = $subjectMark->result_status;

    //                     if (is_array($componentNames) && count($componentNames) > 0 && collect($resultMarks)->filter()->count() > 0) {
    //                         foreach ($componentNames as $index => $component) {
    //                             $componentMark = $resultMarks[$index] ?? 0;
    //                             $passMark = $passMarks[$index] ?? 0;
    //                             $convertMark = $convertMarks[$index] ?? 0;
    //                             $individualExamMark = $examMark[$index] ?? 0;

    //                             $components[] = [
    //                                 'name' => $component,
    //                                 'mark' => ($componentMark === 0) ? 'AB' : $componentMark,
    //                                 'pass_mark' => $passMark,
    //                                 'gpa' => $gpaResult,
    //                                 'grade' => $gradeResult,
    //                                 'result_status' => $resultStatus,
    //                                 'exam_mark' => $individualExamMark,
    //                                 'convert_mark' => $convertMark
    //                             ];

    //                             $subjectTotal += $convertMark;
    //                             $totalPossibleMarks += $componentMark;
    //                         }

    //                         // Count GPA only for valid subjects
    //                         if ($gpaResult > 0) {
    //                             $totalGpa += $gpaResult;
    //                             $validSubjectCount++;
    //                         }
    //                     }
    //                 }

    //                 if (!empty($components)) {
    //                     $studentData['subjects'][$subjectName] = [
    //                         'components' => $components,
    //                         'total' => $subjectTotal
    //                     ];

    //                     $studentData['sub_total'] += $subjectTotal;
    //                     $studentData['total_possible_marks'] += $totalPossibleMarks;
    //                 } else {
    //                     $studentData['subjects'][$subjectName] = [
    //                         'components' => [
    //                             [
    //                                 'name' => 'CQ',
    //                                 'mark' => 'AB',
    //                                 'pass_mark' => 0,
    //                                 'gpa' => 0,
    //                                 'grade' => 'F',
    //                                 'result_status' => 'fail',
    //                                 'exam_mark' => 0,
    //                                 'convert_mark' => 0
    //                             ]
    //                         ],
    //                         'total' => 0
    //                     ];
    //                 }
    //             } else {
    //                 $studentData['subjects'][$subjectName] = [
    //                     'components' => [
    //                         [
    //                             'name' => 'CQ',
    //                             'mark' => $studentData['exam_mark_data'] == 0 ? 'AB' : '-',
    //                             'pass_mark' => 0,
    //                             'gpa' => 0,
    //                             'grade' => $studentData['exam_mark_data'] == 0 ? 'F' : '-',
    //                             'result_status' => $studentData['exam_mark_data'] == 0 ? 'fail' : '-',
    //                             'exam_mark' => 0,
    //                             'convert_mark' => 0
    //                         ]
    //                     ],
    //                     'total' => 0
    //                 ];
    //             }
    //         }

    //         if ($studentData['total_possible_marks'] > 0) {
    //             $studentData['percentage'] = ($studentData['sub_total'] / $studentData['total_possible_marks']) * 100;
    //         }

    //         $studentData['cgpa'] = $validSubjectCount > 0 ? round($totalGpa / $validSubjectCount, 2) : 0;
    //         $studentData['total_subjects'] = $validSubjectCount;

    //         return $studentData;
    //     });


    //     $students = $students->map(function ($student) {
    //         $isFail = false;

    //         foreach ($student['subjects'] as $subject) {
    //             if ($subject['total'] < 40) {
    //                 $isFail = true;
    //                 break;
    //             }
    //         }

    //         $student['is_fail'] = $isFail;
    //         return $student;
    //     });

    //     $passed = $students->where('is_fail', false)->sortByDesc('percentage')->values();
    //     $failed = $students->where('is_fail', true)->sortByDesc('percentage')->values();

    //     $positionWiseStudents = $passed->merge($failed);

    //     $positionMap = [];
    //     $position = 1;
    //     foreach ($positionWiseStudents as $student) {
    //         $positionMap[$student['student_roll']] = $position++;
    //     }

    //     $sortedStudents = $students->sortBy('merit_roll')->values();
    //     $serial = 1;
    //     $sortedStudents = $sortedStudents->transform(function ($student) use (&$serial, $positionMap) {
    //         $student['serial'] = $serial++;
    //         $student['position'] = $positionMap[$student['student_roll']] ?? null;
    //         return $student;
    //     });

    //     // ddA($sortedStudents);

    //     $subjectComponentCount = [];

    //     foreach ($sortedStudents as $student) {
    //         foreach ($subjectList as $subjectName) {
    //             $count = count($student['subjects'][$subjectName]['components'] ?? []);
    //             if (!isset($subjectComponentCount[$subjectName]) || $subjectComponentCount[$subjectName] < $count) {
    //                 $subjectComponentCount[$subjectName] = $count;
    //             }
    //         }
    //     }

    //     $examNames = ExamName::select('id', 'name')->where('id', $examID)->first();
    //     $examTypes = Examination::select('id', 'type_name')->where('id', $examTypeId)->first();
    //     $school = School::first();
    //     $schoolData = $school ? collect($school->toArray()) : null;

    //     $htmlData = view('backend.tabulation_sheet', compact('sortedStudents', 'subjectList', 'subjectComponentCount', 'info', 'schoolData', 'examNames', 'examTypes'))->render();

    //     return returnData(2000, ['html' => $htmlData]);
    // }





}
