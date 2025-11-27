<?php

namespace App\Models\Website;


use App\Models\School;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
   
    protected $table = 'events';
    use ModelScopes;
    use HasFactory;
    protected $fillable = ['school_id','title', 'slug','event_type','description','image','status',];
   
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'title' => 'required',
            'event_type' => 'required',
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
}
