<?php

namespace App\Http\Controllers\Reports\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WaiverReportController extends Controller
{
    public function showReport(Request $request)
    {
        $sectionId = $request->input('section_id');
        $classId = $request->input('class_id');
        $sessionId = $request->input('session_year_id');
        $departmentId = $request->input('department_id');

        $invoiceSummary = DB::table('invoices')
            ->select(
                'invoice_type_id as student_id',
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('SUM(total_waiver) as total_waiver'),
                DB::raw('SUM(total_payable) as total_payable')
            )
            ->where('invoice_type', '=', 1)
            ->groupBy('invoice_type_id');

        $students = DB::table('student_waivers')
            ->leftJoin('students', 'student_waivers.student_id', '=', 'students.id')
            ->leftJoin('departments', 'students.department_id', '=', 'departments.id')
            ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('session_years', 'students.session_year_id', '=', 'session_years.id')
            ->leftJoin('student_waiver_documents', function ($join) {
                $join->on('student_waivers.student_id', '=', 'student_waiver_documents.student_id')
                    ->where('student_waiver_documents.status', 1);
            })
            ->leftJoinSub($invoiceSummary, 'invoice_summary', function ($join) {
                $join->on('student_waivers.student_id', '=', 'invoice_summary.student_id');
            })
            ->select(
                'student_waivers.student_id',
                'students.student_name_en as student_name',
                'students.student_roll',
                'students.merit_roll as class_roll',
                'students.father_name_en as father_name',
                'students.mother_name_en as mother_name',
                'students.father_phone',
                'student_classes.name as class_name',
                'sections.name as section_name',
                'session_years.title as session_name',
                'student_waiver_documents.reason',
                'student_waiver_documents.file',
                'student_waiver_documents.remarks',
                'invoice_summary.total_amount',
                'invoice_summary.total_waiver',
                'invoice_summary.total_payable'
            )
            ->where('students.status', 1)
            ->when($classId, fn($q) => $q->where('students.class_id', $classId))
            ->when($departmentId, fn($q) => $q->where('students.department_id', $departmentId))
            ->when($sectionId, fn($q) => $q->where('students.section_id', $sectionId))
            ->when($sessionId, fn($q) => $q->where('students.session_year_id', $sessionId))
            ->groupBy(
                'student_waivers.student_id',
                'students.student_name_en',
                'students.student_roll',
                'students.merit_roll',
                'students.father_name_en',
                'students.mother_name_en',
                'students.father_phone',
                'student_classes.name',
                'sections.name',
                'session_years.title',
                'student_waiver_documents.reason',
                'student_waiver_documents.file',
                'student_waiver_documents.remarks',
                'invoice_summary.total_amount',
                'invoice_summary.total_waiver',
                'invoice_summary.total_payable'
            )
            ->orderBy('students.merit_roll', 'asc')
            ->get();

        if ($students->isEmpty()) {
            return returnData(5000, null, 'No data found in the selected criteria.');
        }

        foreach ($students as $student) {
            $student->components = DB::table('student_waivers')
                ->leftJoin('components', 'student_waivers.component_id', '=', 'components.id')
                ->where('student_waivers.student_id', $student->student_id)
                ->select('components.name as component_name', 'student_waivers.type')
                ->get()
                ->map(function ($item) {
                    return [
                        'component_name' => $item->component_name,
                        'type' => (int) $item->type,
                        'type_label' => $item->type == 1 ? 'Fixed' : 'Percentage'
                    ];
                });
        }
        $student_info = $students->first();
        return returnData(2000, [
            'data' => $students,
            'section_name' => $student_info->section_name ?? 'All',
            'class_name' => $student_info->class_name ?? 'All',
            'session_name' => $student_info->session_name ?? 'All'
        ]);
    }
}
