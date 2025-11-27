<?php

namespace App\Models\Canteen;

use App\Models\Guest;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyMeal extends Model
{
    use HasFactory;

    protected $fillable = ['consumer_type', 'consumer_id', 'meal_time', 'meal_date', 'meal_count', 'meal_day'];

    public function validate($input = [], $id = null)
    {
        $validate = Validator::make($input, [
            'consumer_type' => 'required',
            'consumer_id' => 'required',
            'meal_time' => 'required|array'
        ]);

        return $validate;
    }

    public function teachers()
    {
        return $this->belongsTo(Teacher::class, 'consumer_id', 'id');
    }

    public function staffs()
    {
        return $this->belongsTo(Staff::class, 'consumer_id', 'id');
    }

    public function students()
    {
        return $this->belongsTo(Student::class, 'consumer_id', 'id');
    }

    public function guests()
    {
        return $this->belongsTo(Guest::class, 'consumer_id', 'id');
    }
}
