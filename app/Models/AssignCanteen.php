<?php

namespace App\Models;

use App\Models\Employee\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\Rule;

class AssignCanteen extends Model
{
    use HasFactory;

    protected $table = 'assign_canteens';
    protected $fillable = [
        'school_id',
        'consumer_type',
        'consumer_id',
        'assign_date',
        'comments',
        'status',
    ];

    public function validate($input = [], $id = null)
    {
        $schoolId = auth()->user()->school_id ?? null;

        $rules = [
            'consumer_type' => 'required|in:1,2,3', // 1=Student, 2=Employee, 3=Guest
            'assign_date' => 'required|date',
            'consumer_id' => [
                'required',
                Rule::unique('assign_canteens')
                    ->ignore($id)
                    ->where(function ($query) use ($input, $schoolId) {
                        return $query->where('consumer_type', $input['consumer_type'])
                            ->where('school_id', $schoolId);
                    }),
            ],
        ];

        $messages = [
            'consumer_id.unique' => '⚠️ This consumer is already assigned to the canteen.',
            'consumer_type.required' => 'Consumer type is required.',
            'assign_date.required' => 'Assign date is required.',
        ];

        return Validator::make($input, $rules, $messages);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'consumer_id', 'id')
            ->where('consumer_type', 1);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'consumer_id', 'id')
            ->where('consumer_type', 2);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'consumer_id', 'id')
            ->where('consumer_type', 3);
    }
}
