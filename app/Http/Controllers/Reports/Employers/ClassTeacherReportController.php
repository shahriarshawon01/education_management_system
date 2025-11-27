<?php

namespace App\Http\Controllers\Reports\Employers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassTeacherReportController extends Controller
{
    public function showReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $classId = $request->class_id;
        $statusId = $request->status;

        $data = DB::table('subjects')
            ->leftJoin('schools', 'subjects.school_id', '=', 'schools.id')
            ->leftJoin('teachers', 'subjects.teacher_id', '=', 'teachers.id')
            ->leftJoin('student_classes', 'subjects.class_id', '=', 'student_classes.id')
            ->selectRaw(
                "subjects.*,
                schools.title as school_name,
                schools.steb_number as school_steb,
                schools.reg_number as registration_no,
                schools.institute_code as institute_code,
                schools.address as school_address,
                teachers.name as teacher_name,
                teachers.employee_id as teacher_id,
                student_classes.name as class_name",
            )
            ->where('subjects.school_id', $schoolId)
            ->when($classId, function ($query) use ($classId) {
                $query->where('subjects.class_id', $classId);
            })
            ->when(isset($statusId), function ($query) use ($statusId) {
                $query->where('subjects.status', $statusId);
            })
            ->get();

        // if ($data->isEmpty()) {
        //     return returnData(4004, null, 'No records found for the specified criteria.');
        // }

        $single = $data->first();

        $responseData = [
            'data' => $data,
            'school_name' => $single ? $single->school_name : '',
            'registration_no' => $single ? $single->registration_no : '',
            'school_steb' => $single ? $single->school_steb : '',
            'school_address' => $single ? $single->school_address : '',
        ];

        return returnData(2000, $responseData,);
    }
}
