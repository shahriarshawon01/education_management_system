<?php

namespace App\Http\Controllers\Reports\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AgingReportController extends Controller
{
    public function showReport(Request $request)
    {
        if (!can('aging_report_view')) {
            return $this->notPermitted();
        }
        $classId = $request->class_id;
        $sectionId = $request->section_id;
        $sessionId = $request->session_year_id;
        $departmentId = $request->department_id;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        $data = DB::table('invoices')
            ->leftJoin('students', 'invoices.invoice_type_id', '=', 'students.id')
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoin('invoice_details', 'invoices.id', '=', 'invoice_details.invoice_id')
            ->leftJoin('components', 'invoice_details.components_id', '=', 'components.id')
            ->leftJoin('payment_details', function ($join) {
                $join->on('payments.id', '=', 'payment_details.payments_id')
                    ->on('components.id', '=', 'payment_details.component_id');
            })
            ->select(
                'invoices.id',
                'students.id as student_id',
                'students.student_name_en as student_name',
                'students.student_roll as student_roll',
                'student_classes.id as class_id',
                'student_classes.name as class_name',
                DB::raw('IFNULL(invoice_details.payable_amount, 0) as total_payable'),
                DB::raw('SUM(IFNULL(payment_details.payed, 0)) as total_payed'),
                'invoices.date as invoice_date'
            )
            ->where('students.status', 1)
            ->when($classId, fn($query) => $query->where('students.class_id', $classId))
            ->when($sectionId, fn($query) => $query->where('students.section_id', $sectionId))
            ->when($sessionId, fn($query) => $query->where('students.session_year_id', $sessionId))
            ->when($departmentId, fn($query) => $query->where('students.department_id', $departmentId))
            ->when($fromDate && $toDate, fn($query) => $query->whereBetween('invoices.date', [$fromDate, $toDate]))
            ->groupBy(
                'students.id',
                'student_classes.id',
                'students.student_name_en',
                'students.student_roll',
                'student_classes.name',
                'invoices.id',
                'invoice_details.components_id',
            )
            ->get();

        $totalClassStudents = DB::table('students')
            ->select('class_id', DB::raw('COUNT(*) as total_students'))
            ->where('status', 1)
            ->when($classId, fn($query) => $query->where('class_id', $classId))
            ->when($sectionId, fn($query) => $query->where('students.section_id', $sectionId))
            ->when($sessionId, fn($query) => $query->where('session_year_id', $sessionId))
            ->when($departmentId, fn($query) => $query->where('department_id', $departmentId))
            ->groupBy('class_id')
            ->pluck('total_students', 'class_id');

        $agingReport = [];
        $grandTotals = [
            'total_students' => 0,
            'total_receivable' => 0,
            '1_30_days' => 0,
            '31_90_days' => 0,
            '91_180_days' => 0,
            '181_365_days' => 0,
            'above_one_year' => 0,
            'total_grand_total' => 0,
            'grand_total_due_student' => 0,
        ];

        $dueStudentsCounted = [];

        foreach ($data as $invoice) {
            $aging = [
                '1_30_days' => 0,
                '31_90_days' => 0,
                '91_180_days' => 0,
                '181_365_days' => 0,
                'above_one_year' => 0,
            ];

            $dueAmount = $invoice->total_payable - $invoice->total_payed;
            if ($dueAmount <= 0) {
                continue;
            }

            $dueDays = now()->diffInDays($invoice->invoice_date);

            if ($dueDays <= 30) {
                $aging['1_30_days'] += $dueAmount;
            } elseif ($dueDays <= 90) {
                $aging['31_90_days'] += $dueAmount;
            } elseif ($dueDays <= 180) {
                $aging['91_180_days'] += $dueAmount;
            } elseif ($dueDays <= 365) {
                $aging['181_365_days'] += $dueAmount;
            } else {
                $aging['above_one_year'] += $dueAmount;
            }

            $total = array_sum($aging);
            $classKey = $invoice->class_id;

            if (!isset($agingReport[$classKey])) {
                $agingReport[$classKey] = [
                    'class_name' => $invoice->class_name,
                    'class_id' => $invoice->class_id,
                    'total_students' => $totalClassStudents[$classKey] ?? 0,
                    'total_receivable' => 0,
                    '1_30_days' => 0,
                    '31_90_days' => 0,
                    '91_180_days' => 0,
                    '181_365_days' => 0,
                    'above_one_year' => 0,
                    'total' => 0,
                    'due_students' => 0,
                ];
                $dueStudentsCounted[$classKey] = [];
            }

            $agingReport[$classKey]['total_receivable'] += $dueAmount;
            $agingReport[$classKey]['1_30_days'] += $aging['1_30_days'];
            $agingReport[$classKey]['31_90_days'] += $aging['31_90_days'];
            $agingReport[$classKey]['91_180_days'] += $aging['91_180_days'];
            $agingReport[$classKey]['181_365_days'] += $aging['181_365_days'];
            $agingReport[$classKey]['above_one_year'] += $aging['above_one_year'];
            $agingReport[$classKey]['total'] += $total;

            if ($dueAmount > 0 && !in_array($invoice->student_id, $dueStudentsCounted[$classKey])) {
                $agingReport[$classKey]['due_students']++;
                $grandTotals['grand_total_due_student']++;
                $dueStudentsCounted[$classKey][] = $invoice->student_id;
            }

            $grandTotals['total_receivable'] += $dueAmount;
            $grandTotals['1_30_days'] += $aging['1_30_days'];
            $grandTotals['31_90_days'] += $aging['31_90_days'];
            $grandTotals['91_180_days'] += $aging['91_180_days'];
            $grandTotals['181_365_days'] += $aging['181_365_days'];
            $grandTotals['above_one_year'] += $aging['above_one_year'];
            $grandTotals['total_grand_total'] += $total;
        }

        $responseData = [
            'data' => $agingReport,
            'grand_totals' => $grandTotals,
        ];

        return returnData(2000, $responseData);
    }

