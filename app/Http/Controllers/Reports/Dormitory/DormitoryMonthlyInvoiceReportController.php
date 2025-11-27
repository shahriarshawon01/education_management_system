<?php

namespace App\Http\Controllers\Reports\Dormitory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DormitoryMonthlyInvoiceReportController extends Controller
{
   public function showReport(Request $request)
{
    $schoolId    = auth()->user()->school_id;
    $dormitoryId = $request->dormitory_id;
    $statusId    = $request->status;
    $month       = $request->month;
    $year        = $request->year;
    $dormitoryUserType = $request->user_type;

    $data = DB::table('invoices')
        ->leftJoin('assign_dormitories', 'invoices.invoice_type_id', '=', 'assign_dormitories.dormitory_user_id')
        ->leftJoin('manage_dormitories', 'assign_dormitories.dormitory_id', '=', 'manage_dormitories.id')
        ->leftJoin('students', function ($join) {
            $join->on('assign_dormitories.dormitory_user_id', '=', 'students.id')
                ->where('assign_dormitories.dormitory_user_type', 1);
        })
        ->leftJoin('employees', function ($join) {
            $join->on('assign_dormitories.dormitory_user_id', '=', 'employees.id')
                ->where('assign_dormitories.dormitory_user_type', 2);
        })
        ->leftJoin('schools', 'assign_dormitories.school_id', '=', 'schools.id')
        ->where('invoices.invoice_category', 3) // dormitory invoices only
        ->when($month, fn($q) => $q->whereMonth('invoices.to_date', $month))
        ->when($year, fn($q) => $q->whereYear('invoices.to_date', $year))
        ->when($dormitoryId, fn($q) => $q->where('assign_dormitories.dormitory_id', $dormitoryId))
        ->when($dormitoryUserType, fn($q) => $q->where('invoices.invoice_type', $dormitoryUserType))
        ->when($statusId, fn($q) => $q->where('assign_dormitories.status', $statusId))
        ->select(
            'invoices.id as invoice_id',
            'invoices.invoice_code',
            'invoices.to_date',
            'invoices.month',
            'invoices.total_payable as invoice_amount',
            'manage_dormitories.dormitory_name',
            'assign_dormitories.dormitory_user_type',
            'assign_dormitories.dormitory_user_id',
            'assign_dormitories.seat_number',
            'assign_dormitories.room_number',
            'schools.title as school_name'
        )
        ->get();

    if ($data->isEmpty()) {
        return returnData(404, null, 'No records found for the specified criteria.');
    }


    $studentIds  = $data->where('dormitory_user_type', 1)->pluck('dormitory_user_id')->toArray();
    $employeeIds = $data->where('dormitory_user_type', 2)->pluck('dormitory_user_id')->toArray();

    $students  = DB::table('students')->whereIn('id', $studentIds)->get()->keyBy('id');
    $employees = DB::table('employees')->whereIn('id', $employeeIds)->get()->keyBy('id');

    foreach ($data as $item) {
        $item->dormitory_user = null;
        $item->user_type_name = $item->dormitory_user_type == 1 ? 'Student' : 'Employee';

        if ($item->dormitory_user_type == 1 && isset($students[$item->dormitory_user_id])) {
            $user = $students[$item->dormitory_user_id];
            $departmentName = $user->department_id
                ? DB::table('departments')->where('id', $user->department_id)->value('department_name')
                : null;

            $item->dormitory_user = [
                'type'            => 'student',
                'student_roll'    => $user->student_roll,
                'student_name_en' => $user->student_name_en,
                'student_phone'   => $user->student_phone,
                'department_name' => $departmentName,
                 'merit_roll'   => $user->merit_roll
            ];
        } elseif ($item->dormitory_user_type == 2 && isset($employees[$item->dormitory_user_id])) {
            $user = $employees[$item->dormitory_user_id];
            $departmentName = $user->department_id
                ? DB::table('employee_departments')->where('id', $user->department_id)->value('name')
                : null;

             $designationName = $user->designation_id
            ? DB::table('employee_designations')->where('id', $user->designation_id)->value('name')
            : null;

            $item->dormitory_user = [
                'type'            => 'employee',
                'employee_id'     => $user->employee_id,
                'name'            => $user->name,
                'phone'           => $user->phone,
                'department_name' => $departmentName,
                'designation_name' => $designationName,
            ];
        }
    }

    $totalPayable = $data->sum('invoice_amount');
    $single       = $data->first();

    $responseData = [
        'data'          => $data,
        'school_name'   => $single->school_name ?? '',
        'total_payable' => $totalPayable,
    ];

    return returnData(2000, $responseData);
}

}
