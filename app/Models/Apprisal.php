<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Apprisal extends Model
{
    protected $table = 'apprisals';


    use HasFactory;

    // protected $fillable = [];
    protected $guarded = [];

    protected $casts = [
        'get_mark' => 'array',
    ];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            // 'apprisal_type' => 'required',
            // 'template_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        return $validate;
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'id');
    }
    public function template()
    {
        return $this->belongsTo(ApprisalTemplate::class, 'template_id', 'id');
    }
    public function student_class()
    {
        return $this->belongsTo(StudentClass::class, 'class_id', 'id');
    }

}
