<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcademicSyllabus extends Model
{
    protected $table = 'academic_syllabus';

    use HasFactory;

    protected $fillable = ['school_id','class_id','department_id','subject_id','title','description','file'];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'title' => 'required',
            'description' => 'required',
            'class_id' => 'required',
            'department_id' => 'required',
            'subject_id' => 'required'
        ]);

        return $validate;
    }

    public function class()
    {
        return $this->belongsTo(StudentClass::class, 'class_id', 'id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
}
