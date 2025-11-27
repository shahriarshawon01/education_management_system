<?php

namespace App\Models\Canteen;

use App\Models\Canteen\MealTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CanteenMenuRoster extends Model
{
    use HasFactory;
    protected $table = 'canteen_menu_rosters';
    protected $fillable = ['year', 'month', 'day', 'menu_date', 'menu_start_date', 'week_number', 'menu_item_id', 'meal_time_id', 'price'];

    public function validate($input = [], $id = null)
    {
        $validate = Validator::make($input, [
            'day' => 'required',
            'menu_date' => 'required',
            'set_canteen_menu' => 'required|array|min:1',
            'set_canteen_menu.*.canteen_component_id' => 'required|integer',
            'set_canteen_menu.*.menu_item_id' => 'required|integer',
            'set_canteen_menu.*.price' => 'required|numeric',
        ]);

        return $validate;
    }

    public function mealTime()
    {
        return $this->belongsTo(MealTime::class, 'meal_time_id');
    }

    public function menuItem()
    {
        return $this->belongsTo(CanteenMenuItem::class, 'menu_item_id');
    }
}
