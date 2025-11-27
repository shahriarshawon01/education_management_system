<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" :default-object="{}" table-title="Employee List">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Employee List" page-modal-title="Employee Add/Edit"
                            :default-add-button="false">
                            <router-link v-if="can('employee_add')" to="/admin/employee/add" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add Employee
                            </router-link>
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.designation_id" name="designation_id" class="form-control">
                                <option value="">Select Designation</option>
                                <template v-for="(eachData, index) in requiredData.designation">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="formFilter.department_id" name="department_id" class="form-control">
                                <option value="">Select Department</option>
                                <template v-for="(eachData, index) in requiredData.employeeDepartment">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="formFilter.employee_type" name="employee_type" class="form-control">
                                <option value="">Select Employee type</option>
                                <option value="1">Teacher</option>
                                <option value="2">Staff</option>
                                <option value="3">Support Staff</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.status" name="status">
                                <option value="">Select all status</option>
                                <option value="">All</option>
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                                <option value="2">Resigned</option>
                                <option value="3">Terminated</option>
                            </select>
                        </div>
                    </template>
                    <template v-slot:data>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Employee ID</th>
                                    <th>Designation</th>
                                    <th>Department</th>
                                    <th>Employee Type</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Joining Date</th>
                                    <th>Status</th>
                                    <th>Comments</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <draggable :list="dataList.data" tag="tbody" handle=".drag-handle" @end="updateOrder">
                                <tr v-for="(data, index) in dataList.data" :key="data.id">
                                    <td class="fw-medium">
                                        <i class="fa fa-bars drag-handle" style="cursor: move; margin-right: 5px;"></i>
                                        {{ parseInt(dataList.from) + index }}
                                    </td>
                                    <td class="td-name">
                                        <div class="employee-info">
                                            <div class="employee-avatar">
                                                <img class="mouse-action" :src="getImage(data.photo)"
                                                    @click="openFile(getImage(data.photo))">
                                            </div>
                                            <span class="employee-name-text">{{ data.name }}</span>
                                        </div>
                                    </td>

                                    <td style="text-align: center;">{{ data.employee_id }}</td>
                                    <td>{{ showData(data.designation, 'name') }}</td>
                                    <td>{{ showData(data.department, 'name') }}</td>
                                    <td style="text-align: center;">{{ showEmployeeType(data.employee_type) }}</td>
                                    <td style="text-align: center;">{{ data.phone || '' }}</td>
                                    <td>{{ showData(data.user, 'email') }}</td>
                                    <td style="text-align: center;">{{ data.joining_date }}</td>
                                    <td>
                                        <a v-if="parseInt(data.status) === 1" @click="statusModal(data)">
                                            <span class="btn btn-outline-success"
                                                style="padding:4px 7px;border-radius:40%;font-size:12px;">Active</span>
                                        </a>
                                        <a v-else-if="parseInt(data.status) === 0" @click="statusModal(data)">
                                            <span class="badge badge-soft-danger"
                                                style="padding:4px 7px;border-radius:40%;font-size:12px;">Inactive</span>
                                        </a>
                                        <a v-else-if="parseInt(data.status) === 2" @click="statusModal(data)">
                                            <span class="badge badge-soft-danger"
                                                style="padding:4px 7px;border-radius:40%;font-size:12px;">Resign</span>
                                        </a>
                                        <a v-else-if="parseInt(data.status) === 3" @click="statusModal(data)">
                                            <span class="badge badge-soft-warning"
                                                style="padding:8px 12px;border-radius:50%;font-size:12px;">Terminated</span>
                                        </a>
                                    </td>
                                    <td>{{ data.job_comments || 'N/A' }}</td>
                                    <td class="table-center">
                                        <div class="hstack gap-3 fs-15 action-buttons">
                                            <router-link class="link-primary" :to="`/admin/employee_view/${data.id}`"><i
                                                    class="fa fa-eye"></i></router-link>
                                            <router-link v-if="can('employee_update')" class="link-primary"
                                                :to="`/admin/employee/add/${data.id}`"><i
                                                    class="fa fa-edit"></i></router-link>
                                            <a v-if="can('employee_delete')" class="link-danger"
                                                @click="deleteInformation(index, data.id)"><i
                                                    class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            </draggable>
                        </table>

                    </template>

                </data-table>
            </div>
        </div>

        <formModal modalSize="modal-xs" modal-id="createModal" @submit="submitStatus(formObject)">
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label"><strong>Job Status</strong></label>
                        <div class="d-flex flex-column">
                            <label class="form-check-label pointer-cursor mb-2">
                                <input type="radio" value="1" v-model="formObject.status" name="status"
                                    v-validate="'required'" data-vv-name="status" class="form-check-input"> Active
                            </label>
                            <label class="form-check-label pointer-cursor mb-2">
                                <input type="radio" value="0" v-model="formObject.status" name="status"
                                    v-validate="'required'" data-vv-name="status" class="form-check-input"> InActive
                            </label>
                            <label class="form-check-label pointer-cursor mb-2">
                                <input type="radio" value="2" v-model="formObject.status" name="status"
                                    v-validate="'required'" data-vv-name="status" class="form-check-input"> Resigned
                            </label>
                            <label class="form-check-label pointer-cursor">
                                <input type="radio" value="3" v-model="formObject.status" name="status"
                                    v-validate="'required'" data-vv-name="status" class="form-check-input"> Terminated
                            </label>
                        </div>
                    </div>

                    <div class="col-md-8" v-if="formObject.status == 0">
                        <label class="col-form-label"><strong>Description</strong></label>
                        <textarea name="job_comments" class="form-control" v-model="formObject.job_comments"
                            v-validate="'required'" data-vv-name="job_comments" rows="3"></textarea>
                    </div>

                    <div class="col-md-8" v-if="formObject.status == 2">
                        <label class="col-form-label"><strong>Resign Date</strong></label>
                        <datepicker v-model="formObject.resign_date" name="resign_date" input_class="form-control"
                            placeholder="Enter Resign Date">
                        </datepicker>

                        <div class="mt-3">
                            <label class="col-form-label"><strong>Resign Description</strong></label>
                            <textarea name="job_comments" class="form-control" data-vv-name="job_comments"
                                v-model="formObject.job_comments" v-validate="'required'" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="col-md-8" v-if="formObject.status == 3">
                        <label class="col-form-label"><strong>Terminated Date</strong></label>
                        <datepicker v-model="formObject.terminate_date" name="terminate_date" input_class="form-control"
                            placeholder="Enter Termination Date">
                        </datepicker>

                        <div class="mt-3">
                            <label class="col-form-label"><strong>Terminated Description</strong></label>
                            <textarea name="job_comments" class="form-control" v-model="formObject.job_comments"
                                rows="3"></textarea>
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
import draggable from 'vuedraggable';
import axios from "axios";
export default {
    name: "employeeList",
    components: { PageTop, Pagination, DataTable, formModal, draggable },
    data() {
        return {
            tableHeading: [],
            isInitialLoad: true
        }
    },
    methods: {

        async updateOrder() {
            try {
                const payload = {
                    orderedIds: this.dataList.data.map((item, index) => ({
                        id: item.id,
                        position: index + 1
                    }))
                }
                const response = await axios.post('/api/employees/reorder', payload)

                if (response.data.status === 2000) {
                    this.$toastr('success', 'Employee Position updated!', 'Success');
                } else {
                    this.$toastr('error', 'Something went wrong!', 'error');
                }

            } catch (error) {
                console.error(error)
                this.$toast.error(
                    error?.response?.data?.message ||
                    error?.message ||
                    'Server error occurred'
                )
            }
        },

        showEmployeeType(type) {
            switch (type) {
                case 1: return 'Teacher';
                case 2: return 'Staff';
                case 3: return 'Support Staff';
                default: return '-';
            }
        },

        statusModal: function (data) {
            const _this = this;
            console.log('emp id ', data.id);

            _this.$set(_this.formObject, 'id', data.id);
            _this.$set(_this.formObject, 'status', data.status);
            _this.$set(_this.formObject, 'resign_date', data.resign_date);
            _this.$set(_this.formObject, 'terminate_date', data.terminate_date);
            _this.$set(_this.formObject, 'job_comments', data.job_comments);
            _this.$store.state.activeFormType = 2;
            _this.edit = false;
            _this.add = true;
            _this.openModal('createModal', 'Employee job status');

        },

        submitStatus() {
            const _this = this;
            if (this.formObject.status === 2) {
                this.formObject.terminate_date = '';
            } else if (this.formObject.status === 3) {
                this.formObject.resign_date = '';
            } else if (this.formObject.status === 1 || this.formObject.status === 0) {
                this.formObject.resign_date = '';
                this.formObject.terminate_date = '';
            }
            _this.submitForm(_this.formObject, false, function (retData) {
                _this.getDataList();
                _this.closeModal('createModal');
            }, true, `api/employee_job_status/${_this.formObject.id}`);
        },

        assign() {
            const _this = this;
            _this.$set(_this.formObject, 'resign_date', '');
            _this.$set(_this.formObject, 'terminate_date', '');
            _this.$set(_this.formFilter, 'designation_id', '');
            _this.$set(_this.formFilter, 'department_id', '');
            _this.$set(_this.formFilter, 'employee_type', '');
        },
    },

    watch: {
        'formObject.status': function (newStatus) {
            if (!this.isInitialLoad) {
                this.formObject.job_comments = '';
                if (newStatus != 2) {
                    this.formObject.resign_date = '';
                }
                if (newStatus != 3) {
                    this.formObject.terminate_date = '';
                }
            } else {
                this.isInitialLoad = false;
            }
        }
    },

    mounted() {
        const _this = this;
        _this.getDataList();
        _this.assign();
        _this.getGeneralData(['designation', 'employeeDepartment']);
    }
}
</script>

<style scoped>
.table thead th {
    background-color: #cccbcb;
    text-align: center;
}

.table tfoot th {
    background-color: #cccbcb;
    text-align: center;
}

.table-center {
    text-align: center;
}

.table-center .action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.employee-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.employee-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: linear-gradient(135deg, #e0e7ff 0%, #f5f3ff 100%);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    border: 2px solid #fff;
}

.employee-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.employee-name-text {
    font-weight: 600;
    color: #2c3e50;
}
</style>
