<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\ManageMark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageMarkController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new ManageMark();
    }

    public function index()
    {
    }

    public function create()
    {
        //
    }
    // public function store(Request $request)
    // {
    //     $schoolId = auth()->user()->school_id;
    //     try {
    //         $input = $request->all();
    //         foreach ($input as $data) {
    //             if (empty($data['exam_mark_data']) || in_array(null, $data['exam_mark_data'], true) || in_array(null, $data['result_mark'], true)) {
    //                 continue;
    //             }

    //             $conditions = [
    //                 'class_id' => $data['class_id'],
    //                 'session_year_id' => $data['session_year_id'],
    //                 'student_id' => $data['student_id'],
    //                 'exam_id' => $data['exam_id'],
    //                 'subject_id' => $data['subject_id'],
    //                 'exam_type_id' => $data['exam_type_id'],
    //                 'department_id' => $data['department_id'],
    //                 'grade_number_id' => $data['grade_number_id'],
    //             ];

    //             $existingMark = ManageMark::where($conditions)->first();

    //             if ($existingMark) {
    //                 $examMarkChanged = json_encode($data['exam_mark_data']) !== $existingMark->exam_mark_data;
    //                 $convertedMarkChanged = json_encode($data['result_mark']) !== $existingMark->result_mark;

    //                 if ($examMarkChanged || $convertedMarkChanged) {
    //                     $existingMark->fill($data);
    //                     $existingMark->exam_mark_data = json_encode($data['exam_mark_data']);
    //                     $existingMark->component_name = json_encode($data['component_name']);
    //                     $existingMark->result_mark = json_encode($data['result_mark']);
    //                     $existingMark->school_id = $schoolId;
    //                     $existingMark->save();
    //                 }
    //             } else {
    //                 $saveData = new ManageMark();
    //                 $saveData->fill($data);
    //                 $saveData->exam_mark_data = json_encode($data['exam_mark_data']);
    //                 $saveData->component_name = json_encode($data['component_name']);
    //                 $saveData->result_mark = json_encode($data['result_mark']);
    //                 $saveData->school_id = $schoolId;
    //                 $saveData->save();
    //             }
    //         }

    //         return returnData(2000, null, 'Successfully Inserted');
    //     } catch (\Exception $exception) {
    //         return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
    //     }
    // }

    // just update for wrong mark data 
    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        try {
            $input = $request->all();

            foreach ($input as $data) {
                $conditions = [
                    'class_id' => $data['class_id'],
                    'session_year_id' => $data['session_year_id'],
                    'student_id' => $data['student_id'],
                    'exam_id' => $data['exam_id'],
                    'subject_id' => $data['subject_id'],
                    'exam_type_id' => $data['exam_type_id'],
                    'department_id' => $data['department_id'],
                    'grade_number_id' => $data['grade_number_id'],
                ];

                $existingMark = ManageMark::where($conditions)->first();

                $isBlank = empty($data['exam_mark_data']) || in_array(null, $data['exam_mark_data'], true) || in_array(null, $data['result_mark'], true);

                if ($existingMark) {
                    if ($isBlank) {
                        // Clear marks
                        $existingMark->delete();
                    } else {
                        // Update marks if changed
                        $examMarkChanged = json_encode($data['exam_mark_data']) !== $existingMark->exam_mark_data;
                        $convertedMarkChanged = json_encode($data['result_mark']) !== $existingMark->result_mark;

                        if ($examMarkChanged || $convertedMarkChanged) {
                            $existingMark->fill($data);
                            $existingMark->exam_mark_data = json_encode($data['exam_mark_data']);
                            $existingMark->component_name = json_encode($data['component_name']);
                            $existingMark->result_mark = json_encode($data['result_mark']);
                            $existingMark->school_id = $schoolId;
                            $existingMark->save();
                        }
                    }
                } else {
                    // Create new record only if valid mark data
                    if (!$isBlank) {
                        $saveData = new ManageMark();
                        $saveData->fill($data);
                        $saveData->exam_mark_data = json_encode($data['exam_mark_data']);
                        $saveData->component_name = json_encode($data['component_name']);
                        $saveData->result_mark = json_encode($data['result_mark']);
                        $saveData->school_id = $schoolId;
                        $saveData->save();
                    }
                }
            }

            return returnData(2000, null, 'Marks successfully Inserted or Updated');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
    public function getSubjectMark()
    {
        $school_id = auth()->user()->school_id;
        $session_id = request('session_year_id');
        $subject_id = request('subject_id');
        $class_id = request('class_id');
        $section_id = request('section_id');
        $departmentId = request('department_id');
        $examId = request('exam_id');
        $examTypeId = request('exam_type_id');

        $subjectData = DB::table('subjects')
            ->leftJoin('student_classes', 'subjects.class_id', '=', 'student_classes.id')
            ->leftJoin('session_years', 'subjects.session_year_id', '=', 'session_years.id')
            ->leftJoin('departments', 'subjects.department_id', '=', 'departments.id')
            ->select(
                'subjects.*',
                'subjects.id as subject_id',
                'student_classes.name as class_name',
                'session_years.title as session_name',
                'departments.department_name as department_name'
            )
            ->where('subjects.school_id', $school_id)
            ->where('subjects.session_year_id', $session_id)
            ->where('subjects.class_id', $class_id)
            ->where('subjects.section_id', $section_id)
            // ->where('subjects.department_id', $departmentId)
            ->where('subjects.id', $subject_id)
            // ->when($departmentId, function ($query, $departmentId) {
            //     return $query->where('subjects.department_id', $departmentId);
            // })
            ->first();

        if (!$subjectData) {
            return returnData(5000, null, 'Subject not found');
        }


        $subjectData = DB::table('subjects')
            ->leftJoin('student_classes', 'subjects.class_id', '=', 'student_classes.id')
            ->leftJoin('session_years', 'subjects.session_year_id', '=', 'session_years.id')
            ->leftJoin('departments', 'subjects.department_id', '=', 'departments.id')
            ->select(
                'subjects.*',
                'subjects.id as subject_id',
                'student_classes.name as class_name',
                'session_years.title as session_name',
                'departments.department_name as department_name'
            )
            ->where('subjects.school_id', $school_id)
            ->where('subjects.session_year_id', $session_id)
            ->where('subjects.class_id', $class_id)
            ->where('subjects.section_id', $section_id)
            ->where('subjects.id', $subject_id)
            ->first();

        if (!$subjectData) {
            return returnData(5000, null, 'Subject not found');
        }

        $markComponents = DB::table('grade_numbers')
            ->select('grade_numbers.*', 'grade_numbers.id as grade_number_id')
            ->where('subject_id', $subject_id)
            ->where('exam_type_id', $examTypeId)
            ->distinct()
            ->get();

        if ($markComponents->isEmpty()) {
            return returnData(5000, null, 'Mark components not found');
        }

        $parsedMarkComponents = [];

        foreach ($markComponents as $markComponent) {
            $markComponent->mark_component = json_decode($markComponent->mark_component, true);
            $markComponent->pass_mark = json_decode($markComponent->pass_mark, true);
            $markComponent->exam_mark = json_decode($markComponent->exam_mark, true);
            $markComponent->convert_num = json_decode($markComponent->convert_num, true);
            $markComponent->total_mark = json_decode($markComponent->total_mark, true);

            $totalSubjectMark = $markComponent->grade_number;
            $examId = $markComponent->exam_id;
            $examTypeId = $markComponent->exam_type_id;

            if (is_array($markComponent->mark_component)) {
                foreach ($markComponent->mark_component as $index => $componentName) {
                    if (!isset($parsedMarkComponents[$componentName])) {
                        $parsedMarkComponents[$componentName] = [
                            'name' => $componentName,
                            'grade_number_id' => $markComponent->grade_number_id,
                            'exam_mark' => $markComponent->exam_mark[$index] ?? null,
                            'convert_num' => $markComponent->convert_num[$index] ?? null,
                            'total_mark' => $markComponent->total_mark[$index] ?? null,
                            'pass_mark' => $markComponent->pass_mark[$index] ?? null,
                            'grade_number' => $totalSubjectMark,
                            'exam_id' => $examId,
                            'exam_type_id' => $examTypeId,
                        ];
                    }
                }
            }
        }

        $parsedMarkComponents = array_values($parsedMarkComponents);

        $existComponentMarks = ManageMark::select('student_id', 'result_mark', 'cgpa', 'grade', 'exam_mark_data')
            ->where('school_id', $school_id)
            ->where('session_year_id', $session_id)
            ->where('class_id', $class_id)
            ->where('subject_id', $subject_id)
            ->where('exam_id', $examId)
            ->where('exam_type_id', $examTypeId)
            ->get()
            ->keyBy('student_id');

        $students = DB::table('students')
            ->select('students.id', 'students.student_name_en as student_name', 'students.student_roll as student_roll', 'students.merit_roll as class_roll')
            ->where('students.school_id', $school_id)
            ->where('students.session_year_id', $session_id)
            ->where('students.class_id', $class_id)
            ->where('students.section_id', $section_id)
            ->where('students.department_id', $departmentId)
            ->where('status', 1)
            ->orderBy('merit_roll', 'asc')
            ->get();

        if ($students->isEmpty()) {
            return returnData(5000, null, 'No students found for this subject');
        }

        $subjectData->student_count = $students->count();
        $subjectData->mark_component = $parsedMarkComponents;
        $subjectData->students = [];

        foreach ($students as $student) {
            $existingMark = $existComponentMarks[$student->id] ?? null;

            $examMarkData = $existingMark ? json_decode($existingMark->exam_mark_data, true) : [];
            $convertedMarkData = $existingMark ? json_decode($existingMark->result_mark, true) : [];

            $marks = [];
            foreach ($parsedMarkComponents as $index => $component) {
                $marks['mark_' . $index] = $examMarkData[$index] ?? '';
                $marks['final_result_mark_' . $index] = $convertedMarkData[$index] ?? '';
            }

            $subjectData->students[] = array_merge([
                'student_id' => $student->id,
                'student_name' => $student->student_name,
                'student_roll' => $student->student_roll,
                'class_roll' => $student->class_roll,
                'cgpa' => $existingMark->cgpa ?? null,
                'grade' => $existingMark->grade ?? null,
            ], $marks);
        }

        return returnData(2000, $subjectData);
    }
}
