<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchoolLife extends Model
{
    use HasFactory;
    protected $table = 'school_lives';
    protected $fillable = ['title','description','school_id','image'];
    protected $casts = [
        'school_id',
        'status',
    ];
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required',
        ]);
        return $validate;
    }

}
