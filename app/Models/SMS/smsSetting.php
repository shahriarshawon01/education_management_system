<?php

namespace App\Models\SMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class smsSetting extends Model
{
    use HasFactory;

    protected $table = 'sms_settings';
    // protected $guarded = [];
    protected $fillable = ['school_id','api_key','api_secret','request_type','message_type','status',];


    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'api_key' => 'required',
            'api_secret' => 'required',
            'request_type' => 'required',
            'message_type' => 'required',
        ]);
        return $validate;
    }
}
