<?php

namespace App\Models\SMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class smsHistory extends Model
{
    protected $table = 'sms_history';
    protected $fillable = ['mobile', 'sms', 'request_type', 'message_type', 'server_code', 'server_response', 'campaign_title'];
    // protected $guarded = [];
}
