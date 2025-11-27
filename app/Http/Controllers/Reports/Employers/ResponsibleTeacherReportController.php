<?php

namespace App\Http\Controllers\Reports\Employers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResponsibleTeacherReportController extends Controller
{
    public function showReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $classId = $request->class_id;
        $statusId = $request->status;
        $teacherId = $request->teacher_id;

        $data = DB::table('students')
            ->leftJoin('schools', 'students.school_id', '=', 'schools.id')
            ->leftJoin('teachers', 'students.responsible_teacher_id', '=', 'teachers.id')
            ->leftJoin('departments', 'students.department_id', '=', 'departments.id')
            ->selectRaw(
                "students.*,
                schools.title as school_name,
                schools.steb_number as school_steb,
                schools.reg_number as registration_no,
                schools.institute_code as institute_code,
                schools.address as school_address,
                teachers.name as teacher_name,
                teachers.employee_id as teacher_id,
                departments.department_name as department_name,
                departments.department_code as department_code",
            )
            ->where('students.school_id', $schoolId)
            ->when($classId, function ($query) use ($classId) {
                $query->where('students.class_id', $classId);
            })
            ->when($teacherId, function ($query) use ($teacherId) {
                $query->where('students.responsible_teacher_id', $teacherId);
            })
            ->when(isset($statusId), function ($query) use ($statusId) {
                $query->where('students.status', $statusId);
            })
            ->get();

        if ($data->isEmpty()) {
            return returnData(5000, null, 'No records found for the specified criteria.');
        }

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
