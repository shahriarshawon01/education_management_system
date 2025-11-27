<?php

namespace App\Http\Controllers\Reports\Attendance;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentAttendanceReportController extends Controller
{
    public function showReport(Request $request)
    {
        $classId = $request->class_id;
        $sessionId = $request->session_id;
        $sectionId = $request->section_id;
        $departmentId = $request->department_id;
        $fromDateInput = $request->from_date;
        $toDateInput = $request->to_date;

        try {
            $fromDate = Carbon::createFromFormat('Y-m-d', $fromDateInput)->startOfDay();
            $toDate = Carbon::createFromFormat('Y-m-d', $toDateInput)->endOfDay();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format: ' . $e->getMessage()], 500);
        }

        if ($fromDate->diffInDays($toDate) >= 31) {
            return returnData(5000, null, "Date range can't exceed one month");
        }

        $attendanceRecords = DB::table('students')
            ->leftJoin('student_attendance', function ($join) use ($fromDate, $toDate) {
                $join->on('students.student_roll', '=', 'student_attendance.student_id')
                    ->whereBetween('student_attendance.check_in', [$fromDate, $toDate]);
            })
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('session_years', 'students.session_year_id', '=', 'session_years.id')
            ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
            ->leftJoin('departments', 'students.department_id', '=', 'departments.id')
            ->select(
                'students.student_roll as student_roll',
                'students.student_name_en as name',
                'students.class_id as class_id',
                'students.session_year_id as session_year_id',
                'students.section_id as section_id',
                'students.department_id as department_id',
                'student_attendance.check_in as check_in',
                'student_attendance.checkout as check_out',
                DB::raw('DATE(student_attendance.check_in) as attendance_date')
            )
            ->where('students.status', 1)
            ->when($classId, function ($query) use ($classId) {
                $query->where('students.class_id', $classId);
            })
            ->when($sessionId, function ($query) use ($sessionId) {
                $query->where('students.session_year_id', $sessionId);
            })
            ->when($sectionId, function ($query) use ($sectionId) {
                $query->where('students.section_id', $sectionId);
            })
            ->when($departmentId, function ($query) use ($departmentId) {
                $query->where('students.department_id', $departmentId);
            })
            ->orderBy('attendance_date', 'desc')
            ->get();

        $studentsData = [];

        foreach ($attendanceRecords as $record) {
            $studentsId = $record->student_roll;
            if (!isset($studentsData[$studentsId])) {
                $studentsData[$studentsId] = [
                    'id' => $record->student_roll,
                    'name' => $record->name,
                    'checkInStatus' => []
                ];
            }

            if ($record->attendance_date) {
                $date = date("d-m-Y", strtotime($record->attendance_date));
                $checkInTime = Carbon::parse($record->check_in);
                $presentStatus = $checkInTime->format('H:i') <= '09:04' ? 'P' : 'L';

                $studentsData[$studentsId]['checkInStatus'][$date] = [
                    'check_in' => $record->check_in,
                    'check_out' => $record->check_out,
                    'presentStatus' => $presentStatus
                ];
            }
        }

        $dateRange = date("d M, Y", strtotime($fromDate)) . ' - ' . date("d M, Y", strtotime($toDate));

        $responseData = [
            'students' => array_values($studentsData),
            'date' => $dateRange,
        ];

        return returnData(2000, $responseData);
    }
}
