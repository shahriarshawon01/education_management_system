<?php

namespace App\Http\Controllers\Reports\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeesCollectionReportController extends Controller
{
    public function showReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $student_id = $request->input('student_id');
        $session_id = $request->input('session_year_id');
        $class_id = $request->input('class_id');
        $rollId = $request->input('roll');

        // Base query
        $query = DB::table('invoices')
            ->leftJoin('students', 'invoices.invoice_type_id', '=', 'students.id')
            ->leftJoin('schools', 'invoices.school_id', '=', 'schools.id')
            ->leftJoin('session_years', 'students.session_year_id', '=', 'session_years.id')
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
            ->leftJoin('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->select(
                'invoices.*',
                // 'invoices.total_payable as total_payable',
                'students.student_name_en as student_name',
                'students.student_roll as student_roll',
                'student_classes.name as class_name',
                'session_years.title as session_name',
                'sections.name as section_name',
                DB::raw('SUM(DISTINCT invoices.total_payable) as total_payable'),
                DB::raw('IFNULL(SUM(payments.total_payed), 0) as total_payed'),
                DB::raw('SUM(DISTINCT invoices.total_payable) - IFNULL(SUM(payments.total_payed), 0) as due_amount')
            )
            ->where('invoices.school_id', $schoolId)
            ->when($rollId, function ($query) use ($rollId) {
                $query->where('students.student_roll', $rollId);
            })
            ->when($class_id, function ($query) use ($class_id) {
                $query->where('students.class_id', $class_id);
            })
            ->when($session_id, function ($query) use ($session_id) {
                $query->where('students.session_year_id', $session_id);
            })
            ->when($student_id, function ($query) use ($student_id) {
                $query->where('invoices.session_year_id', $student_id);
            });

        // Grouping logic
        if ($student_id) {
            $query->groupBy('invoices.students');
        } else {
            $query->groupBy('students.class_id');
        }

        $data['data'] = $query->get();
        $single = $query->first();

        // Calculate totals
        $total_payable = $data['data']->sum('total_payable');
        $total_payed = $data['data']->sum('total_payed');
        $total_due = $data['data']->sum('due_amount');

        $session_name = $data['data']->pluck('session_name')->first();
        $class_name = $data['data']->pluck('class_name')->first();
        $section_name = $data['data']->pluck('section_name')->first();

        $data['option'] = [
            'total_payable' => $total_payable,
            'total_payed' => $total_payed,
            'total_due' => $total_due,
            'session_name' => $session_name,
            'section_name' => $section_name,
            'class_name' => $class_name,
        ];

        if ($class_id || $student_id) {
            $data['session_name'] = $single ? $single->session_name : '';
            $data['section_name'] = $single ? $single->section_name : '';
            $data['class_name'] = $single ? $single->class_name : '';
            $data['student_name'] = $single ? $single->student_name : '';
            $data['student_roll'] = $single ? $single->student_roll : '';

        }

        return returnData(2000, $data);
    }
}
