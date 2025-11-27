<?php

namespace App\Models\Store;

use App\Models\Store\StoreProduct;
use App\Models\Store\StoreCategory;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockIn extends Model
{
    protected $table = 'stock_ins';
    use ModelScopes;
    use HasFactory;

    protected $fillable =
    [
        'product_id',
        'category_id',
        'product_code',
        'purchase_date',
        'sale_date',
        'purchase_price',
        'sale_price',
        'purchase_quantity',
        'sale_quantity',
        'school_id',
        'status'
    ];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'product_id' => 'required',
            'category_id' => 'required',
            'product_code' => 'required',
            'purchase_date' => 'required',
            'purchase_price' => 'required',
            'sale_price' => 'required',
            'purchase_quantity' => 'required'
        ]);

        return $validate;
    }
    public function store_category()
    {
        return $this->belongsTo(StoreCategory::class, 'category_id', 'id',);
    }
    public function product()
    {
        return $this->belongsTo(StoreProduct::class, 'product_id', 'id',);
    }
}
