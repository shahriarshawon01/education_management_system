<template>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3>Add Short Course</h3>
                </div>
                <div class="col-md-6 text-end">
                    <router-link to="/admin/website_course" class="btn btn-primary">
                        <i class="fa fa-arrow-left"></i> Back
                    </router-link>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form @submit.prevent="submitAttestationForm()">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h4>Basic Information</h4>
                        <hr>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-md-3">Course Category</label>
                            <div class="col-md-9">
                                <select v-model="formObject.category_id" v-validate="'required'" name="category_id"
                                    class="form-control">
                                    <option value="">Select Section</option>
                                    <template v-for="(eachData, index) in requiredData.website_course">
                                        <option :value="eachData.id">{{ eachData.name }}</option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-md-3">Student Name</label>
                            <div class="col-md-9">
                                <input type="text" v-model="formObject.student_name" v-validate="'required'"
                                    name="student_name" class="form-control" placeholder="Enter Your Name">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-md-3">Student Email</label>
                            <div class="col-md-9">
                                <input type="email" class="form-control" v-validate="'required'"
                                    v-model="formObject.email" placeholder="Enter Email Address" name="email">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-md-3">Phone</label>
                            <div class="col-md-9">
                                <input type="text" v-model="formObject.phone" 
                                    name="phone" class="form-control" placeholder="Enter Your Number">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-md-3">Guardian Mobile Number</label>
                            <div class="col-md-9">
                                <input type="text" v-model="formObject.guardian_mobile" 
                                    name="guardian_mobile" class="form-control" placeholder="Enter Your Number">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-md-3">Gender</label>
                            <div class="col-md-9">
                                <select name="gender" class="form-control" v-model="formObject.gender">
                                    <option value="" selected>Select Gender</option>
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                    <option value="3">Others</option>
                            </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-md-3">Religion</label>
                            <div class="col-md-9">
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
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-md-3">Date Of Birth</label>
                            <div class="col-md-9">
                                <datepicker input_class="form-control" v-model="formObject.date_of_birth"
                                    name="date_of_birth" v-validate="'required'"></datepicker>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-md-3">Roll Number</label>
                            <div class="col-md-9">
                                <input type="text" v-model="formObject.roll_no" v-validate="'required'" name="roll_no"
                                    class="form-control" placeholder="Enter Roll Number">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-md-3">Registration Number</label>
                            <div class="col-md-9">
                                <input type="text" v-model="formObject.reg_no" v-validate="'required'" name="reg_no"
                                    class="form-control" placeholder="Enter Registration Number">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-md-3">Start Date</label>
                            <div class="col-md-9">
                                <datepicker input_class="form-control" v-model="formObject.start_date"
                                    name="start_date" v-validate="'required'"></datepicker>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-md-3">End Date</label>
                            <div class="col-md-9">
                                <datepicker input_class="form-control" v-model="formObject.end_date"
                                    name="end_date" v-validate="'required'"></datepicker>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="formType == 1">
                    <div class="col-md-12">
                        <h4>User Access</h4>
                        <hr>
                    </div>
                    <div class="col-md-12 mb-2">
                        <div class="row">
                            <label class="col-md-3">User Name</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" v-validate="'required'"
                                    v-model="formObject.user_name" placeholder="Enter User Name" name="user_name">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <div class="row">
                            <label class="col-md-3">Email address</label>
                            <div class="col-md-9">
                                <input type="email" class="form-control" v-validate="'required'"
                                    v-model="formObject.email" placeholder="Enter Email Address" name="email">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <div class="row">
                            <label class="col-md-3">Password</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" v-validate="'required'"
                                    v-model="formObject.password" placeholder="Enter Password" name="password">
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
import formModal from "../../../components/formModal";
import PageTop from "../../../components/pageTop";
import GeneralModal from "../../../components/generalModal"

export default {
    name: "addStudent",
    components: { PageTop, formModal, GeneralModal, },
    data() {
        return { 
        };
    },
    methods: {
        submitAttestationForm: function () {
            const _this = this;
            _this.submitForm(_this.formObject, false, function (retData) {
                if (parseInt(retData.status) === 2000) {
                    _this.$router.push({ path: '/admin/website_course' });
                }
            });
        },
       
        getEditData: function (id) {
            const _this = this;
            var url = `${_this.urlGenerate()}/${id}/edit`;
            _this.getData(url, 'get', {}, {}, function (retData) {
                _this.$store.commit('formObject', retData);

                _this.$store.commit('updateId', id);
                _this.$store.commit('formType', 2);

            });
        },
    },
    mounted() {
        
        const _this = this;
        _this.$set(_this.formObject, 'category_id', '');
        _this.$set(_this.formObject, 'gender', '');
        _this.$set(_this.formObject, 'religion', '');
        _this.$set(_this.formObject, 'date_of_birth', '');
        _this.$set(_this.formObject, 'start_date', '');
        _this.$set(_this.formObject, 'end_date', '');
        _this.getGeneralData(['school', 'website_course', 'section', 'parent', 'session'], function (retData) {
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

</style>
