<?php

namespace App\Http\Controllers\Canteen;

use Carbon\Carbon;
use App\Models\Guest;
use App\Models\Staff;
use App\Helpers\Helper;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\InvoiceDetails;
use App\Models\CanteenConfigure;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RosterClosingController extends Controller
{
    use Helper;

    public function store(Request $request)
    {
        if (!can('roster_add')) {
            return $this->notPermitted();
        }
        $schoolId = auth()->user()->school_id;

        try {
            $input = $request->all();

            $componentId = CanteenConfigure::where('school_id', $schoolId)
                ->where('status', 1)
                ->pluck('component')
                ->first();

            foreach ($input as $data) {
                $entityType = $data['roster_type'];
                $entityId = null;

                // Set the invoice type and invoice type id based on roster type
                switch ($entityType) {
                    case 'student':
                        $entityId = $data['student_id'];
                        $entityData = Student::where('id', $entityId)->first();
                        break;
                    case 'teacher':
                        $entityId = $data['teacher_id'];
                        $entityData = Teacher::where('id', $entityId)->first();
                        break;
                    case 'staff':
                        $entityId = $data['staff_id'];
                        $entityData = Staff::where('id', $entityId)->first();
                        break;
                    case 'guest':
                        $entityId = $data['guest_id'];
                        $entityData = Guest::where('id', $entityId)->first();
                        break;
                    default:
                        return returnData(5000, null, 'Invalid entity type.');
                }

                if (!$entityData) {
                    return returnData(5000, null, 'Entity not found.');
                }

                $date = Carbon::now();
                $dateData = $date->format('Y-m-d');
                $formattedMonth = strtolower($date->format('F'));
                $formattedYear = $date->format('y');

                $invoiceCanteen = 'canteen';
                $invoiceCode = $invoiceCanteen . '/' . $formattedMonth . '/' . $formattedYear . '/' . $entityType;

                $existingInvoice = Invoice::where('invoice_code', $invoiceCode)->first();

                if ($existingInvoice) {
                    return returnData(5000, null, 'Invoice already exists for this month.');
                }

                $invoice = new Invoice();
                $invoice->school_id = $schoolId;
                $invoice->date = $dateData;
                $invoice->invoice_code = $invoiceCode;
                $invoice->total_amount = $data['total_amount'];
                $invoice->total_waiver = 0;
                $invoice->total_payable = $data['total_amount'];
                $invoice->invoice_type = $entityType; // Set the invoice type
                $invoice->invoice_type_id = $entityId; // Set the corresponding entity ID
                $invoice->save();

                $invoiceDetail = new InvoiceDetails();
                $invoiceDetail->invoice_id = $invoice->id;
                $invoiceDetail->components_id = $componentId;
                $invoiceDetail->invoice_amount = $data['total_amount'];
                $invoiceDetail->waiver_amount = 0;
                $invoiceDetail->payable_amount = $data['total_amount'];
                $invoiceDetail->save();
            }

            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function getRosterClosingData()
    {
        $month = request()->input('month');
        $years = request()->input('years');
        $schoolId = auth()->user()->school_id;

        $existingData = DB::table('roster_canteens')
            ->leftJoin('students', function ($join) {
                $join->on('roster_canteens.roster_id', '=', 'students.id')
                    ->where('roster_canteens.roster_type', 'student');
            })
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('teachers', function ($join) {
                $join->on('roster_canteens.roster_id', '=', 'teachers.id')
                    ->where('roster_canteens.roster_type', 'teacher');
            })
            ->leftJoin('designations as teacher_designation', 'teachers.designation_id', '=', 'teacher_designation.id')
            ->leftJoin('work_departments as teacher_department', 'teachers.department_id', '=', 'teacher_department.id')
            ->leftJoin('staff', function ($join) {
                $join->on('roster_canteens.roster_id', '=', 'staff.id')
                    ->where('roster_canteens.roster_type', 'staff');
            })
            ->leftJoin('designations as staff_designation', 'staff.designation_id', '=', 'staff_designation.id')
            ->leftJoin('work_departments as staff_department', 'staff.department_id', '=', 'staff_department.id')
            ->leftJoin('guests', function ($join) {
                $join->on('roster_canteens.roster_id', '=', 'guests.id')
                    ->where('roster_canteens.roster_type', 'guest');
            })
            ->select(
                'roster_canteens.*',
                'students.student_roll as student_roll',
                'students.id as student_primary_id',
                'students.student_name_en as student_name',
                'student_classes.name as student_class',
                'teachers.name as teacher_name',
                'teachers.employee_id as teacher_id',
                'teachers.id as teacher_primary_id',
                'teacher_designation.name as teacher_designation',
                'teacher_department.name as teacher_department',
                'staff.name as staff_name',
                'staff.employee_id as staff_id',
                'staff.id as staff_primary_id',
                'staff_designation.name as staff_designation',
                'staff_department.name as staff_department',
                'guests.guest_name as guest_name',
                'guests.guest_id as guest_id',
                'guests.id as guest_primary_id',
                'guests.guest_designation as guest_designation'
            )
            ->whereMonth('roster_canteens.roster_date', $month)
            ->whereYear('roster_canteens.roster_date', $years)
            ->where('roster_canteens.school_id', $schoolId)
            ->get();

        $summedData = [];

        foreach ($existingData as $item) {
            $component_amount = json_decode($item->component_amount, true);
            if (!is_array($component_amount)) {
                $component_amount = json_decode($component_amount, true);
            }

            $key = ($item->student_primary_id ?? 'student') . '_' .
                ($item->teacher_primary_id ?? 'teacher') . '_' .
                ($item->staff_primary_id ?? 'staff') . '_' .
                ($item->guest_primary_id ?? 'guest');

            if (!isset($summedData[$key])) {
                $summedData[$key] = [
                    'student_roll' => $item->student_roll,
                    'student_primary_id' => $item->student_primary_id,
                    'student_name' => $item->student_name,
                    'student_class' => $item->student_class,
                    'teacher_primary_id' => $item->teacher_primary_id,
                    'teacher_id' => $item->teacher_id,
                    'teacher_name' => $item->teacher_name,
                    'teacher_designation' => $item->teacher_designation,
                    'staff_primary_id' => $item->staff_primary_id,
                    'staff_id' => $item->staff_id,
                    'staff_name' => $item->staff_name,
                    'staff_designation' => $item->staff_designation,
                    'guest_primary_id' => $item->guest_primary_id,
                    'guest_id' => $item->guest_id,
                    'guest_name' => $item->guest_name,
                    'guest_designation' => $item->guest_designation,
                    'roster_type' => $item->roster_type,
                    'component_amount' => []
                ];
            }

            foreach ($component_amount as $component => $amount) {
                if (!isset($summedData[$key]['component_amount'][$component])) {
                    $summedData[$key]['component_amount'][$component] = 0;
                }
                $summedData[$key]['component_amount'][$component] += $amount;
            }
        }

        $component = DB::table('canteen_components')
            ->select('*')
            ->where('status', 1)
            ->where('school_id', $schoolId)
            ->get();

        $existingMultipleData = [
            'data' => array_values($summedData),
            'component' => $component,
        ];

        return returnData(2000, $existingMultipleData);
    }
}