    public function getDueStudents(Request $request)
    {
        $classId = $request->class_id;
        $sectionId = $request->section_id;
        $sessionId = $request->session_year_id;
        $departmentId = $request->department_id;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        $data = DB::table('students as s')
            ->leftJoin('student_classes as sc', 's.class_id', '=', 'sc.id')
            ->leftJoin('session_years as sy', 's.session_year_id', '=', 'sy.id')
            ->leftJoin('departments as d', 's.department_id', '=', 'd.id')
            ->leftJoin(DB::raw("
            (
                SELECT invoice_type_id AS student_id, SUM(total_payable) AS total_payable
                FROM invoices
                WHERE invoice_type = 1
                GROUP BY invoice_type_id
            ) inv_sum
        "), 'inv_sum.student_id', '=', 's.id')
            ->leftJoin(DB::raw("
            (
                SELECT paid_by_id AS student_id, SUM(total_payed) AS total_payed
                FROM payments
                WHERE paid_by_type = 1
                GROUP BY paid_by_id
            ) pay_sum
        "), 'pay_sum.student_id', '=', 's.id')

            ->select(
                's.id as student_id',
                's.student_name_en as student_name',
                's.student_roll as student_roll',
                'sc.name as class_name',
                'sy.title as session_name',
                'd.department_name as department_name',

                DB::raw("COALESCE(inv_sum.total_payable, 0) as total_payable"),
                DB::raw("COALESCE(pay_sum.total_payed, 0) as total_payed")
            )
            ->when($classId, fn($q) => $q->where('s.class_id', $classId))
            ->when($sectionId, fn($q) => $q->where('s.section_id', $sectionId))
            ->when($sessionId, fn($q) => $q->where('s.session_year_id', $sessionId))
            ->when($departmentId, fn($q) => $q->where('s.department_id', $departmentId))
            ->when($fromDate && $toDate, fn($query) => $query->whereBetween('invoices.date', [$fromDate, $toDate]))

            ->where('s.status', 1)

            ->get();
        $dueStudents = [];
        foreach ($data as $student) {
            $dueAmount = $student->total_payable - $student->total_payed;

            if ($dueAmount > 0) {
                $dueStudents[] = [
                    'student_id' => $student->student_id,
                    'student_name' => $student->student_name,
                    'student_roll' => $student->student_roll,
                    'class_name' => $student->class_name,
                    'session_name' => $student->session_name,
                    'department_name' => $student->department_name,
                    'total_due' => $dueAmount,
                ];
            }
        }

        return returnData(2000, [
            'due_students' => $dueStudents
        ]);
    }
}
