<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Building extends Model
{
    use HasFactory;

    protected $table = 'buildings';
    protected $fillable = ['name', 'school_id', 'status',];


    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'name' => 'required',
        ]);
        return $validate;
    }
}
