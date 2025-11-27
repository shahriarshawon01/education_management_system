<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApprisalTemplate extends Model
{
    protected $table = 'apprisal_templates';
    // protected $casts = [
    //     'kra' => 'array',
    //     'wheightage' => 'array',
    // ];

    use HasFactory;

    protected $fillable = ['school_id', 'apprisal_for', 'wheightage_total', 'wheightage', 'kra'];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'apprisal_for' => 'required',
        ]);

        return $validate;
    }
}