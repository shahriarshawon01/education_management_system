<?php

namespace App\Models\SMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class smsSettingGp extends Model
{
    use HasFactory;

    protected $table = 'sms_setting_gps';
    // protected $guarded = [];
    protected $fillable = ['school_id','username','password','apicode','countrycode','cli','messagetype','messageid','test_number','status',];


    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'cli' => 'required',
            'messagetype' => 'required',
            'messageid' => 'required',
        ]);
        return $validate;
    }
}
