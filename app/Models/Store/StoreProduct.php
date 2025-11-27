<?php

namespace App\Models\Store;

use App\Models\Scopes\ModelScopes;
use App\Models\Store\StoreCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreProduct extends Model
{
    protected $table = 'store_products';

    use ModelScopes;

    use HasFactory;

    protected $fillable = ['school_id','category_id','name','description','status',];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'name' => 'required',
            'category_id' => 'required',
            'status'
        ]);

        return $validate;
    }
    public function store_category()
    {
        return $this->belongsTo(StoreCategory::class, 'category_id','id',);
    }

    
}
