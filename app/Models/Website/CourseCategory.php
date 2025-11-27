<?php

namespace App\Models\Website;


use App\Models\School;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseCategory extends Model
{
   
    protected $table = 'course_categories';
    use ModelScopes;
    use HasFactory;
    protected $fillable = ['school_id','name','description','status',];
   
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'name' => 'required',
        ]);

        return $validate;
    }
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
}
