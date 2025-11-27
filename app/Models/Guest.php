<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Guest extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function validate($input = [])
    {
        $validate = Validator::make($input, []);
        return $validate;
    }
}
