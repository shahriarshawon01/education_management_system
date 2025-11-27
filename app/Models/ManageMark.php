<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ManageMark extends Model
{
    use HasFactory;

    protected $table = 'manage_marks';
    protected $fillable =
        [
            'subject_id',
            'class_id',
            'session_year_id',
            'department_id',
            'student_id',
            'grade_number_id',
            'exam_id',
            'exam_type_id',
            'exam_mark_data',
            'component_name',
            'subject_total_mark',
            'result_mark',
            'cgpa',
            'grade',
            'school_id',
            'result_status',
            'status'
        ];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'total_mark' => 'required',
            'class_id' => 'required',
            'session_year_id' => 'required',
            'subject_id' => 'required',
        ]);
        return $validate;
    }
    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }
    public function class()
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
