<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RosterClosing extends Model
{
    use HasFactory;

    protected $table = 'roster_closings';
    protected $guarded = [];
    protected $casts = ['component_amount' => 'array'];


    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            // 'date' => 'required',
            // 'component_amount' => 'required',
        ]);
        return $validate;
    }
}
