<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class WebsiteSetup extends Model
{
    use HasFactory;
    protected $fillable = ['school_id','key', 'type', 'display_name', 'value'];

    public function validate($input)
    {
        return Validator::make($input, [
            // 'display_name' => 'required',
        ]);
    }
}
