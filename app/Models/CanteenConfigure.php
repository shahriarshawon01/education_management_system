<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CanteenConfigure extends Model
{
    use HasFactory;

    protected $table = 'canteen_configures';
    protected $fillable = ['component',];


    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'component' => 'required',
        ]);
        return $validate;
    }

    public function component()
    {
        return $this->belongsTo(Component::class, 'component', 'id');
    }
}
