<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkDepartment extends Model
{
    use HasFactory;

    protected $table = 'work_departments';
    // protected $guarded = [];
    protected $fillable = ['name', 'school_id', 'status',];


    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'name' => 'required',
        ]);
        return $validate;
    }
}
