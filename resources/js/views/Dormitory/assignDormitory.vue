<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Assign Dormitory List">
                    <template v-slot:page_top>
                        <page-top :default-object="{ dormitory_id: '', dormitory_user_type: '' }"
                            topPageTitle="Assign Dormitory List"
                            :default-add-button="can('assign_dormitory_add')"></page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.dormitory_name" name="dormitory_name">
                                <option value="">Select Dormitory</option>
                                <template v-for="(data, index) in requiredData.dormitory">
                                    <option :value="data.id">{{ data.dormitory_name }}
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

                            <template v-if="data.dormitory_user_type == 1">
                                <td>
                                    <strong>Name : </strong>{{ data.dormitory_user.student_name_en }}<br />
                                    <strong>Student ID : </strong>{{ data.dormitory_user.student_roll }}<br />
                                    <strong>Type : </strong>{{ getDormitoryUserType(data.dormitory_user_type)
                                    }}<br />
                                    <strong>Phone : </strong>{{ data.dormitory_user.father_phone || 'N/A'
                                    }}
                                </td>
                            </template>
                            <template v-if="data.dormitory_user_type == 2">
                                <td>
                                    <strong>Name : </strong>{{ data.dormitory_user.name }}<br />
                                    <strong>Employee ID : </strong>{{ data.dormitory_user.employee_id }}<br />
                                    <strong>Type : </strong>{{ getDormitoryUserType(data.dormitory_user_type)
                                    }}<br />
                                    <strong>Phone : </strong>{{ data.dormitory_user.phone || 'N/A'
                                    }}
                                </td>
                            </template><template v-if="data.dormitory_user_type == 3">
                                <td>
                                    <strong>Name : </strong>{{ data.dormitory_user.name }}<br />
                                    <strong>Employee ID : </strong>{{ data.dormitory_user.employee_id }}<br />
                                    <strong>Type : </strong>{{ getDormitoryUserType(data.dormitory_user_type)
                                    }}<br />
                                    <strong>Phone : </strong>{{ data.dormitory_user.phone || 'N/A'
                                    }}
                                </td>
                            </template>

                            <td>
                                <strong>Name : </strong>{{ showData(data.dormitory, 'dormitory_name') }}<br />
                                <strong>Dormitory No: </strong>{{ showData(data.dormitory, 'dormitory_no') }}<br />
                                <strong>Floor No: </strong>{{ data.floor_number }}<br />
                                <strong>Room No: </strong>{{ data.room_number }}<br />
                                <strong>Seat No: </strong>{{ data.seat_number }}<br />
                            </td>
                            <td style="text-align: center;">{{ data.arrive_date }}</td>
                            <td style="text-align: center;">{{ data.discount || 0 }}%</td>
                            <td style="text-align: right;">{{ data.net_amount || 0 }}</td>
                            <td style="text-align: right;">{{ data.payable_amount || 0 }}</td>

                            <td>{{ showData(data.creator, 'username') }}</td>
                            <td>
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <a v-if="can('assign_dormitory_update')" class="link-primary"
                                        @click="editDataInformation(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('assign_dormitory_delete')" class="link-danger"
                                        @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>

        <formModal modalSize="modal-lg" @submit="submitForm(formObject, 'formModal')">
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-12">
                        <label class="col-form-label">Member Category</label>
                        <select v-model="formObject.dormitory_user_type" class="form-control" v-validate="'required'"
                            name="dormitory_user_type" id="dormitory_user_type" :disabled="isEditMode">
                            <option value="">Select Member</option>
                            <option :value="1">Student</option>
                            <option :value="2">Employee</option>
                        </select>
                    </div>
                    <template v-if="formObject.dormitory_user_type == 1">
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
                    <template v-if="formObject.dormitory_user_type == 2">

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

                    <div class="col-md-12">
                        <label class="col-form-label">Description</label>
                        <textarea name="description" class="form-control" v-model="formObject.description" rows="3"
                            placeholder="Description"></textarea>
                    </div>
                </div>

                <div class="col-md-6">

                    <div class="col-md-12">
                        <label class="col-form-label">Assign Date</label>
                        <datepicker input_class="form-control" v-model="formObject.arrive_date" name="arrive_date"
                            v-validate="'required'"><input slot="input" class="form-control"
                                placeholder="Select a date">
                        </datepicker>
                    </div>
                    <template v-if="formObject.dormitory_user_type == 1 || formObject.dormitory_user_type == 2">
                        <div class="col-md-12">
                            <label class="col-form-label">Dormitory Name</label>
                            <select class="form-control" v-model="formObject.dormitory_id" name="dormitory_id"
                                v-validate="'required'"
                                @change="getGeneralData({ class: { school_id: formObject.school_id } })">
                                <option value="">Select Dormitory Name</option>
                                <template v-for="(data, index) in requiredData.dormitory">
                                    <option :value="data.id">{{ data.dormitory_name }}
                                    </option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="col-form-label">Floor Number</label>
                            <input type="text" v-model="formObject.floor_number" v-validate="'required'"
                                name="floor_number" class="form-control" placeholder="Enter Floor Number">
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Room Number</label>
                            <input type="text" v-model="formObject.room_number" v-validate="'required'"
                                name="room_number" class="form-control" placeholder="Enter Room Number">
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Seat Number</label>
                            <input type="text" v-model="formObject.seat_number" v-validate="'required'"
                                name="seat_number" class="form-control" placeholder="Enter Seat Number">
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
import GeneralModal from "../../components/generalModal";

