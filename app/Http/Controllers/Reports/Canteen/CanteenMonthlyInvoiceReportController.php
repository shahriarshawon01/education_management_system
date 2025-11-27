<?php

namespace App\Http\Controllers\Reports\Canteen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CanteenMonthlyInvoiceReportController extends Controller
{
    public function showReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $month = $request->month;
        $year = $request->years;
        $canteenUserType = $request->user_type;

        if (!empty($month) && !is_numeric($month)) {
            $month = date('m', strtotime($month));
        }

        $data = DB::table('invoices')
            // Join students for invoice_type 1
            ->leftJoin('students', function ($join) {
                $join->on('invoices.invoice_type_id', '=', 'students.id')
                    ->where('invoices.invoice_type', 1);
            })
            ->leftJoin('session_years as student_sessions', 'students.session_year_id', '=', 'student_sessions.id')
            ->leftJoin('student_classes as student_classes', 'students.class_id', '=', 'student_classes.id')

            // Join employees for invoice_type 2
            ->leftJoin('employees', function ($join) {
                $join->on('invoices.invoice_type_id', '=', 'employees.id')
                    ->where('invoices.invoice_type', 2);
            })
            ->leftJoin('employee_departments', 'employees.department_id', '=', 'employee_departments.id')
            ->leftJoin('employee_designations', 'employees.designation_id', '=', 'employee_designations.id')

            // Users and schools
            ->leftJoin('users as createdUser', 'invoices.created_by', '=', 'createdUser.id')
            ->leftJoin('users as updatedUser', 'invoices.updated_by', '=', 'updatedUser.id')
            ->leftJoin('schools', 'invoices.school_id', '=', 'schools.id')

            ->where('invoices.invoice_category', 2)
            ->when($month, fn($q) => $q->whereMonth('invoices.to_date', $month))
            ->when($year, fn($q) => $q->whereYear('invoices.to_date', $year))
            ->when($canteenUserType, fn($q) => $q->where('invoices.invoice_type', $canteenUserType))

            ->select(
                'invoices.id as invoice_id',
                'invoices.invoice_code',
                'invoices.to_date',
                'invoices.month',
                'invoices.total_payable as invoice_amount',
                'invoices.invoice_type',

                // Student fields
                'students.student_name_en',
                'students.student_roll',
                'students.merit_roll',
                'student_sessions.title as session_title',
                'student_classes.name as class_name',

                // Employee fields
                'employees.name as employee_name',
                'employees.employee_id',
                'employee_departments.name as department_name',
                'employee_designations.name as designation_name',

                // Users
                'createdUser.username as created_by_username',
                'updatedUser.username as updated_by_username',

                // School
                'schools.title as school_name'
            )
            ->orderBy('invoices.to_date', 'desc')
            ->get();

        if ($data->isEmpty()) {
            return returnData(5000, null, 'No records found for the specified criteria.');
        }

        // Prepare canteen_user based on invoice_type
        foreach ($data as $item) {
            $item->canteen_user = null;

            if ($item->invoice_type == 1) { // Student
                $item->user_type = 'Student';
                $item->canteen_user = [
                    'type' => 'student',
                    'student_roll' => $item->student_roll,
                    'student_name_en' => $item->student_name_en,
                ];
            } elseif ($item->invoice_type == 2) { // Employee
                $item->user_type = 'Employee';
                $item->canteen_user = [
                    'type' => 'employee',
                    'employee_id' => $item->employee_id,
                    'name' => $item->employee_name,
                ];
            }
        }

        $totalPayable = $data->sum('invoice_amount');
        $schoolName = $data->first()->school_name ?? '';

        $responseData = [
            'data' => $data,
            'school_name' => $schoolName,
            'total_payable' => $totalPayable,
        ];

        return returnData(200, $responseData);
    }

}
