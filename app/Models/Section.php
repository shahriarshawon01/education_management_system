<?php

namespace App\Models;

use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Section extends Model
{
    protected $table = 'sections';
    use ModelScopes;
    use HasFactory;
    protected $guarded = [];
    // protected $fillable = ['school_id','class_id','name', 'status',];
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'name' => 'required',
            'class_id' => 'required',
        ]);

        return $validate;
    }
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
    public function classes()
    {
        return $this->belongsTo(StudentClass::class, 'class_id', 'id');
    }
}
