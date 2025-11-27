<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TransportInvoiceController extends Controller
{
    public function index()
    {
        if (!can('transport_invoice_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $month = request()->input('month');
            $year = request()->input('years');

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
                    'employees.employee_type',
                    'createdUser.username as created_by_username',
                    'updatedUser.username as updated_by_username',
                    'schools.title as school_name',
                    DB::raw('(SELECT COUNT(*) FROM payments WHERE payments.invoice_id = invoices.id) as payments_count')
                )
                ->where('invoices.invoice_category', 4)
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
                ->when($year, function ($query) use ($year) {
                    $query->whereYear('invoices.date', $year);
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
        if (!can('transport_invoice_view')) {
            return $this->notPermitted();
        }

        try {
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
                    'employees.employee_type',
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
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('transport_invoice_delete')) {
            return $this->notPermitted();
        }

        $check_payment = DB::table('payments')
            ->where('invoice_id', $id)
            ->first();

        if ($check_payment) {
            return returnData(3000, null, 'Invoice cannot be deleted as it has related payments.');
        }

        $invoice = DB::table('invoices')->where('id', $id)->first();

        if (!$invoice) {
            return returnData(4000, null, 'Invoice not found.');
        }

        $userTypeMap = [
            1 => 1, // student
            2 => 2, // teacher
            3 => 3, // staff
            4 => 4, // guest
        ];

        $userType = $userTypeMap[$invoice->invoice_type] ?? null;

        if (!$userType) {
            return returnData(5000, null, 'Invalid transport user type.');
        }

        DB::beginTransaction();

        try {
            DB::table('invoice_details')
                ->where('invoice_id', $id)
                ->delete();

            DB::table('transport_monthly_fees')
                ->where('roster_id', $invoice->invoice_type_id)
                ->where('transport_user_type', $userType)
                ->where('school_id', $invoice->school_id)
                ->where('roster_year', date('Y', strtotime($invoice->date)))
                ->where('roster_month', date('n', strtotime($invoice->date)))
                ->delete();

            DB::table('invoices')
                ->where('id', $id)
                ->delete();

            DB::commit();
            return returnData(2000, null, 'Invoice and related transport data deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return returnData(5000, null, 'Error: ' . $e->getMessage());
        }
    }
}
