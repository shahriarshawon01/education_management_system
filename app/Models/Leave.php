<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Leave extends Model
{
    protected $table = 'leaves';

    use HasFactory;

    // protected $guarded = [];

    protected $fillable = ['school_id','student_id','teacher_id','staff_id','type','category_id','apply_date','apply_to','from_date',
    'to_date','no_of_days','note','file','approve_days','image','status',];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'type' => 'required',
            'category_id' => 'required',
            'apply_date' => 'required',
            'apply_to' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'no_of_days' => 'required',
            'note' => 'required',
        ]);

        return $validate;
    }

    public function category()
    {
        return $this->belongsTo(LeaveCategory::class, 'category_id', 'id');
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
}
