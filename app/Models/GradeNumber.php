<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GradeNumber extends Model
{
    use HasFactory;

    protected $table = 'grade_numbers';
    protected $fillable = [
        'subject_id',
        'class_id',
        'exam_id',
        'department_id',
        'section_id',
        'session_year_id',
        'exam_type_id',
        'grade_number',
        'mark_component',
        'exam_mark',
        'convert_num',
        'total_mark',
        'pass_mark',
        'school_id',
        'status'
    ];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'grade_number' => 'required',
        ]);
        return $validate;
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
    public function session()
    {
        return $this->belongsTo(SessionYear::class, 'session_year_id', 'id');
    }
    public function classes()
    {
        return $this->belongsTo(StudentClass::class, 'class_id', 'id');
    }
    public function exam()
    {
        return $this->belongsTo(ExamName::class, 'exam_id', 'id');
    }
    public function exam_type()
    {
        return $this->belongsTo(Examination::class, 'exam_type_id', 'id');
    }
}
