<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Apply Leave List" >
                    <template v-slot:page_top>
                        <page-top :default-object="{type:'',category_id:'',student_id:'',teacher_id:'',staff_id:''}" topPageTitle="Apply Leave List" :default-add-button="can('apply_leave_add')" ></page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select type="text" class="form-control" v-model="formFilter.status" name="status">
                                <option value="">Select All</option>
                                <option value="1">Pending</option>
                                <option value="2">Approve</option>
                                <option value="3">Declined</option>
                            </select>
                        </div>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>
                                <template v-if="parseInt(data.type) === 1">
                                    <span>Students</span>
                                </template>
                                <template v-if="parseInt(data.type) === 2">
                                    <span>Teachers</span>
                                </template>
                                <template v-if="parseInt(data.type) === 3">
                                    <span>Staffs</span>
                                </template>
                            </td>
                            <!-- <td>{{showData(data.student, 'student_name_en') || showData(data.teacher, 'name') || showData(data.staff, 'name')}}</td> -->
                            <td v-if="data.student">{{showData(data.student, 'student_name_en')}}</td>
                            <td v-if="data.teacher">{{showData(data.teacher, 'name')}}</td>
                            <td v-if="data.staff">{{showData(data.staff, 'name')}}</td>
                            <td>{{showData(data.category, 'title')}}</td>
                            <td>{{ data.from_date }}</td>
                            <td>{{ data.to_date }}</td>
                            <td>{{ data.no_of_days }}</td>
                            <td>{{showData(data, 'approve_days')}}</td>
                            <td>
                                <a v-if="parseInt(data.status) === 1">
                                    <span class="badge badge-soft-warning">Pending</span>
                                </a>
                                <a v-if="parseInt(data.status) === 3">
                                    <span class="badge badge-soft-danger">Declined</span>
                                </a>
                                <a v-if="parseInt(data.status) === 2">
                                    <span class="badge badge-soft-success">Approved</span>
                                </a>
                            </td>
                            <td>
                                <div class="hstack gap-3 fs-15">
                                    <router-link class="text-center" :to="{ name: 'viewStatus', params: { id: data.id } }"><i class="fa fa-eye"></i></router-link>

                                    <a v-if="can('apply_leave_update')" class="link-primary" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('apply_leave_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-lg" @submit="submitForm(formObject, 'formModal')">

            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Assign To</label>
                    </div>
                    <div class="col-md-8">
                        <select v-model="formObject.type" class="form-control" v-validate="'required'">
                            <option value="">select</option>
                            <option value="1">Student</option>
                            <option value="2">Teacher</option>
                            <option value="3">Staff</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Leave Category:</label>
                    </div>
                    <div class="col-md-8">
                        <select class="form-control" v-model="formObject.category_id" name="category_id" v-validate="'required'">
                            <option value="">Select</option>
                            <template v-for="(data,index) in requiredData.leaveCategory">
                                <option :value="data.id">{{data.title}}</option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-2" v-if="formObject.type ==='1'">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Student</label>
                    </div>
                    <div class="col-md-8">
                        <select v-select2 class="form-control" @change="getDueLeaveData" v-model="formObject.student_id" v-validate="'required'" name="student_id">
                            <option value="">Search Student</option>
                            <template v-for="(data, bIndex) in requiredData.students">
                                <option :value="data.id">{{data.student_name_en}} - [{{ data.student_roll }}]</option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-2" v-if="formObject.type ==='2'">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Teacher</label>
                    </div>
                    <div class="col-md-8">
                        <select v-select2 class="form-control" @change="getDueLeaveData" v-model="formObject.teacher_id" v-validate="'required'" name="teacher_id">
                            <option value="">Teachers</option>
                            <template v-for="(data, bIndex) in requiredData.teachers">
                                <option :value="data.id">{{data.name}} - [{{ data.employee_id }}]</option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-2" v-if="formObject.type ==='3'">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Staff</label>
                    </div>
                    <div class="col-md-8">
                        <select v-select2 class="form-control" @change="getDueLeaveData" v-model="formObject.staff_id" v-validate="'required'" name="staff_id">
                            <option value="">Staff</option>
                            <template v-for="(data, bIndex) in requiredData.staff">
                                <option :value="data.id">{{data.name}} - [{{ data.emp_id }}]</option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-2" v-if="details_leave && details_leave.length">
                <label class="col-form-label">Leave History</label>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">Type</th>
                            <th class="text-center">Used</th>
                            <th class="text-center">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(data, index) in details_leave">
                            <td>{{ data.title }}</td>
                            <td>{{ data.approved }}</td>
                            <td>{{ data.balance }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Apply Date:</label>
                    </div>
                    <div class="col-md-8">
                        <datepicker input_class="form-control" v-model="formObject.apply_date" v-validate="'required'" placeholder="Apply Date"></datepicker>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Apply To:</label>
                    </div>
                    <div class="col-md-8">
                        <textarea rows="1" v-model="formObject.apply_to" name="apply_to" placeholder="Some Details" class="form-control" v-validate="'required'"></textarea>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-5 mb-2">
                    <label class="col-form-label">From Date:</label>
                    <datepicker input_class="form-control" @input="updateTotalDays" v-model="formObject.from_date" name="from_date" v-validate="'required'" placeholder="Leave From Date"></datepicker>
                </div>
                <div class="col-md-5 mb-2">
                    <label class="col-form-label">To Date:</label>
                    <datepicker input_class="form-control" @input="updateTotalDays" v-model="formObject.to_date" name="to_date"  v-validate="'required'" placeholder="Leave To Date"></datepicker>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="col-form-label">Total Days:</label>
                    <input type="text" readonly v-model="formObject.no_of_days" name="no_of_days" class="form-control" v-validate="'required'">
                </div>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Reason:</label>
                    </div>
                    <div class="col-md-8">
                        <textarea rows="2" type="text" v-model="formObject.note" name="note" placeholder="Some Details" class="form-control" v-validate="'required'"></textarea>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <label class="form-label">File :</label>
                <div class="col-md-12">
                    <div @click="clickImageInput('image')" class="mb-2 mt-3">
                        <div class="form-group image_upload" :style="{ backgroundImage: 'url(' + getImage(null, 'images/upload.png') + ')' }" style="background-size:360px !important">
                            <img v-if="formObject.file" :src="getImage(formObject.file)">
                            <input name="thumbnail" :v-validate="formType === 1 ? 'required' : 'sometimes'" style="display: none;" id="image" type="file" @change="uploadFile($event, formObject, 'file')">
                        </div>
                    </div>
                </div>
            </div>
        </formModal>
    </div>
</template>
<script>
    import DataTable from "../../components/dataTable";
    import Pagination from "../../plugins/pagination/pagination";
    import PageTop from "../../components/pageTop";
    import formModal from "../../components/formModal";
    export default {
        name: "LeaveList",
        components: { PageTop, Pagination, DataTable, formModal },
        data() {
            return {
                tableHeading: ["Sl","Type of","Name","Leave Category","Start Date","End Date","Total Days","Approve Days","Leave Status","Action"],
                formModalId: "formModal",
                details_leave:{},
            };
        },
        methods:{
            updateTotalDays() {
                const _this = this;
                if (_this.formObject.from_date && _this.formObject.to_date) {
                    const fromDate = new Date(_this.formObject.from_date);
                    const toDate = new Date(_this.formObject.to_date);

                    const timeDifference = toDate.getTime() - fromDate.getTime();
                    const totalDays = Math.ceil(timeDifference / (1000 * 3600 * 24)+1);

                    _this.formObject.no_of_days = totalDays;
                }
            },
            getDueLeaveData() {
                const _this = this;
                const getValue = {
                    staff_id: _this.formObject.staff_id,
                    student_id: _this.formObject.student_id,
                    teacher_id: _this.formObject.teacher_id,
                };

                const URL = baseUrl + "/api/due_leave";
                _this.axios.post(URL, getValue)
                    .then(response => {
                        if (response.data.status === 2000) {
                            _this.details_leave = response.data.result;
                        } else {
                            _this.$toastr('error', response.data.message, "Error");
                        }
                    })
                    .catch(error => {
                        _this.$toastr('error', 'Failed to submit data.', "Error");
                    });
            }

        },
        mounted() {
            const _this = this;
            _this.getDataList();
            _this.getGeneralData(['leaveCategory','students','teachers','staff']);
            _this.$set(this.formObject, 'apply_date', '')
            _this.$set(this.formObject, 'from_date', '')
            _this.$set(this.formObject, 'to_date', '')
        },
    };
</script>

<style scoped>

</style>

