<?php

namespace App\Models\Employee;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'father_name',
        'mother_name',
        'joining_date',
        'resign_date',
        'terminate_date',
        'employee_id',
        'gender',
        'marital_status',
        'religion',
        'designation_id',
        'department_id',
        'date_of_birth',
        'phone',
        'nid',
        'employee_type',
        'school_id',
        'status',
        'photo',
        'job_comments',
    ];

    public function validate(array $input)
    {
        return Validator::make($input, [
            'name' => 'required|string|max:100',
            'father_name' => 'required|string|max:100',
            'mother_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date',

            'phone' => 'required|string|max:20',
            'gender' => 'required|in:1,2,3',
            'marital_status' => 'required|in:1,2',
            'religion' => 'required|in:1,2,3,4',
        ]);
    }

    public function department()
    {
        return $this->belongsTo(EmployeeDepartment::class, 'department_id');
    }

    public function designation()
    {
        return $this->belongsTo(EmployeeDesignation::class, 'designation_id');
    }

    public function employee_qualifications()
    {
        return $this->hasMany(EmployeeQualification::class, 'employee_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
