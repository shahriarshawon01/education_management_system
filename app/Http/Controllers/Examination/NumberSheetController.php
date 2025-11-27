<?php

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\SessionYear;
use App\Models\StudentClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Examination;

class NumberSheetController extends Controller
{
    public function getStudentNumberSheet()
    {
        $session_id = request('session_id');
        $class_id = request('class_id');
        $section_id = request('section_id');
        $departmentId = request('department_id');
        $examTypeId = request('exam_type_id');

        $students = DB::table('students')
            ->select('students.id', 'students.student_name_en as student_name', 'students.student_roll as student_roll', 'students.merit_roll as class_roll')
            ->where('students.session_year_id', $session_id)
            ->where('students.class_id', $class_id)
            ->where('students.section_id', $section_id)
            ->where('students.department_id', $departmentId)
            ->where('students.status', 1)
            ->orderBy('merit_roll', 'asc')
            ->get();

        // $singleData = $students->first();
        // $teacher_name = $singleData ? $singleData->school_name : '';
        $examName = Examination::where('id', $examTypeId)->select('id', 'type_name', 'exam_id')->first();
        $sessionName = SessionYear::where('id', $session_id)->select('id', 'title as session_name')->first();
        $className = StudentClass::where('id', $class_id)->select('id', 'name as class_name')->first();
        $sectionName = Section::where('id', $section_id)->select('id', 'name as section_name')->first();

        $multipleData = [
            'data' => $students,
            'examName' => $examName,
            'sessionName' => $sessionName,
            'className' => $className,
            'sectionName' => $sectionName,
            // 'school_name' => $school_name,

        ];

        return returnData(2000, $multipleData);
    }
}
