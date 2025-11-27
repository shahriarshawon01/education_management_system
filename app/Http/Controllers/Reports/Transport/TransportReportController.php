<?php

namespace App\Http\Controllers\Reports\Transport;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TransportReportController extends Controller
{

public function showReport(Request $request)
{
    $schoolId = auth()->user()->school_id;
    $dormitoryId = $request->transport_id;
    $statusId = $request->status;


    $data = DB::table('assign_transports')
        ->leftJoin('schools', 'assign_transports.school_id', '=', 'schools.id')
        ->leftJoin('transport_manages', 'assign_transports.transport_id', '=', 'transport_manages.id')
        ->where('assign_transports.school_id', $schoolId)
        ->when($dormitoryId, fn($q) => $q->where('assign_transports.transport_id', $dormitoryId))
        ->when($statusId, fn($q) => $q->where('assign_transports.status', $statusId))
        ->get();

    if ($data->isEmpty()) {
        return returnData(404, null, 'No records found for the specified criteria.');
    }

 
    $studentIds = $data->where('transport_user_type', 1)->pluck('transport_user_id')->toArray();
    $employeeIds = $data->where('transport_user_type', 2)->pluck('transport_user_id')->toArray();

   
    $students = DB::table('students')->whereIn('id', $studentIds)->get()->keyBy('id');
    $employees = DB::table('employees')->whereIn('id', $employeeIds)->get()->keyBy('id');


    $employeeDepartments = DB::table('employee_departments as ed')
        ->join('departments as d', 'ed.id', '=', 'd.id')
        ->whereIn('ed.id', $employeeIds)
        ->select('ed.id', 'd.department_name')
        ->get()
        ->groupBy('id');


    foreach ($data as $item) {
        $item->transport_user = null;

        if ($item->transport_user_type == 1 && isset($students[$item->transport_user_id])) {
            $user = $students[$item->transport_user_id];

            $departmentName = $user->department_id 
                ? DB::table('departments')->where('id', $user->department_id)->value('department_name')
                : null;

            $item->transport_user = [
                'type' => 'student',
                'student_roll' => $user->student_roll,
                'student_name_en' => $user->student_name_en,
                'student_phone' => $user->student_phone,
                'department_name' => $departmentName,
            ];

        } elseif ($item->transport_user_type == 2 && isset($employees[$item->transport_user_id])) {
            $user = $employees[$item->transport_user_id];

             $departmentName = $user->department_id 
                ? DB::table('employee_departments')->where('id', $user->department_id)->value('name')
                : null;

            $item->transport_user = [
                'type' => 'employee',
                'employee_id' => $user->employee_id,
                'name' => $user->name,
                'phone' => $user->phone,
                'department_name' => $departmentName,
            ];
        }
    }

    $single = $data->first();

    $responseData = [
        'data' => $data,
        'school_name' => $single->school_name ?? '',
        'registration_no' => $single->registration_no ?? '',
        'school_step' => $single->school_step ?? '',
        'school_address' => $single->school_address ?? '',
    ];

    return returnData(2000, $responseData);
}


}