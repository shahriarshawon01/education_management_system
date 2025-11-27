<?php

namespace App\Models;

use App\Models\Department;
use App\Models\Employee\Employee;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    protected $table = 'subjects';
    use ModelScopes;
    use HasFactory;

    protected $guarded = [];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'name' => 'required',
            'subject_mark' => 'required',
            'section_id' => 'required',
            'class_id' => 'required',
            'session_year_id' => 'required',
            'department_id' => 'required',
        ]);

        return $validate;
    }
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
    public function classes()
    {
        return $this->belongsTo(StudentClass::class, 'class_id', 'id');
    }
    public function sessions()
    {
        return $this->belongsTo(SessionYear::class, 'session_year_id', 'id');
    }
    public function departments()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function sections()
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }

    public function teachers()
    {
        return $this->belongsTo(Employee::class, 'teacher_id', 'id')->where('employee_type',1);
    }

    public function groupSubject()
    {
        return $this->belongsTo(SubjectGroup::class, 'subject_group_id');
    }
}
