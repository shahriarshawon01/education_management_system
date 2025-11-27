<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicantPermanentAddress extends Model
{
    use HasFactory;
    protected $table = "applicant_permanent_addresses";
    protected $fillable = [
            'user_id',
            'division',
            'district',
            'upazila',
            'union',
            'post_office',
            'village',
        ];

        public function validate($input = [])
        {
            $validate = Validator::make($input, [
                'division' => 'required',
                'district' => 'required',
                'upazila' => 'required',
                'union' => 'required',
                'post_office' => 'required',
                'village' => 'required',
            ]);
            return $validate;
        } 

        public function division_name()
        {
            return $this->belongsTo(Division::class, 'division', 'id');
        }
        public function distict_name()
        {
            return $this->belongsTo(District::class, 'district', 'id');
        }
        public function upazila_name()
        {
            return $this->belongsTo(Upazila::class, 'upazila', 'id');
        }
        public function union_name()
        {
            return $this->belongsTo(Union::class, 'union', 'id');
        }
}
