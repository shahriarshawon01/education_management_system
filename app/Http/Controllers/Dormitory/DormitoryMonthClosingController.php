<?php

namespace App\Http\Controllers\Dormitory;

use Carbon\Carbon;
use App\Models\Guest;
use App\Helpers\Helper;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\InvoiceDetails;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Dormitory\DormitoryComponent;

class DormitoryMonthClosingController extends Controller
{
    use Helper;

    public function getDormitoryClosingData()
    {
        $month = request()->input('dormitory_month');
        $year = request()->input('years');
        $dormitoryUser = request()->input('dormitory_user_type') ?? null;
        $dormitoryId = request()->input('dormitory_id');

        $components = DB::table('dormitory_components')
            ->leftJoin('components', 'dormitory_components.component_id', '=', 'components.id')
            ->select('dormitory_components.*', 'components.name')
            ->where('dormitory_components.status', 1)
            ->where('components.component_type', 3)
            ->get();

        $assignDormitories = DB::table('assign_dormitories')
            ->leftJoin('students', function ($join) {
                $join->on('assign_dormitories.dormitory_user_id', '=', 'students.id')
                    ->where('assign_dormitories.dormitory_user_type', 1);
            })
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('employees', function ($join) {
                $join->on('assign_dormitories.dormitory_user_id', '=', 'employees.id')
                    ->where('assign_dormitories.dormitory_user_type', 2);
            })
            ->leftJoin('employee_designations as employee_designation', 'employees.designation_id', '=', 'employee_designation.id')
            ->leftJoin('employee_departments as employee_department', 'employees.department_id', '=', 'employee_department.id')
            ->leftJoin('schools', 'assign_dormitories.school_id', '=', 'schools.id')
            ->leftJoin('manage_dormitories', 'assign_dormitories.dormitory_id', 'manage_dormitories.id')
            ->select(
                'assign_dormitories.*',
                'students.student_name_en as student_name',
                'students.student_roll as student_roll',
                'students.id as student_primary_id',
                'student_classes.name as student_class',
                'employees.name as employee_name',
                'employees.employee_id as employee_id',
                'employees.id as employee_primary_id',
                'employee_designation.name as employee_designation',
                'employee_department.name as employee_department',
                'schools.title as school_name',
                'schools.id as school_id',
                'manage_dormitories.dormitory_name',
                'manage_dormitories.id as dormitory_id'
            )
            ->where('assign_dormitories.status', 1)
            ->when(!empty($dormitoryUser), function ($query) use ($dormitoryUser) {
                return $query->where('assign_dormitories.dormitory_user_type', $dormitoryUser);
            })->when(!empty($dormitoryId), function ($query) use ($dormitoryId) {
                return $query->where('assign_dormitories.dormitory_id', $dormitoryId);
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
            'data' => $assignDormitories,
            'component' => $components,
            'month' => $month,
            'year' => $year
        ];

        return returnData(2000, $multipleData);
    }

    public function addDormitoryClosingData(Request $request)
    {
        if (!can('dormitory_month_closing_add')) {
            return $this->notPermitted();
        }

        try {
            $input = $request->all();
            $userId = auth()->id();
            $authSchoolId = auth()->user()->school_id;
            $componentId = DormitoryComponent::where('status', 1)
                ->pluck('component_id')
                ->first();

            foreach ($input as $data) {
                $entityType = $data['dormitory_user_type'] ?? null;

                if (!$entityType) {
                    return returnData(5000, null, 'Invalid entity type.');
                }

                $entityId = null;
                $entityData = null;

                switch ($entityType) {
                    case 1:
                        $entityId = $data['student_id'] ?? null;
                        $entityData = DB::table('students')->where('id', $entityId)->first();
                        break;
                    case 2:
                        $entityId = $data['employee_id'] ?? null;
                        $entityData = DB::table('employees')->where('id', $entityId)->first();
                        break;
                    case 3:
                        $entityId = $data['guest_id'] ?? null;
                        $entityData = Guest::where('id', $entityId)->first();
                        break;
                }

                if (!$entityData || !$entityId) {
                    return returnData(5000, null, 'Entity not found.');
                }

                $monthNumber = $data['dormitory_month'];
                $year = $data['years'] ?? Carbon::now()->year;
                $date = Carbon::createFromDate($year, $monthNumber, 1);
                $monthName = strtolower($date->format('F'));
                $formattedMonth = strtolower($date->format('M'));
                $formattedYear = $date->format('y');
                $toDate = now()->toDateString();

                $invoiceCategory = 'dormitory';
                if ($entityType == 1) {
                    $studentID = $entityData->student_roll;
                    $invoiceCode = $invoiceCategory . '-' . $formattedMonth . '-' . $formattedYear . '-stu-' . $studentID;
                } elseif ($entityType == 2) {
                    $employeeId = $entityData->employee_id;
                    $invoiceCode = $invoiceCategory . '-' . $formattedMonth . '-' . $formattedYear . '-emp-' . $employeeId;
                } else {
                    $guestId = $entityData->employee_id ?? $entityData->id;
                    $invoiceCode = $invoiceCategory . '-' . $formattedMonth . '-' . $formattedYear . '-guest-' . $guestId;
                }

                $existingInvoice = DB::table('invoices')
                    ->where('invoice_type', $entityType)
                    ->where('invoice_type_id', $entityId)
                    ->where('invoice_category', 3)
                    ->where('school_id', $authSchoolId)
                    ->whereYear('date', $year)
                    ->where('month', $monthName)
                    ->where('invoice_code', $invoiceCode)
                    ->first();

                // if ($existingInvoice) {
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
                    'invoice_category' => 3,
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
                    'invoice_category' => 3,
                ]);
            }

            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
}
