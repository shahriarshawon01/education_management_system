<?php

namespace App\Models;

use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;
    protected $table = 'services';
    use ModelScopes;
    protected $fillable = ['school_id','title','description', 'image','status'];
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'title' => 'required',
            'description' => 'required',
        ]);

        return $validate;
    }


}
