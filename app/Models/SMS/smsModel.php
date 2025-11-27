<?php

namespace App\Models\SMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class smsModel extends Model
{
    use HasFactory;

    protected $table = 'sms_models';
    // protected $guarded = [];
    protected $fillable = ['school_id','name','info', 'status',];


    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'name' => 'required',
            'info' => 'required',
        ]);
        return $validate;
    }
}
