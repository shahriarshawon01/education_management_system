@extends('frontend.layouts.master')

@section('title','Admission Request')

@push('css')
    <style>
        .nav-pills .nav-link.active {
            background-color: transparent !important;
            color: black !important;
        }

        .nav-link {
            color: black !important;
        }

        .card {
            margin-top: 15px !important;
            box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.12);
        }

        .card-body {
            padding: 10px 22px !important;
        }

        .card-header {
            padding: 0 !important;
            border-bottom: none !important;
        }

        .required_list {
            color: red !important;
        }

        legend {
            color: #fff !important;
            background-color: #4A8BDF;
            border-radius: 30px;
            padding: 2px 12px;
            font-size: 14px;
            position: absolute;
            top: -15px;
            left: 0;
            right: 0;
            z-index: 1;
            width: 280px;
            font-weight: 500;
        }
        fieldset {
            position: relative;
            border: 1px solid #4A8BDF;
            margin-top: 20px;
        }

        body {
            background-color: #f0f0f0;
        }

        #admissionForm table th {
            font-size: 14px;
            font-weight: 500 !important;
            vertical-align: middle !important;
        }

        #submit {
            font-weight: 500;
            font-size: 14px !important;
            width: 120px;
            margin: 32px auto;
            background-color: #4A8BDF !important;
            border: 1px solid #4A8BDF !important;
            height: 34px !important;
            color: #F0F0F0 !important;
            cursor: pointer;
            box-shadow: 0 0 16px rgba(0, 0, 0, 0.175);
        }
        #submit:hover{
            background-color: #6b97d5 !important;
        }
        #submit:focus{
            outline: 1px solid #4A8BDF !important;
        }
        input::placeholder{
            color: darkgrey !important;
            font-size: 14.5px !important;
        }
        #admissionForm label{
            font-size: 15px !important;
            font-weight: 500 !important;
        }
    </style>
@endpush

