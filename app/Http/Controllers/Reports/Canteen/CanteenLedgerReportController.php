<?php

namespace App\Http\Controllers\Reports\Canteen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CanteenLedgerReportController extends Controller
{
    public function showReport(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $memberId = $request->input('member_id');
        $memberType = $request->consumer_type;

        if (!$month || !$year || !$memberId || !$memberType) {
            return returnData(3000, null, 'Month, Year, Member and Member Type are required.');
        }

        $mealTimes = DB::table('meal_times')
            ->where('status', 1)
            ->pluck('name', 'id')
            ->toArray();

        $sales = DB::table('canteen_daily_sales as cds')
            ->select(
                'cds.date',
                'cds.meal_time_id',
                DB::raw('SUM(cds.amount) as total_amount')
            )
            ->where('cds.is_canteen_member', 1)
            ->where('cds.sale_type', 2)
            ->where('cds.member_id', $memberId)
            ->where('cds.member_type', $memberType)
            ->whereMonth('cds.date', $month)
            ->whereYear('cds.date', $year)
            ->groupBy('cds.date', 'cds.meal_time_id')
            ->orderBy('cds.date')
            ->get();

        if ($sales->isEmpty()) {
            return returnData(5000, null, 'No daily ledger found for this member.');
        }

        $formatted = [];
        foreach ($sales as $row) {
            $date = $row->date;
            $mealName = $mealTimes[$row->meal_time_id] ?? 'Unknown';

            if (!isset($formatted[$date])) {
                $formatted[$date] = ['date' => $date, 'total_amount' => 0];

                foreach ($mealTimes as $name) {
                    $formatted[$date][$name] = 0;
                }
            }

            $formatted[$date][$mealName] += $row->total_amount;
            $formatted[$date]['total_amount'] += $row->total_amount;
        }

        $memberInfo = null;
        if ($memberType == 1) {
            $memberInfo = DB::table('students as s')
                ->leftJoin('student_classes as sc', 's.class_id', '=', 'sc.id')
                ->where('s.id', $memberId)
                ->select(
                    's.student_name_en as name',
                    's.student_roll as roll',
                    's.id as member_id',
                    'sc.name as class_name'
                )->first();
        } elseif ($memberType == 2) {
            $memberInfo = DB::table('employees as e')
                ->leftJoin('employee_designations as ed', 'e.designation_id', '=', 'ed.id')
                ->where('e.id', $memberId)
                ->select(
                    'e.name',
                    'e.employee_id',
                    'ed.name as designation_name'
                )->first();
        }

        return returnData(2000, [
            'member_info' => $memberInfo,
            'meal_columns' => array_values($mealTimes),
            'rows' => array_values($formatted),
            'month' => Carbon::createFromDate(null, $month)->format('F'),
            'year' => $year,
            'print_date' => now()->format('Y-m-d')
        ], 'Daily member ledger data retrieved.');
    }
}
