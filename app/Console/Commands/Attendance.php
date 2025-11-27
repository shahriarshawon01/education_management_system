<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Attendance extends Command
{
    protected $signature = 'command:attendance';
    protected $description = 'we can catch the data from the SQL server from Bogura and we have to use this. then we will send the data to our local live server';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $local_attendance_bogra_server = DB::connection('sqlsrv2')
            ->table('CHECKINOUT')
            ->orderBy('CHECKTIME', 'desc')
            ->first();

        $tmss_attendance_tpsc_live_server = DB::connection('mysql')->table('attendance_history')
            ->orderBy('time_history', 'desc')
            ->first();

        // $tmss_attendance_tpsc_live_server = DB::table('attendance_history')
        //     ->orderBy('time_history', 'desc')
        //     ->first();

        $local = $local_attendance_bogra_server->CHECKTIME;
        $live = $tmss_attendance_tpsc_live_server->time_history;

        if ($local > $live) {
            $attendance_entry = DB::connection('sqlsrv2')->table('CHECKINOUT')
                ->whereBetween('CHECKTIME', [$live, $local])
                ->where('CHECKTIME', '!=', $live)
                ->leftjoin('USERINFO', 'USERINFO.USERID', '=', 'CHECKINOUT.USERID')
                ->select('CHECKINOUT.USERID', 'CHECKINOUT.CHECKTIME', 'USERINFO.SSN', 'USERINFO.MINZU')
                ->where('SSN', '!=', null)
                ->get();

            $todate = date('Y-m-d');

            foreach ($attendance_entry as $user_info) {

                $time = date('Y-m-d', strtotime($user_info->CHECKTIME));

                $select_student = DB::table('students')
                    ->Where('student_roll', '=', $user_info->SSN)
                    ->first();

                $select_teacher = DB::table('teachers')
                    ->where('teacher_bio_id', '=', $user_info->SSN)
                    ->first();

                $selectStaff = DB::table('staff')
                    ->where('staff_bio_id', '=', $user_info->SSN)
                    ->first();

                if ($select_student != null) {

                    $stu_user = DB::connection('sqlsrv2')->table('CHECKINOUT')
                        ->leftjoin('USERINFO', 'USERINFO.USERID', '=', 'CHECKINOUT.USERID')
                        ->select('CHECKINOUT.USERID', 'CHECKINOUT.CHECKTIME', 'USERINFO.SSN', 'USERINFO.MINZU')
                        ->where('CHECKTIME', '!=', null);

                    $class_id = 0;
                    $section_id = null;
                    $department_id = null;
                    $session_year_id = null;

                    if ($select_student != null) {
                        $class_id = $select_student->class_id;
                        $section_id = $select_student->section_id;
                        $department_id = $select_student->department_id;
                        $session_year_id = $select_student->session_year_id;
                    }

                    $present_student = DB::table('student_attendance')->insert([
                        'student_id' => $user_info->SSN,
                        'check_in' => $user_info->CHECKTIME,
                        'tpsc_gen_user_id' => $user_info->USERID,
                        'class_id' => $class_id,
                        'section_id' => $section_id,
                        'department_id' => $department_id,
                        'year' => $session_year_id
                    ]);

                    $present_student = DB::table('attendance_history')->insert([
                        'user_id' => $user_info->SSN,
                        'time_history' => $user_info->CHECKTIME,
                        'tpsc_user_id' => $user_info->USERID,
                        'user_type' => 3,
                    ]);
                } elseif ($select_teacher != null) {

                    $present_teacher = DB::table('teacher_attendance')->insert([
                        'teacher_id' => $user_info->SSN,
                        'check_in' => $user_info->CHECKTIME,
                        'tpsc_generate_user_id' => $user_info->USERID
                    ]);

                    $present_teacher = DB::table('attendance_history')->insert([
                        'user_id' => $user_info->SSN,
                        'time_history' => $user_info->CHECKTIME,
                        'user_type' => 1,
                        'tpsc_user_id' => $user_info->USERID
                    ]);
                } elseif ($selectStaff != null) {
                    $present_staff = DB::table('staff_attendance')->insert([
                        'staff_id' => $user_info->SSN,
                        'check_in' => $user_info->CHECKTIME,
                        'tpsc_generate_user_id' => $user_info->USERID
                    ]);

                    $present_staff = DB::table('attendance_history')->insert([
                        'user_id' => $user_info->SSN,
                        'time_history' => $user_info->CHECKTIME,
                        'user_type' => 2,
                        'tpsc_user_id' => $user_info->USERID
                    ]);
                }
            }
        }
    }
}