export default {
    name: "assignDormitory",
    components: { PageTop, Pagination, DataTable, formModal, GeneralModal },
    data() {
        return {
            tableHeading: ["Sl", "User Info", "Dormitory Info", "Assign Date", "Discount", "Net Amount", "Payable Amount", "Create By", "Status", "Action"],
            formModalId: "formModal",
            details: {},
            arrive_date: null,
            isEditMode: false,
        };
    },
    created() {
        this.formObject.arrive_date = this.getCurrentDate();
    },
    methods: {

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

        editDataInformation: function (data, id) {
            const _this = this;
            _this.isEditMode = true;
            _this.editData(data, id, 'formModal', function () {
                const editUrl = `${_this.urlGenerate()}/${id}/edit`;

                _this.getData(editUrl, 'get', {}, {}, function (retData) {
                    const assignDormitory = retData;
                    if (assignDormitory) {

                        _this.$set(_this.formObject, 'dormitory_name', assignDormitory.dormitory?.dormitory_name || '');
                        _this.$set(_this.formObject, 'room_number', assignDormitory.room_number || '');
                        _this.$set(_this.formObject, 'seat_number', assignDormitory.seat_number || '');
                        _this.$set(_this.formObject, 'arrive_date', assignDormitory.arrive_date || '');
                        _this.$set(_this.formObject, 'description', assignDormitory.description || '');
                        _this.$set(_this.formObject, 'dormitory_user_type', assignDormitory.dormitory_user_type || '');

                        switch (assignDormitory.dormitory_user_type) {
                            case 1: {
                                const student = assignDormitory.dormitory_user || {};

                                _this.$set(_this.formObject, 'student_roll', student.student_roll || '');
                                _this.$set(_this.formObject, 'student_name', student.student_name_en || '');
                                _this.$set(_this.formObject, 'phone', student.student_phone || '');
                                _this.$set(_this.formObject, 'father_name', student.father_name_en || '');
                                _this.$set(_this.formObject, 'student_department', student.department_name || '');
                                _this.$set(_this.formObject, 'student_class', student.class_name || '');
                                _this.$set(_this.formObject, 'section_name', student.section_name || '');
                                _this.$set(_this.formObject, 'session_year', student.session_title || '');
                                break;
                            }

                            case 2: {
                                const employee = assignDormitory.dormitory_user || {};

                                _this.$set(_this.formObject, 'employee_id', employee.employee_id || '');
                                _this.$set(_this.formObject, 'employee_name', employee.name || '');
                                _this.$set(_this.formObject, 'employee_phone', employee.phone || '');
                                _this.$set(_this.formObject, 'employee_department', employee.department_name || '');
                                break;
                            }

                            case 3: {
                                const staff = assignDormitory.dormitory_user || {};

                                _this.$set(_this.formObject, 'employee_id', staff.employee_id || '');
                                _this.$set(_this.formObject, 'employee_name', staff.name || '');
                                _this.$set(_this.formObject, 'employee_phone', staff.phone || '');
                                _this.$set(_this.formObject, 'employee_department', staff.department_name || '');
                                break;
                            }

                            default: {
                                _this.$set(_this.formObject, 'student_roll', '');
                                _this.$set(_this.formObject, 'student_name', '');
                                _this.$set(_this.formObject, 'phone', '');
                                _this.$set(_this.formObject, 'father_name', '');
                                _this.$set(_this.formObject, 'student_department', '');
                                _this.$set(_this.formObject, 'student_class', '');
                                _this.$set(_this.formObject, 'section_name', '');
                                _this.$set(_this.formObject, 'session_year', '');
                                _this.$set(_this.formObject, 'employee_id', '');
                                _this.$set(_this.formObject, 'employee_name', '');
                                _this.$set(_this.formObject, 'employee_department', '');
                                _this.$set(_this.formObject, 'staff_id', '');
                                _this.$set(_this.formObject, 'employee_name', '');
                                _this.$set(_this.formObject, 'employee_department', '');
                                break;
                            }
                        }
                    } else {
                        _this.$toastr('error', retData.message || 'Something went wrong!', "Error");
                    }
                });
            });
        },

        getDormitoryUserType(type) {
            switch (type) {
                case 1: return 'Student';
                case 2: return 'employee';
                case 3: return 'Staff';
                default: return 'Unknown';
            }
        },

        getCurrentDate() {
            const date = new Date();
            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        },
    },
    mounted() {
        this.getDataList();
        this.$set(this.formObject, 'discount', '0');
        this.$set(this.formFilter, 'dormitory_name', '');
        this.$set(this.formFilter, 'member_type', '');
        this.getGeneralData(['dormitory']);
    },
};

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
</style>
