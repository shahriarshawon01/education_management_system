<?php

namespace App\Http\Controllers\Transport;

use Carbon\Carbon;
use App\Models\Guest;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\InvoiceDetails;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Transport\TransportComponent;

class TransportMonthClosingController extends Controller
{
    public function getTransportClosingData()
    {
        if (!can('transport_month_closing_view')) {
            return $this->notPermitted();
        }

        $month = request()->input('transport_month') ?? date('m');
        $year = request()->input('roster_year') ?? date('Y');
        $consumerType = request()->input('roster_type') ?? null;
        $transportId = request()->input('transport_id');

        if (empty($month)) {
            return returnData(5000, null, 'Transport date field is required!');
        }

        $components = DB::table('transport_components')
            ->leftJoin('components', 'transport_components.component_id', '=', 'components.id')
            ->select('transport_components.*', 'components.name')
            ->where('transport_components.status', 1)
            ->where('components.component_type', 4)
            ->get();

        $assignTransports = DB::table('assign_transports')
            ->leftJoin('students', function ($join) {
                $join->on('assign_transports.transport_user_id', '=', 'students.id')
                    ->where('assign_transports.transport_user_type', 1);
            })
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('employees', function ($join) {
                $join->on('assign_transports.transport_user_id', '=', 'employees.id')
                    ->where('assign_transports.transport_user_type', 2);
            })
            ->leftJoin('employee_designations as employee_designation', 'employees.designation_id', '=', 'employee_designation.id')
            ->leftJoin('employee_departments as employee_department', 'employees.department_id', '=', 'employee_department.id')
            ->leftJoin('schools', 'assign_transports.school_id', '=', 'schools.id')
            ->leftJoin('transport_manages', 'assign_transports.transport_id', 'transport_manages.id')
            ->select(
                'assign_transports.*',
                'students.student_roll as student_roll',
                'students.id as student_primary_id',
                'students.student_name_en as student_name',
                'student_classes.name as student_class',
                'employees.name as employee_name',
                'employees.employee_id as employee_id',
                'employees.id as employee_primary_id',
                'employee_designation.name as employee_designation',
                'employee_department.name as employee_department',
                'schools.title as school_name',
                'schools.id as school_id',
                'transport_manages.id as transport_id',
                'transport_manages.transport_name',
            )
            ->where('assign_transports.status', 1)
            ->when(!empty($consumerType), function ($query) use ($consumerType) {
                return $query->where('assign_transports.transport_user_type', $consumerType);
            })
            ->when(!empty($transportId), function ($query) use ($transportId) {
                return $query->where('assign_transports.transport_id', $transportId);
            })
            ->get()
            ->map(function ($item) use ($components) {
                foreach ($components as $comp) {
                    $field = 'component_' . $comp->id;

                    $item->{$field} = $item->{$field} ?? $item->payable_amount ?? 0;
                }

                return $item;
            });

        $multipleData = [
            'data' => $assignTransports,
            'component' => $components,
            'month' => $month,
            'year' => $year
        ];

        return returnData(2000, $multipleData);
    }

    public function addTransportClosingData(Request $request)
    {
        if (!can('transport_month_closing_add')) {
            return $this->notPermitted();
        }

        $input = $request->all();
        $userId = auth()->id();
        $authSchoolId = auth()->user()->school_id;
        $componentId = TransportComponent::where('status', 1)->pluck('component_id')->first();

        DB::beginTransaction();
        try {
            foreach ($input as $data) {
                // Validate transport user type
                $entityType = $data['transport_user_type'] ?? null;
                if (!$entityType) {
                    return returnData(5000, null, 'Transport user type is required.');
                }

                // Get entity data
                $entityId = null;
                $entityData = null;
                switch ($entityType) {
                    case 1: // Student
                        $entityId = $data['student_id'] ?? null;
                        $entityData = DB::table('students')->where('id', $entityId)->first();
                        break;
                    case 2: // employee
                        $entityId = $data['employee_id'] ?? null;
                        $entityData = DB::table('employees')->where('id', $entityId)->first();
                        break;
                    case 3: // Guest
                        $entityId = $data['guest_id'] ?? null;
                        $entityData = Guest::where('id', $entityId)->first();
                        break;
                }

                if (!$entityId || !$entityData) {
                    return returnData(5000, null, 'Entity not found.');
                }

                // Month & year
                $monthNumber = $data['transport_month'];
                $year = $data['years'] ?? Carbon::now()->year;
                $date = Carbon::createFromDate($year, $monthNumber, 1);
                $monthName = strtolower($date->format('F'));
                $formattedMonth = strtolower($date->format('M'));
                $formattedYear = $date->format('y');
                $toDate = now()->toDateString();

                // Generate invoice code
                if ($entityType == 1) {
                    $invoiceCode = "transport-{$formattedMonth}-{$formattedYear}-stu-{$entityData->student_roll}";
                } elseif ($entityType == 2) {
                    $invoiceCode = "transport-{$formattedMonth}-{$formattedYear}-emp-{$entityData->employee_id}";
                } else {
                    $guestId = $entityData->employee_id ?? $entityData->id;
                    $invoiceCode = "transport-{$formattedMonth}-{$formattedYear}-guest-{$guestId}";
                }

                // Check existing invoice
                $existingInvoice = DB::table('invoices')
                    ->where('invoice_type', $entityType)
                    ->where('invoice_type_id', $entityId)
                    ->where('invoice_category', 4)
                    ->where('school_id', $authSchoolId)
                    ->whereYear('date', $year)
                    ->where('month', $monthName)
                    ->where('invoice_code', $invoiceCode)
                    ->exists();

                // if ($exists) {
                //     $entityName = $entityData->student_name_en ?? $entityData->name ?? $entityData->guest_name ?? 'Entity';
                //     return returnData(5000, null, "Invoice already exists for {$entityName} for this month.");
                // }
                if ($existingInvoice) {
                    continue;
                }

                // Amounts
                $total_amount = $data['total_amount'] ?? 0;
                $total_waiver = $data['total_waiver'] ?? 0;
                $total_payable = $data['total_payable'] ?? 0;

                // Insert invoice
                $invoice = Invoice::create([
                    'school_id' => $authSchoolId,
                    'date' => $date->format('Y-m-d'),
                    'to_date' => $toDate,
                    'month' => $monthName,
                    'invoice_code' => $invoiceCode,
                    'total_amount' => $total_amount,
                    'total_waiver' => $total_waiver,
                    'total_payable' => $total_payable,
                    'invoice_type' => $entityType,
                    'invoice_type_id' => $entityId,
                    'is_advance' => 0,
                    'invoice_category' => 4,
                    'created_by' => $userId,
                    'updated_by' => null,
                ]);

                // Insert invoice details
                InvoiceDetails::create([
                    'invoice_id' => $invoice->id,
                    'components_id' => $componentId,
                    'invoice_amount' => $total_amount,
                    'waiver_amount' => $total_waiver,
                    'payable_amount' => $total_payable,
                    'invoice_category' => 4,
                ]);
            }

            DB::commit();
            return returnData(2000, null, 'Invoice Generated Successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return returnData(5000, $e->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
}
