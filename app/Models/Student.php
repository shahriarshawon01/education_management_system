<?php

namespace App\Models;

use App\Models\StudentClass;
use App\Models\Employee\Employee;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    protected $table = 'students';

    use ModelScopes;

    use HasFactory;
    protected $guarded = [];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'student_name_en' => 'required',
            'father_name_en' => 'required',
            'mother_name_en' => 'required',
            'blood_group' => 'required',
            'merit_roll' => 'required',
            'class_id' => 'required',
            'session_year_id' => 'required',
        ]);

        return $validate;
    }

    public function guardian()
    {
        return $this->hasMany(Guardian::class, 'student_id', 'id');
    }
    public function address()
    {
        return $this->hasMany(Address::class, 'addressable_id', 'id')->where('addressable_type', 1);
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function qualifications()
    {
        return $this->hasMany(StudentQualification::class, 'student_id');
    }

    public function sessions()
    {
        return $this->belongsTo(SessionYear::class, 'session_year_id', 'id');
    }
    public function sections()
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
    public function classes()
    {
        return $this->belongsTo(StudentClass::class, 'class_id', 'id');
    }

    public function departments()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function teachers()
    {
        return $this->belongsTo(Employee::class, 'responsible_teacher_id', 'id')->where('employee_type', 1);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'id', 'user_id');
    }

    public function optionalSubject()
    {
        return $this->belongsTo(Subject::class, 'optional_subject_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_id', 'id');
    }
}
