<?php

namespace App\Http\Controllers\Reports\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FessCollectionInformationReportController extends Controller
{
    public function showReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $fromDate = input('from_date');
        $toDate = input('to_date');

        $componentId = input('component_id');

        $currentMonthStart = date('Y-m-01');
        $currentMonthEnd = date('Y-m-t');

        $data = DB::table('payments')
            ->leftJoin('payment_details', 'payments.id', '=', 'payment_details.payments_id')
            ->leftJoin('student_classes', 'payments.class_id', '=', 'student_classes.id')
            ->leftJoin('schools', 'payments.school_id', '=', 'schools.id')
            ->leftJoin('components', 'payment_details.component_id', '=', 'components.id')
            ->leftJoin('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->leftJoin('invoice_details', function ($details) {
                $details->on('invoices.id', '=', 'invoice_details.invoice_id');
                $details->whereRaw('invoice_details.components_id = payment_details.component_id');
            })
            ->selectRaw("
                schools.title as school_name,
                student_classes.name as class_name,
                COUNT(DISTINCT payments.student_id) as student_count,
                SUM(payment_details.waiver) as waiver,
                SUM(payment_details.payed) as payed,
                COALESCE(SUM(CASE WHEN payment_details.component_id THEN payment_details.amount END), 0) as paying_amount,

                COALESCE(SUM(DISTINCT invoices.total_payable) - COALESCE(SUM(DISTINCT payments.total_payed), 0), 0) AS total_due_amount,
                COUNT(DISTINCT CASE WHEN invoices.total_payable - payments.total_payed > 0 THEN payments.student_id END) as due_student_count,

                COALESCE(SUM(DISTINCT CASE WHEN invoices.total_payable > payments.total_payed AND invoices.date BETWEEN '$currentMonthStart' AND '$currentMonthEnd' THEN invoices.total_payable - payments.total_payed END), 0) AS current_total_due_amount,
                COUNT(DISTINCT CASE WHEN invoices.total_payable > payments.total_payed AND invoices.date BETWEEN '$currentMonthStart' AND '$currentMonthEnd' THEN payments.student_id END) as current_due_student_count,


                COUNT(DISTINCT CASE WHEN payments.invoice_id = 1 THEN payments.student_id END) as regular_students,
                COUNT(DISTINCT CASE WHEN payments.invoice_id = 2 THEN payments.student_id END) as due_students,
                COUNT(DISTINCT CASE WHEN payments.invoice_id = 3 THEN payments.student_id END) as advance_students,
                COUNT(DISTINCT CASE WHEN payments.invoice_id = 4 THEN payments.student_id END) as other_students,

                COALESCE(SUM(CASE WHEN payments.invoice_id = 1 THEN payment_details.payed END), 0) as regular_amount,
                COALESCE(SUM(CASE WHEN payments.invoice_id = 2 THEN payment_details.payed END), 0) as due_amount,
                COALESCE(SUM(CASE WHEN payments.invoice_id = 3 THEN payment_details.payed END), 0) as advance_amount,
                COALESCE(SUM(CASE WHEN payments.invoice_id = 4 THEN payment_details.payed END), 0) as other_amount,

                COALESCE(
                    COUNT(DISTINCT CASE WHEN payments.invoice_id = 1 THEN payments.student_id END) +
                    COUNT(DISTINCT CASE WHEN payments.invoice_id = 2 THEN payments.student_id END) +
                    COUNT(DISTINCT CASE WHEN payments.invoice_id = 3 THEN payments.student_id END) +
                    COUNT(DISTINCT CASE WHEN payments.invoice_id = 4 THEN payments.student_id END),
                    0
                ) as total_students,

                COALESCE(
                    SUM(CASE WHEN payments.invoice_id = 1 THEN payment_details.amount END), 0) +
                    COALESCE(SUM(CASE WHEN payments.invoice_id = 2 THEN payment_details.amount END), 0) +
                    COALESCE(SUM(CASE WHEN payments.invoice_id = 3 THEN payment_details.amount END), 0) +
                    COALESCE(SUM(CASE WHEN payments.invoice_id = 4 THEN payment_details.amount END), 0
                ) as total_amount

                ")
            ->where('payments.school_id', $schoolId)
            ->whereIn('payment_details.component_id', $componentId)
            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('payments.date', [$fromDate, $toDate]);
            })
            ->groupBy('payments.class_id')
            ->get();

        $singleData = $data->first();

        $school_name = $singleData ? $singleData->school_name : '';

        $multipleData = [
            'data' => $data,
            'school_name' => $school_name,
            'formDate' => $fromDate,
            'toDate' => $toDate,
        ];

        return returnData(2000, $multipleData);
    }
    public function getComponentData()
    {
        $schoolId = input('school_id');

        $data['data'] = DB::table('components')
            ->where('status', 1)
            ->where('school_id', $schoolId)->get();
        return returnData(2000, $data);
    }
}
