<?php

namespace App\Http\Controllers\Reports\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DueReportController extends Controller
{
    public function showReport(Request $request)
    {
        $sectionId = $request->input('section_id');
        $classId = $request->input('class_id');
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $invoiceCategory = $request->input('invoice_category');

        if (empty($classId)) {
            return returnData(5000, null, 'Please select the class.');
        }
        if (empty($sectionId)) {
            return returnData(5000, null, 'Please select the section.');
        }

        $data = DB::table('invoices')
            ->leftJoin('students', 'invoices.invoice_type_id', '=', 'students.id')
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
            ->leftJoin('invoice_details', 'invoices.id', '=', 'invoice_details.invoice_id')
            ->leftJoin('components', 'invoice_details.components_id', '=', 'components.id')
            ->leftJoin('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoin('payment_details', function ($join) {
                $join->on('payments.id', '=', 'payment_details.payments_id')
                    ->on('components.id', '=', 'payment_details.component_id');
            })
            ->select(
                'students.id as student_id',
                'students.student_name_en as student_name',
                'students.student_roll',
                'students.merit_roll as class_roll',
                'student_classes.name as class_name',
                'sections.name as section_name',
                'components.name as component_name',
                'invoices.id as invoice_id',
                'invoices.date as invoice_date',
                DB::raw('IFNULL(invoice_details.payable_amount, 0) as total_payable'),
                DB::raw('SUM(IFNULL(payment_details.payed, 0)) as total_paid'),
                DB::raw('IFNULL(invoice_details.payable_amount, 0) - SUM(IFNULL(payment_details.payed, 0)) as due_amount')
            )
            ->where('students.status', 1)
            ->when($classId, fn($q) => $q->where('students.class_id', $classId))
            ->when($sectionId, fn($q) => $q->where('students.section_id', $sectionId))
            ->when($invoiceCategory, fn($q) => $q->where('invoices.invoice_category', $invoiceCategory))
            ->when($fromDate && $toDate, fn($q) => $q->whereBetween('invoices.date', [$fromDate, $toDate]))
            ->groupBy(
                'students.id',
                'students.student_name_en',
                'students.student_roll',
                'students.merit_roll',
                'student_classes.name',
                'components.name',
                'invoices.id',
                'invoices.date',
            )
            ->having('due_amount', '>', 0)
            ->orderBy('students.merit_roll', 'asc')
            ->get();


        if ($data->isEmpty()) {
            return returnData(5000, null, 'No data found in the selected criteria.');
        }

        $student_info = $data->first();

        $orderedComponents = DB::table('components')
            ->orderBy('id')
            ->pluck('name')
            ->toArray();

        $responseData = [
            'data' => $data,
            'class_name' => $student_info ? $student_info->class_name : 'All',
            'section_name' => $student_info ? $student_info->section_name : 'All',
            'from_date' => $fromDate ?? '-',
            'to_date' => $toDate ?? '-',
            'component_order' => $orderedComponents,
        ];

        return returnData(2000, $responseData);
    }

    public function showMonthlyReport(Request $request)
    {
        $sectionId = $request->input('section_id');
        $classId = $request->input('class_id');
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $invoiceCategory = $request->input('invoice_category');

        if (empty($classId)) {
            return returnData(5000, null, 'Please select the class.');
        }

        if (empty($sectionId)) {
            return returnData(5000, null, 'Please select the section.');
        }

        $invoiceTotals = DB::table('invoice_details')
            ->select('invoice_id', 'components_id', DB::raw('SUM(payable_amount) AS total_payable'))
            ->groupBy('invoice_id', 'components_id');

        $paymentTotals = DB::table('payment_details')
            ->select('payments_id', 'component_id', DB::raw('SUM(payed) AS total_paid'))
            ->groupBy('payments_id', 'component_id');

        $rawData = DB::table('invoices')
            ->leftJoin('students', 'invoices.invoice_type_id', '=', 'students.id')
            ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('invoice_details', 'invoices.id', '=', 'invoice_details.invoice_id')
            ->leftJoin('components', 'invoice_details.components_id', '=', 'components.id')
            ->leftJoin('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoinSub($invoiceTotals, 'inv_totals', function ($join) {
                $join->on('inv_totals.invoice_id', '=', 'invoices.id')
                    ->on('inv_totals.components_id', '=', 'invoice_details.components_id');
            })
            ->leftJoinSub($paymentTotals, 'pay_totals', function ($join) {
                $join->on('pay_totals.payments_id', '=', 'payments.id')
                    ->on('pay_totals.component_id', '=', 'invoice_details.components_id');
            })
            ->select(
                'students.id as student_id',
                'students.student_name_en as student_name',
                'students.student_roll',
                'students.merit_roll as class_roll',
                'student_classes.name as class_name',
                'sections.name as section_name',
                'components.id as component_id',
                'components.name as component_name',
                'invoices.invoice_category',
                DB::raw("DATE_FORMAT(invoices.date, '%b') as month"),
                DB::raw('IFNULL(inv_totals.total_payable, 0) - IFNULL(pay_totals.total_paid, 0) as due_amount')
            )
            ->where('students.status', 1)
            ->when($sectionId, fn($q) => $q->where('students.section_id', $sectionId))
            ->when($classId, fn($q) => $q->where('students.class_id', $classId))
            ->when($fromDate && $toDate, fn($q) => $q->whereBetween('invoices.date', [$fromDate, $toDate]))
            ->when($invoiceCategory, fn($q) => $q->where('invoices.invoice_category', $invoiceCategory))
            ->having('due_amount', '>', 0)
            ->orderBy('students.merit_roll')
            ->orderBy('components.id')
            ->get();

        if ($rawData->isEmpty()) {
            return returnData(5000, null, 'No data found in the selected criteria.');
        }

        $componentMonthHeaders = [];
        $seenKeys = [];
        foreach ($rawData as $row) {
            $key = $row->component_name . '-' . $row->month;
            if (!isset($seenKeys[$key])) {
                $componentMonthHeaders[] = ['component' => $row->component_name, 'month' => $row->month];
                $seenKeys[$key] = true;
            }
        }

        $groupedData = [];
        foreach ($rawData as $row) {
            $roll = $row->student_roll;

            if (!isset($groupedData[$roll])) {
                $groupedData[$roll] = [
                    'name' => $row->student_name,
                    'student_roll' => $row->student_roll,
                    'class_roll' => $row->class_roll,
                    'class' => $row->class_name,
                    'section' => $row->section_name,
                    'invoice_categories' => [],
                    'dues' => [],
                    'totalDue' => 0,
                ];
            }

            if ($row->invoice_category && !in_array($row->invoice_category, $groupedData[$roll]['invoice_categories'])) {
                $groupedData[$roll]['invoice_categories'][] = $row->invoice_category;
            }

            $groupedData[$roll]['dues'][$row->component_name][$row->month] = (float) $row->due_amount;
            $groupedData[$roll]['totalDue'] += (float) $row->due_amount;
        }

        $student_info = $rawData->first();

        $responseData = [
            'data' => array_values($groupedData),
            'component_month_headers' => $componentMonthHeaders,
            'section_name' => $student_info ? $student_info->section_name : 'All',
            'class_name' => $student_info ? $student_info->class_name : 'All',
            'from_date' => $fromDate ?? '-',
            'to_date' => $toDate ?? '-',
        ];

        return returnData(2000, $responseData);
    }
}
