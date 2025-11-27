<?php

namespace App\Http\Controllers\Reports\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyCollectionReportController extends Controller
{
    public function showReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $paymentType = $request->payment_type;
        $collectionBy = $request->collection_by;
        $invoiceCategory = request()->input('invoice_category');

        $data = DB::table('payments')
            ->leftJoin('students', function ($join) {
                $join->on('payments.paid_by_id', '=', 'students.id')
                    ->where('payments.paid_by_type', 1);
            })
            ->leftJoin('student_classes', 'payments.class_id', '=', 'student_classes.id')
            ->leftJoin('session_years', 'payments.session_year_id', '=', 'session_years.id')
            ->leftJoin('employees', function ($join) {
                $join->on('payments.paid_by_id', '=', 'employees.id')
                    ->where('payments.paid_by_type', 2);
            })
            ->leftJoin('employee_departments', 'employees.department_id', '=', 'employee_departments.id')
            ->leftJoin('employee_designations', 'employees.designation_id', '=', 'employee_designations.id')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('invoice_details', 'invoices.id', '=', 'invoice_details.invoice_id')
            ->join('components', 'invoice_details.components_id', '=', 'components.id')
            ->join('users', 'payments.created_by', '=', 'users.id')
            ->select(
                'payments.id',
                'payments.date as payment_date',
                'payments.invoice_category',
                'payments.payment_type',
                'payments.total_payed',
                'payments.paid_by_type',

                'students.student_name_en',
                'students.student_roll',
                'students.merit_roll',
                'student_classes.name as class_name',
                'session_years.title as session_name',

                'employees.name as employee_name',
                'employees.employee_id',
                'employee_designations.name as designation_name',

                'components.name as component_name',
                'users.username as collected_by'
            )
            ->where('students.status', 1)
            ->where('payments.school_id', $schoolId)
            ->when($paymentType, function ($query) use ($paymentType) {
                $query->where('payments.payment_type', $paymentType);
            })
            ->when($collectionBy, function ($query) use ($collectionBy) {
                $query->where('payments.created_by', $collectionBy);
            })
            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('payments.date', [$fromDate, $toDate]);
            }, function ($query) use ($fromDate, $toDate) {
                if ($fromDate) {
                    $query->whereDate('payments.date', '>=', $fromDate);
                }
                if ($toDate) {
                    $query->whereDate('payments.date', '<=', $toDate);
                }
            })
            ->when($invoiceCategory, function ($query) use ($invoiceCategory) {
                $query->where('invoices.invoice_category', $invoiceCategory);
            })
            ->groupBy('payments.id')
            ->get();

        foreach ($data as $payment) {
            $payment->invoice_details = DB::table('payment_details')
                ->leftJoin('components', 'payment_details.component_id', '=', 'components.id')
                ->where('payment_details.payments_id', $payment->id)
                ->select(
                    'payment_details.id',
                    'payment_details.amount as payable_amount',
                    'payment_details.payed as paid_amount',
                    'components.name as component_name'
                )
                ->get();
        }

        $responseData = [
            'data' => $data,
        ];
        return returnData(2000, $responseData);
    }
}