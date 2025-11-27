<?php

namespace App\Models\Canteen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class CanteenMenuItem extends Model
{
    use HasFactory;

    protected $table = 'canteen_menu_items';
    protected $fillable = ['item_name','status'];

    public function validate($input = [], $id = null)
    {
        $validate = Validator::make($input, [
            'item_name' => 'required|unique:canteen_menu_items,item_name,' . $id,
        ]);
        return $validate;
    }
}
