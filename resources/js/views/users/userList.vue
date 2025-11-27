<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="User List">
                    <template v-slot:page_top>
                        <page-top topPageTitle="User List" page-modal-title="User Add/Edit"></page-top>
                    </template>
                    <template v-slot:header>
                        <button @click="openModal('formModal')" class="btn btn-outline-secondary">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.role_id" name="role_id">
                                <option value="">Select Role</option>
                                <template v-for="(data, index) in requiredData.role">
                                    <!-- <option :value="data.id" v-if="data.id == 1 || data.id == 3 || data.id == 6">{{
                                        data.display_name }}</option> -->
                                    <option :value="data.id">{{ data.display_name }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.status" name="status">
                                <option value="">Select User Status</option>
                                <option value="">All</option>
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                            </select>
                        </div>

                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.username }}</td>
                            <td>{{ data.email }}</td>
                            <!-- <td style="text-align: center;">{{ data.nid }}</td>
                            <td style="text-align: center;">{{ data.phone }}</td> -->
                            <td>{{ showData(data.roles, 'display_name') }}</td>
                            <td>{{ getLoginType(data.type) }}</td>

                            <td class="table-center">
                                <span>
                                    <template v-if="data && data.image">
                                        <img class="mouse-action" :src="getImage(data.image)"
                                            @click="openFile(getImage(data.image))">
                                    </template>
                                    <template v-else>
                                        N/A
                                    </template>
                                </span>
                            </td>

                            <td style="text-align: center;">{{ data.created_at }}</td>
                            <td style="text-align: center !important;">
                                <a @click="changeStatus(data.id)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <a v-if="can('user_update')" class="link-primary"
                                        @click="editDataInformation(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('user_delete')" class="link-danger"
                                        @click="deleteInformation(data, data.id)"
                                        style="text-align: center !important;"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <form-modal @submit="submitForm(formObject, 'formModal')" modal-id="formModal" modal-size="modal-lg"
            back-drop="static">
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-12">
                        <label class="col-form-label">Login type </label>
                        <select class="form-control" v-model="formObject.type" v-validate="'required'" name="type" disabled>
                            <option value="">Login Type</option>
                            <option value="1">Admin</option>
                            <!-- <option value="2">Teacher</option>
                            <option value="3">Staff</option>
                            <option value="4">Parents</option>
                            <option value="5">Student</option>
                            <option value="6">Website</option> -->
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="col-form-label">User Role </label>
                        <select class="form-control" v-model="formObject.role_id" v-validate="'required'"
                            name="role_id">
                            <option value="">---Role---</option>
                            <template v-for="(data, index) in requiredData.role">
                                <option :value="data.id">{{ data.display_name }}</option>
                            </template>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="col-form-label">User Name</label>
                        <input type="text" class="form-control" v-validate="'required'" v-model="formObject.username"
                            name="username" placeholder="Enter User Name">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="col-md-12">
                        <label class="col-form-label">NID</label>
                        <input type="number" class="form-control" v-model="formObject.nid" name="nid"
                            placeholder="Enter NID">
                    </div>
                    <div class="col-md-12">
                        <label class="col-form-label">Phone Number</label>
                        <input type="number" class="form-control" v-model="formObject.phone" name="phone"
                            placeholder="Enter Phone Number">
                    </div>
                    <div class="col-md-12">
                        <label class="col-form-label">Birth Date</label>
                        <datepicker input_class="form-control" v-model="formObject.date_of_birth" name="date_of_birth"
                            v-validate="'required'" placeholder="Date of Birth">
                        </datepicker>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top: 15px;">
                <div class="col-12">
                    <h4 class="mb-3">User's Photo & Login Information</h4>
                    <hr style="margin-bottom: 2px;">
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email" class="col-form-label">Login Email Address</label>
                        <input type="text" id="email" class="form-control" v-validate="'required'"
                            v-model="formObject.email" placeholder="Enter Valid Email" name="email"
                            aria-describedby="emailHelp" aria-required="true">
                    </div>

                    <div class="form-group">
                        <label for="password" class="col-form-label">Password </label>
                        <input type="password" id="password" class="form-control"
                            v-validate="formType === 1 ? 'required|min:6' : ''" v-model="formObject.password"
                            placeholder="Enter Strong Password" name="password" aria-required="true">

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group text-center">
                        <label for="image" class="col-form-label">Upload Photo</label>
                        <div @click="clickImageInput('image')"
                            class="image_upload mb-2 d-flex align-items-center justify-content-center"
                            :style="{ backgroundImage: 'url(' + getImage(null, 'images/upload.png') + ')' }"
                            style="background-size: contain; cursor: pointer;">
                            <img v-if="formObject.image" :src="getImage(formObject.image)" alt="User Photo"
                                class="img-fluid">
                            <input type="file" id="image" name="thumbnail" class="d-none"
                                v-validate="formType === 1 ? 'required' : ''"
                                @change="uploadFile($event, formObject, 'image')" aria-required="true">
                        </div>
                    </div>
                </div>
            </div>

        </form-modal>
    </div>
</template>

<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import FormModal from "../../components/formModal";
export default {
    name: "userList",
    components: { PageTop, Pagination, DataTable, FormModal },
    data() {
        return {
            tableHeading: ['Sl', 'User Name', 'Email','Role', 'Login Type', 'Photo', 'Create Date', 'Status', 'Action'],
            details: ''
        }
    },
    methods: {
        getLoginType(type) {
            switch (type) {
                case 1:
                    return 'Admin';
                case 2:
                    return 'Teacher';
                case 3:
                    return 'Staff';
                case 4:
                    return 'Parent';
                case 5:
                    return 'Student';
                case 6:
                    return 'Website';
                case 7:
                    return 'Applicant';
                default:
                    return 'Unknown';
            }
        },

        editDataInformation: function (data, id) {
            const _this = this;
            _this.editData(data, id, 'formModal', function (retData) {
                var editUrl = `${_this.urlGenerate()}/${id}/edit`;
                _this.getData(editUrl, 'get', {}, {}, function (retData) {
                    if (retData.status === 1) {
                        const getUser = retData;

                        _this.$set(_this.formObject, 'type', getUser.type);
                        _this.$set(_this.formObject, 'username', getUser.username);
                        _this.$set(_this.formObject, 'date_of_birth', getUser.date_of_birth);
                        _this.$set(_this.formObject, 'phone', getUser.phone);
                        _this.$set(_this.formObject, 'nid', getUser.nid);
                        // _this.$set(_this.password, 'username', getUser.password);

                        if (getUser.roles && getUser.roles.id) {
                            _this.$set(_this.formObject, 'role_id', getUser.roles.id);
                        } else {
                            _this.$set(_this.formObject, 'role_id', '');
                        }
                    } else {
                        _this.$toastr('error', retData.message, "Error");
                    }
                });
            });
        },

        assign() {
            const _this = this;
            _this.$set(_this.formObject, 'role_id', '');
            _this.$set(_this.formObject, 'date_of_birth', '');
            _this.$set(this.formObject, 'type', '1');
            _this.$set(this.formFilter, 'role_id', '');
        },
        changeStatus(id) {
            this.axios.get(`${baseUrl}/api/status_changes/${id}`)
                .then(res => {
                    this.getDataList();
                    this.$toastr('success', 'Status Successfully Changed', "Success");
                })
                .catch(error => {
                    console.log(error);

                })
        }
    },
    mounted() {
        this.assign();
        this.getDataList();
        this.getGeneralData(['role']);
    }
}
</script>

<style scoped>
.table-center {
    text-align: center;
}

.table-center .action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}
</style>
