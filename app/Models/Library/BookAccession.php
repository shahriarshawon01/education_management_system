<?php

namespace App\Models\Library;

use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class BookAccession extends Model
{
    protected $table = 'book_accessions';

    use ModelScopes;
    use HasFactory;
    protected $guarded = [];
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'book_title' => 'required',
            'author' => 'required',
            'publisher' => 'required',
            'edition' => 'required',
            'language' => 'required',
            'cell_number' => 'required',
        ]);
        return $validate;
    }
}
