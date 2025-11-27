<?php

namespace App\Models\Library;

use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class BookPublisher extends Model
{
    protected $table = 'book_publishers';
    protected $fillable = ['name','school_id'];
    use ModelScopes;
    use HasFactory;
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'name' => 'required'
        ]);
        return $validate;
    }
}
