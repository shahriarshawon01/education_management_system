<?php

namespace App\Models\Store;

use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreCategory extends Model
{
    protected $table = 'store_categories';

    use ModelScopes;

    use HasFactory;

    protected $fillable = ['school_id', 'name', 'status',];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'name' => 'required',
        ]);

        return $validate;
    }

    public function products()
    {
        return $this->hasMany(StoreProduct::class, 'category_id');
    }
}
