<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StudentTransactionController extends Controller
{
    public function studentTransaction()
    {
        $class_id = input('class_id');

        $data['data'] = DB::table('invoices')
            ->leftJoin('students', 'invoices.invoice_type_id', '=', 'students.id')
            ->leftJoin('schools', 'invoices.school_id', '=', 'schools.id')
            ->leftJoin('session_years', 'students.session_year_id', '=', 'session_years.id')
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoin('payment_details', 'payments.id', '=', 'payment_details.payments_id')
            ->select(
                'invoices.*',
                'schools.title as school_name',
                'invoices.total_payable as total_payable',
                'students.student_name_en as student_name',
                'students.student_roll as student_roll',
                'session_years.title as session_name',
                'student_classes.name as class_name',
                DB::raw('SUM(payment_details.payed) as total_transaction'),
                DB::raw('invoices.total_payable - IFNULL(SUM(payment_details.payed), 0) AS due_amount')
            )
            ->when($class_id, function ($query) use ($class_id) {
                $query->where('students.class_id', $class_id);
            })
            ->groupBy('students.class_id')
            ->get();

        return returnData(2000, $data);
    }

    public function transactionDetails($id)
    {
        $data['data'] = DB::table('invoice_details')
            ->leftJoin('components', 'invoice_details.components_id', '=', 'components.id')
            ->leftJoin('invoices', 'invoice_details.invoice_id', '=', 'invoices.id')
            ->select('invoice_details.*', 'components.name as component_name', 'invoices.date as date')
            ->where('invoice_id', $id)->get();

        return returnData(2000, $data);
    }
}
