<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicantGuardian extends Model
{
    use HasFactory;

    protected $table = "applicant_guardians";
    protected $fillable = [
            'user_id',
            'guardian_name',
            'guardian_relation',
            'guardian_phone',
            'guardian_address',
        ];

        public function validate($input = [])
        {
            $validate = Validator::make($input, [
                'guardian_name' => 'required',
                'guardian_relation' => 'required',
                'guardian_phone' => 'required',
                'guardian_address' => 'required',
            ]);
            return $validate;
        }   

}
