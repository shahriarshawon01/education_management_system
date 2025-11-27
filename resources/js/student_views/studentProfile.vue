<template>
    <div class="container">
        <form @submit.prevent="submitForm(formObject, false, false, true, 'updateProfile')">
            <div class="row gutters">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="row gutters">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <h5 class="mb-2 text-white px-2 py-1 rounded"
                                        style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
                                        Login Information & Change Password
                                    </h5>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>User Name</label>
                                        <input type="text" class="form-control" v-model="formObject.username" readonly>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Guardian Phone</label>
                                        <input type="text" class="form-control" v-model="formObject.father_phone"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row gutters">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Student Email/ID</label>
                                        <input type="text" class="form-control" v-model="formObject.email" readonly>
                                    </div>
                                </div>

                                <!-- Column 2 -->
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Current Password</label>
                                        <input type="password" class="form-control"
                                            v-model="formObject.current_password" placeholder="Current Password">
                                    </div>
                                </div>

                                <!-- Column 3 -->
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>New Password</label>
                                        <input type="password" class="form-control" v-model="formObject.password"
                                            placeholder="New Password">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" name="submit" class="btn btn-primary">
                                        Update
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import DataTable from "../components/dataTable";
import PageTop from "../components/pageTop";

export default {
    name: "studentProfile",
    components: { PageTop, DataTable },
    methods: {
        getUserProfile() {
            const _this = this;
            var editUrl = `${_this.urlGenerate('profile')}`;
            _this.getData(editUrl, 'get', {}, {}, function (retData) {
                _this.$store.commit('formObject', retData);
            });
        },
    },
    mounted() {
        this.getUserProfile();
    },
};
</script>

<style scoped>
input[readonly] {
    background-color: #f1f1f1;
    color: #6c757d;
    cursor: not-allowed;
}

body {
    margin: 0;
    padding-top: 40px;
    color: #2e323c;
    background: #f5f6fa;
    position: relative;
    height: 100%;
}

.form-control {
    border: 1px solid #cfd1d8;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
    font-size: .825rem;
    background: #ffffff;
    color: #2e323c;
}

.card {
    background: #ffffff;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    border: 0;
    margin-bottom: 1rem;
}
</style>
