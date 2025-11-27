<?php

namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CanteenInvoiceController extends Controller
{
    public function index()
    {
        if (!can('canteen_invoice_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $month = request()->input('month');
            $invoice_category = (int) request()->input('invoice_category');

            if ($month) {
                $monthName = strtolower(date('F', mktime(0, 0, 0, (int) $month, 1)));
            } else {
                $monthName = null;
            }

            $data = DB::table('invoices')
                ->leftJoin('students', function ($join) {
                    $join->on('invoices.invoice_type_id', '=', 'students.id')
                        ->where('invoices.invoice_type', 1);
                })
                ->leftJoin('session_years as student_sessions', 'students.session_year_id', '=', 'student_sessions.id')
                ->leftJoin('student_classes as student_classes', 'students.class_id', '=', 'student_classes.id')
                ->leftJoin('employees', function ($join) {
                    $join->on('invoices.invoice_type_id', '=', 'employees.id')
                        ->where('invoices.invoice_type', 2);
                })
                ->leftJoin('employee_departments', 'employees.department_id', '=', 'employee_departments.id')
                ->leftJoin('employee_designations', 'employees.designation_id', '=', 'employee_designations.id')
                ->leftJoin('users as createdUser', 'invoices.created_by', '=', 'createdUser.id')
                ->leftJoin('users as updatedUser', 'invoices.updated_by', '=', 'updatedUser.id')
                ->leftJoin('schools', 'invoices.school_id', '=', 'schools.id')
                ->select(
                    'invoices.*',
                    'students.student_name_en',
                    'students.student_roll',
                    'student_sessions.title as session_title',
                    'student_classes.name as class_name',
                    'employees.name as employee_name',
                    'employees.employee_id',
                    'employee_departments.name as department_name',
                    'employee_designations.name as designation_name',
                    'createdUser.username as created_by_username',
                    'updatedUser.username as updated_by_username',
                    'schools.title as school_name',
                    DB::raw('(SELECT COUNT(*) FROM payments WHERE payments.invoice_id = invoices.id) as payments_count')
                )
                // Only invoices with category 2 or 5
                ->whereIn('invoices.invoice_category', [2, 5])
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('invoices.invoice_code', 'like', "%$keyword%")
                            ->orWhere('students.student_name_en', 'like', "%$keyword%")
                            ->orWhere('students.student_roll', 'like', "%$keyword%")
                            ->orWhere('employees.name', 'like', "%$keyword%")
                            ->orWhere('employees.employee_id', 'like', "%$keyword%");
                    });
                })
                ->when($monthName, function ($query) use ($monthName) {
                    $query->where('invoices.month', $monthName);
                })
                ->when($invoice_category, function ($query) use ($invoice_category) {
                    $query->where('invoices.invoice_category', $invoice_category);
                })
                ->orderBy('invoices.id', 'desc')
                ->paginate(input('perPage'));

            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function show($id)
    {
        if (!can('canteen_invoice_view')) {
            return $this->notPermitted();
        }
        $data['invoice'] = DB::table('invoices')
            ->leftJoin('students', function ($join) {
                $join->on('invoices.invoice_type_id', '=', 'students.id')
                    ->where('invoices.invoice_type', 1);
            })
            ->leftJoin('session_years', 'students.session_year_id', '=', 'session_years.id')
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('employees', function ($join) {
                $join->on('invoices.invoice_type_id', '=', 'employees.id')
                    ->where('invoices.invoice_type', 2);
            })
            ->leftJoin('employee_departments', 'employees.department_id', '=', 'employee_departments.id')
            ->leftJoin('employee_designations', 'employees.designation_id', '=', 'employee_designations.id')
            ->leftJoin('users as createdUser', 'invoices.created_by', '=', 'createdUser.id')
            ->leftJoin('users as updatedUser', 'invoices.updated_by', '=', 'updatedUser.id')
            ->leftJoin('schools', 'invoices.school_id', '=', 'schools.id')

            ->where('invoices.id', $id)
            ->select(
                'invoices.*',
                'students.student_name_en',
                'students.student_roll',
                'students.session_year_id',
                'session_years.title as session_title',
                'student_classes.name as class_name',
                'employees.name as employee_name',
                'employees.employee_id',
                'employee_departments.name as department_name',
                'employee_designations.name as designation_name',
                'createdUser.username as created_by_username',
                'updatedUser.username as updated_by_username',
                'schools.title as school_name',
            )
            ->first();

        $data['invoice_details'] = DB::table('invoice_details')
            ->leftJoin('components', 'invoice_details.components_id', '=', 'components.id')
            ->where('invoice_details.invoice_id', $id)
            ->select(
                'invoice_details.*',
                'components.name as component_name'
            )
            ->get();

        return returnData(2000, $data);
    }

    public function destroy($id)
    {
        if (!can('canteen_invoice_delete')) {
            return $this->notPermitted();
        }

        $check_payment = DB::table('payments')
            ->where('invoice_id', $id)
            ->first();

        if ($check_payment) {
            return returnData(3000, null, 'Invoice cannot be deleted as it has related payments.');
        }

        $invoice = DB::table('invoices')->where('id', $id)->first();

        if ($invoice->invoice_category == 5) {
            return returnData(3000, null, 'Cash Payment cannot be deleted.');
        }

        if (!$invoice) {
            return returnData(4000, null, 'Invoice not found.');
        }

        $rosterTypeMap = [
            1 => 'student',
            2 => 'employee',
            3 => 'guest',
        ];

        $rosterType = $rosterTypeMap[$invoice->invoice_type] ?? null;

        DB::beginTransaction();

        try {
            DB::table('invoice_details')
                ->where('invoice_id', $id)
                ->delete();

            DB::table('roster_canteens')
                ->where('roster_id', $invoice->invoice_type_id)
                ->where('roster_type', $rosterType)
                ->where('school_id', $invoice->school_id)
                ->whereMonth('roster_date', date('m', strtotime($invoice->date)))
                ->whereYear('roster_date', date('Y', strtotime($invoice->date)))
                ->delete();

            DB::table('invoices')
                ->where('id', $id)
                ->delete();

            DB::commit();
            return returnData(2000, null, 'Invoice and related roster data deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return returnData(5000, null, 'Error: ' . $e->getMessage());
        }
    }
}
