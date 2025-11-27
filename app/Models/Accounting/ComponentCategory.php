<?php

namespace App\Models\Accounting;


use App\Models\School;
use Illuminate\Validation\Rule;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComponentCategory extends Model
{
    protected $table = 'component_categories';
    use ModelScopes;
    use HasFactory;

    protected $fillable = ['name', 'school_id'];

    public function validate($input = [], $id = null)
    {
        $validate = Validator::make($input, [
            'name' => [
                'required',
                Rule::unique('component_categories', 'name')->ignore($id),
            ],
        ]);

        return $validate;
    }
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
}
