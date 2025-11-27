<?php

namespace App\Http\Controllers\Reports\Employers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeReportController extends Controller
{
    public function showReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $departmentId = $request->department_id;
        $designationId = $request->designation_id;
        $employeeType = $request->employee_type;
        $jobStatus = $request->job_status;

        $data = DB::table('employees')
            ->leftJoin('schools', 'employees.school_id', '=', 'schools.id')
            ->leftJoin('employee_departments', 'employees.department_id', '=', 'employee_departments.id')
            ->leftJoin('employee_designations', 'employees.designation_id', '=', 'employee_designations.id')
            ->leftJoin('users','employees.user_id','users.id')
            ->selectRaw(
                "employees.*,
                schools.title as school_name,
                schools.steb_number as steb_number,
                schools.reg_number as registration_no,
                schools.institute_code as institute_code,
                schools.address as school_address,
                employee_departments.name as department_name,
                employee_designations.name as designation_name,
                users.id as user_id,
                users.email"
            )
            ->where('employees.school_id', $schoolId)
            ->when($departmentId, function ($query) use ($departmentId) {
                $query->where('employees.department_id', $departmentId);
            })
            ->when($designationId, function ($query) use ($designationId) {
                $query->where('employees.designation_id', $designationId);
            })
            ->when($employeeType, function ($query) use ($employeeType) {
                $query->where('employees.employee_type', $employeeType);
            })
            ->when($jobStatus !== '' && $jobStatus !== null && $jobStatus !== 'all', function ($query) use ($jobStatus) {
                $query->where('employees.status', $jobStatus);
            })
            ->orderBy('employees.sort_order', 'asc')
            ->get();

        if ($data->isEmpty()) {
            return returnData(5000, null, 'No records found for the specified criteria.');
        }

        $single = $data->first();

        $responseData = [
            'data' => $data,
            'school_name' => $single ? $single->school_name : '',
            'registration_no' => $single ? $single->registration_no : '',
            'steb_number' => $single ? $single->steb_number : '',
            'school_address' => $single ? $single->school_address : '',
        ];

        return returnData(2000, $responseData);
    }
}
