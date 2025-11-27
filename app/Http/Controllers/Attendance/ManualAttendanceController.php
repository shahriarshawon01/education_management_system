<?php

namespace App\Http\Controllers\Attendance;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Attendance\StudentAttendance;
use App\Models\Attendance\EmployeeAttendance;

class ManualAttendanceController extends Controller
{
    public function getManualStudentAttendance(Request $request)
    {
        $attendanceDate = $request->attendance_date;
        $sectionId = $request->section_id;
        $departmentId = $request->department_id;
        $sessionId = $request->session_id;
        $classId = $request->class_id;
        $authSchoolId = auth()->user()->school_id;

        if (empty($attendanceDate)) {
            return returnData(5000, 'Please select a attendance date');
        }

        if ($request->attendance_date) {
            $attendanceDate = is_array($attendanceDate) ? $attendanceDate[0] : $attendanceDate;

            $student = DB::table('students')
                ->leftJoin('student_classes', 'student_classes.id', '=', 'students.class_id')
                ->leftJoin('departments', 'departments.id', '=', 'students.department_id')
                ->leftJoin('sections', 'sections.id', '=', 'students.section_id')
                ->leftJoin('session_years', 'session_years.id', '=', 'students.session_year_id')
                ->select(
                    'students.id',
                    'students.student_roll',
                    'students.photo',
                    'students.merit_roll',
                    'students.student_name_en as student_name',
                    'sections.name as section_name',
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
                ->where('students.school_id', $authSchoolId)
                ->orderBy('students.student_roll', 'asc')
                ->distinct('students.student_roll')
                ->get();

            $date = date("d M, Y ", strtotime($attendanceDate));

            $responseData = [
                'data' => $student,
                'attendance_date' => $date,
            ];

            return returnData(2000, $responseData);
        }
    }

    public function submitManualStudentAttendance(Request $request)
    {
        $authSchoolId = auth()->user()->school_id;

        try {
            $inputData = $request->all();

            foreach ($inputData as $data) {
                if (!isset($data['status']) || in_array($data['status'], [3, 4])) {
                    continue;
                }

                $data['school_id'] = $authSchoolId;
                $existingRecord = StudentAttendance::where('student_id', $data['student_id'])
                    ->where('class_id', $data['class_id'])
                    ->where('department_id', $data['department_id'])
                    ->where('section_id', $data['section_id'])
                    ->whereDate('check_in', $data['check_in'])
                    ->first();
                $attendanceDate = \Carbon\Carbon::parse($data['check_in']);
                if ($data['status']== 1) {
                    $attendance_time = getGeneralConfig('check_in');
                }else{$time = getGeneralConfig('check_in'); // "08:01:00"

                    $newTime = \Carbon\Carbon::createFromFormat('H:i:s', $time)
                        ->addMinutes(15)
                        ->format('H:i:s');
                    $attendance_time = $newTime;
                }
                $checkIn = $attendanceDate->format('Y-m-d') . ' ' . $attendance_time;

                if ($existingRecord) {
                    $existingRecord->update([
                        'check_in' => $checkIn ?? $existingRecord->check_in,
                        'comment' => $data['remarks'] ?? $existingRecord->remarks,
                    ]);
                } else {
;
                    $saveData = new StudentAttendance();
//                    $saveData->fill($data);
                    $saveData->student_id =  $data['student_id'] ;
                    $saveData->year = $data['session_id'] ;
                    $saveData->check_in = $checkIn ;
                    $saveData->class_id = $data['class_id'] ;
                    $saveData->section_id = $data['section_id'] ;
                    $saveData->department_id = $data['department_id'];
                    $saveData->save();
                }
            }

            return returnData(2000, null, 'Attendance submitted successfully');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }



public function getEmployeeAttendance(Request $request)
{
   
    $attendanceDate = $request->attendance_date;
    $employeeType = $request->employee_type;
    $authSchoolId = auth()->user()->school_id;

    if (empty($attendanceDate)) {
        return returnData(5000, 'Please select an attendance date');
    }

    $attendanceDate = is_array($attendanceDate) ? $attendanceDate[0] : $attendanceDate;

    // Identify table and staff type condition
    if ($employeeType == 1) {
        $table = 'teachers';
        $whereStaffType = null;
        $employeeTypeLabel = 'Teacher';
    } elseif (in_array($employeeType, [2, 3])) {
        $table = 'staff';
        $whereStaffType = $employeeType == 2 ? 1 : 2;
        $employeeTypeLabel = $employeeType == 2 ? 'Staff' : 'Support Staff';
    } else {
        return returnData(5000, 'Invalid employee type selected');
    }

    $employees = DB::table($table)
        ->leftJoin('employee_departments', 'employee_departments.id', '=', "{$table}.department_id")
        ->leftJoin('employee_designations', 'employee_designations.id', '=', "{$table}.designation_id")
        ->select(
            "{$table}.id",
            "{$table}.employee_id",
            "{$table}.photo",
            "{$table}.name as employee_name",
            'employee_departments.name as department_name',
            'employee_designations.name as designation_name'
        )
        ->when($whereStaffType, function ($query, $whereStaffType) use ($table) {
            $query->where("{$table}.staff_type", $whereStaffType);
        })
        ->where("{$table}.status", 1)
        ->where("{$table}.school_id", $authSchoolId)
        ->orderBy("{$table}.id", 'asc')
        ->distinct("{$table}.employee_id")
        ->get()
        ->map(function ($item) use ($employeeTypeLabel) {
            $item->employee_type = $employeeTypeLabel;
            return $item;
        });
       

    $date = date("d M, Y", strtotime($attendanceDate));

    $responseData = [
        'data' => $employees,
        'attendance_date' => $date,
    ];

    return returnData(2000, $responseData);
}





    public function getEmployeeAttendanceBackup(Request $request)
    {
        $attendanceDate = $request->attendance_date;
        $employeeType = $request->employee_type;
        $authSchoolId = auth()->user()->school_id;

        if (empty($attendanceDate)) {
            return returnData(5000, 'Please select a attendance date');
        }

        $attendanceDate = is_array($attendanceDate) ? $attendanceDate[0] : $attendanceDate;
        if ($request->attendance_date) {
            $attendanceDate = is_array($attendanceDate) ? $attendanceDate[0] : $attendanceDate;

            $employee = DB::table('teachers')
                ->leftJoin('work_departments', 'work_departments.id', '=', 'teachers.department_id')
                ->leftJoin('designations', 'designations.id', '=', 'teachers.designation_id')
                ->select(
                    'teachers.id',
                    'teachers.employee_id',
                    'teachers.photo',
                    'teachers.name as employee_name',
                    'work_departments.name as department_name',
                    'designations.name as designation_name'
                )
                ->when($employeeType, function ($query, $employeeType) {
                    $query->where('teachers.employee_type', $employeeType);
                })
                ->where('teachers.status', 1)
                ->where('teachers.school_id', $authSchoolId)
                ->orderBy('teachers.id', 'asc')
                ->distinct('teachers.employee_id')
                ->get();

            $date = date("d M, Y ", strtotime($attendanceDate));

            $responseData = [
                'data' => $employee,
                'attendance_date' => $date,
            ];

            return returnData(2000, $responseData);
        }
    }

    public function submitEmployeeAttendance(Request $request)
    {
        $authSchoolId = auth()->user()->school_id;

        try {
            $inputData = $request->all();
            foreach ($inputData as $data) {
                $data['school_id'] = $authSchoolId;
                $data['in_time'] = $data['in_time'] ?? now()->format('H:i:s');
                $data['out_time'] = $data['out_time'] ?? null;
                $existingRecord = EmployeeAttendance::where('employee_id', $data['employee_id'])
                    ->where('attendance_date', $data['attendance_date'])
                    ->where('school_id', $authSchoolId)
                    ->first();

                if ($existingRecord) {
                    $existingRecord->update([
                        'status' => $data['status'] ?? $existingRecord->status,
                        'remarks' => $data['remarks'] ?? $existingRecord->remarks,
                        'in_time' => $data['in_time'] ?? $existingRecord->in_time,
                        'out_time' => $data['out_time'] ?? $existingRecord->out_time,
                    ]);
                } else {
                    $saveData = new EmployeeAttendance();
                    $saveData->fill($data);
                    $saveData->save();
                }
            }

            return returnData(2000, null, 'Attendance submitted successfully');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
}
