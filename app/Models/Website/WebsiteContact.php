<?php

namespace App\Models\Website;


use App\Models\School;
use App\Models\Designation;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebsiteContact extends Model
{
   
    protected $table = 'website_contacts';
    use ModelScopes;
    use HasFactory;
    protected $fillable = ['school_id','name','designation_id','phone','email','status',];
   
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'name' => 'required',
            'designation_id' => 'required',
            'phone' => 'required',
            'email' => 'required',
        ]);

        return $validate;
    }
  
  
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id', 'id');
    }

}
