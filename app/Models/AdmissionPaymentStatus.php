<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdmissionPaymentStatus extends Model
{
    use HasFactory;

    protected $table = 'admission_payment_statuses';

    protected $casts = [
        'school_id' => 'int',
        'user_id' => 'int'
    ];

    protected $fillable = [
        'applicant_id',
        'user_id',
        'class_id',
        'session_year_id',
        'section_id',
        'department_id',
        'fees_type_id',
        'fees_master_id',
        'payment_method',
        'total_amount',
        'payment_method',
        'status',
    ];
    public function validate($input = [])
    {
        $validator = Validator::make($input,[


        ]);

        return $validator;
    }
    public function class_name(){
        return $this->belongsTo(StudentClass::class,'class_id','id');
    }
    public function section()
    {
        return $this->belongsTo(section::class, 'section_id', 'id');
    }
    public function session()
    {
        return $this->belongsTo(SessionYear::class, 'session_year_id', 'id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function fees_type()
    {
        return $this->belongsTo(FeesType::class, 'fees_type_id', 'id');
    }
    public function fees_master()
    {
        return $this->belongsTo(FeesType::class, 'fees_master_id', 'id');
    }
}
