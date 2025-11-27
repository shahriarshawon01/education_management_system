<?php

namespace App\Models\Canteen;

use App\Models\User;
use App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CanteenDailySale extends Model
{
    use HasFactory;
    protected $table = 'canteen_daily_sales';
    protected $appends = ['menus'];

    protected $fillable = [
        'school_id',
        'name',
        'phone',
        'date',
        'month',
        'meal_time_id',
        'menu_id',
        'amount',
        'is_canteen_member',
        'member_id',
        'member_type',
        'sale_type',
        'invoice_generate',
        'voucher_number',
        'status',
        'created_by',
        'updated_by',
    ];

    public function validate($input = [])
    {
        return Validator::make($input, [
            'date' => 'required|date',
            'month' => 'nullable|integer|min:1|max:12',
            'meal_time_id' => 'required|integer|exists:meal_times,id',
            'menu_id' => 'required|array',
            'amount' => 'required|numeric|min:0',
            'is_canteen_member' => 'required|boolean',
            'member_id' => 'nullable|integer',
            'member_type' => 'required|in:1,2,3',
            'sale_type' => 'required|in:1,2',
        ]);
    }

    public function mealTime()
    {
        return $this->belongsTo(MealTime::class, 'meal_time_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getMenusAttribute()
    {
        $menuIds = json_decode($this->menu_id, true);
        if (!is_array($menuIds)) {
            $clean = preg_replace('/[\[\]"]/', '', $this->menu_id);
            $menuIds = array_filter(explode(',', $clean));
        }

        if (empty($menuIds)) {
            return [];
        }

        return CanteenMenuItem::whereIn('id', $menuIds)->get(['id', 'item_name']);
    }
       public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }


    protected $casts = [
        'invoice_generate' => 'boolean',
        'is_canteen_member' => 'boolean',
    ];
}
