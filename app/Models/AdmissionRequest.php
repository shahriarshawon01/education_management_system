<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdmissionRequest extends Model
{
    use HasFactory;

    protected $table = "admission_requests";
    protected $fillable = [
            'user_id',
            'applicant_name_en',
            'applicant_name_bn',
            'gender',
            'date_of_birth',
            'blood_group',
            'religion',
            'nationality',
            'applicant_email',
            'applicant_phone',
            'weight',
            'height',
            'class_id',
            'section_id',
            'department_id',
            'session_id',
            'father_name_en',
            'father_name_bn',
            'father_phone',
            'father_profession',
            'yearly_income',
            'mother_name_en',
            'mother_name_bn',
            'mother_phone',
            'mother_profession',
            'quota',
            'profile_photo',
            'birth_nid_certificate',
            'final_enroll_status',
            'merit_waiting_status',
            'admission_payment_status',
            'status',
        ];

        public function rules()
        {
            return [
                'applicant_name_en' => 'required',
                'applicant_name_bn' => 'required',
                'gender' => 'required',
                'date_of_birth' => 'required',
                'applicant_phone' => 'required',
                'blood_group' => 'required',
                'religion' => 'required',
                'nationality' => 'required',
                'weight' => 'required',
                'height' => 'required',
                'class_id' => 'required',
                'section_id' => 'required',
                'department_id' => 'required',
                'session_id' => 'required',
                'father_name_en' => 'required',
                'father_name_bn' => 'required',
                'father_phone' => 'required',
                'father_profession' => 'required',
                'yearly_income' => 'required',
                'mother_name_en' => 'required',
                'mother_name_bn' => 'required',
                'mother_phone' => 'required',
                'mother_profession' => 'required',
                'profile_photo' => 'required',
                'birth_nid_certificate' => 'required',
            ];
           
        }   

        protected static function boot()
        {
            parent::boot();
    
            static::creating(function ($model) {
                $year = date('y');
                $month = date('m');
                $randomNumber = mt_rand(10000, 99999);
                $model->applicant_id = $year . $month . $randomNumber;
            });
        }

        public function user(){
            return $this->belongsTo(User::class,'user_id','id')->select('username')->first();
        }

        public function class_info(){
            return $this->belongsTo(StudentClass::class,'class_id','id');
        }
        public function session(){
            return $this->belongsTo(SessionYear::class,'session_id','id');
        }

        public function section(){
            return $this->belongsTo(Section::class,'section_id','id');
        }
        public function department(){
            return $this->belongsTo(Department::class,'department_id','id');
        }

        public function previous_result(){
            return $this->hasMany(ApplicantPreviousEducation::class, 'admission_requests_id','id');
        }
        public function guardian(){
            return $this->belongsTo(ApplicantGuardian::class,'id','admission_requests_id');
        }
        public function present_address(){
            return $this->belongsTo(ApplicantPresentAddress::class,'id','admission_requests_id');
        }
        public function permanent_address(){
            return $this->belongsTo(ApplicantPermanentAddress::class,'id','admission_requests_id');
        }
        public function payments()
        {
            return $this->hasMany(AdmissionPaymentStatus::class, 'applicant_id', 'applicant_id');
        }
        public function  exam(){
            return $this->belongsTo(AdmissionExam::class,'class_id','class_id');
        }




}
