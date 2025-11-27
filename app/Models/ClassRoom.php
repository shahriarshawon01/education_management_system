<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'class_rooms';
    protected $fillable = ['name', 'building_id', 'school_id', 'status',];


    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'name' => 'required',
            'building_id' => 'required',
        ]);
        return $validate;
    }

    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }
}
