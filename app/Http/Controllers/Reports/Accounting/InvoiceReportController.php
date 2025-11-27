<?php

namespace App\Http\Controllers\Reports\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceReportController extends Controller
{
    public function showReport(Request $request)
    {
        $invoice_type_id = input('student_id');
        $class_id = input('class_id');
        $invoice_id = input('invoice_id');
        $invoiceCategory = request()->input('invoice_category');

        if (!$invoice_id) {
            $groupBy = 'invoices.id';
            $select = 'invoices.invoice_code as component_name';
        }
        if (!$invoice_type_id) {
            $groupBy = 'invoices.invoice_type_id';
            $select = 'students.student_name_en as component_name';
        }
        if ($invoice_id) {
            $groupBy = 'invoice_details.components_id';
            $select = 'components.name as component_name';
        }

        $invoiceData = DB::table('invoices')
            ->leftJoin('invoice_details', 'invoices.id', '=', 'invoice_details.invoice_id')
            ->leftJoin('components', 'invoice_details.components_id', '=', 'components.id')
            ->leftJoin('students', 'invoices.invoice_type_id', '=', 'students.id')
            ->leftJoin('schools', 'invoices.school_id', '=', 'schools.id')
            ->leftJoin('session_years', 'students.session_year_id', '=', 'session_years.id')
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
            ->selectRaw("invoices.*
            ,invoice_details.*,
            students.student_name_en as student_name,
            students.student_roll as student_roll,
            $select,
            student_classes.name as class_name,
            session_years.title as session_name,
            sections.name as section_name,
            SUM(invoice_amount) as invoice_amount,
            SUM(waiver_amount) as waiver_amount,
            SUM(payable_amount) as payable_amount")
            ->where('students.class_id', $class_id)
            ->when($invoice_type_id, function ($query) use ($invoice_type_id) {
                $query->where('invoices.invoice_type_id', $invoice_type_id);
            })
            ->when($invoice_id, function ($query) use ($invoice_id) {
                $query->where('invoice_details.invoice_id', $invoice_id);
            })
            ->when($invoiceCategory, function ($query) use ($invoiceCategory) {
                $query->where('invoices.invoice_category', $invoiceCategory);
            })
            ->groupBy($groupBy)
            ->get();

        if ($invoiceData->isEmpty()) {
            return returnData(5000, null, 'No data found in the selected criteria.');
        }

        $single = $invoiceData->first();

        $responseData = [
            'data' => $invoiceData,
            'total_amount' => $invoiceData->sum('invoice_amount'),
            'total_waiver' => $invoiceData->sum('waiver_amount'),
            'total_payable' => $invoiceData->sum('payable_amount'),
            'invoice_date' => $single ? $single->date : '',
            'invoice_code' => $single ? $single->invoice_code : '',
            'class_name' => $single ? $single->class_name : '',
            'section_name' => $single ? $single->section_name : '',
            'session_name' => $single ? $single->session_name : '',
            'student_roll' => $single ? $single->student_roll : '',
            'student_name' => ($invoice_type_id && $single) ? $single->student_name : '',
        ];

        return returnData(2000, $responseData);
    }
}
