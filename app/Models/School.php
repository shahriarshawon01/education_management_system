<?php

namespace App\Models;

use App\Models\Institute;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class School extends Model
{
    protected $table = 'schools';

    use ModelScopes;

    use HasFactory;

    protected $fillable = ['title', 'address', 'steb_number', 'reg_number','institute_code','emis_code','phone','email','logo','map_link','founder_info'];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'title' => 'required',
            'address' => 'required',
            'institute_code' => 'required',
            'phone' => 'required',
            'email' => 'required',
        ]);

        return $validate;
    }

    
}
