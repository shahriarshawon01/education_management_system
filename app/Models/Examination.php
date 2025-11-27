<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Examination extends Model
{
    use HasFactory;

    protected $table = 'examinations';
    protected $guarded = [];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'type_name' => 'required',
            'exam_id' => 'required',
            'class_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        return $validate;
    }

    public function examName()
    {
        return $this->belongsTo(ExamName::class, 'exam_id', 'id');
    }
    public function class()
    {
        return $this->belongsTo(StudentClass::class, 'class_id', 'id');
    }
    public function session()
    {
        return $this->belongsTo(SessionYear::class, 'session_year_id', 'id');
    }
}
