<?php

namespace App\Models\Accounting;

use App\Models\User;
use App\Models\Student;
use App\Models\Employee\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentWaiverDocument extends Model
{
    use HasFactory;

    protected $table = 'student_waiver_documents';
    protected $fillable = ['student_id', 'reason', 'reference_id', 'reference_code', 'reference_name', 'reference_nid', 'reference_phone', 'file', 'remarks', 'status', 'created_by', 'updated_by'];

    public function validate($input = [])
    {
        $rules = [
            'student_id' => 'required',
            'reason' => 'required',
            'file' => 'required',
            'remarks' => 'required|string|max:255',
        ];

        if (isset($input['reason'])) {
            if (in_array($input['reason'], ['1', '3'])) {
                $rules['reference_code'] = 'required';
                $rules['reference_name'] = 'required';
                $rules['reference_nid'] = 'required';
                $rules['reference_phone'] = 'required';
            } elseif ($input['reason'] == '2') {
                $rules['tpsc_staff_id'] = 'required';
            } elseif ($input['reason'] == '5') {
                $rules['merit_child_roll'] = 'required';
            }
        }
        $attributes = [
            'student_id' => 'Student',
            'reason' => 'Reason',
            'file' => 'Waiver Document',
            'remarks' => 'Remarks',
            'reference_code' => 'Debtor/HEM Staff Code',
            'reference_name' => 'Debtor/HEM Staff Name',
            'reference_nid' => 'Debtor/HEM Staff NID',
            'reference_phone' => 'Debtor/HEM Staff Phone',
            'tpsc_staff_id' => 'TPSC Staff ID',
            'merit_child_roll' => 'Child Merit Student Roll',
        ];

        return Validator::make($input, $rules, [], $attributes);
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function updatedUser()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
    public function teachers()
    {
        return $this->belongsTo(Employee::class, 'reference_id', 'id');
    }
}
