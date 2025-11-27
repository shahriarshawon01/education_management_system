<template>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3>Student Add Form</h3>
                </div>
                <div class="col-md-6 text-end">
                    <router-link to="/admin/student" class="btn btn-primary">
                        <i class="fa fa-arrow-left"></i> Back
                    </router-link>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form @submit.prevent="submitAttestationForm()">
                <div class="row">
                    <h5 class="mb-3 text-white px-2 py-1 rounded"
                        style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
                        Academic Information
                    </h5>
                    <div class="col-md-4">
                        <div class="col-md-12 mb-2">
                            <label>Responsible Teacher</label>
                            <select v-select2 v-model="formObject.responsible_teacher_id" v-validate="'required'"
                                name="responsible_teacher_id" class="form-control">
                                <option value="">Select Teacher</option>
                                <template v-for="(eachData, index) in requiredData.teachers">
                                    <option :value="eachData.id">{{ eachData.name }} - [{{ eachData.employee_id }}]
                                    </option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Admission Date</label>
                            <datepicker class="form-control" v-model="formObject.admission_date" name="admission_date"
                                v-validate="'required'" placeholder="Admission Date">
                            </datepicker>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Session</label>
                            <select :disabled="isEditing" class="form-control" v-model="formObject.session_year_id"
                                name="session_year_id" v-validate="'required'" @change="generateStudentRoll">
                                <option value="">Select Session</option>
                                <template v-for="(data, index) in requiredData.session">
                                    <option :value="data.id">{{ data.title }}</option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="col-md-12 mb-2">
                            <label>Class</label>
                            <select :disabled="isEditing" v-model="formObject.class_id" name="class_id"
                                class="form-control" @change="
                                    getGeneralData({
                                        section: { class_id: formObject.class_id },
                                        departments: { class_id: formObject.class_id },
                                    }, () => { generateStudentRoll() })
                                    " v-validate="'required'">
                                <option value="">Select Class</option>
                                <template v-for="(eachData, index) in requiredData.class">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Department</label>
                            <select v-model="formObject.department_id" v-validate="'required'" name="department_id"
                                class="form-control">
                                <option value="">Select Department</option>
                                <template v-for="(eachData, index) in requiredData.departments">
                                    <option :value="eachData.id">{{ eachData.department_name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Section</label>
                            <select v-model="formObject.section_id" v-validate="'required'" name="section_id"
                                class="form-control">
                                <option value="">Select Section</option>
                                <template v-for="(eachData, index) in requiredData.section">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="col-md-12 mb-2">
                            <label>Student ID</label>

                            <input type="text" v-validate="'required'" name="student_roll" class="form-control"
                                v-model="formObject.student_roll" placeholder="Enter Student ID" readonly>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Registration Number</label>
                            <input type="number" name="reg_number" class="form-control" v-model="formObject.reg_number"
                                placeholder="Enter Registration Number">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Merit Roll</label>
                            <input type="number" v-validate="'required'" name="merit_roll" class="form-control"
                                v-model="formObject.merit_roll" placeholder="Enter Merit Roll">
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <h5 class="mb-3 text-white px-2 py-1 rounded"
                        style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
                        Basic Information
                    </h5>

                    <div class="col-md-4">
                        <div class="col-md-12 mb-2">
                            <label>Name In English</label>
                            <input type="text" v-model="formObject.student_name_en" v-validate="'required'"
                                name="student_name_en" class="form-control" placeholder="Enter Student Name in English">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Name In Bangla</label>
                            <input type="text" v-model="formObject.student_name_bn" name="student_name_bn"
                                class="form-control" placeholder="Enter Student Name in Bangla">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Student Phone</label>
                            <input type="text" v-model="formObject.student_phone" name="student_phone"
                                class="form-control" placeholder="Enter Phone Number">
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Previous Institute</label>
                            <input type="text" v-model="formObject.previous_institute" name="previous_institute"
                                class="form-control" placeholder="Enter Previous Institute">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="col-md-12 mb-2">
                            <label>Nationality</label>
                            <input type="text" v-model="formObject.nationality" name="nationality" class="form-control"
                                placeholder="Enter Nationality">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Religion</label>
                            <select name="religion" v-validate="'required'" class="form-control"
                                v-model="formObject.religion">
                                <option value="">Select Religion</option>
                                <option value="1">Muslim</option>
                                <option value="2">Hindus</option>
                                <option value="3">Christian</option>
                                <option value="4">Buddhist</option>
                                <option value="5">Others</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Date Of Birth</label>
                            <datepicker input_class="form-control" v-model="formObject.date_of_birth"
                                name="date_of_birth" v-validate="'required'" placeholder="Date of Birth">
                            </datepicker>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Birth Certificate No</label>
                            <input type="number" name="birth_certificate_no" class="form-control"
                                v-model="formObject.birth_certificate_no" placeholder="Birth Certificate No">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="col-md-12 mb-2">
                            <label>Blood Group</label>
                            <select name="blood_group" class="form-control" v-model="formObject.blood_group">
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
                        <div class="col-md-12 mb-2">
                            <label>Gender</label>
                            <select v-model="formObject.gender" v-validate="'required'" name="gender"
                                class="form-control">
                                <option value="">Select Gender</option>
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Email</label>
                            <input type="text" v-model="formObject.email" name="email" class="form-control"
                                placeholder="Enter Email">
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <h5 class="mb-3 text-white px-2 py-1 rounded"
                        style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
                        Guardian Information
                    </h5>

                    <div class="col-md-4">
                        <div class="col-md-12 mb-2">
                            <label>Father Name In English</label>
                            <input type="text" v-model="formObject.father_name_en" v-validate="'required'"
                                name="father_name_en" class="form-control" placeholder="Enter Father Name in English">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Father Name In Bangla</label>
                            <input type="text" v-model="formObject.father_name_bn" name="father_name_bn"
                                class="form-control" placeholder="Enter Father Name in Bangla">
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Father Phone</label>
                            <input type="text" v-model="formObject.father_phone" name="father_phone"
                                class="form-control" placeholder="Enter Father Phone Number">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Father NID</label>
                            <input type="number" v-model="formObject.father_nid" name="father_nid" class="form-control"
                                placeholder="Enter Father NID Number">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="col-md-12 mb-2">
                            <label>Mother Name In English</label>
                            <input type="text" v-model="formObject.mother_name_en" v-validate="'required'"
                                name="mother_name_en" class="form-control" placeholder="Enter Mother Name in English">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Mother Name In Bangla</label>
                            <input type="text" v-model="formObject.mother_name_bn" name="mother_name_bn"
                                class="form-control" placeholder="Enter Mother Name in Bangla">
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Mother Phone</label>
                            <input type="text" v-model="formObject.mother_phone" name="mother_phone"
                                class="form-control" placeholder="Enter Mother Phone Number">
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Mother NID</label>
                            <input type="number" v-model="formObject.mother_nid" name="mother_nid" class="form-control"
                                placeholder="Enter Mother NID Number">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="col-md-12 mb-2">
                            <label>Father Profession</label>
                            <input type="text" v-model="formObject.father_profession" name="father_profession"
                                class="form-control" placeholder="Enter Father Profession">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Mother Profession</label>
                            <input type="text" v-model="formObject.mother_profession" name="mother_profession"
                                class="form-control" placeholder="Enter Mother Profession">
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Father Yearly Income</label>
                            <input type="text" v-model="formObject.yearly_income" name="yearly_income"
                                class="form-control" placeholder="Enter Yearly Income">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <h5 class="mb-3 text-white px-2 py-1 rounded"
                        style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
                        Educational Information
                    </h5>
                    <table class="table table-bordered mt-2">
                        <thead class="data_table">
                            <tr>
                                <th style="width: 13% !important;">Exam Name</th>
                                <th style="width: 13% !important;">Board Name</th>
                                <th style="width: 13% !important;">Registration No</th>
                                <th style="width: 13% !important;">Roll No</th>
                                <th style="width: 13% !important;">Group</th>
                                <th style="width: 8% !important;">Passing Year</th>
                                <th style="width: 8% !important;">GPA</th>
                                <th style="width: 13% !important;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, index) in formObject.qualification" :key="index">
                                <td>
                                    <input class="form-control" v-model="row.exam_name" name="exam_name"
                                        placeholder="Enter Exam Name">
                                </td>
                                <td>
                                    <input class="form-control" v-model="row.board_name" name="board_name"
                                        placeholder="Enter Board Name">
                                </td>
                                <td>
                                    <input class="form-control" v-model="row.reg_number"
                                        placeholder="Enter Registration No">
                                </td>
                                <td>
                                    <input class="form-control" v-model="row.roll_number" name="roll_number"
                                        placeholder="Enter Roll No">
                                </td>
                                <td>
                                    <input class="form-control" v-model="row.group" name="group"
                                        placeholder="Enter Group Name">
                                </td>
                                <td>
                                    <input class="form-control" v-model="row.passing_year" name="passing_year"
                                        placeholder="Enter Passing Year">
                                </td>
                                <td>
                                    <input class="form-control" v-model="row.gpa" name="gpa" placeholder="Enter GPA">
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-outline-success"
                                        @click="addRow(formObject.qualification, { exam_name: '', board_name: '', reg_number: '', roll_number: '', group: '', passing_year: '', gpa: '' })">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-outline-danger" v-if="formObject.qualification.length > 1"
                                        @click="deleteRow(formObject.qualification, index)">
                                        <i class="fa fa-close" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row mb-2">
                    <h5 class="mb-3 text-white px-2 py-1 rounded"
                        style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
                        Address Information
                    </h5>
                    <div class="col-md-3">
                        <label>Same As Permanent <input type="checkbox" @click="checkSameData($event)"
                                v-model="formObject.as_same"></label>
                    </div>
                    <div class="col-md-9">
                        <div class="row mb-2" v-for="(address, index) in formObject.address">
                            <label class="col-md-2" v-if="parseInt(address.type) === 1">Permanent</label>
                            <label class="col-md-2" v-else>Present</label>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-2">
                                        <select v-model="address.division" v-validate="'required'"
                                            @change="getGeneralData({ district: { division_id: address.division, objectName: `${address.type}_district` } })"
                                            name="district" class="form-control">
                                            <option value="">Division</option>
                                            <template v-for="(eachData, index) in requiredData.division">
                                                <option :value="eachData.id">{{ eachData.name }}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <select v-model="address.district" v-validate="'required'"
                                            @change="getGeneralData({ upazila: { district_id: address.district, objectName: `${address.type}_upazilla` } })"
                                            name="district" class="form-control">
                                            <option value="">District</option>
                                            <template
                                                v-for="(eachData, index) in requiredData[`${address.type}_district`]">
                                                <option :value="eachData.id">{{ eachData.name }}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <select v-model="address.upazila"
                                            @change="getGeneralData({ union: { upazilla_id: address.upazila, objectName: `${address.type}_union` } })"
                                            name="upazila" class="form-control">
                                            <option value="">Upazila</option>
                                            <template
                                                v-for="(eachData, index) in requiredData[`${address.type}_upazilla`]">
                                                <option :value="eachData.id">{{ eachData.name }}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <select v-model="address.union" name="union" class="form-control">
                                            <option value="">Union</option>
                                            <template
                                                v-for="(eachData, index) in requiredData[`${address.type}_union`]">
                                                <option :value="eachData.id">{{ eachData.name }}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <input type="text" placeholder="Post Office" v-model="address.post_office"
                                            name="post_office" class="form-control">
                                    </div>
                                    <div class="col-2">
                                        <input type="text" placeholder="Village" v-model="address.village"
                                            name="village" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="row">
                    <h5 class="mb-3 text-white px-2 py-1 rounded"
                        style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
                        Local Guardian Information
                    </h5>
                    <div class="col-md-12">
                        <div class="row mb-2" v-for="(guardian, gIndex) in formObject.guardians">
                            <label class="col-md-3">{{ guardian.title }}</label>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-4">
                                        <input type="text" v-model="guardian.guardian_name" placeholder="Guardian name"
                                            name="guardian_name" class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <input type="int" v-model="guardian.guardian_phone" name="guardian_phone"
                                            placeholder="Guardian phone/mobile" class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" placeholder="Guardian relation" name="relation"
                                            data-vv-as="relation" v-model="guardian.relation" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <div class="row mb-2">
                    <h5 class="mb-3 text-white px-2 py-1 rounded"
                        style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
                        Reference Information
                    </h5>

                    <div class="row">
                        <div class="col-md-4">
                            <label >Reference Name</label>
                            <input type="text" class="form-control" v-validate="'required'"
                                v-model="formObject.reference_name" placeholder="Reference Name"
                                name="reference_name">
                        </div>
                        <div class="col-md-4">
                            <label >Reference Mobile</label>
                            <input type="text" class="form-control" v-validate="'required'"
                                v-model="formObject.reference_mobile" placeholder="Reference Mobile"
                                name="reference_mobile">
                        </div>
                        <div class="col-md-4">
                            <label >Reference Address</label>
                            <input type="text" class="form-control" v-validate="'required'"
                                v-model="formObject.reference_address" placeholder="Reference Address"
                                name="reference_address">
                        </div>
                    </div>

                </div>
                <div class="row">
                    <h5 class="mb-3 text-white px-2 py-1 rounded"
                        style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
                        Student's Photo & Login Information
                    </h5>
                    <div class="col-md-6">
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-md-3">User Name</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" v-validate="'required'"
                                        v-model="formObject.student_name_en" placeholder="Student User Name"
                                        name="student_name_en" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-md-3">Student ID as Email</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" v-validate="'required'"
                                        v-model="formObject.student_roll" placeholder="Student ID as a Email Address"
                                        name="student_roll" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-md-3">Password</label>
                                <div class="col-md-9">
                                    <input type="password" class="form-control"
                                        v-validate="formType == 1 ? 'required|min:6' : ''" v-model="formObject.password"
                                        placeholder="Enter Password" name="password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div @click="clickImageInput('image')" class="mb-2 text-center">
                            <div class="form-group image_upload"
                                :style="{ backgroundImage: 'url(' + getImage(null, 'images/upload.png') + ')' }"
                                style="background-size: 300px !important">
                                <img v-if="formObject.image" :src="getImage(formObject.image)">
                                <input name="thumbnail" v-validate="formType == 1 ? 'required' : ''"
                                    style="display: none;" id="image" type="file"
                                    @change="uploadFile($event, formObject, 'image')">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
import FormModal from "../../components/formModal";
import PageTop from "../../components/pageTop";
import GeneralModal from "../../components/generalModal"
import formModal from "../../components/formModal"
import formComponent from "../parent/formComponent";
import axios from 'axios';

export default {
    name: "addStudent",
    components: { PageTop, FormModal, GeneralModal, formModal, formComponent },
    data() {
        return {
            address: [
                {
                    district: '',
                    upazila: '',
                    union: '',
                    post_office: '',
                    village: ''
                }],
            guardians: [{
                guardian_name: '',
                guardian_phone: '',
                relation: '',
            }],
            qualification: [
                {
                    exam_name: '',
                    board_name: '',
                    reg_number: '',
                    roll_number: '',
                    group: '',
                    passing_year: '',
                    gpa: ''
                }],
            isEditing: false,
        };
    },
    methods: {
        addParent: function (data) {
            const _this = this;
            _this.openModal('formModal', "Add Parent");
        },

        submitAttestationForm: function () {
            const _this = this;
            _this.submitForm(_this.formObject, false, function (retData) {
                if (parseInt(retData.status) === 2000) {
                    _this.$router.push({ path: '/admin/student' });
                }
            });
        },

        checkSameData(event) {
            const _this = this;
            if (event.target.checked) {
                let presentAddressIndex = _this.formObject.address.findIndex(item => item.type == 1);
                let permanentAddressIndex = _this.formObject.address.findIndex(item => item.type == 2);

                if (presentAddressIndex !== -1) {
                    let presentAddress = JSON.parse(JSON.stringify(_this.formObject.address[presentAddressIndex]));
                    presentAddress.type = 2;

                    if (permanentAddressIndex !== -1) {
                        _this.$set(_this.formObject.address, permanentAddressIndex, presentAddress);
                    } else {
                        _this.formObject.address.push(presentAddress);
                    }

                    let keys = ['district', 'upazilla', 'union'];
                    $.each(keys, function (ind, value) {
                        let keyOne = `1_${value}`;
                        let keyTwo = `2_${value}`;
                        if (_this.requiredData[keyOne] !== undefined) {
                            _this.$set(_this.requiredData, keyTwo, _this.requiredData[keyOne]);
                        }
                    });
                }
            } else {
                let permanentAddressIndex = _this.formObject.address.findIndex(item => item.type == 2);
                if (permanentAddressIndex !== -1) {
                    _this.$set(_this.formObject.address, permanentAddressIndex, { district: '', upazila: '', union: '', post_office: '', village: '', type: 2 });
                }
            }
        },

        generateStudentRoll() {
            const _this = this;
            if (_this.formObject.session_year_id && _this.formObject.class_id) {
                axios
                    .post('/api/generate-student-roll', {
                        session_year_id: _this.formObject.session_year_id,
                        class_id: _this.formObject.class_id,
                    })
                    .then(response => {
                        if (response.data && response.data.student_roll) {
                            _this.$set(_this.formObject, 'student_roll', response.data.student_roll);
                        } else {
                            console.error('Unexpected response format:', response.data);
                        }
                    })
                    .catch(error => {
                        console.error('Error generating student roll:', error);
                    });
            }
        },

        assign() {
            const _this = this;
            _this.$set(_this.formObject, 'division', '');
            _this.$set(_this.formObject, 'blood_group', '');
            _this.$set(_this.formObject, 'gender', '');
            _this.$set(_this.formObject, 'religion', '');
            _this.$set(_this.formObject, 'birth_certificate_no', '');
            _this.$set(_this.formObject, 'father_nid', '');
            _this.$set(_this.formObject, 'mother_nid', '');
            _this.$set(_this.formObject, 'class_id', '');
            _this.$set(_this.formObject, 'responsible_teacher_id', '');
            _this.$set(_this.formObject, 'section_id', '');
            _this.$set(_this.formObject, 'session_year_id', '');
            _this.$set(_this.formObject, 'admission_date', '');
            _this.$set(_this.formObject, 'date_of_birth', '');
            _this.$set(_this.formObject, 'parent_id', '');
            _this.$set(_this.formObject, 'department_id', '');
            _this.$set(_this.formObject, 'address', [
                { division: '', district: '', upazila: '', union: '', post_office: '', village: '', type: 1 },
                { division: '', district: '', upazila: '', union: '', post_office: '', village: '', type: 2 }
            ]);
            _this.$set(_this.formObject, 'guardians', [
                { gurdian_name: '', gurdian_mobile: '', relation: '', type: 1, title: 'Guardian (Absent of Parents)' },
            ]);
            _this.$set(_this.formObject, 'qualification', [{ exam_name: '', board_name: '', reg_number: '', roll_number: '', group: '', passing_year: '', gpa: '' }]);
        },

        getEditData: function (id) {
            const _this = this;
            _this.isEditing = true;
            const url = `${_this.urlGenerate()}/${id}/edit`;

            _this.getData(url, 'get', {}, {}, function (retData) {
                const formObject = {
                    ...retData,
                    date_of_birth: retData ? retData.date_of_birth : null,
                    student_phone: retData ? retData.student_phone : null,
                    image: retData ? retData.photo : null
                };

                _this.$store.commit('formObject', formObject);
                _this.$store.commit('updateId', id);
                _this.$store.commit('formType', 2);

                // Fetch department and section based on class_id 
                _this.getGeneralData({
                    section: { class_id: formObject.class_id },
                    departments: { class_id: formObject.class_id }
                });

                const addressPromises = retData.address.map((value) => {
                    return new Promise((resolve) => {
                        _this.getGeneralData({
                            district: { division_id: value.division, objectName: `${value.type}_district` },
                            upazila: { district_id: value.district, objectName: `${value.type}_upazilla` },
                            union: { upazilla_id: value.upazila, objectName: `${value.type}_union` }
                        }, (ret) => {
                            resolve(ret);
                        });
                    });
                });

                Promise.all(addressPromises).then((addressUpdates) => {
                    addressUpdates.forEach((update) => {
                        Object.assign(_this.$store.state.formObject, update);
                    });
                    console.log('Form Object after address updates:', _this.$store.state.formObject);
                });
            });
        },

    },

    mounted() {
        const _this = this;
        _this.assign();
        _this.getGeneralData(['school', 'district', 'division', 'section', 'departments', 'parent', 'session', 'teachers'], function (retData) {
            _this.getGeneralData({
                class: { school_id: _this.Config.school_id }
            });
        });
        if (_this.$route.params.id !== undefined) {
            _this.getEditData(_this.$route.params.id);
        }
    },
}
</script>

<style scoped>
.select-style {
    margin-left: 15px !important;
}
</style>
