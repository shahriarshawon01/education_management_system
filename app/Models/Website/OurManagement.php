<?php

namespace App\Models\Website;


use App\Models\School;
use App\Models\Designation;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OurManagement extends Model
{
   
    protected $table = 'our_management';
    use ModelScopes;
    use HasFactory;
    protected $fillable = ['school_id','name','designation_id','image','status',];
   
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'name' => 'required',
            'designation_id' => 'required',
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
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id', 'id');
    }
}
