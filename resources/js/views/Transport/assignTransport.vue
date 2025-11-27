<template :key="$route.meta.name">
    <div class="main_component">
        <data-table :table-heading="tableHeading">
            <template v-slot:page_top>
                <page-top :default-object="{ transport_id: '', transport_user_type: '', member_type: '' }"
                    top-page-title="Assign Transport" v-if="can('assign_transport_add')"></page-top>
            </template>
            <template v-slot:filter>
                <div class="col-md-2">
                    <select class="form-control" v-model="formFilter.transport_id" name="transport_id">
                        <option value="">Select Transport</option>
                        <template v-for="(data, index) in requiredData.transports">
                            <option :value="data.id">{{ data.transport_name }}
                            </option>
                        </template>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" v-model="formFilter.member_type" name="member_type">
                        <option value="" selected>Select member</option>
                        <option value="1">Student</option>
                        <option value="2">Employee</option>
                    </select>
                </div>
            </template>
            <template v-slot:data>
                <tr v-for="(data, index) in dataList.data">
                    <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                    <template v-if="data.transport_user_type == 1">
                        <td>
                            <strong>Name : </strong>{{ data.transport_user.student_name_en }}<br />
                            <strong>Student ID : </strong>{{ data.transport_user.student_roll }}<br />
                            <strong>Type : </strong>{{ getTransportUserType(data.transport_user_type) }}
                        </td>
                    </template>
                    <template v-if="data.transport_user_type == 2">
                        <td>
                            <strong>Name : </strong>{{ data.transport_user.name }}<br />
                            <strong>Employee ID : </strong>{{ data.transport_user.employee_id }}<br />
                            <strong>Type : </strong>{{ getTransportUserType(data.transport_user_type) }}<br />
                            <strong>Employee Type : </strong>{{ getEmployeeType(data.transport_user.employee_type) }}
                        </td>
                    </template>

                    <td>{{ data.assign_date }}</td>
                    <td>{{ showData(data, 'transport_name') }} - [{{ showData(data, 'transport_no') }}]</td>
                    <td>{{ data.route_name }}</td>
                    <td><i class="fa fa-check-square link-primary"></i> {{ data.stoppage }}</td>
                    <td style="text-align: center;">{{ data.discount }} <b>&#37;</b></td>
                    <td style="text-align: right;">{{ data.net_amount }}</td>
                    <td style="text-align: right;">{{ data.payable_amount }}</td>
                    <td>
                        <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                    </td>
                    <td class="table-center">
                        <div class="hstack gap-3 fs-15 action-buttons">
                            <a class="link-primary" v-if="can('assign_transport_update')"
                                @click="editDataInformation(data, data.id)"><i class="fa fa-edit"></i></a>
                            <a class="link-danger" v-if="can('assign_transport_delete')"
                                @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
            </template>
        </data-table>

        <formModal modalSize="modal-lg" @submit="submitForm(formObject, 'formModal')">
            <div class="row">
                <h5 class="mb-3 text-white px-2 py-1 rounded"
                    style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
                    Assign Transport
                </h5>
                <div class="col-md-6">
                    <div class="col-md-12">
                        <label class="col-form-label">Member Category</label>
                        <select v-model="formObject.transport_user_type" class="form-control" v-validate="'required'"
                            name="transport_user_type" id="transport_user_type" :disabled="isEditMode">
                            <option value="">Select Member</option>
                            <option :value="1">Student</option>
                            <option :value="2">Employee</option>
                        </select>
                    </div>
                    <template v-if="formObject.transport_user_type == 1">
                        <div class="col-md-12">
                            <label class="col-form-label">Student ID</label>
                            <autocomplete v-model="formObject.student_roll" api-url="/api/get-student"
                                placeholder="Enter Student ID" name="student_roll" validation_name="Student ID"
                                @select="fillStudentData" :disabled="isEditMode" />
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Student Name</label>
                            <input type="text" v-model="formObject.student_name" name="student_name"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Class</label>
                            <input type="text" v-model="formObject.student_class" name="student_class"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Department</label>
                            <input type="text" v-model="formObject.student_department" name="student_department"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>

                        <div class="col-md-12">
                            <label class="col-form-label">Section</label>
                            <input type="text" v-model="formObject.section_name" name="section_name"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>
                    </template>

                    <template v-if="formObject.transport_user_type == 2">
                        <div class="col-md-12">
                            <label class="col-form-label">Employee ID</label>
                            <autocomplete v-model="formObject.employee_id" api-url="/api/get-employee"
                                placeholder="Enter employee ID" name="employee_id" validation_name="employee ID"
                                @select="fillEmployeeData" :disabled="isEditMode" />
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Employee Name</label>
                            <input type="text" v-model="formObject.employee_name" name="employee_name"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Employee Department</label>
                            <input type="text" v-model="formObject.employee_department" name="employee_department"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>

                        <div class="col-md-12">
                            <label class="col-form-label">Employee Phone</label>
                            <input type="text" v-model="formObject.employee_phone" name="employee_phone"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>
                    </template>
                </div>

                <div class="col-md-6">

                    <div class="col-md-12">
                        <label class="col-form-label">Assign Date</label>
                        <datepicker input_class="form-control" v-model="formObject.assign_date" name="assign_date"
                            v-validate="'required'"><input slot="input" class="form-control"
                                placeholder="Select a date">
                        </datepicker>
                    </div>
                    <template v-if="formObject.transport_user_type == 1 || formObject.transport_user_type == 2">
                        <div class="col-md-12">
                            <label class="col-form-label">Transport Name</label>
                            <select v-select2 class="form-control" v-model="formObject.transport_id"
                                v-validate="'required'" name="transport">
                                <option value="">Select Transport</option>
                                <template v-for="(data, index) in requiredData.transports">
                                    <option :value="data.id">{{ data.transport_name }} - [{{ data.route_name }}]
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Net Amount </label>
                            <input type="number" v-model="formObject.net_amount" v-validate="'required'"
                                name="net_amount" class="form-control" placeholder="Net Amount">
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Fare Discount(%)</label>
                            <input type="number" v-model="formObject.discount" name="discount" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Drop Point</label>
                            <input type="text" v-model="formObject.stoppage" v-validate="'required'" name="stoppage"
                                class="form-control" placeholder="Drop Point">
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Comments</label>
                            <textarea type="text" v-model="formObject.comments" name="description" class="form-control"
                                placeholder="Comments"></textarea>
                        </div>
                    </template>

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
    name: "assignTransport",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ['Sl', "User Info", 'Assign date', 'Transport Name & No', 'Route Name', 'Drop Point', 'Discount(%)', 'Net Amount', 'Payable Amount', 'Status', 'Action'],
            assign_date: null,
            isEditMode: false,
        }
    },

    created() {
        this.formObject.assign_date = this.getCurrentDate();
    },

    methods: {

        getTransportUserType(type) {
            switch (type) {
                case 1: return 'Student';
                case 2: return 'Employee';
                default: return 'Unknown';
            }
        },

        getEmployeeType(type) {
            switch (type) {
                case 1: return 'Teacher';
                case 2: return 'Staff';
                case 2: return 'Support Staff';
                default: return 'Unknown';
            }
        },

        editDataInformation: function (data, id) {
            const _this = this;
            _this.isEditMode = true;
            _this.editData(data, id, 'formModal', function () {
                var editUrl = `${_this.urlGenerate()}/${id}/edit`;
                _this.getData(editUrl, 'get', {}, {}, function (retData) {
                    const assignData = retData;
                    if (assignData) {
                        // student info
                        _this.$set(_this.formObject, 'student_roll', assignData.transport_user?.student_roll);
                        _this.$set(_this.formObject, 'student_name', assignData.transport_user?.student_name_en);
                        _this.$set(_this.formObject, 'student_class', assignData.transport_user?.class_name);
                        _this.$set(_this.formObject, 'student_department', assignData.transport_user?.department_name);
                        _this.$set(_this.formObject, 'section_name', assignData.transport_user?.section_name);

                        // teacher info
                        _this.$set(_this.formObject, 'employee_id', assignData.transport_user?.employee_id);
                        _this.$set(_this.formObject, 'employee_name', assignData.transport_user?.name);
                        _this.$set(_this.formObject, 'employee_phone', assignData.transport_user?.phone);
                        _this.$set(_this.formObject, 'employee_department', assignData.transport_user?.employee_department);


                        // transport info
                        _this.$set(_this.formObject, 'transport_id', assignData.transport_id);
                        _this.$set(_this.formObject, 'net_amount', assignData.net_amount);
                        _this.$set(_this.formObject, 'discount', assignData.discount);
                        _this.$set(_this.formObject, 'stoppage', assignData.stoppage);
                        _this.$set(_this.formObject, 'comments', assignData.comments);
                        _this.$set(_this.formObject, 'assign_date', assignData.assign_date);
                    } else {
                        _this.$toastr('error', retData.message, "Error");
                    }
                });
            });
        },

        fillStudentData(student) {
            if (student && student.id) {
                this.$set(this.formObject, 'student_id', student.id);
                this.$set(this.formObject, 'student_roll', student.student_roll);
                this.$set(this.formObject, 'student_name', student.student_name_en);
                this.$set(this.formObject, 'student_class', student.class_name);
                this.$set(this.formObject, 'student_department', student.department_name);
                this.$set(this.formObject, 'section_name', student.section_name);
                this.$set(this.formObject, 'session_name', student.session_name);
                this.$set(this.formObject, 'school_id', student.school_id);
                this.$set(this.formObject, 'school_name', student.school_name);

                this.$toastr('success', 'Student details successfully retrieved.', 'Success');
            } else {
                this.$toastr('error', 'Student data not found!', 'Error');
            }
        },
        fillEmployeeData(employee) {
            if (employee && employee.id) {
                this.$set(this.formObject, 'employee_primary_id', employee.id);
                this.$set(this.formObject, 'employee_id', employee.employee_id);
                this.$set(this.formObject, 'employee_name', employee.name);
                this.$set(this.formObject, 'employee_department', employee.department);
                this.$set(this.formObject, 'employee_designation', employee.designation);
                this.$set(this.formObject, 'employee_email', employee.email);
                this.$set(this.formObject, 'employee_phone', employee.phone);
                this.$set(this.formObject, 'school_id', employee.school_id);
                this.$set(this.formObject, 'school_name', employee.school_name);

                this.$toastr('success', 'Employee details successfully retrieved.', 'Success');
            } else {
                this.$toastr('error', 'employee data not found!', 'Error');
            }
        },

        getCurrentDate() {
            const date = new Date();
            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
    },
    mounted() {
        const _this = this;
        _this.$set(_this.formObject, 'transport_user_type', '');
        _this.$set(_this.formFilter, 'transport_id', '');
        _this.$set(_this.formFilter, 'member_type', '');
        _this.$set(_this.formObject, 'discount', '0');
        _this.getDataList();
        _this.getGeneralData(['transports']);
    }
}
</script>

<style scoped>
.table-center .action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}

input[readonly],
textarea[readonly],
select[disabled] {
    cursor: not-allowed !important;
    background-color: #f9f9f9 !important;
    color: #666 !important;
}

.readonly-field {
    background-color: #f1f3f5;
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
    color: #495057;
    cursor: not-allowed;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
    font-size: 0.95rem;
}

.readonly-field::placeholder {
    color: #868e96;
}

input[readonly].form-control {
    background-color: #f1f3f5;
    cursor: not-allowed;
    color: #495057;
}
</style>
