<template>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <h3>Employee Add Form</h3>
                </div>
                <div class="col-md-6 mb-2 text-end">
                    <router-link to="/admin/employee" class="btn btn-primary">
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
                        Basic Information
                    </h5>

                    <div class="col-md-4">
                        <div class="col-md-12 mb-2">
                            <label>Name</label>
                            <input type="text" v-model="formObject.name" v-validate="'required'" name="name"
                                class="form-control" placeholder="Enter Name">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Father's Name</label>
                            <input type="text" v-model="formObject.father_name" v-validate="'required'"
                                name="father_name" class="form-control" placeholder="Enter Father Name">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Mother's Name</label>
                            <input type="text" v-model="formObject.mother_name" v-validate="'required'"
                                name="mother_name" class="form-control" placeholder="Enter Mother Name">
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Date of Birth</label>
                            <datepicker v-model="formObject.date_of_birth" name="date_of_birth"
                                input_class="form-control" v-validate="'required'" placeholder="Enter Date of Birth">
                            </datepicker>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Joining Birth</label>
                            <datepicker v-model="formObject.joining_date" name="joining_date" input_class="form-control"
                                v-validate="'required'" placeholder="Enter Joining Birth">
                            </datepicker>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="col-md-12 mb-2">
                            <label>Mobile Number</label>
                            <input type="text" v-model="formObject.phone" v-validate="'required'" name="phone"
                                class="form-control" placeholder="Enter Mobile Number">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Email</label>
                            <input type="text" v-model="formObject.email" name="email" class="form-control"
                                placeholder="Enter Email Address">
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Gender</label>
                            <select v-model="formObject.gender" v-validate="'required'" name="gender"
                                class="form-control">
                                <option value="">Select</option>
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Employee Type</label>
                            <select v-model="formObject.employee_type" v-validate="'required'" name="employee_type"
                                class="form-control">
                                <option value="">Select Employee type</option>
                                <option value="1">Teacher</option>
                                <option value="2">Staff</option>
                                <option value="3">Support Staff</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Employee NID</label>
                            <input type="number" v-model="formObject.nid" name="nid" class="form-control"
                                placeholder="Enter Employee NID">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="col-md-12 mb-2">
                            <label>Marital Status</label>
                            <select v-model="formObject.marital_status" v-validate="'required'" name="marital_status"
                                class="form-control">
                                <option value="">Select</option>
                                <option value="1">Married</option>
                                <option value="2">Unmarried</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Religion</label>
                            <select v-model="formObject.religion" v-validate="'required'" name="religion"
                                class="form-control">
                                <option value="">Select</option>
                                <option value="1">Islam</option>
                                <option value="2">Hindu</option>
                                <option value="3">Buddhist</option>
                                <option value="4">Christian</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Designation</label>
                            <select v-model="formObject.designation_id" v-validate="'required'" name="designation_id"
                                class="form-control">
                                <option value="">Select Designation</option>
                                <template v-for="(eachData, index) in requiredData.designation">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Department</label>
                            <select v-model="formObject.department_id" v-validate="'required'" name="department_id"
                                class="form-control">
                                <option value="">Select Department</option>
                                <template v-for="(eachData, index) in requiredData.employeeDepartment">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Employee ID</label>
                            <input type="text" v-model="formObject.employee_id" name="employee_id"
                                class="form-control" placeholder="Enter Employee ID">
                        </div>
                    </div>
                </div>

                <div class="row mb-2">
                    <h5 class="mb-3 text-white px-2 py-1 rounded"
                        style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
                        Address Information
                    </h5>
                    <div class="col-md-3">
                        <label> Address</label><br>
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
                                        <select v-model="address.upazila" v-validate="'required'"
                                            @change="getGeneralData({ union: { upazilla_id: address.upazila, objectName: `${address.type}_union` } })"
                                            name="upazila" class="form-control">
                                            <option value="">Upazila</option>
                                            <template v-validate="'required'"
                                                v-for="(eachData, index) in requiredData[`${address.type}_upazilla`]">
                                                <option :value="eachData.id">{{ eachData.name }}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <select v-model="address.union" name="union" class="form-control"
                                            v-validate="'required'">
                                            <option value="">Union</option>
                                            <template
                                                v-for="(eachData, index) in requiredData[`${address.type}_union`]">
                                                <option :value="eachData.id">{{ eachData.name }}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <input type="text" placeholder="Post Office" v-model="address.post_office"
                                            v-validate="'required'" name="post_office" class="form-control">
                                    </div>
                                    <div class="col-2">
                                        <input type="text" placeholder="Village" v-model="address.village"
                                            v-validate="'required'" name="village" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <h5 class="mb-3 text-white px-2 py-1 rounded"
                        style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
                        Qualification Information
                    </h5>
                    <table class="table table-bordered">
                        <thead class="data_table">
                            <tr>
                                <th style="width: 25% !important;">Degree Name</th>
                                <th style="width: 20%;">Board Name</th>
                                <th style="width: 20%;">Passing Year</th>
                                <th style="width: 25%;">Department Name</th>
                                <th style="width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, index) in formObject.qualification" :key="index">
                                <td>
                                    <input class="form-control" v-model="row.degree_name" name="degree_name"
                                        v-validate="'required'" placeholder="Degree Name">
                                </td>
                                <td>
                                    <input class="form-control" v-model="row.board_name" name="board_name"
                                        v-validate="'required'" placeholder="Board Name">
                                </td>
                                <td>
                                    <input type="number"  class="form-control" v-model="row.passing_year" name="passing_year"
                                        v-validate="'required'" placeholder="Passing Year">
                                </td>
                                <td>
                                    <input class="form-control" v-model="row.dept_name" name="dept_name"
                                        v-validate="'required'" placeholder="department Name">
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-outline-success"
                                        @click="addRow(formObject.qualification, { degree_name: '', board_name: '', passing_year: '', dept_name: '' })">
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

                <label>
                    Show Login Information
                    <input type="checkbox" @click="toggleLoginInfo($event)" v-model="formObject.as_login">
                </label>

                <div class="row" v-if="formObject.as_login">
                    <h5 class="mb-3 text-white px-2 py-1 rounded"
                        style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
                        Employee's Photo & Login Information
                    </h5>
                    <div class="col-md-6">
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-md-3">User Name</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" v-validate="'required'"
                                        v-model="formObject.name" placeholder="Employee User Name" name="name" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-md-3">Email</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" v-validate="'required'"
                                        v-model="formObject.email" placeholder="Employee Email Address" name="email"
                                        readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-md-3">Password</label>
                                <div class="col-md-9">
                                    <input type="password" class="form-control" v-model="formObject.password"
                                        v-validate="formType === 1 ? 'required|min:6' : ''" placeholder="Enter Password"
                                        name="password">
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
                                <input name="thumbnail" v-validate="formType === 1 ? 'required' : ''"
                                    style="display: none;" id="image" type="file"
                                    @change="uploadFile($event, formObject, 'image')">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
