<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // public function collectionReport()
    
    // {
    //     $school_id = auth()->user()->school_id;
    //     $student_id = input('student_id');
    //     $session_id = input('session_id');
    //     $class_id = input('class_id');
    //     $invoice_id = input('invoice_id');
    //     $department_id = input('department_id');

    //     if (!$invoice_id) {
    //         $groupBy = 'payments.id';
    //         $select = 'students.student_name_en as component_name';
    //     }
    //     if (!$student_id) {
    //         $groupBy = 'payments.student_id';
    //         $select = 'students.student_name_en as component_name';
    //     }
    //     if ($invoice_id) {
    //         $groupBy = 'payment_details.component_id';
    //         $select = 'components.name as component_name';
    //     }

    //     $data = DB::table('payments')
    //         ->leftJoin('payment_details', 'payments.id', '=', 'payment_details.payments_id')
    //         ->leftJoin('components', 'payment_details.component_id', '=', 'components.id')
    //         ->leftJoin('students', 'payments.student_id', '=', 'students.id')
    //         ->leftJoin('schools', 'payments.school_id', '=', 'schools.id')
    //         ->leftJoin('session_years', 'payments.session_year_id', '=', 'session_years.id')
    //         ->leftJoin('student_classes', 'payments.class_id', '=', 'student_classes.id')
    //         ->selectRaw("payments.*,payment_details.*,students.student_name_en as student_anme, schools.title as school_name,schools.title as school_name,$select,session_years.title as session_name,student_classes.name as class_name,SUM(amount) as amount, SUM(waiver) as waiver, SUM(payed) as payed,schools.steb_number as steb_number,schools.institute_code as code,schools.address as address")
    //         ->where('payments.school_id', $school_id)
    //         ->where('payments.class_id', $class_id)
    //         ->when($session_id, function ($query) use ($session_id) {
    //             $query->where('payments.session_year_id', $session_id);
    //         })
    //         ->when($student_id, function ($query) use ($student_id) {
    //             $query->where('payments.student_id', $student_id);
    //         })
    //         ->when($department_id, function ($query) use ($department_id) {
    //             $query->where('students.department_id', $department_id);
    //         })
    //         ->when($invoice_id, function ($query) use ($invoice_id) {
    //             $query->where('payments.invoice_id', $invoice_id);
    //         })
    //         ->groupBy($groupBy)
    //         ->get();

    //     $single = $data->first();

    //     $responseData = [
    //         'componentWiseData' => $data,
    //         'total_amount' => $data->sum('amount'),
    //         'total_waiver' => $data->sum('waiver'),
    //         'total_payed' => $data->sum('payed'),
    //         'school_name' => $single ? $single->school_name : '',
    //         'steb_number' => $single ? $single->steb_number : '',
    //         'code' => $single ? $single->code : '',
    //         'address' => $single ? $single->address : '',
    //         'session_name' => $single ? $single->session_name : '',
    //         'class_name' => $single ? $single->class_name : '',
    //         'student_name' => ($student_id && $single) ? $single->student_anme : '',
    //     ];

    //     return returnData(2000, $responseData);
    // }

    
    // public function invoiceReport()
    // {
    //     $school_id = auth()->user()->school_id;
    //     $student_id = input('student_id');
    //     $session_id = input('session_id');
    //     $class_id = input('class_id');
    //     $invoice_id = input('invoice_id');
    //     $department_id = input('department_id');

    //     if (!$invoice_id) {
    //         $groupBy = 'invoices.id';
    //         $select = 'invoices.invoice_code as component_name';
    //     }
    //     if (!$student_id) {
    //         $groupBy = 'invoices.student_id';
    //         $select = 'students.student_name_en as component_name';
    //     }
    //     if ($invoice_id) {
    //         $groupBy = 'invoice_details.components_id';
    //         $select = 'components.name as component_name';
    //     }

    //     $data = DB::table('invoices')
    //         ->leftJoin('invoice_details', 'invoices.id', '=', 'invoice_details.invoice_id')
    //         ->leftJoin('components', 'invoice_details.components_id', '=', 'components.id')
    //         ->leftJoin('students', 'invoices.student_id', '=', 'students.id')
    //         ->leftJoin('schools', 'invoices.school_id', '=', 'schools.id')
    //         ->leftJoin('session_years', 'invoices.session_year_id', '=', 'session_years.id')
    //         ->leftJoin('student_classes', 'invoices.class_id', '=', 'student_classes.id')
    //         ->selectRaw("invoices.*,invoice_details.*,students.student_name_en as student_anme, schools.title as school_name,schools.title as school_name,$select,session_years.title as session_name,student_classes.name as class_name,SUM(invoice_amount) as invoice_amount, SUM(waiver_amount) as waiver_amount, SUM(payable_amount) as payable_amount,schools.steb_number as steb_number,schools.institute_code as code,schools.address as address")
    //         ->where('invoices.school_id', $school_id)
    //         ->where('invoices.class_id', $class_id)
    //         ->when($session_id, function ($query) use ($session_id) {
    //             $query->where('invoices.session_year_id', $session_id);
    //         })
    //         ->when($student_id, function ($query) use ($student_id) {
    //             $query->where('invoices.student_id', $student_id);
    //         })
    //         ->when($department_id, function ($query) use ($department_id) {
    //             $query->where('students.department_id', $department_id);
    //         })
    //         ->when($invoice_id, function ($query) use ($invoice_id) {
    //             $query->where('invoice_details.invoice_id', $invoice_id);
    //         })
    //         ->groupBy($groupBy)
    //         ->get();

    //     $single = $data->first();

    //     $responseData = [
    //         'componentWiseData' => $data,
    //         'total_amount' => $data->sum('invoice_amount'),
    //         'total_waiver' => $data->sum('waiver_amount'),
    //         'total_payable' => $data->sum('payable_amount'),
    //         'school_name' => $single ? $single->school_name : '',
    //         'session_name' => $single ? $single->session_name : '',
    //         'steb_number' => $single ? $single->steb_number : '',
    //         'code' => $single ? $single->code : '',
    //         'address' => $single ? $single->address : '',
    //         'class_name' => $single ? $single->class_name : '',
    //         'student_name' => ($student_id && $single) ? $single->student_anme : '',
    //     ];

    //     return returnData(2000, $responseData);
    // }

    // public function dueReport()
    // {
    //     $school_id = input('school_id');
    //     $student_id = input('student_id');
    //     $session_id = input('session_year_id');
    //     $class_id = input('class_id');
    //     $rollNo = input('roll');

    //     $data['data'] = DB::table('invoices')
    //         ->leftJoin('students', 'invoices.student_id', '=', 'students.id')
    //         ->leftJoin('schools', 'invoices.school_id', '=', 'schools.id')
    //         ->leftJoin('session_years', 'invoices.session_year_id', '=', 'session_years.id')
    //         ->leftJoin('student_classes', 'invoices.class_id', '=', 'student_classes.id')
    //         ->leftJoin('payments', 'invoices.id', '=', 'payments.invoice_id')
    //         ->select(
    //             'invoices.*',
    //             'schools.title as school_name',
    //             'invoices.total_payable as total_payable',
    //             'students.student_name_en as student_name',
    //             'session_years.title as session_name',
    //             'student_classes.name as class_name',
    //             // DB::raw('SUM(invoices.total_payable) as total_payable'),
    //             DB::raw('IFNULL(SUM(payments.total_payed), 0) as total_payed'),
    //             DB::raw('SUM(invoices.total_payable) - IFNULL(SUM(payments.total_payed), 0) AS due_amount')
    //         )
    //         ->where('invoices.school_id', $school_id)
    //         ->where('invoices.class_id', $class_id)
    //         ->when($session_id, function ($query) use ($session_id) {
    //             $query->where('invoices.session_year_id', $session_id);
    //         })
    //         ->when($student_id, function ($query) use ($student_id) {
    //             $query->where('invoices.student_id', $student_id);
    //         })
    //         ->when($rollNo, function ($query) use ($rollNo) {
    //             $query->where('students.roll', $rollNo);
    //         })
    //         ->groupBy('invoices.student_id')
    //         ->get();

    //     $total_payable = $data['data']->sum('total_payable');
    //     $total_payed = $data['data']->sum('total_payed');
    //     $total_due = $data['data']->sum('due_amount');

    //     $school_name = $data['data']->pluck('school_name')->first();
    //     $session_name = $data['data']->pluck('session_name')->first();
    //     $class_name = $data['data']->pluck('class_name')->first();
    //     $data['option'] = (object)[
    //         'total_payable' => $total_payable,
    //         'total_payed' => $total_payed,
    //         'total_due' => $total_due,
    //         'school_name' => $school_name,
    //         'session_name' => $session_name,
    //         'class_name' => $class_name,
    //     ];

    //     return returnData(2000, $data);
    // }

    // public function ledgerReport()
    // {
    //     $school_id = input('school_id');
    //     $student_id = input('student_id');
    //     $session_id = input('session_year_id');
    //     $class_id = input('class_id');
    //     $section_id = input('section_id');

    //     $data = DB::table('payments')
    //         ->leftJoin('payment_details', 'payments.id', '=', 'payment_details.payments_id')
    //         ->leftJoin('students', 'payments.student_id', '=', 'students.id')
    //         ->leftJoin('schools', 'payments.school_id', '=', 'schools.id')
    //         ->leftJoin('session_years', 'payments.session_year_id', '=', 'session_years.id')
    //         ->leftJoin('student_classes', 'payments.class_id', '=', 'student_classes.id')
    //         ->leftJoin('components', 'payment_details.component_id', '=', 'components.id')
    //         ->selectRaw("payments.*,payment_details.*,students.student_name_en as student_name,schools.title as school_name,components.name as component_name,session_years.title as session_name,student_classes.name as class_name")
    //         ->where('payments.school_id', $school_id)
    //         ->where('payments.class_id', $class_id)
    //         ->where('students.section_id', $section_id)
    //         ->where('payments.session_year_id', $session_id)
    //         ->where('payments.student_id', $student_id)
    //         ->get()->groupBy('date')->sortByDesc('date');


    //     $headerData = DB::table('components')
    //         ->leftJoin('component_groups', 'components.group_id', '=', 'component_groups.id')
    //         ->selectRaw("DISTINCT component_groups.id as group_id, component_groups.name as group_name")
    //         ->groupBy('component_groups.id')
    //         ->get();

    //     $single = [];
    //     foreach ($headerData as $group) {
    //         $components = DB::table('components')
    //             ->leftJoin('component_groups', 'components.group_id', '=', 'component_groups.id')
    //             ->selectRaw("components.name as component_name, components.id")
    //             ->where('component_groups.id', $group->group_id)
    //             ->get();
    //         $single[$group->group_name] = $components;
    //     }

    //     $components = DB::table('components')
    //         ->selectRaw("components.id, components.name, CASE WHEN SUM(payment_details.payed) > 0 THEN SUM(payment_details.payed) ELSE 0 END as totalAmount")
    //         ->leftJoin('payment_details', 'components.id', '=', 'payment_details.component_id')
    //         ->join('payments', function ($query) use ($school_id, $class_id, $section_id, $session_id, $student_id) {
    //             $query->on('payment_details.payments_id', '=', 'payments.id');
    //             $query->join('students', 'payments.student_id', '=', 'students.id');
    //             $query->where('payments.school_id', $school_id);
    //             $query->where('payments.class_id', $class_id);
    //             $query->where('payments.session_year_id', $session_id);
    //             $query->where('payments.student_id', $student_id);
    //             $query->where('students.section_id', $section_id);
    //         })
    //         ->selectRaw("components.name as component_name, components.id")
    //         ->groupBy('payment_details.component_id')
    //         ->get();

    //     $singleData = $data->first();

    //     $school_name = $singleData ? $singleData->pluck('school_name')->first() : '';
    //     $session_name = $singleData ? $singleData->pluck('session_name')->first() : '';
    //     $student_name = $singleData ? $singleData->pluck('student_name')->first() : '';
    //     $class_name = $singleData ? $singleData->pluck('class_name')->first() : '';


    //     $multipleData = [
    //         'data' => $data,
    //         'headerData' => $single,
    //         'components_total' => $components,

    //         'school_name' => $school_name,
    //         'session_name' => $session_name,
    //         'student_name' => $student_name,
    //         'class_name' => $class_name,
    //     ];


    //     return returnData(2000, $multipleData);
    // }

    // public function studentReport()
    // {
    //     $school_id = input('school_id');
    //     $student_id = input('student_id');
    //     $session_id = input('session_id');
    //     $class_id = input('class_id');

    //     $data = DB::table('students')
    //         ->leftJoin('schools', 'students.school_id', '=', 'schools.id')
    //         ->leftJoin('session_years', 'students.session_id', '=', 'session_years.id')
    //         ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
    //         ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
    //         ->selectRaw("students.*,schools.title as school_name,session_years.title as session_name,student_classes.name as class_name,sections.name as section_name")
    //         ->where('students.school_id', $school_id)
    //         ->when($class_id, function ($query) use ($class_id) {
    //             $query->where('students.class_id', $class_id);
    //         })
    //         ->when($session_id, function ($query) use ($session_id) {
    //             $query->where('students.session_id', $session_id);
    //         })
    //         ->when($student_id, function ($query) use ($student_id) {
    //             $query->where('students.id', $student_id);
    //         })
    //         ->get();
    //     $single = $data->first();

    //     $responseData = [
    //         'data' => $data,
    //         'school_name' => $single ? $single->school_name : '',
    //     ];

    //     return returnData(2000, $responseData);
    // }
}
