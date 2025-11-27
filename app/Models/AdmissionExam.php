<?php

namespace App\Models;

use App\Models\Admission\admission_requests_exam_mark_entry;
use App\Models\Classes;
use App\Models\GroupType;
use App\Models\Scopes\ModelScopes;
use App\Rules\UniqueWithinSessionAndGroupWiseClasses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\Rule;

class AdmissionExam extends Model
{
    use ModelScopes;
    use HasFactory;

    protected $table = 'admission_exams';

    protected $casts = [
        'school_id' => 'int',
    ];

    protected $fillable = [
        'class_id',
        'session_year_id',
        'section_id',
        'department_id',
        'floor_id',
        'room_id',
        // 'pass_mark',
        // 'max_mark',
        // 'seat_allowance',
        'exam_date',
        'is_seat_fixed',
    ];

    public function validate($input = [])
    {
        $attributes = [
            'class_id' => $input['class_id'],
            'session_year_id' => $input['session_year_id'],
            'section_id' => $input['section_id'],
            'department_id' => $input['department_id'],
        ];
        $validator = Validator::make($input,[
            // 'class_id' => ['required', new UniqueWithinSessionAndGroupWiseClasses($attributes)],
            // 'group_id' => ['required', new UniqueWithinSessionAndGroupWiseClasses($attributes)],
            // 'session_id' => ['required', new UniqueWithinSessionAndGroupWiseClasses($attributes)],
            // 'seat_allowance' => 'required_if:is_seat_fixed,true',
            // 'pass_mark' => 'required',
            // 'max_mark' => 'required',
            'class_id' => 'required',
            'session_year_id' => 'required',
            'section_id' => 'required',
            'department_id' => 'required',
            // 'building_id' => 'required',
            // 'floor_id' => 'required',
            // 'room_id' => 'required',
            'exam_date' => 'required',
            'starting_time' => 'required',
             'ending_time' => 'required'
        ]);

        return $validator;
    }

    public function validateUpdate($input = [])
    {
        $attributes = [
            'id' => $input['id'],
            'class_id' => $input['class_id'],
            'session_year_id' => $input['session_year_id'],
            'section_id' => $input['section_id'],
            'department_id' => $input['department_id'],
        ];
        $validator = Validator::make($input,[
            // 'class_id' => ['required', new UniqueWithinSessionAndGroupWiseClasses($attributes)],
            // 'group_id' => ['required', new UniqueWithinSessionAndGroupWiseClasses($attributes)],
            // 'session_id' => ['required', new UniqueWithinSessionAndGroupWiseClasses($attributes)],
            // 'seat_allowance' => 'required_if:seat_is_fixed,true',
            // 'pass_mark' => 'required',
            // 'max_mark' => 'required',
            'class_id' => 'required',
            'session_year_id' => 'required',
            'section_id' => 'required',
            'department_id' => 'required',
            // 'building_id' => 'required',
            // 'floor_id' => 'required',
            // 'room_id' => 'required',
            'exam_date' => 'required',
        ]);

        return $validator;
    }
    // public function category()
    // {
    //     return $this->belongsTo(Classes::class, 'class_id', 'id');
    // }
    public function class_info()
    {
        return $this->belongsTo(StudentClass::class, 'class_id', 'id');
    }
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function current_sessions()
    {
        return $this->belongsTo(SessionYear::class, 'session_year_id', 'id');
    }
    public function selection_seat_mapping()
    {
        return $this->hasMany(admission_selection_mapping::class, 'admission_exam_id', 'id');
    }
    public function selection_subject_mapping()
    {
        return $this->hasMany(admission_selection_subject_mapping::class, 'admission_exam_id', 'id');
    }
    public function admission_exam_mark()
    {
        return $this->hasOne(admission_requests_exam_mark_entry::class, 'exam_id', 'id');
    }

}
