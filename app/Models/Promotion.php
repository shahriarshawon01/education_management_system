<?php

namespace App\Models;

use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promotion extends Model
{
    protected $table = 'promotions';

    use ModelScopes;

    use HasFactory;

    protected $guarded = [];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'class_id' => 'required',
        ]);

        return $validate;
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
    public function oldClass()
    {
        return $this->belongsTo(StudentClass::class, 'class_id', 'id');
    }
    public function newClass()
    {
        return $this->belongsTo(StudentClass::class, 'promotion_class_id', 'id');
    }
    public function oldSection()
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
    public function newSection()
    {
        return $this->belongsTo(Section::class, 'promotion_section_id', 'id');
    }
    public function oDepartment()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function newDepartment()
    {
        return $this->belongsTo(Department::class, 'promotion_department_id', 'id');
    }
}
