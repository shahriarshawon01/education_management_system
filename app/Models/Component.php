<?php

namespace App\Models;

use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Component extends Model
{
    protected $table = 'components';

    use ModelScopes;

    use HasFactory;

    protected $fillable = ['name', 'school_id', 'component_type', 'value'];

    public function validate($input = [])
    {
        $validate = Validator::make($input, rules: [
            'name' => 'required',
            'component_type' => 'required',
        ]);
        return $validate;
    }
}
