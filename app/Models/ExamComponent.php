<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamComponent extends Model
{
    use HasFactory;

    protected $table = 'exam_components';
    protected $fillable = ['name', 'school_id', 'status'];


    public function validate($input = [], $id = null)
    {
        $validate = Validator::make($input, [
            'name' => 'required|unique:exam_names,name,' . $id,
        ]);
        return $validate;
    }
}
