<?php

namespace App\Models\Library;

use App\Models\Employee\Employee;
use App\Models\Student;
use App\Models\Scopes\ModelScopes;
use App\Models\Staff;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Membership extends Model
{
    protected $table = 'memberships';
    protected $fillable = [
        'type',
        'member_id',
        'school_id',
        'status',
        'membership_date',
        'created_by',
        'updated_by',
    ];

    use ModelScopes;
    use HasFactory;
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'type' => 'required',
        ]);
        return $validate;
    }
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }  
    public function staff()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function member()
    {
        switch ($this->type) {
            case '1':
                return $this->belongsTo(Student::class, 'member_id', 'id');
            case '2':
                return $this->belongsTo(Employee::class, 'member_id', 'id');
                    case '3':
                return $this->belongsTo(Employee::class, 'member_id', 'id');
            default:
                return null;
        }
    }
}
