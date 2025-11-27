<?php

namespace App\Models\Transport;

use App\Models\Guest;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Validation\Rule;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssignTransport extends Model
{
    use ModelScopes;
    use HasFactory;
    protected $table = 'assign_transports';
    protected $fillable = [
        'school_id',
        'transport_user_type',
        'transport_user_id',
        'transport_id',
        'discount',
        'net_amount',
        'payable_amount',
        'stoppage',
        'assign_date',
        'comments',
        'status',
        'created_by',
        'updated_by',
    ];

    public function validate($input = [], $id = null)
    {

        $rules = [
            'transport_id' => 'required',
            'stoppage' => 'required',
            'assign_date' => 'required',
            'transport_user_type' => 'required|in:1,2,3',
        ];

        $uniqueRule = Rule::unique('assign_transports', 'transport_user_id')
            ->where(function ($query) use ($input) {
                return $query
                    ->where('transport_user_type', $input['transport_user_type'] ?? null);
            });

        if (!is_null($id)) {
            $uniqueRule->ignore($id, 'id');
        }

        $rules['transport_user_id'] = ['required', $uniqueRule];

        return Validator::make($input, $rules);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function transport()
    {
        return $this->belongsTo(TransportManage::class, 'transport_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Teacher::class, 'transport_user_id', 'id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'transport_user_id', 'id');
    }
}
