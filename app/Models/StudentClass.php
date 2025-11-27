<?php

namespace App\Models;

use App\Models\Employee\Employee;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentClass extends Model
{
    protected $table = 'student_classes';

    use ModelScopes;

    use HasFactory;

    protected $fillable = ['school_id', 'name','numeric_name','teacher_id', 'status',];

    // public function validate($input = [])
    // {
    //     $validate = Validator::make($input, [
    //         'name' => 'required',
    //         'numeric_name' => 'required',
    //     ]);

    //     return $validate;
    // }

    public function validate($input = [], $id = null)
    {
        $rules = [
            'name' => 'required',
            'numeric_name' => 'required|unique:student_classes,numeric_name,' . $id . ',id', 
        ];

        $validate = Validator::make($input, $rules);
        return $validate;
    }


    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
    public function teacher()
    {
        return $this->belongsTo(Employee::class, 'teacher_id', 'id')->where('employee_type',1);
    }

}
