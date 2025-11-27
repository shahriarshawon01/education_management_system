<?php

namespace App\Http\Controllers\BillManagement;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BillableStudentController extends Controller
{
    public function getBillableStudents(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        $fromDate = date('Y-m-d', strtotime($request->input('from')));
        $toDate = date('Y-m-d', strtotime($request->input('to')));

        try {
            $months = [];
            $current = strtotime(date('Y-m-01', strtotime($fromDate)));
            $end = strtotime(date('Y-m-01', strtotime($toDate)));

            while ($current <= $end) {
                $months[] = (object) [
                    'month_start' => date('Y-m-01', $current),
                    'month_name' => date('F', $current),
                    'year_month' => date('Y-m', $current)
                ];
                $current = strtotime('+1 month', $current);
            }

            $monthsResult = $months;

            $students = [];

            foreach ($monthsResult as $monthData) {
                $monthStart = $monthData->month_start;
                $monthEnd = date('Y-m-t', strtotime($monthStart));
                $studentCount = DB::select("
                SELECT COUNT(DISTINCT ss.id) as total_student
                FROM (
                    SELECT DISTINCT st.id
                    FROM students st
                    WHERE st.status = 1
                      AND st.class_id != 29
                      AND st.department_id != 27
                      AND DATE(st.admission_date) <= :monthEnd1

                    UNION ALL

                    SELECT DISTINCT st.id
                    FROM students st
                    INNER JOIN invoices i ON st.id = i.invoice_type_id
                    WHERE st.status IN (0,2)
                      AND i.invoice_type = 1
                      AND DATE(i.date) <= :monthEnd2
                ) ss
            ", [
                    'monthEnd1' => $monthEnd,
                    'monthEnd2' => $monthEnd
                ]);

                $students[] = (object) [
                    'total_student' => (int) $studentCount[0]->total_student,
                    'month_name' => strtolower($monthData->month_name)
                ];
            }

            return response()->json([
                'data' => $students,
                'message' => count($students) > 0
                    ? 'Billable students fetched successfully.'
                    : 'No billable students found.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 5000,
                'message' => 'An error occurred while fetching billable students.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

// http://127.0.0.1:8000/api/students/billable?date=2025-07-27&api_key=RANDOM098093247%23%24%23%24%40Key
// new url 
// http://127.0.0.1:8000/api/students/billable?from=2025-07-27&to=2025-07-27&api_key=RANDOM098093247%23%24%23%24%40Key
// Live URL : https://app.tpsc.edu.bd/api/students/billable?
// local : http://127.0.0.1:8001/api/students/billable? 
// Auth Token : RANDOM098093247%23%24%23%24%40Key