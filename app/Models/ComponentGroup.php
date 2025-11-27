<?php

namespace App\Models;

use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class ComponentGroup extends Model
{
    protected $table = 'component_groups';
    use ModelScopes;
    use HasFactory;

    protected $fillable = ['name', 'school_id'];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'name' => 'required',
        ]);

        return $validate;
    }
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
}
