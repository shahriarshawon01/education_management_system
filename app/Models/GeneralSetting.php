<?php

namespace App\Models;

use App\Models\Institute;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GeneralSetting extends Model
{
    protected $table = 'general_settings';

    use ModelScopes;

    use HasFactory;

    protected $fillable = ['school_id','key', 'type', 'display_name', 'value'];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
           
        ]);

        return $validate;
    }
    public function getSettingTypeAttribute($value)
    {
        if ($value == 1) {
            return 'General Setting';
        }
        if ($value == 2 || $value == 3 || $value == 4 || $value == 5) {
            return 'Invoice Manege By';
        }
        if ($value == 6||$value == 7) {
            return 'School Start And Off Time Setting';
        }
        return 'OtherSetting';
    }
    
}
