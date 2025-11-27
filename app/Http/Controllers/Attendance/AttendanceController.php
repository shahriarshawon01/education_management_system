<?php

namespace App\Http\Controllers\Attendance;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    public function studentAttendance(Request $request)
    {
        $attendanceDate = $request->date;
        $sectionId = $request->section_id;
        $departmentId = $request->department_id;
        $sessionId = $request->session_id;
        $classId = $request->class_id;

        if (empty($attendanceDate)) {
            return returnData(5000, 'Please select a date');
        }

        if ($request->date) {
            $attendanceDate = is_array($attendanceDate) ? $attendanceDate[0] : $attendanceDate;

            $presentStudent = DB::table('student_attendance')
                ->leftJoin('students', 'students.student_roll', '=', 'student_attendance.student_id')
                ->leftJoin('student_classes', 'student_classes.id', '=', 'student_attendance.class_id')
                ->leftJoin('departments', 'departments.id', '=', 'student_attendance.department_id')
                ->leftJoin('sections', 'sections.id', '=', 'student_attendance.section_id')
                ->leftJoin('session_years', 'session_years.id', '=', 'student_attendance.section_id')
                ->whereDate('student_attendance.check_in', $attendanceDate)
                ->when($classId, function ($query, $classId) {
                    $query->where('student_attendance.class_id', $classId);
                })
                ->when($sessionId, function ($query, $sessionId) {
                    $query->where('student_attendance.year', $sessionId);
                })
                ->when($sectionId, function ($query, $sectionId) {
                    $query->where('student_attendance.section_id', $sectionId);
                })
                ->when($departmentId, function ($query, $departmentId) {
                    $query->where('student_attendance.department_id', $departmentId);
                })
                ->where('students.status', 1)
                ->where('students.class_id', '!=', 29)
                ->distinct('students.student_roll')
                ->select(
                    'students.id',
                    'students.student_roll',
                    'students.merit_roll',
                    'students.father_phone',
                    'students.mother_phone',
                    'students.student_name_en',
                    'students.section_id',
                    'sections.name',
                    'departments.department_name',
                    'student_attendance.student_id',
                    'student_attendance.class_id',
                    'student_attendance.check_in',
                    'student_attendance.section_id',
                    'student_classes.name as class_name'
                )
                ->groupBy('students.student_roll')
                ->orderBy('students.merit_roll', 'asc')
                // ->orderBy('students.class_id', 'asc')
                ->get();

            $attendStudentIds = $presentStudent->pluck('student_roll');

            $absenceStudent = DB::table('students')
                ->leftJoin('student_classes', 'student_classes.id', '=', 'students.class_id')
                ->leftJoin('departments', 'departments.id', '=', 'students.department_id')
                ->leftJoin('sections', 'sections.id', '=', 'students.section_id')
                ->leftJoin('session_years', 'session_years.id', '=', 'students.session_year_id')
                ->select(
                    'students.id',
                    'students.student_roll',
                    'students.merit_roll',
                    'students.father_phone',
                    'students.mother_phone',
                    'students.student_name_en',
                    'students.section_id',
                    'sections.name',
                    'departments.department_name',
                    'student_classes.name as class_name'
                )
                ->when($classId, function ($query, $classId) {
                    $query->where('students.class_id', $classId);
                })
                ->when($sessionId, function ($query, $sessionId) {
                    $query->where('session_years.id', $sessionId);
                })
                ->when($sectionId, function ($query, $sectionId) {
                    $query->where('students.section_id', $sectionId);
                })
                ->when($departmentId, function ($query, $departmentId) {
                    $query->where('students.department_id', $departmentId);
                })
                ->where('students.status', 1)
                ->where('students.class_id', '!=', 29)
                ->whereNotIn('students.student_roll', $attendStudentIds)
                ->orderBy('students.merit_roll', 'asc')
                // ->orderBy('students.class_id', 'asc')
                ->distinct('students.student_roll')
                ->get();

            $date = date("d M, Y ", strtotime($attendanceDate));

            $responseData = [
                'data' => $absenceStudent,
                'presentStudent' => $presentStudent,
                'date' => $date,
            ];

            return returnData(2000, $responseData);
        }
    }

    public function teacherAttendance(Request $request)
    {
        // $schoolId = auth()->user()->school_id;
        $designationID = $request->designation_id;
        $departmentID = $request->department_id;
        $attendanceDate = $request->date;

        if (empty($attendanceDate)) {
            return returnData(5000, 'Please select a date');
        }

        $attendanceDate = is_array($attendanceDate) ? $attendanceDate[0] : $attendanceDate;

        $presentTeacher = DB::table('teacher_attendance')
            ->leftJoin('employees', 'employees.teacher_bio_id', '=', 'teacher_attendance.teacher_id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->leftJoin('work_departments', 'employees.department_id', '=', 'work_departments.id')
            ->select(
                'employees.id as id',
                'employees.employee_id as employee_id',
                'teachers.teacher_bio_id as teacher_bio_id',
                'teachers.name as name',
                'teachers.phone as phone',
                'teachers.designation_id as designation',
                'teachers.department_id as department',
                'designations.name as designation_name',
                'work_departments.name as department_name',
                'teacher_attendance.teacher_id as teacher_id',
                'teacher_attendance.check_in as check_in',
            )
            ->whereDate('teacher_attendance.check_in', $attendanceDate)
            ->where('teachers.status', 1)
            ->when($designationID, function ($query) use ($designationID) {
                $query->where('teachers.designation_id', $designationID);
            })
            ->when($departmentID, function ($query) use ($departmentID) {
                $query->where('teachers.department_id', $departmentID);
            })
            ->groupBy('teachers.teacher_bio_id')
            ->get();

        $attendTeacherIds = $presentTeacher->pluck('teacher_id');
        $absentTeacher = DB::table('teachers')
            ->leftJoin('designations', 'teachers.designation_id', '=', 'designations.id')
            ->leftJoin('work_departments', 'teachers.department_id', '=', 'work_departments.id')
            ->selectRaw(
                "teachers.*,
                designations.name as designation_name,
                work_departments.name as department_name"
            )
            // ->where('teachers.school_id', $schoolId)
            ->when($designationID, function ($query) use ($designationID) {
                $query->where('teachers.designation_id', $designationID);
            })
            ->when($departmentID, function ($query) use ($departmentID) {
                $query->where('teachers.department_id', $departmentID);
            })
            ->whereNotIn('teachers.teacher_bio_id', $attendTeacherIds)
            ->where('teachers.status', 1)
            ->distinct('teachers.teacher_bio_id')
            ->get();

        $date = date("d M, Y ", strtotime($attendanceDate));

        $responseData = [
            'data' => $absentTeacher,
            'presentTeacher' => $presentTeacher,
            'date' => $date,
        ];

        return returnData(2000, $responseData);
    }

    public function staffAttendance(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $designationID = $request->designation_id;
        $departmentID = $request->department_id;
        $attendanceDate = $request->date;

        if (empty($attendanceDate)) {
            return returnData(5000, 'Please select a date');
        }

        $attendanceDate = is_array($attendanceDate) ? $attendanceDate[0] : $attendanceDate;

        $presentStaff = DB::table('staff_attendance')
            ->leftJoin('staff', 'staff.staff_bio_id', '=', 'staff_attendance.staff_id')
            ->leftJoin('designations', 'staff.designation_id', '=', 'designations.id')
            ->leftJoin('work_departments', 'staff.department_id', '=', 'work_departments.id')
            ->select(
                'staff.id as id',
                'staff.employee_id as employee_id',
                'staff.staff_bio_id as staff_bio_id',
                'staff.name as name',
                'staff.phone as phone',
                'staff.designation_id as designation',
                'staff.department_id as department',
                'designations.name as designation_name',
                'work_departments.name as department_name',
                'staff_attendance.staff_id as staff_id',
                'staff_attendance.check_in as check_in',
            )
            ->whereDate('staff_attendance.check_in', $attendanceDate)
            ->where('staff.status', 1)
            ->when($designationID, function ($query) use ($designationID) {
                $query->where('staff.designation_id', $designationID);
            })
            ->when($departmentID, function ($query) use ($departmentID) {
                $query->where('staff.department_id', $departmentID);
            })
            ->groupBy('staff.staff_bio_id')
            ->get();

        $attendStaffIds = $presentStaff->pluck('staff_id');
        $absentStaff = DB::table('staff')
            ->leftJoin('designations', 'staff.designation_id', '=', 'designations.id')
            ->leftJoin('work_departments', 'staff.department_id', '=', 'work_departments.id')
            ->selectRaw(
                "staff.*,
                designations.name as designation_name,
                work_departments.name as department_name"
            )
            ->where('staff.school_id', $schoolId)
            ->when($designationID, function ($query) use ($designationID) {
                $query->where('staff.designation_id', $designationID);
            })
            ->when($departmentID, function ($query) use ($departmentID) {
                $query->where('staff.department_id', $departmentID);
            })
            ->whereNotIn('staff.staff_bio_id', $attendStaffIds)
            ->where('staff.status', 1)
            ->distinct('staff.staff_bio_id')
            ->get();

        $date = date("d M, Y ", strtotime($attendanceDate));

        $responseData = [
            'data' => $absentStaff,
            'presentStaff' => $presentStaff,
            'date' => $date,
        ];

        return returnData(2000, $responseData);
    }
}
