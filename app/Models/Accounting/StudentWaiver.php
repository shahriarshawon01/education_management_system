<?php

namespace App\Models\Accounting;

use App\Models\Student;
use App\Models\Component;
use App\Models\SessionYear;
use App\Models\StudentClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentWaiver extends Model
{
    use HasFactory;

    protected $table = 'student_waivers';
    protected $guarded = [];

    public function validate($input)
    {
        return Validator::make($input, [
            'student_id' => 'required|integer|exists:students,id',
            'component_id' => 'required|integer|exists:components,id',
            'type' => 'required|in:1,2',
            'value' => 'required|numeric|min:0',
        ]);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function classes()
    {
        return $this->belongsTo(StudentClass::class, 'class_id', 'id');
    }

    public function sessions()
    {
        return $this->belongsTo(SessionYear::class, 'session_year_id', 'id');
    }

    public function component()
    {
        return $this->belongsTo(Component::class, 'component_id', 'id');
    }
}
