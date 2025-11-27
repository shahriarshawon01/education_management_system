<?php

namespace App\Http\Controllers;

use App\Models\Student;

class StudentIdCardController extends Controller
{
    public function getIdCard($student_id)
    {
        if (!can('student_id_print')) {
            return $this->notPermitted();
        }

        $student = Student::with([
            'classes:id,name',
            'sections:id,name',
            'address:id,addressable_id,village,post_office,district',
            'address.districts:id,name',
            'school:id,title,email,phone,address'
        ])->where('id', $student_id)->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 5000);
        }

        $address = $student->address->first() ?? null;

        $student_data = [
            'student_name' => $student->student_name_en,
            'photo' => $student->photo,
            'merit_roll' => $student->merit_roll,
            'class' => $student->classes->name ?? null,
            'section' => $student->sections->name ?? null,
            'session_year' => $student->session_year_id,
            'class_roll' => $student->merit_roll,
            'student_roll' => $student->student_roll,
            'father_name' => $student->father_name_en,
            'father_phone' => $student->father_phone,
            'mother_name' => $student->mother_name_en,
            'blood_group' => $student->blood_group,
            'address' => [
                'district' => $address ? $address->districts['name'] ?? null : null,
                'village' => $address ? $address->village ?? null : null,
                'post_office' => $address->post_office ?? null,
            ],
            'school' => [
                'title' => $student->school->title ?? null,
                'email' => $student->school->email ?? null,
                'phone' => $student->school->phone ?? null,
                'address' => $student->school->address ?? null,
            ],
        ];
        return response()->json($student_data);
    }
}
