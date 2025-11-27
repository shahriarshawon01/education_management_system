<?php

namespace App\Http\Controllers\Reports\Employers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffReportController extends Controller
{
    public function showReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $departmentId = $request->department_id;
        $designationId = $request->designation_id;
        $gender = $request->gender_id;
        $jobStatus = $request->jod_status;

        $data = DB::table('staff')
            ->leftJoin('schools', 'staff.school_id', '=', 'schools.id')
            ->leftJoin('work_departments', 'staff.department_id', '=', 'work_departments.id')
            ->leftJoin('designations', 'staff.designation_id', '=', 'designations.id')
            ->selectRaw(
                "staff.*,
                schools.title as school_name,
                schools.steb_number as school_steb,
                schools.reg_number as registration_no,
                schools.institute_code as institute_code,
                schools.address as school_address,
                work_departments.name as department_name,
                designations.name as designation_name"
            )
            ->where('staff.school_id', $schoolId)
            ->when($departmentId, function ($query) use ($departmentId) {
                $query->where('staff.department_id', $departmentId);
            })
            ->when($designationId, function ($query) use ($designationId) {
                $query->where('staff.designation_id', $designationId);
            })
            ->when($gender, function ($query) use ($gender) {
                $query->where('staff.gender', $gender);
            })
            ->when(isset ($jobStatus), function ($query) use ($jobStatus) {
                $query->where('staff.status', $jobStatus);
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