@section('content')
<div class="container" style="padding-bottom: 12px">
    <div class="card" style="background: #FFF !important;">
        <div class="card-header" style="background-color: #35B4BA">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        <div class="card-body">
           
            <div id="admissionForm">
                <fieldset style="position: relative">
                    <legend class="fw-semibold text-center" style="font-size: 15px; text-transform: uppercase; font-weight: 600; position:absolute; left:50%;top:-15px; transform: translateX(-50%)">
                        Application Form
                    </legend>

                    <form class="contact_form wrap-form clearfix" @submit.prevent="submitForm"
                          enctype="multipart/form-data">

                        <div class="card mb-2" style="border: none !important; box-shadow: none !important">

                            <div class="card-body">
                                <p class="fw-semibold py-2"
                                   style="margin-bottom: 0 !important;color: rgb(112 112 112); font-size: 14px">Fill up the information :</p>

                                <div class="row pt-2 mb-5">
                                    <div class="col-12">
                                        <fieldset class="px-3 pt-2 pb-3">
                                            <legend class="mb-5 d-block">Basic Information</legend>
                                            <div class="col-sm-12 mb-2 pt-4">
                                                <div class="row">
                                                    <label class="col-sm-3 align-self-center fw-semibold">Applicant Name (English):
                                                    </label>

                                                    <div class="col-sm-9">
                                                        <input name="applicant_name_en" type="text" v-validate="'required'"
                                                                v-model="formData.applicant_name_en" class="form-control" placeholder="Applicant Name English">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-3 align-self-center"> Applicant Name (Bangla):</label>
                                                    <div class="col-sm-9">
                                                        <input name="applicant_name_bn" type="text" v-validate="'required'"
                                                               data-vv-as="applicant_name_bn" v-model="formData.applicant_name_bn"
                                                               class="form-control" placeholder="Applicant Name Bangla">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-3 align-self-center fw-semibold">Gender: </label>
                                                    <div class="col-sm-9">
                                                        <select name="gender" v-validate="'required'" data-vv-as="gender"
                                                                v-model="formData.gender" class="form-control">
                                                            <option value="">Select Gender</option>
                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>
                                                            <option value="Other">Other</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-3 align-self-center fw-semibold">Date Of Birth:
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <input name="date_of_birth" type="date" v-validate="'required'"
                                                               data-vv-as="date_of_birth" v-model="formData.date_of_birth"
                                                               class="form-control">
                                                       
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-3 align-self-center fw-semibold">Blood Group:
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <select name="blood_group" v-validate="'required'" data-vv-as="blood_group" v-model="formData.blood_group"
                                                        class="form-control">
                                                            <option value="">Select Blood Group</option> 
                                                            <option value="A+">A+</option> 
                                                            <option value="B+">B+</option> 
                                                            <option value="AB+">AB+</option> 
                                                            <option value="O+">O+</option> 
                                                            <option value="A-">A-</option>
                                                            <option value="B-">B-</option> 
                                                            <option value="AB-">AB-</option> 
                                                            <option value="O-">O-</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-3 align-self-center fw-semibold">Religion: </label>
                                                    <div class="col-sm-9">
                                                        <select name="religion" v-validate="'required'"
                                                        data-vv-as="religion" v-model="formData.religion"
                                                        class="form-control">
                                                            <option value="">Select Religion</option> 
                                                            <option value="Islam">Islam</option>
                                                            <option value="Hinduism">Hinduism</option> 
                                                            <option value="Christianity">Christianity</option> 
                                                            <option value="Buddhism">Buddhism</option> 
                                                            <option value="Judaism">Judaism</option> 
                                                            <option value="Sikhism">Sikhism</option> 
                                                            <option value="Others">Others</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-3 align-self-center fw-semibold">Nationality: </label>
                                                    <div class="col-sm-9">
                                                        <input name="nationality" type="text" v-validate="'required'"
                                                            data-vv-as="nationality" v-model="formData.nationality"
                                                            class="form-control" placeholder="Nationality">
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-3 align-self-center fw-semibold">Applicant Email (Optional): </label>
                                                    <div class="col-sm-9">
                                                        <input name="applicant_email" type="email"
                                                            v-model="formData.applicant_email"
                                                            class="form-control" placeholder="Applicant Email">
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-3 align-self-center fw-semibold">Applicant Phone : </label>
                                                    <div class="col-sm-9">
                                                        <input name="applicant_phone" type="text" v-validate="'required'"
                                                            data-vv-as="applicant_phone"
                                                            v-model="formData.applicant_phone"
                                                            class="form-control" placeholder="Applicant Phone">
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-3 align-self-center fw-semibold">Weight: </label>
                                                    <div class="col-sm-9">
                                                        <input name="weight" type="text" v-validate="'required'"
                                                               data-vv-as="weight" v-model="formData.weight"
                                                               class="form-control" placeholder="Weight">
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-3 align-self-center fw-semibold">Height: </label>
                                                    <div class="col-sm-9">
                                                        <input name="height" type="text" v-validate="'required'"
                                                               data-vv-as="height" v-model="formData.height"
                                                               class="form-control" placeholder="Height">
                                                        
                                                    </div>
                                                </div>
                                            </div>

                                        </fieldset>
                                    </div>
                                </div>

                                <div class="row mt-4 mb-5">
                                    <div class="col-12">
                                        <fieldset class="px-3 pt-4 pb-3">
                                            <legend class="mb-5 d-block">Academic Information</legend>
                                           
                                                <div class="col-sm-12 mb-2">
                                                    <div class="row">
                                                        <label class="col-sm-3 align-self-center fw-semibold">Applied for
                                                            class : </label>
                                                        <div class="col-sm-9">
                                                            <select class="form-control" name="class_id" v-validate="'required'"
                                                                     v-model="formData.class_id" data-vv-as="class_id">
                                                                <option value="">Select Class</option>
                                                                <option :value="classInfo_list.id"
                                                                        v-for="(classInfo_list, index) in classinfos"
                                                                        v-text="classInfo_list.name">
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 mb-2">
                                                    <div class="row">
                                                        <label class="col-sm-3 align-self-center fw-semibold">Session :
                                                        </label>
                                                        <div class="col-sm-9">
                                                            <select class="form-control" name="session_id"
                                                                    v-model="formData.session_id" v-validate="'required'">
                                                                <option value="">Select Session</option>
                                                                <option :value="session_list.id" v-if = "session_list.title == getCurrentYear() || session_list.title == getNextYear1() || session_list.title == getNextYear2() || session_list.title == getNextYear3()" 
                                                                        v-for="(session_list, index) in sessions"
                                                                        v-text="session_list.title">
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                          
                                                <div class="col-sm-12 mb-2" v-if="sectionFieldVisible">
                                                    <div class="row">
                                                        <label class="col-sm-3 align-self-center fw-semibold">Section :
                                                        </label>
                                                        <div class="col-sm-9">
                                                            <select class="form-control" name="section_id"
                                                                    v-model="formData.section_id" data-vv-as="section_id">
                                                                <option value="">Select Section</option>

                                                                <option :value="section.id"
                                                                        v-for="(section, index) in sections"
                                                                        v-text="section.name">
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-sm-12 mb-2" v-if="departmentFieldVisible">
                                                    <div class="row">
                                                        <label class="col-sm-3 align-self-center fw-semibold">Department :
                                                        </label>
                                                        <div class="col-sm-9">
                                                            <select class="form-control" name="department_id"
                                                                    v-model="formData.department_id" data-vv-as="department_id">
                                                                <option value="">Select Department</option>

                                                                <option :value="department.id"
                                                                        v-for="(department, index) in departments"
                                                                        v-text="department.department_name">
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                           
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="row mt-4 mb-5">
                                    <div class="col-12">
                                        <fieldset class="px-3 pt-4 pb-3" style="position: relative">
                                            <legend class="mb-5 d-block"
                                                    style="position:absolute; left:50%;top:-15px; transform: translateX(-50%)">
                                                    Parents Information</legend>
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <fieldset class="px-3 pt-4 pb-3">
                                                            <legend class="mb-5 d-block">Father Information</legend>
                                                            <div class="col-sm-12 mb-2">
                                                                <div class="row">
                                                                    <label class="col-sm-5 align-self-center fw-semibold">Father's Name (English):
                                                                    </label>
                                                                    <div class="col-sm-7">
                                                                        <input name="father_name_en" type="text" v-validate="'required'"
                                                                               data-vv-as="father_name_en" v-model="formData.father_name_en"
                                                                               class="form-control" placeholder="Father's Name (English)">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 mb-2">
                                                                <div class="row">
                                                                    <label class="col-sm-5 align-self-center fw-semibold">Father's Name (Bangla):
                                                                    </label>
                                                                    <div class="col-sm-7">
                                                                        <input name="father_name_bn" type="text" v-validate="'required'"
                                                                               data-vv-as="father_name_bn" v-model="formData.father_name_bn"
                                                                               class="form-control" placeholder="Father's Name (Bangla)">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="col-sm-12 mb-2">
                                                                <div class="row">
                                                                    <label class="col-sm-5 align-self-center fw-semibold">Father's Phone :
                                                                    </label>
                                                                    <div class="col-sm-7">
                                                                        <input name="father_phone" type="text"
                                                                               v-validate="'required|min:11'" data-vv-as="father_phone"
                                                                               v-model="formData.father_phone" class="form-control" placeholder="Father's Phone">
                                                                       
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 mb-2">
                                                                <div class="row">
                                                                    <label class="col-sm-5 align-self-center fw-semibold">Father's Profession :
                                                                    </label>
                                                                    <div class="col-sm-7">
                                                                        <input name="father_profession" type="text" v-validate="'required'"
                                                                               data-vv-as="father_profession" v-model="formData.father_profession"
                                                                               class="form-control" placeholder="Father's Profession">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 mb-2">
                                                                <div class="row">
                                                                    <label class="col-sm-5 align-self-center fw-semibold">Father/Mother Yearly Income :
                                                                    </label>
                                                                    <div class="col-sm-7">
                                                                        <input name="yearly_income" type="number" v-validate="'required'"
                                                                               data-vv-as="yearly_income" v-model="formData.yearly_income"
                                                                               class="form-control" placeholder="Father/Mother Yearly Income">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <fieldset class="px-3 pt-4 pb-3">
                                                            <legend class="mb-5 d-block">Mother Information</legend>
                                                            <div class="col-sm-12 mb-2">
                                                                <div class="row">
                                                                    <label class="col-sm-5 align-self-center">Mother's Name (English):
                                                                    </label>
                                                                    <div class="col-sm-7">
                                                                        <input name="mother_name_en" type="text" v-validate="'required'"
                                                                               data-vv-as="mother_name_en" v-model="formData.mother_name_en"
                                                                               class="form-control" placeholder="Mother's Name (English)">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 mb-2">
                                                                <div class="row">
                                                                    <label class="col-sm-5 align-self-center fw-semibold">Mother's Name (Bangla):
                                                                    </label>
                                                                    <div class="col-sm-7">
                                                                        <input name="mother_name_bn" type="text" v-validate="'required'"
                                                                               data-vv-as="mother_name_bn" v-model="formData.mother_name_bn"
                                                                               class="form-control" placeholder="Mother's Name (Bangla)">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                           
                                                            <div class="col-sm-12 mb-2">
                                                                <div class="row">
                                                                    <label class="col-sm-5 align-self-center fw-semibold">Mother's Phone :
                                                                    </label>
                                                                    <div class="col-sm-7">
                                                                        <input name="mother_phone" type="text"
                                                                               v-validate="'required|min:11'" data-vv-as="mother_phone"
                                                                               v-model="formData.mother_phone" class="form-control" placeholder="Mother's Phone">
                                                                       
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 mb-2">
                                                                <div class="row">
                                                                    <label class="col-sm-5 align-self-center fw-semibold">Mother's Profession :
                                                                    </label>
                                                                    <div class="col-sm-7">
                                                                        <input name="mother_profession" type="text" v-validate="'required'"
                                                                               data-vv-as="mother_profession" v-model="formData.mother_profession"
                                                                               class="form-control" placeholder="Mother's Profession">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="row mt-4 mb-5">
                                    <div class="col-12">
                                        <fieldset class="px-3 pt-4 pb-3">
                                            <legend class="mb-5 d-block">Guardian Information (Absent of Parents)</legend>
                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-3 align-self-center fw-semibold">Name : </label>
                                                    <div class="col-sm-9">
                                                        <input name="guardian_name" type="text"
                                                            v-validate="'required'" data-vv-as="guardian_name"
                                                            v-model="formData.guardian_name" class="form-control" placeholder="Guardian Name">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-3 align-self-center fw-semibold">Relation :
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <input name="guardian_relation" type="text"
                                                            v-validate="'required'" data-vv-as="guardian_relation"
                                                            v-model="formData.guardian_relation" class="form-control" placeholder="Guardian Relation">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-3 align-self-center fw-semibold">Phone : </label>
                                                    <div class="col-sm-9">
                                                        <input name="guardian_phone" type="text"
                                                            v-validate="'required|min:11'" data-vv-as="guardian_phone"
                                                            v-model="formData.guardian_phone" class="form-control" placeholder="Guardian Phone">
                                                        
                                                    </div>
                                                </div>
    
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <label class="col-sm-3 align-self-center fw-semibold">Address :
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <input name="guardian_address" type="text"
                                                            v-validate="'required'" data-vv-as="guardian_address"
                                                            v-model="formData.guardian_address" class="form-control" placeholder="Guardian Address">
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                               
                                <div class="row mt-4 mb-5">
                                    <div class="col-sm-6">
                                        <fieldset class="px-3 pt-4 pb-3">
                                            <legend class="mb-5 d-block">Present Address</legend>
                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-4 align-self-center fw-semibold">Division :
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <select name="present_division"
                                                                v-model="formData.present_division"
                                                                @change="get_location('present','divisions',$event.target.value)"
                                                                v-validate="'required'" class="form-control">
                                                            <option value="">Select Division</option>
                                                            <option :value="division_list.id" name="present_divisions"
                                                                    v-for="(division_list, index) in formData.present_divisions"
                                                                    v-text="division_list.name">
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-4 align-self-center fw-semibold">District :
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <select name="present_district"
                                                                v-model="formData.present_district"
                                                                @change="get_location('present','upazila',$event.target.value)"
                                                                v-validate="'required'" class="form-control">
                                                            <option value="">Select District</option>
                                                            <option :value="item.id" name="district"
                                                                    v-for="(item, index) in formData.present_districts"
                                                                    v-text="item.name">
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-4 align-self-center fw-semibold">Upazila :
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <select name="present_upazila"
                                                                v-model="formData.present_upazila" v-validate="'required'"
                                                                @change="get_location('present','union',$event.target.value)"
                                                                class="form-control">
                                                            <option value="">Select Upazila</option>
                                                            <option :value="item.id" name="upozilla"
                                                                    v-for="(item, index) in formData.present_upazilas"
                                                                    v-text="item.name">
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-4 align-self-center fw-semibold">Union :
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <select name="present_union"
                                                                v-model="formData.present_union" v-validate="'required'"
                                                                class="form-control">
                                                            <option value="">Select Union</option>
                                                            <option :value="item.id" name="union"
                                                                    v-for="(item, index) in formData.present_unions"
                                                                    v-text="item.name">
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-4 align-self-center fw-semibold">Post Office
                                                        Code : </label>
                                                    <div class="col-sm-8">
                                                        <input name="present_post_code" type="number"
                                                               v-validate="'required'"
                                                               data-vv-as="present_post_code"
                                                               v-model="formData.present_post_code"
                                                               class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <label class="col-sm-4 align-self-center fw-semibold">Village
                                                        Blocks : </label>
                                                    <div class="col-sm-8">
                                                        <input name="present_village" type="text"
                                                               v-validate="'required'" data-vv-as="present_village"
                                                               v-model="formData.present_village"
                                                               class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="col-sm-6" style="position: relative">
                                        <div style="position: absolute; top: -23px; left: 15px">
                                            <label><input name="same_as_present" id="same_as_present"
                                                          v-model="formData.sameAsPresent_data" type="checkbox"
                                                          @change="same_as_present($event)"> Same as Present Address</label>
                                        </div>
                                        <fieldset class="px-3 pt-4 pb-3" v-if="present_address_status==true">
                                            <legend class="mb-5 d-block">Permanent Address</legend>

                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-4 align-self-center fw-semibold">Divisions :
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <select name="permanent_division"
                                                                v-model="formData.permanent_division"
                                                                @change="get_location('permanent','divisions',$event.target.value)"
                                                                v-validate="'required'" class="form-control">
                                                            <option value="">Select Devision</option>
                                                            <option :value="division_list.id" name="division"
                                                                    v-for="(division_list, index) in formData.permanent_divisions"
                                                                    v-text="division_list.name">
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-4 align-self-center fw-semibold">District :
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <select name="permanent_district"
                                                                v-model="formData.permanent_district"
                                                                @change="get_location('permanent','upazila',$event.target.value)"
                                                                v-validate="'required'" class="form-control">
                                                            <option value="">Select District</option>
                                                            <option :value="item.id" name="district"
                                                                    v-for="(item, index) in formData.permanent_districts"
                                                                    v-text="item.name">
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-4 align-self-center fw-semibold">Upazila :
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <select name="permanent_upazila"
                                                                v-model="formData.permanent_upazila"
                                                                @change="get_location('permanent','union',$event.target.value)"
                                                                v-validate="'required'" class="form-control">
                                                            <option value="">Select Upazila</option>
                                                            <option :value="item.id" name="upozilla"
                                                                    v-for="(item, index) in formData.permanent_upazilas"
                                                                    v-text="item.name">
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-4 align-self-center fw-semibold">Union :
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <select name="permanent_union"
                                                                v-model="formData.permanent_union"
                                                                v-validate="'required'" class="form-control">
                                                            <option value="">Select Union</option>
                                                            <option :value="item.id" name="union"
                                                                    v-for="(item, index) in formData.permanent_unions"
                                                                    v-text="item.name">
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 mb-2">
                                                <div class="row">
                                                    <label class="col-sm-4 align-self-center fw-semibold">Post Office
                                                        Code : </label>
                                                    <div class="col-sm-8">
                                                        <input name="permanent_post_code"
                                                               v-model="formData.permanent_post_code"
                                                               type="number" v-validate="'required'"
                                                               data-vv-as="permanent_post_code"
                                                               class="form-control">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <label class="col-sm-4 align-self-center fw-semibold">Village
                                                        Blocks : </label>
                                                    <div class="col-sm-8">
                                                        <input name="permanent_village" type="text"
                                                               v-validate="'required'"
                                                               data-vv-as="permanent_village"
                                                               v-model="formData.permanent_village"
                                                               class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>  
                                    </div>
                                </div>

                                <div class="row mt-4 mb-5">
                                    <div class="col-12">
                                        <fieldset class="px-3 pt-4 pb-3" style="position: relative">
                                            <legend class="mb-5 d-block"
                                                    style="position:absolute; left:50%;top:-15px; transform: translateX(-50%)">
                                                Others Information</legend>
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <fieldset class="px-3 pt-4 pb-3">
                                                            <legend class="mb-5 d-block">Quota</legend>
                                                            <div class="col-sm-12">
                                                                <div class="row">
                                                                    <label class="col-sm-3 align-self-center fw-semibold">Quota
                                                                        : </label>
                                                                    <div class="col-sm-9">
                                                                        <select name="quota" data-vv-as="quota"
                                                                                v-model="formData.quota" class="form-control">
                                                                            <option value="">Select Quota</option>
                                                                            <option value="Freedom Fighter">Freedom Fighter</option>
                                                                            <option value="Physically Challenged">Physically Challenged</option>
                                                                            <option value="Women quota">Women quota</option>
                                                                            <option value="Tribe">Tribe</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <fieldset class="px-3 pt-4 pb-3">
                                        <legend class="mb-5 d-block">Previous Education Information</legend>
                                        <div class="col-12 col-sm-12 col-lg-12 col-xl-12 col-md-12">

                                            <table class="table table-bordered table-responsive"
                                                   style="max-width: 100%">
                                                <thead>
                                                    <tr>
                                                        <th>BOARD NAME</th>
                                                        <th>EXAM NAME</th>
                                                        <th>REGISTRATION NO</th>
                                                        <th>ROLL NO</th>
                                                        <th>GROUP</th>
                                                        <th>PASSING YEAR</th>
                                                        <th>GPA</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(education, index) in formData.educations">
                                                   
                                                    <td style="width: 15% !important">
                                                        <input name="board_name" type="text"
                                                                v-model="formData.educations[index].board_name"
                                                                class="form-control" placeholder="Board Name">
                                                    </td>
                                                   
                                                    <td style="width: 15% !important">
                                                        <input name="exam_name" type="text"                                                             
                                                               v-model="formData.educations[index].exam_name"
                                                               class="form-control" placeholder="Exam Name">
                                                    </td>

                                                    <td style="width: 15% !important">
                                                        <input name="reg_no" type="text"                                                            
                                                               v-model="formData.educations[index].reg_no"
                                                               class="form-control" placeholder="Registration Number">
                                                    </td>
                                                    <td style="width: 15% !important">
                                                        <input name="roll_no" type="text"
                                                               
                                                               v-model="formData.educations[index].roll_no"
                                                               class="form-control" placeholder="Roll No">
                                                    </td>
                                                    <td style="width: 15% !important">
                                                        <input name="group" type="text"
                                                               
                                                               v-model="formData.educations[index].group"
                                                               class="form-control" placeholder="Group">
                                                    </td>
                                                    <td style="width:12% !important">
                                                        <select name="passing_year" class="form-control"
                                                                v-model="formData.educations[index].passing_year">
                                                            <option value="">Select Passing Year</option>
                                                            <option :value="year_info"
                                                                    v-for="(year_info, index) in years"
                                                                    v-text="year_info">

                                                            </option>
                                                        </select>
                                                    </td>
                                                    <td style="width:11% !important">
                                                        <input name="gpa" min="1" max="5"
                                                            type="number" data-vv-as="gpa"
                                                            v-model="formData.educations[index].gpa"
                                                            step="0.1" max="5" class="form-control" placeholder="GPA">                                                    
                                                    </td>
                                                    <td style="width: 80px !important; display: flex" class="no-print">
                                                        <a @click="addRow()" class="pointer btn btn-sm">
                                                            <i class="fa fa-plus text-primary"></i></a>
                                                        <a @click="deleteRow(index)" class="pointer btn btn-sm">
                                                            <i class="fa fa-close text-danger"></i></a>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="row mt-3 mb-3" >
                                    <div class="col-12">
                                        <fieldset class="px-3 pt-4 pb-3">
                                            <legend class="mb-5 d-block">Required Document</legend>
                                            <div class="row">
                                                <div class="col-md-3 mt-2">
                                                    <div style="display: flex; gap: 10px">
                                                        <label class="fw-bold">Profile Photo  
                                                            <span v-if="profilePhotoUploadedSuccessfully"
                                                                class="text-success"><i class="fa fa-check-square-o"
                                                                    aria-hidden="true" style="font-size: 18px"></i>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <input @change="onchangeProfilePhotoUpload($event)"
                                                         type="file" v-validate="'required'"
                                                        data-vv-as="required" class="form-control">
                                                        <input type="hidden" name="profile_photo" v-validate="'required'"
                                                        v-model="formData.profile_photo">
                                                         <!-- Image preview -->
                                                        <div v-if="fileUrls.length" style="margin-top: 7px">
                                                            <img :src="fileUrls" alt="Profile Photo Preview" width="100px" height="100px">
                                                        </div>
                                                </div>
                                               
                                                <div class="col-md-3 mt-2">
                                                    <div style="display: flex; gap: 10px">
                                                        <label class="fw-bold">NID / BIRTH Certificate
                                                            <span v-if="nidBirthUploadedSuccessfully"
                                                                class="text-success"><i class="fa fa-check-square-o"
                                                                    aria-hidden="true" style="font-size: 18px"></i>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <input @change="onchangeNidBirthUpload($event)"
                                                         type="file" v-validate="'required'"
                                                        data-vv-as="required" class="form-control">
                                                        <input type="hidden" name="birth_nid_certificate" v-validate="'required'"
                                                        v-model="formData.birth_nid_certificate">
                                                </div>
                                               
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div style="width: 100%; display:flex; justify-content:center">
                            <button type="submit" id="submit">Submit </button>
                        </div>
                    </form>
                </fieldset>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript" src="{{ asset('frontend_assets/vue/vue.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('frontend_assets/vue/axios.min.js') }}"></script>
<script src="{{ asset('frontend_assets/vue/vee-validate.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    window.appUrl = '{{ url('/') }}';
</script>
<script>
     Vue.use(VeeValidate);
    document.addEventListener('DOMContentLoaded', function () {
       
        new Vue({
            el: '#admissionForm',
            data:{
                years: [],
                classinfos: [],
                sections: [],
                departments: [],
                sessions: [],
                auth_user: '',
                auth_user_email: '',
                auth_user_phone: '',
                present_address_status: true,
                fileUrls: '',
                profilePhotoUploadedSuccessfully: false,
                nidBirthUploadedSuccessfully : false,
                sectionFieldVisible: true,
                departmentFieldVisible: true,
                formData: {
                    applicant_name_en: '',
                    applicant_name_bn: '',
                    gender: '',
                    date_of_birth: '',
                    blood_group: '',
                    religion: '',
                    nationality: '',
                    applicant_email: '',
                    applicant_phone: '',
                    weight : '',
                    height: '',
                    class_id: '',
                    section_id: '',
                    department_id: '',
                    session_id: '',
                    quota: '',

                    father_name_en: '',
                    father_name_bn: '',
                    father_profession: '',
                    father_phone: '',

                    mother_name_en: '',
                    mother_name_bn: '',
                    mother_profession: '',
                    mother_phone: '',

                    yearly_income: '',

                    guardian_name:'',
                    guardian_relation:'',
                    guardian_phone:'',
                    guardian_address:'',

                     //Present Address
                    present_division: '',
                    present_district: '',
                    present_upazila: '',
                    present_union: '',
                    present_post_code: '',
                    present_village: '',
                    sameAsPresent_data: '',
                   

                    //Permanent Address
                    permanent_division: '',
                    permanent_district: '',
                    permanent_upazila: '',
                    permanent_union: '',
                    permanent_post_code: '',
                    permanent_village: '',

                    present_divisions: [],
                    permanent_divisions: [],
                    present_districts: [],
                    permanent_districts: [],
                    present_upazilas: [],
                    present_unions: [],
                    permanent_upazilas: [],
                    permanent_unions: [],
                    present_post_code: '',
                    permanent_post_code: '',
                    present_village: '',
                    permanent_village: '',

                    profile_photo: '',
                    birth_nid_certificate: '',
                    
                    educations: [{
                        board_name: '',
                        exam_name: '',
                        reg_no: '',
                        roll_no: '',
                        group: '',
                        passing_year: '',
                        gpa: '',
                       
                    }],
                    
                }
            },
            watch: {
                'formData.class_id': function(newClassId) {
                    if (newClassId) {
                        this.fetchSectionsAndDepartments(newClassId);
                    } else {
                        this.sections = [];
                        this.departments = [];
                        this.sectionFieldVisible = false;
                        this.departmentFieldVisible = false;
                    }
                },
                'errors': {
                    handler: function(value) {

                        var is_invalid = $('.is-invalid');
                        $(is_invalid).removeAttr("title", '');
                        $(is_invalid).removeClass('is-invalid');
                        $('.error_message').remove();
                        $('.radio').removeClass('error_message_radio');
                        if (value.items.length > 0) {
                            value.items.forEach(function(val) {
                                var target = $("[name='" + val.field + "']");
                                var input_type = $(target).attr('type');
                                if (input_type !== 'radio') {
                                    if ($('.is-invalid').length == 0) {
                                        $(target).parent().remove(`#${val.field}_message`);
                                    }
                                    var message = val.msg;
                                    if ($(`#${val.field}_message`).length == 0) {

                                        message = message.replace(/_/g, ' ');
                                        $(target).parent().append(
                                            `<span class="error_message text-danger" id="${val.field}_message">${message}</span>`
                                        );
                                    }
                                    $(target).addClass('is-invalid');
                                    $(target).attr("title", message.replace(val.field, ""));
                                }
                            });
                        }
                    },
                    deep: true
                },
            },
            methods: {
                submitForm() {
                    const _this = this;
                    _this.$validator.validate().then(valid => {
                        if (valid) {
                            _this.$validator.errors.clear();
                            axios.post(`${appUrl}/student_admission`, this.formData)
                            .then(response => {
                                this.tosterMessage(response.message,
                                    'Thank you for your application', 'success')

                                setTimeout(function() {
                                    
                                    location.reload();
                                }, 1200)
                            })
                            .catch(error => {
                                console.log(error);
                            })

                        }
                    });
                },

                fetchSectionsAndDepartments(classId) {
                    axios.get(`/class/${classId}/sections-departments`)
                        .then(response => {
                            this.sections = response.data.sections;
                            this.departments = response.data.departments;
                            
                            this.sectionFieldVisible = this.sections.length > 0;
                            this.departmentFieldVisible = this.departments.length > 0;
                        })
                        .catch(error => {
                            console.error("There was an error fetching the sections and departments!", error);
                        });
                },
                tosterMessage: function(message, title = 'ok', icon = '') {
                   
                    toastr[icon](message, title);
                },
               
                admission_data: function() {
                    const _this = this;
                    axios.get('/admission_data')
                        .then((response) => {
                            _this.classinfos = response.data.result.classes;
                            _this.sessions = response.data.result.sessions;
                            _this.formData.present_divisions = response.data.result.divisions;
                            _this.formData.permanent_divisions = response.data.result.divisions;
                            _this.formData.applicant_name_en = response.data.result.auth_user.username;
                            _this.formData.applicant_email = response.data.result.auth_user.email;
                        })
                        .catch((erorr) => {
                            console.log(erorr);
                        })
                    },

                
                    onchangeNidBirthUpload(e){
                        const formDataPhoto = new FormData();
                        const birthNidFiles = event.target.files[0];
                        
                        formDataPhoto.append('birth_nid_certificate', birthNidFiles);

                        this.nidBirthUploadedSuccessfully = true;
                        const config = {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        };
                    
                        axios.post('/admission/upload_birth_nid_certificate', formDataPhoto, config)
                            .then(response => {
                                this.formData.birth_nid_certificate = response.data.result.uploaded_nid_birth;
                            })
                            .catch(error => {
                                console.error(error);
                         });
                    },

                    onchangeProfilePhotoUpload(event) {
                        const formDataPhoto = new FormData();
                        const imageFiles = event.target.files[0];
        
                        formDataPhoto.append('profile_photo', imageFiles);

                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.fileUrls = e.target.result;
                        };
                        reader.readAsDataURL(imageFiles);

                        this.profilePhotoUploadedSuccessfully = true;

                        const config = {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        };

                        axios.post('/admission/upload_profile_photo', formDataPhoto, config)
                            .then(response => {
                                this.formData.profile_photo = response.data.result.uploaded_file_name;
                            })
                            .catch(error => {
                                console.error(error);
                         });
                    
                    },

                addRow() {
                    if (!this.isMaxRowsReached) {
                        
                        if (this.isFormDataValid()) {
                            this.formData.educations.push({
                                board_name: '',
                                exam_name: '',
                                reg_no: '',
                                roll_no: '',
                                group: '',
                                passing_year: '',
                                gpa: '',
                            });
                        } else {
                            alert('Please fill in all required fields before adding a new row.');
                        }
                    } else {
                        alert('You have reached the maximum limit of 4 rows.');
                    }
                },
                isFormDataValid() {
                    
                    for (const education of this.formData.educations) {
                        if (!education.exam_name || !education.gpa || !education.board_name || !education.roll_no) {
                            return false;
                        }
                    }
                    return true;
                },

                deleteRow(index) {

                    const config = {
                        headers: {
                            'content-type': 'multipart/form-data'
                        }
                    }
                    let formData = new FormData();
                    const _this = this;
                    axios.post('/api/remove_image', formData, config)
                        .then(function(response) {
                            console.log('successfully removed');
                        })
                        .catch(function(error) {
                            currentObj.output = error;
                        });

                    this.formData.educations.splice(index, 1);
                },
                getCurrentYear() {
                    return new Date().getFullYear();
                },
                getNextYear1() { 
                    const currentYear = new Date().getFullYear();
                    const nextYear = currentYear + 1;
                    return `${currentYear}_${nextYear.toString().slice(-2)}`;
                },
                getNextYear2() {
                    const currentYear = new Date().getFullYear();
                    const nextYear = currentYear + 1;
                    return `${currentYear}_${nextYear}`;
                },
                getNextYear3() {
                    const currentYear = new Date().getFullYear();
                    const nextYear = currentYear + 1;
                    return `${currentYear}-${nextYear}`;
                },
                yearList: function() {
                    const _this = this;
                    const currentYear = new Date().getFullYear();
                    for (let i = currentYear; i >= currentYear - 50; i--) {
                        _this.years.push(i);
                    }
                },
                onChangePrevEduCertificate:function(event,callback = false){
                    const _this = this;
                    let currentObj = this;
                    var formData = new FormData();
                    var imagefile = event.target.files[0];
                    formData.append("prev_certificate", imagefile);
                    const config = {
                        headers: {'content-type': 'multipart/form-data'}
                    }
                    axios.post('/upload_prev_edu_certificate', formData, config)
                        .then(function (response) {
                            _this.formData.profile_photo = response.data.result;
                            this.tosterMessage('Photo successfully uploaded');
                            currentObj.success = response.data.success;
                        })
                        .catch(function (error) {
                            currentObj.output = error;
                        });
                },

                get_location: function(location_type, type, id) {

                const _this = this;

                axios.get(`/admission_get_location/${type}/${id}`)
                    .then((response) => {
                        // if the location is present
                        if (location_type == 'present') {

                            if (type == "divisions") {
                                _this.formData.present_districts = response.data.result
                            } else if (type == "upazila") {
                                _this.formData.present_upazilas = response.data.result

                            }else if (type == "union") {
                                _this.formData.present_unions = response.data.result

                            }

                        }
                        // if the location is permanent
                        else if (location_type == 'permanent') {
                            if (type == "divisions") {
                                _this.formData.permanent_districts = response.data.result
                            } else if (type == "upazila") {
                                _this.formData.permanent_upazilas = response.data.result

                            }else if (type == "union") {
                                _this.formData.permanent_unions = response.data.result

                            }
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                },
                same_as_present(event) {

                    if (this.formData.sameAsPresent_data == true) {
                        this.present_address_status = false;
                    } else {
                        this.present_address_status = true;
                    }
                },
            },
            created: function() {
                this.yearList();
                this.admission_data();
            }

        });
    });
</script>

   
@endpush