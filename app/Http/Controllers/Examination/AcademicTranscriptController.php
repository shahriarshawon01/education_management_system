<?php

namespace App\Http\Controllers\Examination;

use App\Models\School;
use App\Models\Student;
use App\Models\ExamName;
use App\Models\ManageMark;
use App\Models\Examination;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class AcademicTranscriptController extends Controller
{
    public function getTranscript(Request $request)
    {
        // Step 1: Request Input
        $classID = $request->input('class_id');
        $sessionID = $request->input('session_year_id');
        $sectionID = $request->input('section_id');
        $departmentID = $request->input('department_id');
        $examID = $request->input('exam_id');
        $examTypeId = $request->input('exam_type_id');
        $studentRoll = $request->input('student_roll');

        // Step 2: Validate Required Field
        if (empty($studentRoll)) {
            return returnData(3000, null, "Student roll is required!");
        }

        // Step 3: Fetch Reference Data
        $examNames = ExamName::all()->keyBy('id');
        $examTypes = Examination::all()->keyBy('id');

        // Step 4: Fetch Student Marks
        $studentMarks = $this->fetchStudentMarks($classID, $sessionID, $departmentID, $examID, $examTypeId, $studentRoll);
        // Step 5: Determine Exam Count
        if (empty($examID)) {
            $examIDs = $studentMarks->pluck('exam_id')->unique();
            $examCount = $examIDs->count();
        } else {
            $examCount = 1;
        }
        // Step 6: Check if Student Marks Exist
        if ($studentMarks->isEmpty()) {
            return returnData(3000, null, 'No results found for the provided student roll.');
        }

        // Step 7: Process Student Data
        $students = $this->processStudentData($studentMarks, $examID, $examTypeId, $examNames, $examTypes);

        // Step 8: Sort & Prepare Merit List
        $examName = $examTypes[$examTypeId]->type_name ?? 'Final Exam';
        $totalStudents = $this->fetchTotalStudents($classID, $sessionID, $departmentID, $sectionID);
        $meritMarks = $this->calculateMeritMarks($classID, $sessionID, $departmentID, $examID, $examTypeId);
        $studentsWithId = collect($students)->mapWithKeys(fn($item, $id) => [$id => $item]);
        $highestMarks = $meritMarks->isEmpty() ? 0 : $meritMarks->first()['merit_mark'];
        $meritData = $this->rankStudents($meritMarks);

        // 9: Fetch Subject Group if available
        $totalSubjects = $this->countGroupedSubjects($studentMarks);
        $sortedStudents = $students->transform(function ($student) use ($totalStudents, $examName, $highestMarks, $meritData, $totalSubjects) {
            $meritPosition = $meritData->firstWhere('student_id', $student['student_id']) ?? null;

            $student['total_student'] = $totalStudents;
            $student['highest_marks'] = $highestMarks;
            $student['exam_name'] = $examName;
            $student['subject_group'] = $subjectGroup ?? 0;
            $student['total_subjects'] = $totalSubjects;

            if (isset($student['overall_gpa']) && floatval($student['overall_gpa']) == 0) {
                $student['merit_position'] = '-';
            } else {
                $student['merit_position'] = $meritPosition['position'] ?? 'N/A';
            }

            return $student;
        });

        // dd($sortedStudents);
        // Step 10: Generate QR Code
        $qrCode = $this->generateQrCode($sortedStudents->first(), $examCount);
        // Step 11: Fetch School Info
        $schoolData = School::select('title', 'email', 'phone', 'steb_number', 'institute_code', 'address')->first();
        $schoolData = $schoolData ? collect($schoolData->toArray()) : null;
        // Step 12: Fetch Exam name for table header
        $examination = ExamName::find($examID)?->name;
        // Step 13: Return View
        $renderData = view('backend.academic_transcript', compact('sortedStudents', 'schoolData', 'qrCode', 'examID', 'examTypeId', 'examination'))->render();

        return returnData(2000, ['html' => $renderData]);
    }

    private function countGroupedSubjects($studentMarks)
    {
        $subjectGroups = [];

        foreach ($studentMarks as $mark) {
            $groupID = $mark->subject_group;
            $subjectID = $mark->subject_id;
            if ($groupID) {
                $subjectGroups['group_' . $groupID][$subjectID] = true;
            } else {
                $subjectGroups['subject_' . $subjectID] = true;
            }
        }

        return count($subjectGroups);
    }

    private function fetchStudentMarks($classID, $sessionID, $departmentID, $examID, $examTypeId, $studentRoll)
    {
        return ManageMark::with(['studentClass:name,id'])
            ->select(
                'manage_marks.*',
                'student_classes.name as class_name',
                'students.student_name_en',
                'students.photo',
                'students.student_roll',
                'students.merit_roll',
                'students.id as student_id',
                'subjects.id as subject_id',
                'subjects.name as subject_name',
                'subjects.subject_mark as subject_mark',
                'subjects.subject_group_id as subject_group',
                'grade_numbers.*',
                'manage_marks.cgpa',
                'session_years.title as session_title',
                'departments.department_name as department_name',
                'sections.name as section_name'
            )
            ->leftJoin('student_classes', 'manage_marks.class_id', 'student_classes.id')
            ->leftJoin('students', 'manage_marks.student_id', 'students.id')
            ->leftJoin('subjects', 'manage_marks.subject_id', 'subjects.id')
            ->leftJoin('grade_numbers', 'manage_marks.grade_number_id', 'grade_numbers.id')
            ->leftJoin('session_years', 'manage_marks.session_year_id', 'session_years.id')
            ->leftJoin('departments', 'manage_marks.department_id', 'departments.id')
            ->leftJoin('sections', 'students.section_id', 'sections.id')
            ->where('manage_marks.class_id', $classID)
            ->where('manage_marks.session_year_id', $sessionID)
            ->where('manage_marks.department_id', $departmentID)
            ->where('students.student_roll', $studentRoll)
            ->when($examID, fn($q) => $q->where('manage_marks.exam_id', $examID))
            ->when($examTypeId, fn($q) => $q->where('manage_marks.exam_type_id', $examTypeId))
            ->get();
    }

    private function processStudentData($data, $examID, $examTypeId, $examNames, $examTypes)
    {
        return $data->groupBy('student_id')->map(function ($marks) use ($examID, $examTypeId, $examNames, $examTypes) {
            $student = $marks->first();
            $studentData = [
                'student_name' => $student->student_name_en,
                'student_photo' => $student->photo,
                'student_id' => $student->student_id,
                'student_roll' => $student->student_roll,
                'class_name' => $student->class_name,
                'session_title' => $student->session_title,
                'department_name' => $student->department_name,
                'section_name' => $student->section_name,
                'merit_roll' => $student->merit_roll,
                'cgpa' => $student->cgpa,
                'subjects' => [],
                'sub_total' => 0,
                'percentage' => 0,
                'total_possible_marks' => 0,
                'overall_gpa_sum' => 0,
                'overall_gpa_count' => 0,
                'has_overall_fail_status' => false,
            ];

            $groupCalc = [];
            $groupCounter = 1;

            $examMarkKeys = [];

            foreach ($marks->groupBy('subject_id') as $subjectId => $subjectMarks) {
                $subject = $subjectMarks->first();
                $subjectData = [];
                $subjectTotalAllExam = 0;
                $totalPossAllExam = 0;
                $groupKey = $subject->subject_group ? 'ag_' . $subject->subject_group : 'gp_tmp' . $groupCounter;
                if (!isset($groupCalc[$groupKey])) {
                    $groupCalc[$groupKey] = [
                        'has_group' => (bool) $subject->subject_group,
                        'unique_subject_ids' => [],
                        'exam_types' => []
                    ];
                }
                if (!in_array($subjectId, $groupCalc[$groupKey]['unique_subject_ids'])) {
                    $groupCalc[$groupKey]['unique_subject_ids'][] = $subjectId;
                }


                foreach ($subjectMarks as $subjectMark) {
                    if ($examID && $subjectMark->exam_id != $examID) {
                        continue;
                    }
                    if ($examTypeId && $subjectMark->exam_type_id != $examTypeId) {
                        continue;
                    }

                    $examName = $examNames[$subjectMark->exam_id]->name ?? 'UnknownExam';
                    $subExamName = $examTypes[$subjectMark->exam_type_id]->type_name ?? 'UnknownType';
                    $examFlatKey = preg_replace('/[\s\-]+/', '', $examName . $subExamName);

                    $componentNames = json_decode($subjectMark->component_name, true) ?? [];
                    $resultMarks = json_decode($subjectMark->result_mark, true) ?? [];
                    $totalMarks = json_decode($subjectMark->total_mark, true) ?? [];
                    $convertedMarks = json_decode($subjectMark->convert_num, true) ?? [];
                    $examMarks = json_decode($subjectMark->exam_mark, true) ?? [];
                    $passMarks = json_decode($subjectMark->pass_mark, true) ?? [];
                    $gpaResult = (float) $subjectMark->cgpa;
                    $gradeResult = $subjectMark->grade;
                    $resultStatus = json_decode($subjectMark->result_status, true) ?? [];
                    $subjectTotalMark = $subjectMark->subject_total_mark;

                    $currentTotal = 0;
                    $currentConverted = 0;
                    $currentPossible = 0;
                    $components = [];
                    $isSubjectFailedForThisSubExam = false;

                    foreach ($componentNames as $i => $component) {
                        $mark = $resultMarks[$i] ?? 0;
                        $converted = $convertedMarks[$i] ?? 0;
                        $possible = $totalMarks[$i] ?? 0;
                        $componentResultStatus = $resultStatus[$i] ?? 'Pass';

                        if (strtolower($componentResultStatus) === 'fail') {
                            $isSubjectFailedForThisSubExam = true;
                        }

                        $components[] = [
                            'name' => $component,
                            'mark' => $mark,
                            'lg' => $gpaResult,
                            'gpa' => $gradeResult,
                            'pass_mark' => $passMarks[$i] ?? 0,
                            'result_status' => $componentResultStatus,
                            'converted_mark' => $converted,
                            'exam_mark' => $examMarks[$i] ?? 0,
                            'total_mark' => $possible,
                            'subject_total_mark' => $subjectTotalMark,
                        ];
                        $currentTotal += $mark;
                        $currentConverted += $converted;
                        $currentPossible += $possible;
                    }
                    if ($isSubjectFailedForThisSubExam) {
                        $gpaResult = 0.00;
                        $gradeResult = 'F';
                    }

                    $subjectData[$examName][$subExamName] = [
                        'components' => $components,
                        'total' => $currentTotal,
                        'possible_marks' => $currentPossible,
                        'gpa' => $gpaResult,
                        'lg' => $gradeResult,
                        'is_subject_failed_this_sub_exam' => $isSubjectFailedForThisSubExam,
                    ];
                    if (!isset($groupCalc[$groupKey]['exam_types'][$examFlatKey])) {
                        $groupCalc[$groupKey]['exam_types'][$examFlatKey] = [
                            'total_marks_obtained' => 0,
                            'total_converted_marks' => 0,
                            'gpa_sum_for_group_avg' => 0,
                            'subject_instances_for_gpa_avg' => 0,
                            'has_failed_subject_in_group' => false,
                        ];
                    }

                    $groupCalc[$groupKey]['exam_types'][$examFlatKey]['total_marks_obtained'] += $currentTotal;
                    $groupCalc[$groupKey]['exam_types'][$examFlatKey]['total_converted_marks'] += $currentConverted;
                    $groupCalc[$groupKey]['exam_types'][$examFlatKey]['gpa_sum_for_group_avg'] += $gpaResult;
                    $groupCalc[$groupKey]['exam_types'][$examFlatKey]['subject_instances_for_gpa_avg'] += 1;
                    if ($isSubjectFailedForThisSubExam) {
                        $groupCalc[$groupKey]['exam_types'][$examFlatKey]['has_failed_subject_in_group'] = true;
                    }

                    $subjectTotalAllExam += $currentTotal;
                    $totalPossAllExam += $currentPossible;
                }
                if (!$subject->subject_group) {
                    $groupCounter++;
                }

                if (!isset($studentData['subjects'][$groupKey])) {
                    $studentData['subjects'][$groupKey] = [
                        'has_group' => (bool) $subject->subject_group,
                        'group_totals' => [
                            'exam_count' => 0,
                        ],
                        'final_exam_totals' => [],
                        'group_data' => [],
                    ];
                }
                $studentData['subjects'][$groupKey]['group_data'][] = [
                    'subject_name' => $subject->subject_name,
                    'subject_mark' => $subject->subject_mark,
                    'subject_group' => $subject->subject_group,
                    'data' => $subjectData,
                    'subject_id' => $subjectId,
                ];

                $studentData['sub_total'] += $subjectTotalAllExam;
                $studentData['total_possible_marks'] += $totalPossAllExam;
            }

            foreach ($groupCalc as $gKey => $groupInfo) {
                $numSubjectsInGroup = count($groupInfo['unique_subject_ids']);
                $studentData['subjects'][$gKey]['final_exam_totals']['subject_count'] = $numSubjectsInGroup;

                $hasGroupFailedOverall = false;

                foreach ($groupInfo['exam_types'] as $examFlatKey => $info) {
                    $obtained = $info['total_marks_obtained'];
                    $convertedSum = $info['total_converted_marks'];
                    $hasFailedSubjectInGroup = $info['has_failed_subject_in_group'];
                    $percentage = $convertedSum > 0 ? ($obtained / $convertedSum) * 100 : 0;
                    $lg = $this->calculateLetterGrade($percentage);
                    $gpa = $this->calculateGPA($lg);
                    if ($hasFailedSubjectInGroup) {
                        $gpa = 0.00;
                        $lg = 'F';
                        $hasGroupFailedOverall = true;
                    }

                    $exTotalKey = $examFlatKey;
                    if (!in_array($exTotalKey, $examMarkKeys)) {
                        $examMarkKeys[] = $exTotalKey;
                    }
                    $exGpaKey = $examFlatKey;
                    if (!in_array($exGpaKey, $examMarkKeys)) {
                        $examMarkKeys[] = $exGpaKey;
                    }
                    $exLgKey = $examFlatKey;
                    if (!in_array($exLgKey, $examMarkKeys)) {
                        $examMarkKeys[] = $exLgKey;
                    }
                    $exSubjectCountKey = $examFlatKey;
                    if (!in_array($exSubjectCountKey, $examMarkKeys)) {
                        $examMarkKeys[] = $exSubjectCountKey;
                    }
                    $exExamMarkKey = $examFlatKey;
                    if (!in_array($exExamMarkKey, $examMarkKeys)) {
                        $examMarkKeys[] = $exExamMarkKey;
                    }
                    $exHasFailKey = $examFlatKey;

                    $studentData['subjects'][$gKey]['group_totals'][$exTotalKey . '_total'] = round($obtained, 2);
                    $studentData['subjects'][$gKey]['group_totals'][$exGpaKey . '_gpa'] = number_format($gpa, 2);
                    $studentData['subjects'][$gKey]['group_totals'][$exLgKey . '_lg'] = $lg;
                    $studentData['subjects'][$gKey]['group_totals'][$exSubjectCountKey . '_subject_count'] = $numSubjectsInGroup;
                    $studentData['subjects'][$gKey]['group_totals'][$exExamMarkKey . '_exam_mark'] = $convertedSum;
                    $studentData['subjects'][$gKey]['group_totals'][$exHasFailKey . '_has_fail'] = $hasFailedSubjectInGroup;

                    $studentData['overall_gpa_sum'] += $gpa;
                    $studentData['overall_gpa_count']++;
                }
                $allExamTotals = [];
                foreach ($studentData['subjects'][$gKey]['group_totals'] as $key => $value) {
                    if (Str::endsWith($key, '_total')) {
                        $allExamTotals[] = $value;
                    }
                }

                $examCount = count($allExamTotals);
                $finalTotal = array_sum($allExamTotals);
                $finalAvg = $examCount > 0 ? $finalTotal / $examCount : 0;
                $finalLg = $this->calculateLetterGrade($finalAvg);
                $finalGpa = $this->calculateGPA($finalLg);

                if ($hasGroupFailedOverall) {
                    $finalGpa = 0.00;
                    $finalLg = 'F';
                }

                $studentData['subjects'][$gKey]['final_exam_totals']['final_total'] = round($finalAvg, 2);
                $studentData['subjects'][$gKey]['final_exam_totals']['final_gpa'] = $finalGpa;
                $studentData['subjects'][$gKey]['final_exam_totals']['final_lg'] = $finalLg;

                $studentData['subjects'][$gKey]['final_exam_totals']['subject_count'] = $numSubjectsInGroup;
                $studentData['subjects'][$gKey]['final_exam_totals']['has_fail'] = $hasGroupFailedOverall;
            }

            if ($studentData['total_possible_marks'] > 0) {
                $studentData['percentage'] = ($studentData['sub_total'] / $studentData['total_possible_marks']) * 100;
            }
            $studentData['overall_gpa'] = $studentData['overall_gpa_count'] > 0 ?
                number_format($studentData['overall_gpa_sum'] / $studentData['overall_gpa_count'], 2) :
                'N/A';
            foreach ($studentData['subjects'] as $gKey => $groupInfo) {
                if (($groupInfo['final_exam_totals']['has_fail'] ?? false) === true) {
                    $studentData['has_overall_fail_status'] = true;
                    break;
                }
            }

            if ($studentData['has_overall_fail_status']) {
                $studentData['overall_gpa'] = '0.00';
                $studentData['overall_lg'] = 'F';
            } elseif ($studentData['overall_gpa'] !== 'N/A') {
                $studentData['overall_lg'] = $this->calculateLetterGrade((float) $studentData['overall_gpa'] * 20);
                $studentData['overall_lg'] = 'N/A';
            }
            $this->calculateSubExamWiseSummary($studentData, $examMarkKeys);
            // dd($studentData);
            return $studentData;
        })->values();
    }


    private function calculateSubExamWiseSummary(array &$studentData, $examMarkKeys): void
    {
        $studentData['total_info'] = [];
        $subjectCount = count($studentData['subjects']);
        $isPass = [];

        foreach ($studentData['subjects'] as $gKey => $groupInfo) {
            foreach ($examMarkKeys as $exMarkKey) {
                if (!isset($isPass[$exMarkKey])) {
                    $isPass[$exMarkKey] = true;
                }
                if (!isset($studentData['total_info'][$exMarkKey . '_total'])) {
                    $studentData['total_info'][$exMarkKey . '_total'] = 0;
                }

                $studentData['total_info'][$exMarkKey . '_total'] += (float) $groupInfo['group_totals'][$exMarkKey . '_total'];

                if (!isset($studentData['total_info'][$exMarkKey . '_gpa'])) {
                    $studentData['total_info'][$exMarkKey . '_gpa'] = 0;
                }

                $studentData['total_info'][$exMarkKey . '_gpa'] += (float) $groupInfo['group_totals'][$exMarkKey . '_gpa'];

                if ($isPass[$exMarkKey] == true && $groupInfo['group_totals'][$exMarkKey . '_lg'] == 'F') {
                    $isPass[$exMarkKey] = false;
                }
            }
        }

        foreach ($examMarkKeys as $exMarkKey) {
            $studentData['total_info'][$exMarkKey . '_gpa'] = $isPass[$exMarkKey] == true ? $studentData['total_info'][$exMarkKey . '_gpa'] / $subjectCount : '0.00';
            $studentData['total_info'][$exMarkKey . '_lg'] = $this->calculateLetterGradeFromGPA($studentData['total_info'][$exMarkKey . '_gpa']);
        }
    }

    private function fetchTotalStudents($classID, $sessionID, $departmentID, $sectionID)
    {
        return Student::where('class_id', $classID)
            ->where('session_year_id', $sessionID)
            ->where('department_id', $departmentID)
            ->where('section_id', $sectionID)
            ->where('status', 1)
            ->count();
    }

    private function calculateMeritMarks($classID, $sessionID, $departmentID, $examID, $examTypeId)
    {
        $subjectPriority = ['Mathematic', 'English 1st Paper', 'English 2nd Paper', 'English'];

        return ManageMark::where('class_id', $classID)
            ->where('session_year_id', $sessionID)
            ->where('department_id', $departmentID)
            ->when($examID, fn($q) => $q->where('exam_id', $examID))
            ->when($examTypeId, fn($q) => $q->where('exam_type_id', $examTypeId))
            ->get()
            ->groupBy('student_id')
            ->map(function ($rows) use ($subjectPriority) {
                $totalMark = 0;
                $subjectMarks = [];

                // Sum total marks (all components in result_mark)
                foreach ($rows as $r) {
                    $marksArray = json_decode($r->result_mark, true);
                    $subjectName = $r->subject_name ?? null;

                    $subjectTotal = array_sum($marksArray);

                    $totalMark += $subjectTotal;

                    // Store marks for priority subjects only
                    if ($subjectName && in_array($subjectName, $subjectPriority)) {
                        $subjectMarks[$subjectName] = $subjectTotal;
                    }
                }

                // Ensure all priority subjects have a value (0 if missing)
                foreach ($subjectPriority as $sub) {
                    if (!isset($subjectMarks[$sub])) {
                        $subjectMarks[$sub] = 0;
                    }
                }

                $avgGpa = round($rows->avg('cgpa'), 2);

                return [
                    'student_id' => $rows->first()->student_id,
                    'cgpa' => $avgGpa,
                    'merit_mark' => $totalMark,
                    'subject_marks' => $subjectMarks,
                ];
            })
            ->sort(function ($a, $b) use ($subjectPriority) {
                // Sort by cgpa desc
                if ($a['cgpa'] !== $b['cgpa']) {
                    return $b['cgpa'] <=> $a['cgpa'];
                }
                // Then by merit_mark (sub_total) desc
                if ($a['merit_mark'] !== $b['merit_mark']) {
                    return $b['merit_mark'] <=> $a['merit_mark'];
                }
                // Then by subject marks in priority order desc
                foreach ($subjectPriority as $sub) {
                    if ($a['subject_marks'][$sub] !== $b['subject_marks'][$sub]) {
                        return $b['subject_marks'][$sub] <=> $a['subject_marks'][$sub];
                    }
                }
                return 0; // tie
            })
            ->values();
    }

    private function rankStudents($meritMarks)
    {
        $position = 1;
        $prevGpa = null;
        $prevMark = null;
        $prevSubjectMarks = [];
        $subjectPriority = ['Mathematic', 'English 1st Paper', 'English 2nd Paper', 'English'];

        return $meritMarks->map(function ($st, $idx) use (&$position, &$prevGpa, &$prevMark, &$prevSubjectMarks, $subjectPriority) {

            $isNewPosition = false;

            if ($prevGpa === null) {
                $isNewPosition = true;
            } else if ($st['cgpa'] < $prevGpa) {
                $isNewPosition = true;
            } else if ($st['cgpa'] == $prevGpa) {
                if ($st['merit_mark'] < $prevMark) {
                    $isNewPosition = true;
                } else if ($st['merit_mark'] == $prevMark) {
                    // Compare subject marks by priority
                    foreach ($subjectPriority as $sub) {
                        if ($st['subject_marks'][$sub] < $prevSubjectMarks[$sub]) {
                            $isNewPosition = true;
                            break;
                        } else if ($st['subject_marks'][$sub] > $prevSubjectMarks[$sub]) {
                            break;
                        }
                    }
                }
            }

            if ($isNewPosition) {
                $position = $idx + 1;
            }

            $prevGpa = $st['cgpa'];
            $prevMark = $st['merit_mark'];
            $prevSubjectMarks = $st['subject_marks'];

            $st['position'] = $position;
            return $st;
        });
    }

    private function calculateQrSummary($student, $examCount)
    {
        $totalMarks = 0;
        $totalGPA = 0;
        $subjectCount = 0;

        foreach ($student['subjects'] as $subject) {
            $subjectTotal = 0;
            $subjectGPA = 0;

            if (isset($subject['group_data']) && is_array($subject['group_data'])) {
                foreach ($subject['group_data'] as $group) {
                    if (isset($group['data']) && is_array($group['data'])) {
                        foreach ($group['data'] as $exam) {
                            if (is_array($exam)) {
                                foreach ($exam as $examName => $subExam) {
                                    if (isset($subExam['total'])) {
                                        $subjectTotal += $subExam['total'];
                                    }

                                    if (isset($subExam['gpa'])) {
                                        $subjectGPA += floatval($subExam['gpa']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $subjectAverage = $examCount > 0 ? ($subjectTotal / $examCount) : 0;
            $subjectGPA = $examCount > 0 ? ($subjectGPA / $examCount) : 0;
            $finalLG = $this->calculateLetterGradeFromGPA($subjectGPA);

            $totalMarks += $subjectAverage;
            $totalGPA += $subjectGPA;
            $subjectCount++;
        }

        $finalGPA = $subjectCount > 0 ? round($totalGPA / $subjectCount, 2) : 0.00;
        $finalLG = $this->calculateLetterGradeFromGPA($finalGPA);

        return [
            'total_marks' => round($totalMarks),
            'gpa' => $finalGPA,
            'lg' => $finalLG
        ];
    }

    private function generateQrCode($student, $examCount)
    {
        $summary = $this->calculateQrSummary($student, $examCount);
        return QrCode::size(100)->generate(
            "TMSS Public School & College\n" .
            "Address: Joypurpara, Bogura-5800\n" .
            "Tel: +880 1313 367425\n" .
            "Email: tmsstpsc1@gmail.com\n" .
            "Note: Academic Transcript\n" .
            "--------------------------\n" .
            "Student Name: " . ($student['student_name'] ?? 'N/A') . "\n" .
            "Roll: " . ($student['merit_roll'] ?? 'N/A') . "\n" .
            "Class: " . ($student['class_name'] ?? 'N/A') . "\n" .
            "Section: " . ($student['section_name'] ?? 'N/A') . "\n" .
            "Session: " . ($student['session_title'] ?? 'N/A') . "\n" .
            "Department: " . ($student['department_name'] ?? 'N/A') . "\n" .

            "Total Marks: " . $summary['total_marks'] . "\n" .
            "GPA: " . $summary['gpa'] . "\n" .
            "LG: " . $summary['lg'] . "\n" .
            "Position: " . ($student['merit_position'] ?? 'N/A')
        );
    }

    private function calculateLetterGrade($percentage)
    {
        if ($percentage >= 80) {
            return 'A+';
        } elseif ($percentage >= 70) {
            return 'A';
        } elseif ($percentage >= 60) {
            return 'A-';
        } elseif ($percentage >= 50) {
            return 'B';
        } elseif ($percentage >= 40) {
            return 'C';
        } else {
            return 'F';
        }
    }

    private function calculateGPA($grade)
    {
        switch ($grade) {
            case 'A+':
                return 5.00;
            case 'A':
                return 4.00;
            case 'A-':
                return 3.50;
            case 'B':
                return 3.00;
            case 'C':
                return 2.00;
            default:
                return 0.00;
        }
    }

    private function calculateLetterGradeFromGPA($gpa)
    {
        if ($gpa >= 5.00) {
            return 'A+';
        } elseif ($gpa >= 4.00) {
            return 'A';
        } elseif ($gpa >= 3.50) {
            return 'A-';
        } elseif ($gpa >= 3.00) {
            return 'B';
        } elseif ($gpa >= 2.00) {
            return 'C';
        } else {
            return 'F';
        }
    }


    // first function without overall GPA calculation
    // private function processStudentData($data, $examID, $examTypeId, $examNames, $examTypes)
    // {
    //     return $data->groupBy('student_id')->map(function ($marks) use ($examID, $examTypeId, $examNames, $examTypes) {
    //         $student = $marks->first();
    //         $studentData = [
    //             'student_name' => $student->student_name_en,
    //             'student_photo' => $student->photo,
    //             'student_id' => $student->student_id,
    //             'student_roll' => $student->student_roll,
    //             'class_name' => $student->class_name,
    //             'session_title' => $student->session_title,
    //             'department_name' => $student->department_name,
    //             'section_name' => $student->section_name,
    //             'merit_roll' => $student->merit_roll,
    //             'cgpa' => $student->cgpa,
    //             'subjects' => [],
    //             'sub_total' => 0,
    //             'percentage' => 0,
    //             'total_possible_marks' => 0,
    //         ];

    //         $groupCalc = [];
    //         $groupCounter = 1;

    //         foreach ($marks->groupBy('subject_id') as $subjectId => $subjectMarks) {
    //             $subject = $subjectMarks->first();
    //             $subjectData = [];
    //             $subjectTotalAllExam = 0;
    //             $totalPossAllExam = 0;

    //             foreach ($subjectMarks as $subjectMark) {
    //                 if ($examID && $subjectMark->exam_id != $examID)
    //                     continue;
    //                 if ($examTypeId && $subjectMark->exam_type_id != $examTypeId)
    //                     continue;

    //                 $examName = $examNames[$subjectMark->exam_id]->name ?? 'UnknownExam';
    //                 $subExamName = $examTypes[$subjectMark->exam_type_id]->type_name ?? 'UnknownType';
    //                 $examFlatKey = preg_replace('/[\s\-]+/', '', $examName . $subExamName);

    //                 $componentNames = json_decode($subjectMark->component_name, true) ?? [];
    //                 $resultMarks = json_decode($subjectMark->result_mark, true) ?? [];
    //                 $totalMarks = json_decode($subjectMark->total_mark, true) ?? [];
    //                 $convertedMarks = json_decode($subjectMark->convert_num, true) ?? [];
    //                 $examMarks = json_decode($subjectMark->exam_mark, true) ?? [];
    //                 $passMarks = json_decode($subjectMark->pass_mark, true) ?? [];

    //                 $gpaResult = $subjectMark->cgpa;
    //                 $gradeResult = $subjectMark->grade;
    //                 $resultStatus = $subjectMark->result_status;
    //                 $subjectTotalMark = $subjectMark->subject_total_mark;

    //                 $currentTotal = 0;
    //                 $currentConverted = 0;
    //                 $currentPossible = 0;
    //                 $components = [];

    //                 foreach ($componentNames as $i => $component) {
    //                     $mark = $resultMarks[$i] ?? 0;
    //                     $converted = $convertedMarks[$i] ?? 0;
    //                     $possible = $totalMarks[$i] ?? 0;

    //                     $components[] = [
    //                         'name' => $component,
    //                         'mark' => $mark,
    //                         'lg' => $gpaResult,
    //                         'gpa' => $gradeResult,
    //                         'pass_mark' => $passMarks[$i] ?? 0,
    //                         'result_status' => $resultStatus[$i] ?? 0,
    //                         'converted_mark' => $converted,
    //                         'exam_mark' => $examMarks[$i] ?? 0,
    //                         'total_mark' => $possible,
    //                         'subject_total_mark' => $subjectTotalMark,
    //                     ];

    //                     $currentTotal += $mark;
    //                     $currentConverted += $converted;
    //                     $currentPossible += $possible;
    //                 }

    //                 $subjectData[$examName][$subExamName] = [
    //                     'components' => $components,
    //                     'total' => $currentTotal,
    //                     'possible_marks' => $currentPossible,
    //                 ];

    //                 $groupKey = $subject->subject_group ? 'ag_' . $subject->subject_group : 'gp_tmp' . $groupCounter;

    //                 if (!isset($groupCalc[$groupKey][$examFlatKey])) {
    //                     $groupCalc[$groupKey][$examFlatKey] = [
    //                         'total' => 0,
    //                         'converted' => 0,
    //                         'subject_count' => 0,
    //                     ];
    //                 }

    //                 $groupCalc[$groupKey][$examFlatKey]['total'] += $currentTotal;
    //                 $groupCalc[$groupKey][$examFlatKey]['converted'] += $currentConverted;
    //                 $groupCalc[$groupKey][$examFlatKey]['subject_count'] += 1;

    //                 $subjectTotalAllExam += $currentTotal;
    //                 $totalPossAllExam += $currentPossible;
    //             }

    //             if (!isset($studentData['subjects'][$groupKey])) {
    //                 $studentData['subjects'][$groupKey] = [
    //                     'has_group' => (bool) $subject->subject_group,
    //                     'group_totals' => [],
    //                     'final_exam_totals' => [],
    //                     'group_data' => [],
    //                 ];
    //             }

    //             $studentData['subjects'][$groupKey]['group_data'][] = [
    //                 'subject_name' => $subject->subject_name,
    //                 'subject_mark' => $subject->subject_mark,
    //                 'subject_group' => $subject->subject_group,
    //                 'data' => $subjectData,
    //             ];

    //             $studentData['sub_total'] += $subjectTotalAllExam;
    //             $studentData['total_possible_marks'] += $totalPossAllExam;
    //         }

    //         foreach ($groupCalc as $gKey => $examBundle) {
    //             foreach ($examBundle as $examFlatKey => $info) {
    //                 $obtained = $info['total'];
    //                 $convSum = $info['converted'];
    //                 $subjectCnt = $info['subject_count'];

    //                 $percentage = $convSum > 0 ? ($obtained / $convSum) * 100 : 0;
    //                 $markOutOf80 = ($percentage / 100) * 80;

    //                 $lg = $this->calculateLetterGrade($percentage);
    //                 $gpa = $this->calculateGPA($lg);

    //                 $studentData['subjects'][$gKey]['group_totals'][$examFlatKey . '_total'] = round($obtained, 2);
    //                 $studentData['subjects'][$gKey]['group_totals'][$examFlatKey . '_gpa'] = number_format($gpa, 2);
    //                 $studentData['subjects'][$gKey]['group_totals'][$examFlatKey . '_lg'] = $lg;
    //                 $studentData['subjects'][$gKey]['group_totals'][$examFlatKey . '_subject_count'] = $subjectCnt;
    //                 $studentData['subjects'][$gKey]['group_totals'][$examFlatKey . '_exam_mark'] = $convSum;
    //             }
    //             $allExamTotals = [];
    //             foreach ($studentData['subjects'][$gKey]['group_totals'] as $key => $value) {
    //                 if (Str::endsWith($key, '_total')) {
    //                     $allExamTotals[] = $value;
    //                 }
    //             }

    //             $examCount = count($allExamTotals);
    //             $finalTotal = array_sum($allExamTotals);
    //             $finalAvg = $examCount > 0 ? $finalTotal / $examCount : 0;
    //             // Assuming 2 exams for final calculation
    //             $avgTotal = $examCount > 0 ? $finalTotal / 2 : 0;
    //             $finalLg = $this->calculateLetterGrade($finalAvg);
    //             $finalGpa = $this->calculateGPA($finalLg);

    //             $studentData['subjects'][$gKey]['final_exam_totals'] = [
    //                 'final_total' => round($avgTotal, 2),
    //                 'final_gpa' => $finalGpa,
    //                 'final_lg' => $finalLg,
    //             ];

    //             foreach ($groupCalc[$gKey] as $flatKey => $info) {
    //                 $studentData['subjects'][$gKey]['final_exam_totals'][$flatKey . '_subject_count'] = $info['subject_count'];
    //             }
    //         }

    //         if ($studentData['total_possible_marks'] > 0) {
    //             $studentData['percentage'] = ($studentData['sub_total'] / $studentData['total_possible_marks']) * 100;
    //         }
    //         $this->calculateSubExamWiseSummary($studentData);
    //         dd($studentData);
    //         return $studentData;
    //     })->values();
    // }
}
