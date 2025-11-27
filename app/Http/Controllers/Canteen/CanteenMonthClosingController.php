<?php

namespace App\Http\Controllers\Canteen;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\InvoiceDetails;
use App\Models\CanteenConfigure;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CanteenMonthClosingController extends Controller
{
    public function getCanteenClosingData(Request $request)
    {
        if (!can('canteen_month_closing_view')) {
            return $this->notPermitted();
        }
        try {
            $month = $request->input('canteen_month');
            $year = $request->input('years');
            $memberType = $request->input('member_type') ?? null;
            $schoolId = auth()->user()->school_id;

            if (!$month || !$year) {
                return returnData(3000, null, 'Month and Year are required.');
            }

            $canteenSales = DB::table('canteen_daily_sales as cds')
                ->leftJoin('students as s', function ($join) {
                    $join->on('cds.member_id', '=', 's.id')
                        ->where('cds.member_type', 1);
                })
                ->leftJoin('employees as e', function ($join) {
                    $join->on('cds.member_id', '=', 'e.id')
                        ->where('cds.member_type', 2);
                })
                ->select(
                    'cds.member_type',
                    'cds.member_id',
                    'cds.meal_time_id',
                    DB::raw('SUM(cds.amount) as total_amount'),
                    DB::raw("CASE WHEN cds.member_type = 1 THEN s.student_name_en ELSE e.name END as member_name"),
                    's.student_roll',
                    's.id as student_id',
                    'e.employee_id',
                    'e.id as employee_primary_id',
                    'sc.name as student_class',
                    'employee_designation.name as employee_designation',
                )
                ->leftJoin('student_classes as sc', 's.class_id', '=', 'sc.id')

                ->leftJoin('employee_designations as employee_designation', 'e.designation_id', '=', 'employee_designation.id')
                ->where('cds.status', 1)
                ->where('cds.sale_type', 2)
                ->where('cds.is_canteen_member', 1)
                ->where('cds.school_id', $schoolId)
                ->whereMonth('cds.date', $month)
                ->whereYear('cds.date', $year)
                ->when($memberType, fn($q) => $q->where('cds.member_type', $memberType))
                ->groupBy('cds.member_type', 'cds.member_id', 'cds.meal_time_id', 'member_name')
                ->get();

            if ($canteenSales->isEmpty()) {
                return returnData(5000, null, 'No canteen sales records found for this month.');
            }

            $mealTimes = [
                1 => 'breakfast',
                2 => 'lunch',
                3 => 'dinner',
            ];

            $report = [];

            foreach ($canteenSales as $sale) {
                $key = $sale->member_type . '_' . $sale->member_id;

                if (!isset($report[$key])) {
                    $report[$key] = [
                        'member_type' => $sale->member_type == 1 ? 'Student' : 'Employee',
                        'member_id' => $sale->member_id,
                        'member_name' => $sale->member_name ?? 'N/A',
                        'student_roll' => $sale->student_roll ?? null,
                        'employee_id' => $sale->employee_id ?? null,
                        'student_class' => $sale->student_class ?? null,
                        'employee_designation' => $sale->employee_designation ?? null,
                        'breakfast' => 0,
                        'lunch' => 0,
                        'dinner' => 0,
                        'total_amount' => 0,
                    ];
                }

                $mealKey = $mealTimes[$sale->meal_time_id] ?? null;
                if ($mealKey) {
                    $report[$key][$mealKey] += $sale->total_amount;
                    $report[$key]['total_amount'] += $sale->total_amount;
                }
            }

            $finalReport = collect(array_values($report));
            $monthName = Carbon::createFromDate(null, $month)->format('F');
            $multipleData = [
                'data' => $finalReport,
                'month' => $monthName,
                'year' => $year,
            ];

            return returnData(2000, $multipleData, 'Canteen monthly closing data get successfully.');

        } catch (\Exception $e) {
            return returnData(5000, $e->getMessage(), 'Whoops, something went wrong while generating closing data.');
        }
    }

    public function addCanteenClosingData(Request $request)
    {
        if (!can('canteen_month_closing_add')) {
            return $this->notPermitted();
        }

        try {
            $dataList = $request->input('data', []); // frontend sends { data: [...] }
            if (empty($dataList)) {
                return returnData(5000, null, 'No data found to insert.');
            }

            $schoolId = auth()->user()->school_id;
            $userId = auth()->id();

            $componentId = CanteenConfigure::where('school_id', $schoolId)
                ->where('status', 1)
                ->value('component');

            foreach ($dataList as $item) {
                $memberType = (int) $item['member_type']; // 1=Student, 2=Employee
                $memberId = $item['member_id'] ?? null;
                $month = (int) $item['month'];
                $year = (int) $item['year'];
                $totalAmount = (float) ($item['total_amount'] ?? 0);

                if (!$memberId || !$memberType) {
                    continue;
                }

                // Fetch entity info
                if ($memberType === 1) {
                    $entity = Student::find($memberId);
                    $entityCode = $entity?->student_roll ?? $memberId;
                    $entityName = $entity?->student_name_en ?? 'Student';
                    $typePrefix = 'stu';
                } else {
                    $entity = Employee::find($memberId);
                    $entityCode = $entity?->employee_id ?? $memberId;
                    $entityName = $entity?->name ?? 'Employee';
                    $typePrefix = 'emp';
                }

                if (!$entity) {
                    return returnData(5000, null, "Entity not found for ID {$memberId}");
                }

                // Format invoice identifiers
                $monthName = strtolower(Carbon::create()->month($month)->format('F'));
                $monthShort = strtolower(Carbon::create()->month($month)->format('M'));
                $yearShort = substr($year, -2);
                $date = Carbon::createFromDate($year, $month, 1)->format('Y-m-d');
                $toDate = now()->toDateString();

                $invoiceCode = "canteen-{$monthShort}-{$yearShort}-{$typePrefix}-{$entityCode}";

                // Prevent duplicate invoices
                $exists = Invoice::where('invoice_code', $invoiceCode)
                    ->where('school_id', $schoolId)
                    ->where('invoice_type', $memberType)
                    ->where('invoice_type_id', $memberId)
                    ->where('invoice_category', 2)
                    ->exists();


                if ($exists) {
                    return returnData(5000, null, "Invoice already created for {$monthName}");
                }

                // Save main invoice
                $invoice = Invoice::create([
                    'school_id' => $schoolId,
                    'date' => $date,
                    'to_date' => $toDate,
                    'month' => $monthName,
                    'invoice_code' => $invoiceCode,
                    'total_amount' => $totalAmount,
                    'total_waiver' => 0,
                    'total_payable' => $totalAmount,
                    'invoice_type' => $memberType,
                    'invoice_type_id' => $memberId,
                    'is_advance' => 0,
                    'invoice_category' => 2,
                    'created_by' => $userId,
                ]);

                // Save invoice detail
                InvoiceDetails::create([
                    'invoice_id' => $invoice->id,
                    'components_id' => $componentId,
                    'invoice_amount' => $totalAmount,
                    'waiver_amount' => 0,
                    'payable_amount' => $totalAmount,
                    'invoice_category' => 2,
                ]);
            }

            return returnData(2000, null, 'Canteen monthly closing successfully inserted.');
        } catch (\Exception $e) {
            return returnData(5000, $e->getMessage(), 'Whoops, something went wrong while generating invoice.');
        }
    }
}

