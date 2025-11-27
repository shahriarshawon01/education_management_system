<?php

namespace App\Models\Dormitory;

use App\Models\Component;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DormitoryComponent extends Model
{
       use HasFactory;

    protected $table = 'dormitory_components';
    protected $fillable = ['component_id', 'school_id', 'status'];


    public function validate($input = [], $id = null)
    {
        $validate = Validator::make($input, [
            'component_id' => [
                'required',
                Rule::unique('dormitory_components', 'component_id')->ignore($id),
            ],
        ]);
        return $validate;
    }

    public function component()
    {
        return $this->belongsTo(Component::class, 'component_id', 'id');
    }
}
