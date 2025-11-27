<?php

namespace App\Models;

use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guardian extends Model
{
    protected $table = 'guardians';

    use ModelScopes;

    use HasFactory;

    protected $fillable = ['school_id', 'student_id', 'guardian_name', 'guardian_phone', 'relation'];

    public function validate($input = [])
    {
        $validate = Validator::make($input, []);

        return $validate;
    }
}