import FormModal from "../../components/formModal";
import PageTop from "../../components/pageTop";

export default {
    name: "employeeAddForm",
    components: { PageTop, FormModal },
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
            qualification: [
                {
                    degree_name: '',
                    board_name: '',
                    passing_year: '',
                    dept_name: ''
                }],
            showLoginInfo: false,
        };
    },
    methods: {
        submitAttestationForm: function () {
            const _this = this;
            _this.submitForm(_this.formObject, false, function (retData) {
                if (parseInt(retData.status) === 2000) {
                    _this.$router.push({ path: '/admin/employee' });
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

        assign() {
            const _this = this;
            _this.$set(_this.formObject, 'gender', '');
            _this.$set(_this.formObject, 'employee_type', '');
            _this.$set(_this.formObject, 'designation_id', '');
            _this.$set(_this.formObject, 'department_id', '');
            _this.$set(_this.formObject, 'religion', '');
            _this.$set(_this.formObject, 'marital_status', '');
            _this.$set(_this.formObject, 'date_of_birth', '');
            _this.$set(_this.formObject, 'joining_date', '');
            _this.$set(_this.formObject, 'job_category', '');
            _this.$set(_this.formObject, 'address', [
                { division: '', district: '', upazila: '', union: '', post_office: '', village: '', type: 1 },
                { division: '', district: '', upazila: '', union: '', post_office: '', village: '', type: 2 }
            ]);
            _this.$set(_this.formObject, 'qualification', [{ degree_name: '', board_name: '', passing_year: '', dept_name: '' }]);
        },

        getEditData: function (id) {
            const _this = this;
            var url = `${_this.urlGenerate()}/${id}/edit`;
            
            _this.getData(url, 'get', {}, {}, function (retData) {
                const formObject = {
                    ...retData,
                    email: retData.user ? retData.user.email : null,

                    date_of_birth: retData ? retData.date_of_birth : null,
                    phone: retData ? retData.phone : null,
                    nid: retData ? retData.nid : null,
                };
                _this.$store.commit('formObject', formObject);
                _this.$store.commit('updateId', id);
                _this.$store.commit('formType', 2);
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

        toggleLoginInfo(event) {
            this.showLoginInfo = event.target.checked;
            if (!this.showLoginInfo) {
                this.formObject.email = '';
                this.formObject.password = '';
            }
        }
    },
    mounted() {
        const _this = this;
        _this.assign();
        _this.getGeneralData(['designation', 'employeeDepartment', 'division']);

        if (_this.$route.params.id !== undefined) {
            _this.getEditData(_this.$route.params.id);
        }
    },

}
</script>

<style scoped></style>
