<?php

namespace App\Http\Controllers\SMS;

use App\Models\Staff;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\SmsLibrary\SMSLibrary;
use App\SmsLibrary\GPSMSLibrary;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SmsController extends Controller
{
    // public function smsToAbsentStudent($student_id)
    // {
    //     $studentId = Student::where('id', $student_id)
    //         ->with('classes:id,name', 'sections:id,name')
    //         ->select('id', 'student_name_en', 'student_roll', 'class_id', 'section_id', 'father_phone', 'mother_phone')
    //         ->first();

    //     $formatPhone = str_replace('-', '', $studentId->father_phone);

    //     $request_type = 'SINGLE_SMS';
    //     $sms = 'Dear Guardian, your child ' .
    //         $studentId->student_name_en . ', class: ' .
    //         $studentId->classes->name . ', section: ' .
    //         $studentId->sections->name . ', Student ID : ' .
    //         $studentId->student_roll . ' Is absent today. Best regards, Principal-TPSC';

    //     $GPSMSLibrary = new GPSMSLibrary;
    //     // $GPSMSLibrary = new SMSLibrary;
    //     $data = $GPSMSLibrary->SendSMS($formatPhone, $sms, $message_type = $request_type);

    //     // return response()->json($studentId);

    //     if ($data['statusCode'] == 200) {
    //         return returnData(200, $studentId, 'SMS sent successfully to ' . $studentId->student_name_en);
    //     } else {
    //         return returnData(400, null, 'Failed to send SMS');
    //     }
    // }

    public function smsToAbsentStudent($student_id)
    {
        $studentId = Student::where('id', $student_id)
            ->with('classes:id,name', 'sections:id,name')
            ->select('id', 'student_name_en', 'student_roll', 'class_id', 'section_id', 'father_phone', 'mother_phone')
            ->first();

        if (!$studentId) {
            return response()->json(['status' => 404, 'message' => 'Student not found']);
        }

        $formatPhone = str_replace('-', '', $studentId->father_phone ? $studentId->father_phone : $studentId->mother_phone);

        $request_type = 'SINGLE_SMS';
        $sms = 'Dear Guardian, your child ' .
            $studentId->student_name_en . ', class: ' .
            $studentId->classes->name . ', section: ' .
            $studentId->sections->name . ', Student ID : ' .
            $studentId->student_roll . ' Is absent today. Best regards, Principal-TPSC';

        $GPSMSLibrary = new GPSMSLibrary;
        $data = $GPSMSLibrary->SendSMS($formatPhone, $sms, $message_type = $request_type);

        if ($data['statusCode'] == 200) {
            return response()->json([
                'status' => 200,
                'message' => 'SMS sent successfully',
                'student_name_en' => $studentId->student_name_en
            ]);
        } else {
            return response()->json([
                'status' => $data['statusCode'],
                'message' => $data['message'],
            ]);
        }
    }

    public function smsToClassWiseAbsentStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|array',
            'student_id.*' => 'exists:students,id',
        ]);

        $studentIds = $request->input('student_id');

        $sentSmsResponses = [];
        foreach ($studentIds as $studentId) {
            $student = Student::with('classes:id,name', 'sections:id,name')
                ->select('id', 'student_name_en', 'student_roll', 'class_id', 'section_id', 'father_phone')
                ->find($studentId);

            if ($student) {
                $formatPhone = str_replace('-', '', $student->father_phone);
                $request_type = 'SINGLE_SMS';
                $sms = 'Dear Guardian, your child ' .
                    $student->student_name_en . ', class : ' .
                    $student->classes->name . ', section : ' .
                    $student->sections->name . ', Student ID: ' .
                    $student->student_roll . ' is absent today. Best regards, Principal-TPSC';

                $GPSMSLibrary = new GPSMSLibrary();
                // $GPSMSLibrary = new SMSLibrary();
                $data = $GPSMSLibrary->SendSMS($formatPhone, $sms, $message_type = $request_type);

                $sentSmsResponses[] = [
                    'student_id' => $studentId,
                    'status' => $data
                ];
            }
        }

        return response()->json(['message' => 'SMS sent successfully', 'responses' => $sentSmsResponses]);
    }

    public function customSmsToStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|array',
            'student_id.*' => 'exists:students,id',
            'message' => 'required|string|max:160',
        ]);

        $studentIds = $request->input('student_id');
        $message = $request->input('message');

        $sentSmsResponses = [];
        foreach ($studentIds as $studentId) {
            $student = Student::with('classes:id,name', 'sections:id,name')
                ->select('id', 'student_name_en', 'student_roll', 'class_id', 'section_id', 'father_phone')
                ->find($studentId);

            if ($student) {
                $formatPhone = str_replace('-', '', $student->father_phone);
                $request_type = 'GROUP-CUSTOM-SMS';

                $GPSMSLibrary = new GPSMSLibrary();
                // $GPSMSLibrary = new SMSLibrary();
                $data = $GPSMSLibrary->SendSMS($formatPhone, $message, $request_type);

                $sentSmsResponses[] = [
                    'student_id' => $studentId,
                    'status' => $data
                ];
            }
        }

        return response()->json(['message' => 'SMS sent successfully', 'responses' => $sentSmsResponses]);
    }

    public function getAllStudents(Request $request)
    {
        $all_student = DB::table('students')
            ->leftJoin('student_classes', 'student_classes.id', '=', 'students.class_id')
            ->leftJoin('sections', 'sections.id', '=', 'students.section_id')
            ->where('sections.name', '!=', 'EX')
            ->where('students.status', 1)
            ->when($request->class_id && $request->class_id !== 'all', function ($query) use ($request) {
                return $query->where('students.class_id', $request->class_id);
            })
            ->when($request->session_id && $request->session_id !== 'all', function ($query) use ($request) {
                return $query->where('students.session_year_id', $request->session_id);
            })
            ->when($request->gender_id && $request->gender_id !== 'all', function ($query) use ($request) {
                return $query->where('students.gender', $request->gender_id);
            })
            ->when($request->religion_id && $request->religion_id !== 'all', function ($query) use ($request) {
                return $query->where('students.religion', $request->religion_id);
            });

        $responseData = $all_student->orderBy('student_classes.name')
            ->orderBy('students.section_id')
            ->orderBy('students.merit_roll', 'asc')
            ->select(
                'students.id as student_id',
                'students.student_name_en',
                'students.student_roll',
                'students.class_id',
                'students.merit_roll',
                'students.gender',
                'students.religion',
                'students.father_phone',
                'students.mother_phone',
                'students.section_id',
                'sections.name as section_name',
                'students.status',
                'student_classes.name as class_name'
            )
            ->get();

        return returnData(2000, $responseData);
    }

    public function smsToSingleTeacher($teacher_id)
    {
        $teacherId = Teacher::where('id', $teacher_id)
            ->with('department:id,name', 'designation:id,name')
            ->select('id', 'name', 'teacher_bio_id', 'department_id', 'designation_id', 'phone')
            ->first();

        $formatPhone = str_replace('-', '', $teacherId->phone);

        $request_type = 'TEACHER_SINGLE_SMS';
        $sms = 'Dear Employer, ' .
            $teacherId->name . ', Department: ' .
            $teacherId->department->name . ', Designation: ' .
            $teacherId->designation->name . ', Teacher ID : ' .
            $teacherId->teacher_bio_id . ' You are absent today. Best regards, Principal-TPSC';


        $GPSMSLibrary = new GPSMSLibrary;
        // $GPSMSLibrary = new SMSLibrary;
        $data = $GPSMSLibrary->SendSMS($formatPhone, $sms, $message_type = $request_type);

        return response()->json($teacherId);
    }

    public function smsToMultipleTeacher(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|array',
            'teacher_id.*' => 'exists:teachers,id',
        ]);

        $teacherIds = $request->input('teacher_id');

        $sentSmsResponses = [];
        foreach ($teacherIds as $teacherId) {
            $teacher = Teacher::with('department:id,name', 'designation:id,name')
                ->select('id', 'name', 'teacher_bio_id', 'department_id', 'designation_id', 'phone')
                ->find($teacherId);

            if ($teacher) {
                $formatPhone = str_replace('-', '', $teacher->phone);
                $request_type = 'TEACHER_SINGLE_SMS';
                $sms = 'Dear Employer, ' .
                    $teacher->name . ', Department: ' .
                    $teacher->department->name . ', Designation: ' .
                    $teacher->designation->name . ', Teacher ID : ' .
                    $teacher->teacher_bio_id . ' You are absent today. Best regards, Principal-TPSC';

                $GPSMSLibrary = new GPSMSLibrary();
                // $GPSMSLibrary = new SMSLibrary();
                $data = $GPSMSLibrary->SendSMS($formatPhone, $sms, $message_type = $request_type);

                $sentSmsResponses[] = [
                    'teacher_id' => $teacherId,
                    'status' => $data
                ];
            }
        }

        return response()->json(['message' => 'SMS sent successfully', 'responses' => $sentSmsResponses]);
    }

    public function smsToSingleStaff($staff_id)
    {
        $teacherId = Staff::where('id', $staff_id)
            ->with('department:id,name', 'designation:id,name')
            ->select('id', 'name', 'staff_bio_id', 'department_id', 'designation_id', 'phone')
            ->first();

        $formatPhone = str_replace('-', '', $teacherId->phone);

        $request_type = 'STAFF_SINGLE_SMS';
        $sms = 'Dear Staff, ' .
            $teacherId->name . ', Department: ' .
            $teacherId->department->name . ', Designation: ' .
            $teacherId->designation->name . ', Staff ID : ' .
            $teacherId->staff_bio_id . ' You are absent today. Best regards, Principal-TPSC';


        $GPSMSLibrary = new GPSMSLibrary;
        // $GPSMSLibrary = new SMSLibrary;
        $data = $GPSMSLibrary->SendSMS($formatPhone, $sms, $message_type = $request_type);

        return response()->json($teacherId);
    }

    public function smsToMultipleStaff(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|array',
            'staff_id.*' => 'exists:staff,id',
        ]);

        $staffIds = $request->input('staff_id');

        $sentSmsResponses = [];
        foreach ($staffIds as $staffId) {
            $teacher = Staff::with('department:id,name', 'designation:id,name')
                ->select('id', 'name', 'staff_bio_id', 'department_id', 'designation_id', 'phone')
                ->find($staffId);

            if ($teacher) {
                $formatPhone = str_replace('-', '', $teacher->phone);
                $request_type = 'STAFF_SINGLE_SMS';
                $sms = 'Dear Staff, ' .
                    $teacher->name . ', Department: ' .
                    $teacher->department->name . ', Designation: ' .
                    $teacher->designation->name . ', Staff ID : ' .
                    $teacher->staff_bio_id . ' You are absent today. Best regards, Principal-TPSC';

                $GPSMSLibrary = new GPSMSLibrary();
                // $GPSMSLibrary = new SMSLibrary();
                $data = $GPSMSLibrary->SendSMS($formatPhone, $sms, $message_type = $request_type);

                $sentSmsResponses[] = [
                    'staff_id' => $staffId,
                    'status' => $data
                ];
            }
        }

        return response()->json(['message' => 'SMS sent successfully', 'responses' => $sentSmsResponses]);
    }
}
