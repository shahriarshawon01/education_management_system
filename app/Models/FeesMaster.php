<?php

namespace App\Models;

use App\Models\Classes;
use App\Models\Session;
use App\Models\FeesType;
use App\Models\FeesGroup;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeesMaster extends Model
{
    use HasFactory;
    use ModelScopes;

    protected $table = 'fees_masters';

    protected $casts = [
        'school_id' => 'int'
    ];

    protected $fillable = [
        'type',
        'date',
        'amount',
        'percentage',
        'fixed',
        'discount',
        'session_year_id',
        'class_id',
        'section_id',
        'department_id',
        'school_id',
        'status',
    ];
    public function validate($input = []){
        $validator = Validator::make($input,[

        ]);

        return $validator;
    }
    public function class_name()
    {
        return $this->belongsTo(StudentClass::class, 'class_id', 'id');
    }
    public function session()
    {
        return $this->belongsTo(SessionYear::class, 'session_year_id', 'id');
    }

    public function types()
    {
        return $this->belongsTo(FeesType::class, 'type', 'id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function fees_master(){
        return $this->hasMany(AdmissionPaymentStatus::class,'fees_master_id','id');
    }

    public function configs(){
        return $this->hasMany(FeesMaster::class, 'id', 'id');
    }
}
