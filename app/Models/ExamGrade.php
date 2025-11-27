<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamGrade extends Model
{
    use HasFactory;


    protected $table = 'exam_grades';
    protected $fillable = ['grade_name', 'grade_number_id', 'grade_point', 'mark_from', 'mark_to', 'school_id', 'file', 'status',];


    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'grade_name' => 'required',
            'grade_point' => 'required',
            'mark_from' => 'required',
            'mark_to' => 'required',
        ]);
        return $validate;
    }

    public function gradeNumber()
    {
        return $this->belongsTo(GradeNumber::class, 'grade_number_id', 'id');
    }
    
}
