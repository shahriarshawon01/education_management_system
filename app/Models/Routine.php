<?php

namespace App\Models;

use App\Models\Employee\Employee;
use App\Models\Section;
use Mockery\Matcher\Subset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Routine extends Model
{
    use HasFactory;

    protected $table = 'routines';
    // protected $guarded = [];
    protected $fillable = ['class_id', 'session_id', 'section_id','session_year_id', 'subject_id', 'building_id', 'room_id', 'teacher_id', 'school_id', 'day', 'status', 'starting_hour', 'end_hour','image'];


    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'day' => 'required',
            'class_id' => 'required',
            'session_year_id' => 'required',
            'subject_id' => 'required',
            'building_id' => 'required',
            'room_id' => 'required',
            'teacher_id' => 'required',

            'starting_hour' => 'required',
            'end_hour' => 'required',
            'image' => 'required',
        ]);
        return $validate;
    }
    protected $casts = [
        'file' => 'json',
    ];


    public function class()
    {
        return $this->belongsTo(StudentClass::class, 'class_id', 'id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
    public function teacher()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'room_id', 'id');
    }
    public function sessions()
    {
        return $this->belongsTo(SessionYear::class, 'session_year_id', 'id');
    }
    public function sections()
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
}
