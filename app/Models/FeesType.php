<?php

namespace App\Models;


use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;


class FeesType extends Model
{
    use HasFactory;
    use ModelScopes;

    protected $casts = [
        'school_id' => 'int'
    ];

    protected $fillable = [
        'name',
        'school_id',
        'code',
        // 'description',
        'status',
    ];
    public function validate($input = []){
        $validator = Validator::make($input,[
            'name' => 'required',
            // 'code' => 'required',
        ]);

        return $validator;
    }
    public function fees_types(){
        return $this->hasMany(AdmissionPaymentStatus::class,'fees_type_id','id');
    }
}
