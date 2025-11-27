<?php

namespace App\Http\Controllers\Reports\Attendance;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffAttendanceReportController extends Controller
{
    // public function showReport(Request $request)
    // {
    //     $designationID = $request->designation_id;
    //     $departmentID = $request->department_id;
    //     $fromDateInput = $request->from_date;
    //     $toDateInput = $request->to_date;

    //     try {
    //         $fromDate = Carbon::createFromFormat('Y-m-d', $fromDateInput)->startOfDay();
    //         $toDate = Carbon::createFromFormat('Y-m-d', $toDateInput)->endOfDay();
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Invalid date format: ' . $e->getMessage()], 5000);
    //     }

    //     if ($fromDate->diffInDays($toDate) >= 31) {
    //         return returnData(5000, null, "Date range can't exceed one month");
    //     }

    //     $attendanceRecords = DB::table('staff')
    //         ->leftJoin('staff_attendance', function ($join) use ($fromDate, $toDate) {
    //             $join->on('staff.staff_bio_id', '=', 'staff_attendance.staff_id')
    //                 ->whereBetween('staff_attendance.check_in', [$fromDate, $toDate]);
    //         })
    //         ->leftJoin('designations', 'staff.designation_id', '=', 'designations.id')
    //         ->leftJoin('work_departments', 'staff.department_id', '=', 'work_departments.id')
    //         ->select(
    //             'staff.id as id',
    //             'staff.employee_id as employee_id',
    //             'staff.staff_bio_id as staff_bio_id',
    //             'staff.name as name',
    //             'staff.phone as phone',
    //             'staff.designation_id as designation',
    //             'staff.department_id as department',
    //             'designations.name as designation_name',
    //             'work_departments.name as department_name',
    //             'staff_attendance.check_in as check_in',
    //             'staff_attendance.checkout as check_out',
    //             DB::raw('DATE(staff_attendance.check_in) as attendance_date')
    //         )
    //         ->where('staff.status', 1)
    //         ->where('staff_attendance.staff_id', 1612690087)
    //         ->when($designationID, function ($query) use ($designationID) {
    //             $query->where('staff.designation_id', $designationID);
    //         })
    //         ->when($departmentID, function ($query) use ($departmentID) {
    //             $query->where('staff.department_id', $departmentID);
    //         })
    //         ->orderBy('attendance_date','desc')
    //         ->get();

    //     $staffData = [];

    //     foreach ($attendanceRecords as $record) {
    //         $staffId = $record->staff_bio_id;
    //         if (!isset($staffData[$staffId])) {
    //             $staffData[$staffId] = [
    //                 'id' => $record->staff_bio_id,
    //                 'name' => $record->name,
    //                 'checkInStatus' => []
    //             ];
    //         }

    //         if ($record->attendance_date) {
    //             $date = date("d-m-Y", strtotime($record->attendance_date));
    //             $checkInTime = Carbon::parse($record->check_in);
    //             $presentStatus = $checkInTime->format('H:i') <= '08:20' ? 'P' : 'L';

    //             $staffData[$staffId]['checkInStatus'][$date] = [
    //                 'check_in' => $record->check_in,
    //                 'check_out' => $record->check_out,
    //                 'presentStatus' => $presentStatus
    //             ];
    //         }
    //     }

    //     $dateRange = date("d M, Y", strtotime($fromDate)) . ' - ' . date("d M, Y", strtotime($toDate));

    //     $responseData = [
    //         'staffs' => array_values($staffData),
    //         'date' => $dateRange,
    //     ];

    //     ddA($responseData);
    //     return returnData(2000, $responseData);
    // }


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

        $attendanceRecords = DB::table('staff')
            ->leftJoin('staff_attendance', 'staff.staff_bio_id', '=', 'staff_attendance.staff_id')
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
                'staff_attendance.check_in as check_in',
                'staff_attendance.checkout as check_out',
                DB::raw('DATE(staff_attendance.check_in) as attendance_date')
            )
            ->where('staff.status', 1)
            ->whereBetween('staff_attendance.check_in', [$fromDate, $toDate])
            ->when($designationID, function ($query) use ($designationID) {
                $query->where('staff.designation_id', $designationID);
            })
            ->when($departmentID, function ($query) use ($departmentID) {
                $query->where('staff.department_id', $departmentID);
            })
            ->orderBy('attendance_date', 'desc')
            ->get()
            ->groupBy(['staff_bio_id', 'attendance_date']);

        $staffData = [];

        foreach ($attendanceRecords as $staffId => $dates) {
            foreach ($dates as $date => $records) {
                $formattedDate = Carbon::parse($date)->format('d-m-Y');
                $checkInTime = $records->min('check_in');
                $checkOutTime = count($records) > 1 ? $records->max('check_in') : null;

                if (!isset($staffData[$staffId])) {
                    $staffData[$staffId] = [
                        'id' => $staffId,
                        'name' => $records[0]->name,
                        'checkInStatus' => []
                    ];
                }

                $presentStatus = Carbon::parse($checkInTime)->format('H:i') <= '08:20' ? 'P' : 'L';

                $staffData[$staffId]['checkInStatus'][$formattedDate] = [
                    'check_in' => $checkInTime,
                    'check_out' => $checkOutTime,
                    'presentStatus' => $presentStatus
                ];
            }
        }

        $dateRange = date("d M, Y", strtotime($fromDate)) . ' - ' . date("d M, Y", strtotime($toDate));

        $responseData = [
            'staffs' => array_values($staffData),
            'date' => $dateRange,
        ];

        return returnData(2000, $responseData);
    }
}




