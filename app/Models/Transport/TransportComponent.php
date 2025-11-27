<?php

namespace App\Models\Transport;

use App\Models\Component;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransportComponent extends Model
{
    use HasFactory;

    protected $table = 'transport_components';
    protected $fillable = ['component_id', 'school_id', 'status'];


    public function validate($input = [], $id = null)
    {
        $validate = Validator::make($input, [
            'component_id' => [
                'required',
                Rule::unique('transport_components', 'component_id')->ignore($id),
            ],
        ]);
        return $validate;
    }

    public function component()
    {
        return $this->belongsTo(Component::class, 'component_id', 'id');
    }
}
