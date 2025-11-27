<?php

namespace App\Models;

use App\Models\Employee\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ManageDormitory extends Model
{
    use HasFactory;
    protected $fillable = [
        'dormitory_name',
        'employee_id',
        'dormitory_no',
        'total_floor',
        'total_room',
        'total_seat',
        'location',
        'description',
        'status',
        'school_id',
        'created_by',
        'updated_by',
    ];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'dormitory_name' => 'required',
            'dormitory_no' => 'required',
            'total_floor' => 'required',
            'total_room' => 'required',
            'total_seat' => 'required',
            'location' => 'required',
        ]);
        return $validate;
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
