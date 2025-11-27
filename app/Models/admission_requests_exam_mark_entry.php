<?php

namespace App\Models;

use App\Models\GroupType;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\admission_selection_subject_mapping;
use App\Models\admission_subjectwise_marks_mapping;

class admission_requests_exam_mark_entry extends Model
{
    use HasFactory;
    use ModelScopes;
    protected $table='admission_requests_exam_mark_entries';
    protected $primaryKey='id';
    protected $softDelete = true;
    protected $fillable=[
        'user_id',
        'applicant_id',
        'school_id',
        // 'exam_id',
        'class_id',
        'session_year_id',
        'section_id',
        'department_id',
        'mark',
    ];


    public function admission_request_info()
    {
        return $this->belongsTo(AdmissionRequest::class, 'admission_request_id', 'id');
    }

    public function admission_exam()
    {
        return $this->belongsTo(AdmissionExam::class, 'exam_id', 'id');
    }

    public  function  admission_info(){
        return $this->belongsTo(AdmissionRequest::class);
    }

    public function class_info()
    {
        return $this->belongsTo(StudentClass::class, 'class_id', 'id');
    }

    public function session_info()
    {
        return $this->belongsTo(SessionYear::class, 'session_year_id', 'id');
    }
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    // public function group()
    // {
    //     return $this->belongsTo(GroupType::class, 'group_id', 'id');
    // }

    public function subjectwise()
    {
        return $this->hasMany(admission_subjectwise_marks_mapping::class, 'applicant_id', 'applicant_id');
    }

    public function subjectname()
    {
        return $this->belongsTo(admission_selection_subject_mapping::class, 'subject_id', 'id');
    }


}
