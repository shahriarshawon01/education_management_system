<?php

namespace App\Http\Controllers\Reports\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransportMonthlyInvoiceReportController extends Controller
{

    public function showReport(Request $request)
    {
        $schoolId    = auth()->user()->school_id;
        $transportd = $request->transport_id;
        $statusId    = $request->status;
        $month       = $request->month;
        $year        = $request->year;
        $transportUserType = $request->user_type;


        $data = DB::table('invoices')

            ->leftJoin('assign_transports', 'invoices.invoice_type_id', '=', 'assign_transports.transport_user_id')
            ->leftJoin('transport_manages', 'assign_transports.transport_id', '=', 'transport_manages.id')

            ->leftJoin('students', function ($join) {
                $join->on('assign_transports.transport_user_id', '=', 'students.id')
                    ->where('assign_transports.transport_user_type', 1);
            })
            ->leftJoin('employees', function ($join) {
                $join->on('assign_transports.transport_user_id', '=', 'employees.id')
                    ->where('assign_transports.transport_user_type', 2);
            })
            ->leftJoin('schools', 'assign_transports.school_id', '=', 'schools.id')
            ->where('invoice_category', 4)
            ->when($month, fn($q) => $q->whereMonth('invoices.to_date', $month))
            ->when($year, fn($q) => $q->whereYear('invoices.to_date', $year))

            ->when($transportd, fn($q) => $q->where('assign_transports.transport_id', $transportd))
              ->when($transportUserType, fn($q) => $q->where('invoices.invoice_type', $transportUserType))
            ->when($statusId, fn($q) => $q->where('assign_transports.status', $statusId))
            ->select(
                'invoices.id as invoice_id',
                'invoices.invoice_code',
                'invoices.to_date',
                'invoices.month',
                'invoices.total_payable as invoice_amount',
                'transport_manages.transport_name',
                'transport_manages.transport_no',
                'assign_transports.transport_user_type',
                'assign_transports.transport_user_id',
                'assign_transports.stoppage as drop_point',
                'schools.title as school_name'
            )
            ->get();

        // dd($data);

        if ($data->isEmpty()) {
            return returnData(404, null, 'No records found for the specified criteria.');
        }

        $studentIds  = $data->where('transport_user_type', 1)->pluck('transport_user_id')->toArray();
        $employeeIds = $data->where('transport_user_type', 2)->pluck('transport_user_id')->toArray();


        $students  = DB::table('students')->whereIn('id', $studentIds)->get()->keyBy('id');
        $employees = DB::table('employees')->whereIn('id', $employeeIds)->get()->keyBy('id');


        foreach ($data as $item) {
            $item->transport_user = null;
            $item->user_type_name = $item->transport_user_type == 1 ? 'Student' : 'Employee';

            if ($item->transport_user_type == 1 && isset($students[$item->transport_user_id])) {
                $user = $students[$item->transport_user_id];


                $item->transport_user = [
                    'type'            => 'student',
                    'student_roll'    => $user->student_roll,
                    'merit_roll'    => $user->merit_roll,
                    'student_name_en' => $user->student_name_en,
                    'student_phone'   => $user->student_phone,
                ];
            } elseif ($item->transport_user_type == 2 && isset($employees[$item->transport_user_id])) {
                $user = $employees[$item->transport_user_id];

                $designationName = $user->designation_id
                    ? DB::table('employee_designations')->where('id', $user->designation_id)->value('name')
                    : null;

                $item->transport_user = [
                    'type'            => 'employee',
                    'employee_id'     => $user->employee_id,
                    'name'            => $user->name,
                    'phone'           => $user->phone,
                    'designation_name' => $designationName,
                ];
            }
        }

        $totalPayable = $data->sum('invoice_amount');

        $single = $data->first();

        $responseData = [
            'data'          => $data,
            'school_name'   => $single->school_name ?? '',
            'total_payable' => $totalPayable,
        ];

        return returnData(2000, $responseData);
    }
}
