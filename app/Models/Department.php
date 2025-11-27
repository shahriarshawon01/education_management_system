<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Department extends Model
{
    use HasFactory;
    protected $table = 'departments';

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y',
    ];
    public function validate($input)
    {
        $validate = Validator::make($input, [
            'department_name' => 'required',
            'department_code' => 'required',
        ]);

        return $validate;
    }
    public function class()
    {
        return $this->belongsTo(StudentClass::class, 'class_id', 'id');
    }
    public function users()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
