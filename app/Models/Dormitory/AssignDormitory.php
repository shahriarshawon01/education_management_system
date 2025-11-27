<?php

namespace App\Models\Dormitory;

use App\Models\User;
use App\Models\School;
use App\Models\ManageDormitory;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssignDormitory extends Model
{
    use HasFactory;
    protected $fillable = [
        'dormitory_user_id',
        'dormitory_user_type',
        'dormitory_id',
        'discount',
        'net_amount',
        'payable_amount',
        'floor_number',
        'room_number',
        'seat_number',
        'arrive_date',
        'status',
        'description',
        'school_id',
        'created_by',
        'updated_by',
    ];

    public function validate($input = [], $id = null)
    {
        $schoolId = auth()->user()->school_id ?? null;

        $rules = [
            'dormitory_user_id' => [
                'required',
                Rule::unique('assign_dormitories')
                    ->ignore($id)
                    ->where(function ($query) use ($input, $schoolId) {
                        return $query->where('dormitory_user_type', $input['dormitory_user_type'])
                            ->where('school_id', $schoolId);
                    }),
            ],
            'floor_number' => 'required',
            'room_number' => 'required',
            'arrive_date' => 'required',
            'dormitory_id' => 'required',
        ];

        $messages = [
            'dormitory_user_id.unique' => '⚠️ This member is already assigned to a dormitory.',
            'floor_number.required' => 'Floor number is required.',
            'room_number.required' => 'Room number is required.',
            'arrive_date.required' => 'Arrive date is required.',
            'dormitory_id.required' => 'Dormitory is required.',
        ];

        return Validator::make($input, $rules, $messages);
    }

    public function dormitory()
    {
        return $this->belongsTo(ManageDormitory::class, 'dormitory_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
