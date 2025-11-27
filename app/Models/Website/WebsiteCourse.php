<?php

namespace App\Models\Website;


use App\Models\School;
use App\Models\Scopes\ModelScopes;
use App\Models\Website\CourseCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebsiteCourse extends Model
{
   
    protected $table = 'website_courses';
    use ModelScopes;
    use HasFactory;
    protected $fillable = ['school_id','category_id','student_name','start_date','end_date','roll_no','reg_no','email','date_of_birth',
    'phone','gender','religion','guardian_mobile','religion','image','status',];
   
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'student_name' => 'required',
            'roll_no' => 'required',
            'reg_no' => 'required',
            'email' => 'required',
            // 'phone' => 'required',
            'gender' => 'required',
            'date_of_birth' => 'required',
            'guardian_mobile' => 'required',
            'religion' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        return $validate;
    }
    protected $casts = [
        'file' => 'json',
    ];
  
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
    public function website_course()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id', 'id');
    }
   
}
