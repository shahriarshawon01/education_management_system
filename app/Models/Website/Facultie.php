<?php

namespace App\Models\Website;


use App\Models\School;
use App\Models\Teacher;
use App\Models\Department;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Facultie extends Model
{
   
    protected $table = 'faculties';
    use ModelScopes;
    use HasFactory;
    protected $fillable = ['school_id','department_id','teacher_id','msg_from_head','lab_info','status',];
   
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'department_id' => 'required',
            'teacher_id' => 'required',
            'msg_from_head' => 'required',
            'lab_info' => 'required',
        ]);

        return $validate;
    }
  
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
    public function departments()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function teachers()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }
}
