<?php

namespace App\Http\Controllers\Reports\Student;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StudentDetailsReportController extends Controller
{
    public function showReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $studentId = $request->student_id;
        $sessionId = $request->session_year_id;
        $classID = $request->class_id;
        $sectionID = $request->section_id;
        $activityStatus = $request->activityStatus;

        $data = DB::table('students')
            ->leftJoin('schools', 'students.school_id', '=', 'schools.id')
            ->leftJoin('session_years', 'students.session_year_id', '=', 'session_years.id')
            ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('departments', 'students.department_id', '=', 'departments.id')
            ->leftJoin('addresses', function ($join) {
                $join->on('students.id', '=', 'addresses.addressable_id')
                    ->where('addresses.type', 1);
            })
            ->selectRaw(
                "students.*,
            schools.title as school_name,
            schools.steb_number as school_steb,
            schools.reg_number as registration_no,
            schools.institute_code as institute_code,
            schools.address as school_address,
            session_years.title as session_name,
            student_classes.name as class_name,
            sections.name as section_name,
            departments.department_name as department_name,
            addresses.village as village_name",
            )
            ->where('students.school_id', $schoolId)
            ->when($classID, function ($query) use ($classID) {
                $query->where('students.class_id', $classID);
            })
            ->when($sectionID, function ($query) use ($sectionID) {
                $query->where('students.section_id', $sectionID);
            })
            ->when($sessionId, function ($query) use ($sessionId) {
                $query->where('students.session_year_id', $sessionId);
            })
            ->when($studentId, function ($query) use ($studentId) {
                $query->where('students.id', $studentId);
            })
            ->when($activityStatus !== null && $activityStatus !== '', function ($query) use ($activityStatus) {
                $query->where('students.status', '=', (int) $activityStatus);
            })
            ->orderBy('merit_roll', 'asc')
            ->distinct()
            ->get();

        $single = $data->first();

        $responseData = [
            'data' => $data,
            'school_name' => $single ? $single->school_name : '',
            'registration_no' => $single ? $single->registration_no : '',
            'school_steb' => $single ? $single->school_steb : '',
            'school_address' => $single ? $single->school_address : '',
            'class_name' => $single->class_name ?? '',
            'section_name' => $single->section_name ?? '',
            'session_name' => $single->session_name ?? '',
        ];
        return returnData(2000, $responseData);
    }

    public function classWiseStudentReport(Request $request)
    {
        $schoolId   = auth()->user()->school_id;
        $classId    = $request->class_id;
        $sectionId  = $request->section_id;
        $fromDate   = $request->from_date;
        $toDate     = $request->to_date;

        $excludeClasses = [29, 30, 31];

        $data = DB::table('students')
            ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->selectRaw("
            students.class_id,
            students.section_id,
            student_classes.name as class_name,
            sections.name as section_name,

            GROUP_CONCAT(students.student_roll) AS student_ids,  

            COUNT(*) AS total_students,
            SUM(CASE WHEN students.gender = 1 THEN 1 ELSE 0 END) AS total_male,
            SUM(CASE WHEN students.gender = 2 THEN 1 ELSE 0 END) AS total_female,
            SUM(CASE WHEN students.religion = 1 AND students.gender = 1 THEN 1 ELSE 0 END) AS muslim_male,
            SUM(CASE WHEN students.religion = 1 AND students.gender = 2 THEN 1 ELSE 0 END) AS muslim_female,
            SUM(CASE WHEN students.religion = 2 AND students.gender = 1 THEN 1 ELSE 0 END) AS hindu_male,
            SUM(CASE WHEN students.religion = 2 AND students.gender = 2 THEN 1 ELSE 0 END) AS hindu_female
        ")
            ->where('students.school_id', $schoolId)
            ->where('students.status', 1)
            ->when(!$classId, function ($q) use ($excludeClasses) {
                $q->whereNotIn('students.class_id', $excludeClasses);
            })
            ->when($classId, fn($q) => $q->where('students.class_id', $classId))
            ->when($sectionId, fn($q) => $q->where('students.section_id', $sectionId))
            ->when($fromDate && $toDate, fn($query) => $query->whereBetween('students.admission_date', [$fromDate, $toDate]))
            ->groupBy('students.class_id', 'students.section_id')
            ->orderBy('students.class_id')
            ->orderBy('students.section_id')
            ->get();

        $data->map(function ($item) {
            $item->student_ids = explode(',', $item->student_ids);
            return $item;
        });

        if ($data->isEmpty()) {
            return returnData(404, null, 'No records found for the specified criteria.');
        }

        return returnData(2000, [
            'data' => $data
        ]);
    }
}
