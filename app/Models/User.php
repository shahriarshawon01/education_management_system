<?php

namespace App\Models;

use App\Models\Employee\Employee;
use App\Models\Scopes\ModelScopes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Validator;

class User extends Authenticatable
{
    use ModelScopes;

    protected $table = 'users';
    protected $guarded = [];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y',
    ];
    public function validate($input)
    {
        $validate = Validator::make($input, [
            'nid' => 'required',
            'username' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        return $validate;
    }

    public function roles()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'id', 'user_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Employee::class, 'id','user_id' );
    }
    public function staff()
    {
        return $this->belongsTo(Employee::class, 'id','user_id' );
    }

    public function school()
    {
        return $this->belongsTo(School::class,  'school_id','id');
    }
}
