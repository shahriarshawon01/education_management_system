<?php

namespace App\Http\Controllers\Examination;

use App\Models\Examination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdmitCardSeatPlanController extends Controller
{
    public function getAdmitCardSeatPlan(Request $request)
    {
        $sessionId = $request->session_id;
        $classID = $request->class_id;
        $sectionID = $request->section_id;
        // $departmentId = $request->department_id;
        $examTypeId = $request->exam_type_id;
        $generateType = $request->generate_type;
        $genderId = $request->gender_id;

        $data = DB::table('students')
            ->leftJoin('session_years', 'students.session_year_id', '=', 'session_years.id')
            ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('departments', 'students.department_id', '=', 'departments.id')
            ->leftJoin('schools', 'students.school_id', '=', 'schools.id')
            ->selectRaw(
                "students.*,
            session_years.title as session_name,
            student_classes.name as class_name,
            sections.name as section_name,
            departments.department_name as department_name,
            schools.title as school_name,
            schools.address as school_address",
            )
            ->when($classID, function ($query) use ($classID) {
                $query->where('students.class_id', $classID);
            })
            ->when($sectionID, function ($query) use ($sectionID) {
                $query->where('students.section_id', $sectionID);
            })
            ->when($sessionId, function ($query) use ($sessionId) {
                $query->where('students.session_year_id', $sessionId);
            })
            // ->when($departmentId, function ($query) use ($departmentId) {
            //     $query->where('students.department_id', $departmentId);
            // })
            ->when($genderId, function ($query) use ($genderId) {
                $query->where('students.gender', $genderId);
            })
            ->where('students.status', 1)
            ->orderBy('merit_roll', 'asc')
            ->get();

        $examName = Examination::where('id', $examTypeId)->select('id', 'type_name', 'exam_id')->first();
        $school_info = $data->first();

        $responseData = [
            'data' => $data,
            'generateType' => $generateType,
            'examName' => $examName,
            'school_name' => $school_info ? $school_info->school_name : '',
            'school_address' => $school_info ? $school_info->school_address : '',
        ];
        return returnData(2000, $responseData);
    }
}
