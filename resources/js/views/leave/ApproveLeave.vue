<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Approve Leave" >
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Approve Leave" :default-add-button="false" ></page-top>
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
                                <a v-if="data.file" @click="openFile(getImage(data.file))">
                                    <i class="fa fa-download"> File</i>
                                </a>
                                <p v-else>-</p>
                            </td>
                            <td>
                                <a class="btn btn-outline-warning" v-if="parseInt(data.status) === 1" @click="LeaveStatusModal(data, data.id, 'createModal')">Pending</a>
                                <a v-if="parseInt(data.status) === 3">
                                    <span class="badge badge-soft-danger">Declined</span>
                                </a>
                                <a v-if="parseInt(data.status) === 2">
                                    <span class="badge badge-soft-success">Approved</span>
                                </a>
                            </td>
                            <td>
                                <router-link class="text-center" :to="{ name: 'viewStatus', params: { id: data.id } }"><i class="fa fa-eye"></i></router-link>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-lg"  modal-id="createModal"  @submit="submitLeaveStatus(formObject)">
                <div class="mb-2">
                    <div class="row">
                        <div class="col-md-2">
                            <label>From Date</label>
                        </div>
                        <div class="col-md-6">
                            <label> : {{from_date.from_date}}</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <label>To Date</label>
                        </div>
                        <div class="col-md-6">
                            <label> : {{to_date.to_date}}</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <label>Total Days</label>
                        </div>
                        <div class="col-md-6">
                            <label> : {{no_of_days.no_of_days}}</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <label>Leave Status</label>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="pointcursor">
                                        <input type="radio" value="2" v-model="formObject.status" name="status">Approve
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="pointcursor">
                                        <input type="radio" value="1" v-model="formObject.status" name="status">Pending
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="pointcursor">
                                        <input type="radio" value="3" v-model="formObject.status" name="status">
                                        Declined
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-2" v-if="type.type ==1">
                    <div class="col-md-5 mb-2">
                        <label class="col-form-label">From Date:</label>
                        <datepicker input_class="form-control" @input="updateTotalDays" v-model="formObject.leave_from" name="leave_from" v-validate="'required'" placeholder="Leave From Date"></datepicker>
                    </div>
                    <div class="col-md-5 mb-2">
                        <label class="col-form-label">To Date:</label>
                        <datepicker input_class="form-control" @input="updateTotalDays" v-model="formObject.leave_to" name="leave_to"  v-validate="'required'" placeholder="Leave To Date"></datepicker>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="col-form-label">Total Days:</label>
                        <input type="text" readonly v-model="formObject.approve_days" name="approve_days" class="form-control" v-validate="'required'">
                    </div>
                </div>
                <table class="teble table-bordered" v-else>
                    <thead>
                        <tr>
                            <th style="width: 29%;">Type</th>
                            <th style="width: 18%;">From Date</th>
                            <th style="width: 18%;">To Date</th>
                            <th style="width: 15%;">Total Days</th>
                            <th style="width: 20%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, index) in formObject.leaveType" :key="index">
                            <td class="text-center">
                                <select type="text" class="form-control" v-model="row.leave_type" v-validate="'required'" name="leave_type">
                                    <option value="">Select All</option>
                                    <option value="1">With Pay</option>
                                    <option value="2">Without Pay</option>
                                    <option value="3">Cancel</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <datepicker class="form-control" v-model="row.leave_from" @change="calculateTotalDays(row)"  v-validate="'required'" name="from_date" placeholder="From Date"></datepicker>
                            </td>
                            <td class="text-center">
                                <datepicker class="form-control" v-model="row.leave_to" @change="calculateTotalDays(row)"  v-validate="'required'" placeholder="To Date"></datepicker>
                            </td>
                            <td>
                                <input type="text" disabled name="to_date" v-model="row.approve_days" class="form-control">
                            </td>
                            <td class="text-center">
                                <a class="btn btn-outline-success" @click="addRow(formObject.leaveType,{leave_type:'',leave_from:'',leave_to:'',approve_days:''})">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </a>
                                <a class="btn btn-outline-danger" v-if="formObject.leaveType.length > 1" @click="deleteRow(formObject.leaveType, index)">
                                    <i class="fa fa-close" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>

                    </tbody>
                </table>
                <br>
                <label>Comments</label>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <textarea v-model="formObject.comment" rowspan="2" class="form-control"></textarea>
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
        name: "ApproveLeave",
        components: { PageTop, Pagination, DataTable, formModal },
        data() {
            return {
                tableHeading: ["Sl","Type of","Name","Leave Category","Start Date","End Date","Total Days","Approve Days","File","Leave Status",'View Status'],
                formModalId: "formModal",
                leaveType: [{
                    leave_type:'',
                      leave_to: '',
                      leave_from: '',
                      approve_days: 0,
                  }],
                  from_date:{},
                  to_date:{},
                  no_of_days:{},
                  type:{},
            };
        },
        methods:{
            LeaveStatusModal: function (data) {
                const _this = this;
                _this.$set(_this.formObject, 'id', data.id);
                _this.$set(_this.type, 'type', data.type);
                _this.$set(_this.from_date, 'from_date', data.from_date);
                _this.$set(_this.to_date, 'to_date', data.to_date);
                _this.$set(_this.no_of_days, 'no_of_days', data.no_of_days);
                _this.$set(_this.formObject, 'status', data.status);

                _this.$set(_this.formObject, 'leaveType', [{leave_type:'',leave_to:'',leave_from:'',approve_days:''}]);
                _this.$set(_this.formObject, 'comment', 'Leave Approved');
                _this.$store.state.activeFormType = 2;
                _this.edit = false;
                _this.add = true;
                _this.openModal('createModal', 'Approved Leave');

            },
            calculateTotalDays(row) {
                const _this = this;
                if (row.leave_from && row.leave_to) {
                    const fromDate = new Date(row.leave_from);
                    const toDate = new Date(row.leave_to);
                    const timeDifference = toDate.getTime() - fromDate.getTime();
                    const totalDays = Math.ceil(timeDifference / (1000 * 3600 * 24) + 1);

                    row.approve_days = parseInt(totalDays);
                }
            },
            updateTotalDays() {
                const _this = this;
                if (_this.formObject.leave_from && _this.formObject.leave_to) {
                    const fromDate = new Date(_this.formObject.leave_from);
                    const toDate = new Date(_this.formObject.leave_to);

                    const timeDifference = toDate.getTime() - fromDate.getTime();
                    const totalDays = Math.ceil(timeDifference / (1000 * 3600 * 24)+1);

                    _this.formObject.approve_days = totalDays;
                }
            },
            submitLeaveStatus() {
                const _this = this;
                _this.submitForm(_this.formObject, false, function (retData) {
                    _this.getDataList();
                    _this.closeModal('createModal');
                }, true, `api/approve_leave/${_this.formObject.id}`);
            },
        },
        mounted() {
            const _this = this;
            _this.getDataList();
            // _this.getGeneralData(['leaveCategory','students','teachers','staff']);
        },
    };
</script>

<style scoped>

</style>

