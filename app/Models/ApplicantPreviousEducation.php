<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicantPreviousEducation extends Model
{
    use HasFactory;

    protected $table = "applicant_previous_education";
    protected $fillable = [
             'user_id',
            'admission_requests_id',
            'board_name',
            'exam_name',
            'reg_no',
            'roll_no',
            'group',
            'passing_year',
            'gpa',
            'status',
        ];

        public function validate($input = [])
        {
            $validate = Validator::make($input, [
                // 'board_name' => 'required',
                // 'exam_name' => 'required',
                // 'reg_no' => 'required',
                // 'roll_no' => 'required',
                // 'group' => 'required',
                // 'passing_year' => 'required',
                // 'gpa' => 'required',
            ]);
            return $validate;
        }   
}
