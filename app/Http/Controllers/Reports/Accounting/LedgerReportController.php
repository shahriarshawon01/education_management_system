<?php

namespace App\Http\Controllers\Reports\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LedgerReportController extends Controller
{

    public function showReport(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $studentId = $request->student_id;
        $invoiceCategory = request()->input('invoice_category');


        $ledgerData = DB::table('invoices')
            ->join('students', 'invoices.invoice_type_id', '=', 'students.id')
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoin('invoice_details', 'invoices.id', '=', 'invoice_details.invoice_id')
            ->leftJoin('components', 'invoice_details.components_id', '=', 'components.id')
            ->leftJoin('payment_details', function ($join) {
                $join->on('payments.id', '=', 'payment_details.payments_id')
                    ->on('components.id', '=', 'payment_details.component_id');
            })
            ->selectRaw("
            invoices.id,
            invoices.date AS invoice_date,
            invoices.invoice_code AS invoice_code,
            invoices.invoice_category,
            students.student_name_en as student_name,
            students.admission_date as admission_date,
            students.father_phone as guardian_phone,
            students.student_roll as student_id,
            student_classes.name as class_name,
            payments.date AS payment_date,
            components.name AS particulars,
            IFNULL(invoice_details.payable_amount, 0) AS debit,
            SUM(IFNULL(payment_details.payed, 0)) AS credit
        ")
         ->when($invoiceCategory, function ($query) use ($invoiceCategory) {
                $query->where('invoices.invoice_category', $invoiceCategory);
            })
            ->when($fromDate && $toDate, fn($q) => $q->whereBetween('invoices.date', [$fromDate, $toDate]))
            ->where('students.id', $studentId)
            ->where('students.status', 1)
            ->orderBy('invoices.date')
            ->orderBy('components.id')
            ->groupBy('invoices.id','components.id')
            ->get();

        $formattedLedgerData = [];
        $cumulativeDebit = 0;
        $cumulativeCredit = 0;

        foreach ($ledgerData as $item) {
            $cumulativeDebit += $item->debit;
            $cumulativeCredit += $item->credit;

            if (!isset($formattedLedgerData[$item->invoice_code])) {
                $formattedLedgerData[$item->invoice_code] = [];
            }

            $formattedLedgerData[$item->invoice_code][] = [
                'invoice_date' => $item->invoice_date,
                'invoice_code' => $item->invoice_code,
                'student_name' => $item->student_name,
                'admission_date' => $item->admission_date,
                'guardian_phone' => $item->guardian_phone,
                'student_id' => $item->student_id,
                'class_name' => $item->class_name,
                'payment_date' => $item->payment_date,
                'particulars' => $item->particulars,
                'debit' => $item->debit,
                'credit' => $item->credit,
                'cumulative_debit' => $cumulativeDebit,
                'cumulative_credit' => $cumulativeCredit,
                'debit_balance' => $cumulativeDebit - $cumulativeCredit,
                'credit_balance' => $cumulativeCredit,
                'balance' => $cumulativeDebit - $cumulativeCredit,
            ];
        }

        if (empty($formattedLedgerData)) {
            return returnData(5000, "No records found for the selected filters.");
        }

        $firstItem = $ledgerData->first();

        $responseData = [
            'ledger_data' => $formattedLedgerData,
            'grand_total' => [
                'debit' => collect($ledgerData)->sum('debit'),
                'credit' => collect($ledgerData)->sum('credit'),
                'balance' => $cumulativeDebit - $cumulativeCredit,
                'credit_balance' => $cumulativeCredit,
            ],
            'student_name' => $firstItem->student_name ?? '',
            'student_id' => $firstItem->student_id ?? '',
            'admission_date' => $firstItem->admission_date ?? '',
            'class_name' => $firstItem->class_name ?? '',
            'guardian_phone' => $firstItem->guardian_phone ?? '',
            'from_date' => $fromDate ?? '-',
            'to_date' => $toDate ?? '-',
        ];

        return returnData(2000, $responseData);
    }
}