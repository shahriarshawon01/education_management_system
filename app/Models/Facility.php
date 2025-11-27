<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Facility extends Model
{
    use HasFactory;
    protected $table = 'facilities';
    protected $fillable = ['title','description','school_id'];
    protected $casts = [
        'school_id',
        'status',
    ];
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'title' => 'required',
            'description' => 'required',
        ]);
        return $validate;
    }


}
