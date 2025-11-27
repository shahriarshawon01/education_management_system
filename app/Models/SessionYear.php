<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\ModelScopes;
use Illuminate\Support\Facades\Validator;

class SessionYear extends Model
{
    protected $table = 'session_years';
    use ModelScopes;
    use HasFactory;
    protected $fillable = ['title', 'school_id', 'status'];

    public function validate($input = [], $id = null)
    {
        $rules = [
            'title' => 'required|unique:session_years,title,' . $id,
        ];

        $validate = Validator::make($input, $rules);
        return $validate;
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
}
