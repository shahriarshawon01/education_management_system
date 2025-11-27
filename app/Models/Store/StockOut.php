<?php

namespace App\Models\Store;

use App\Models\Store\StoreProduct;
use App\Models\Scopes\ModelScopes;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockOut extends Model
{
    protected $table = 'stock_outs';

    use ModelScopes;

    use HasFactory;

    protected $fillable =
    [
        'product_id',
        'product_code',
        'stock_id',
        'customer_type',
        'customer_id',
        'sale_date',
        'price',
        'total_price',
        'quantity',
        'status',
        'school_id'
    ];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'product_id' => 'required',
            'product_code' => 'required',
            'customer_type' => 'required',
            'sale_date' => 'required',
            'price' => 'required',
            'total_price' => 'required',
            'quantity' => 'required',
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
    public function stock_in()
    {
        return $this->belongsTo(StockIn::class, 'stock_id', 'id',);
    }
    public function customer()
    {
        switch ($this->customer_type) {
            case 'student':
                return $this->belongsTo(Student::class, 'customer_id', 'id');
            case 'teacher':
                return $this->belongsTo(Teacher::class, 'customer_id', 'id');
            case 'staff':
                return $this->belongsTo(Staff::class, 'customer_id', 'id');
            default:
                return null;
        }
    }
}
