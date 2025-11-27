<?php

namespace App\Http\Controllers;

use App\Exports\TeacherDataExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExcelReportExportController extends Controller
{

    public function teacherExcel()
    {

        $schoolId = input('school_id');
        $departmentId = input('department_id');
        $designationId = input('designation_id');
        $gender = input('gender_id');
        $jobStatus = input('job_status');

        $data = DB::table('employees')
            ->leftJoin('schools', 'employees.school_id', '=', 'schools.id')
            ->leftJoin('employee_departments', 'employees.department_id', '=', 'employee_departments.id')
            ->leftJoin('employee_designations', 'employees.designation_id', '=', 'employee_designations.id')
            ->selectRaw(
                "employees.*,
                schools.title as school_name,
                schools.steb_number as school_steb,
                schools.reg_number as registration_no,
                schools.institute_code as institute_code,
                schools.address as school_address,
                employee_departments.name as department_name,
                employee_designations.name as designation_name"
            )
            ->where('employees.school_id', $schoolId)
            ->when($departmentId, function ($query) use ($departmentId) {
                $query->where('employees.department_id', $departmentId);
            })
            ->when($designationId, function ($query) use ($designationId) {
                $query->where('employees.designation_id', $designationId);
            })
            ->when($gender, function ($query) use ($gender) {
                $query->where('employees.gender', $gender);
            })
            ->when(isset($jobStatus), function ($query) use ($jobStatus) {
                $query->where('employees.status', $jobStatus);
            })
            ->get();

        $export = new TeacherDataExport($data);

        return Excel::download($export, 'teacherExcel.xlsx');
    }
}
