<?php

namespace App\Models\Website;


use App\Models\School;
use App\Models\Department;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebsiteFeesStructure extends Model
{
   
    protected $table = 'website_fees_structures';
    use ModelScopes;
    use HasFactory;
    protected $fillable = ['school_id','department_id','fees_structure','status',];
   
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'department_id' => 'required',
            'fees_structure' => 'required',
        ]);

        return $validate;
    }
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
    public function departments()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
}
