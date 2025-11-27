<?php

namespace App\Http\Controllers\Reports\Dormitory;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DormitoryReportController extends Controller
{

public function showReport(Request $request)
{
    $schoolId = auth()->user()->school_id;
    $dormitoryId = $request->dormitory_id;
    $statusId = $request->status;


    $data = DB::table('assign_dormitories')
        ->leftJoin('schools', 'assign_dormitories.school_id', '=', 'schools.id')
        ->leftJoin('manage_dormitories', 'assign_dormitories.dormitory_id', '=', 'manage_dormitories.id')
        ->where('assign_dormitories.school_id', $schoolId)
        ->when($dormitoryId, fn($q) => $q->where('assign_dormitories.dormitory_id', $dormitoryId))
        ->when($statusId, fn($q) => $q->where('assign_dormitories.status', $statusId))
        ->get();

    if ($data->isEmpty()) {
        return returnData(404, null, 'No records found for the specified criteria.');
    }

 
    $studentIds = $data->where('dormitory_user_type', 1)->pluck('dormitory_user_id')->toArray();
    $employeeIds = $data->where('dormitory_user_type', 2)->pluck('dormitory_user_id')->toArray();

   
    $students = DB::table('students')->whereIn('id', $studentIds)->get()->keyBy('id');
    $employees = DB::table('employees')->whereIn('id', $employeeIds)->get()->keyBy('id');


    $employeeDepartments = DB::table('employee_departments as ed')
        ->join('departments as d', 'ed.id', '=', 'd.id')
        ->whereIn('ed.id', $employeeIds)
        ->select('ed.id', 'd.department_name')
        ->get()
        ->groupBy('id');


    foreach ($data as $item) {
        $item->dormitory_user = null;

        if ($item->dormitory_user_type == 1 && isset($students[$item->dormitory_user_id])) {
            $user = $students[$item->dormitory_user_id];

            $departmentName = $user->department_id 
                ? DB::table('departments')->where('id', $user->department_id)->value('department_name')
                : null;

            $item->dormitory_user = [
                'type' => 'student',
                'student_roll' => $user->student_roll,
                'student_name_en' => $user->student_name_en,
                'student_phone' => $user->student_phone,
                'department_name' => $departmentName,
            ];

        } elseif ($item->dormitory_user_type == 2 && isset($employees[$item->dormitory_user_id])) {
            $user = $employees[$item->dormitory_user_id];

             $departmentName = $user->department_id 
                ? DB::table('employee_departments')->where('id', $user->department_id)->value('name')
                : null;

            $item->dormitory_user = [
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