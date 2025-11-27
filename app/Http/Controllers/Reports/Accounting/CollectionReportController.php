<?php

namespace App\Http\Controllers\Reports\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionReportController extends Controller
{
    public function showReport(Request $request)
    {
        $school_id = auth()->user()->school_id;
        $student_id = input('student_id');
        $session_id = input('session_id');
        $class_id = input('class_id');
        $invoice_id = input('invoice_id');
        $department_id = input('department_id');

        if (!$invoice_id) {
            $groupBy = 'payments.id';
            $select = 'students.student_name_en as component_name';
        }
        if (!$student_id) {
            $groupBy = 'payments.student_id';
            $select = 'students.student_name_en as component_name';
        }
        if ($invoice_id) {
            $groupBy = 'payment_details.component_id';
            $select = 'components.name as component_name';
        }

        $data = DB::table('payments')
            ->leftJoin('payment_details', 'payments.id', '=', 'payment_details.payments_id')
            ->leftJoin('components', 'payment_details.component_id', '=', 'components.id')
            ->leftJoin('students', 'payments.student_id', '=', 'students.id')
            ->leftJoin('schools', 'payments.school_id', '=', 'schools.id')
            ->leftJoin('session_years', 'payments.session_year_id', '=', 'session_years.id')
            ->leftJoin('student_classes', 'payments.class_id', '=', 'student_classes.id')
            ->selectRaw(
                "payments.*,
                payment_details.*,
                students.student_name_en as student_name,
                $select,session_years.title as session_name,
                student_classes.name as class_name,
                SUM(amount) as amount,
                SUM(waiver) as waiver,
                SUM(payed) as payed,
                schools.title as school_name,
                schools.steb_number as school_steb,
                schools.reg_number as registration_no,
                schools.institute_code as institute_code,
                schools.address as school_address"
            )
            ->where('payments.school_id', $school_id)
            ->where('payments.class_id', $class_id)
            ->when($session_id, function ($query) use ($session_id) {
                $query->where('payments.session_year_id', $session_id);
            })
            ->when($student_id, function ($query) use ($student_id) {
                $query->where('payments.student_id', $student_id);
            })
            ->when($department_id, function ($query) use ($department_id) {
                $query->where('students.department_id', $department_id);
            })
            ->when($invoice_id, function ($query) use ($invoice_id) {
                $query->where('payments.invoice_id', $invoice_id);
            })
            ->groupBy($groupBy)
            ->get();

        dd($data);
        $single = $data->first();

        $responseData = [
            'componentWiseData' => $data,
            'total_amount' => $data->sum('amount'),
            'total_waiver' => $data->sum('waiver'),
            'total_payed' => $data->sum('payed'),
            'session_name' => $single ? $single->session_name : '',
            'class_name' => $single ? $single->class_name : '',
            'student_name' => ($student_id && $single) ? $single->student_name : '',
            'school_name' => $single ? $single->school_name : '',
            'registration_no' => $single ? $single->registration_no : '',
            'school_steb' => $single ? $single->school_steb : '',
            'school_address' => $single ? $single->school_address : '',
        ];

        return returnData(2000, $responseData);
    }
}
