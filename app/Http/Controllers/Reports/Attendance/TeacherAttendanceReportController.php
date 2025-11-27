<?php

namespace App\Http\Controllers\Reports\Attendance;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
class TeacherAttendanceReportController extends Controller
{
    public function showReport(Request $request)
    {
        $designationID = $request->designation_id;
        $departmentID = $request->department_id;
        $fromDateInput = $request->from_date;
        $toDateInput = $request->to_date;

        try {
            $fromDate = Carbon::createFromFormat('Y-m-d', $fromDateInput)->startOfDay();
            $toDate = Carbon::createFromFormat('Y-m-d', $toDateInput)->endOfDay();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format: ' . $e->getMessage()], 5000);
        }

        if ($fromDate->diffInDays($toDate) >= 31) {
            return returnData(5000, null, "Date range can't exceed one month");
        }

        $attendanceRecords = DB::table('teachers')
            ->leftJoin('teacher_attendance', 'teachers.teacher_bio_id', '=', 'teacher_attendance.teacher_id')
            ->leftJoin('designations', 'teachers.designation_id', '=', 'designations.id')
            ->leftJoin('work_departments', 'teachers.department_id', '=', 'work_departments.id')
            ->select(
                'teachers.id as id',
                'teachers.employee_id as employee_id',
                'teachers.teacher_bio_id as teacher_bio_id',
                'teachers.name as name',
                'teachers.phone as phone',
                'teachers.designation_id as designation',
                'teachers.department_id as department',
                'designations.name as designation_name',
                'work_departments.name as department_name',
                'teacher_attendance.check_in as check_in',
                'teacher_attendance.checkout as check_out',
                DB::raw('DATE(teacher_attendance.check_in) as attendance_date')
            )
            ->where('teachers.status', 1)
            ->whereBetween('teacher_attendance.check_in', [$fromDate, $toDate])
            ->when($designationID, function ($query) use ($designationID) {
                $query->where('teachers.designation_id', $designationID);
            })
            ->when($departmentID, function ($query) use ($departmentID) {
                $query->where('teachers.department_id', $departmentID);
            })
            ->orderBy('attendance_date', 'desc')
            ->get()
            ->groupBy(['teacher_bio_id', 'attendance_date']);

        $teacherData = [];

        foreach ($attendanceRecords as $teacherId => $dates) {
            foreach ($dates as $date => $records) {
                $formattedDate = Carbon::parse($date)->format('d-m-Y');
                $checkInTime = $records->min('check_in');
                $checkOutTime = count($records) > 1 ? $records->max('check_in') : null;

                if (!isset($teacherData[$teacherId])) {
                    $teacherData[$teacherId] = [
                        'id' => $teacherId,
                        'name' => $records[0]->name,
                        'checkInStatus' => []
                    ];
                }

                $presentStatus = Carbon::parse($checkInTime)->format('H:i') <= '08:35' ? 'P' : 'L';

                $teacherData[$teacherId]['checkInStatus'][$formattedDate] = [
                    'check_in' => $checkInTime,
                    'check_out' => $checkOutTime,
                    'presentStatus' => $presentStatus
                ];
            }
        }

        $dateRange = date("d M, Y", strtotime($fromDate)) . ' - ' . date("d M, Y", strtotime($toDate));

        $responseData = [
            'teachers' => array_values($teacherData),
            'date' => $dateRange,
        ];

        return returnData(2000, $responseData);
    }
}
