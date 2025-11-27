<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamDocument extends Model
{
    use HasFactory;

    protected $table = 'exam_documents';
    protected $fillable = ['class_id', 'session_id', 'exam_id', 'user_id', 'school_id', 'file', 'status'];


    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'class_id' => 'required',
            'session_year_id' => 'required',
            'exam_id' => 'required',
            'file' => 'required',
        ]);
        return $validate;
    }

    public function class()
    {
        return $this->belongsTo(StudentClass::class, 'class_id', 'id');
    }

    public function session()
    {
        return $this->belongsTo(SessionYear::class, 'session_id', 'id');
    }
    public function examName()
    {
        return $this->belongsTo(Examination::class, 'exam_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
