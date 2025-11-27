<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Library membership List">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Library membership List" :default-object="{ type: '' }"
                            :default-add-button="can('membership_add')"
                            page-modal-title="Library membership Add/Edit"></page-top>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data" :key="index">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>


                            <template>
                                <td v-if="data.type == '1' && data.member">
                                    {{ data.member.student_name_en || 'N/A' }}
                                </td>
                                <td v-if="data.type == '2' && data.member">
                                    {{ data.member.name || 'N/A' }}
                                </td>
                                <td v-if="data.type == '3' && data.member">
                                    {{ data.member.name || 'N/A' }}
                                </td>
                            </template>
                            <td>{{ memberTypeCapitalize(data.type) }}</td>

                            <template>
                                <td v-if="data.type == '1' && data.member">
                                    {{ data.member.student_roll }}
                                </td>
                                <td v-if="data.type == '2' && data.member">
                                    {{ data.member.employee_id || 'N/A' }}
                                </td>
                                <td v-if="data.type == '3' && data.member">
                                    {{ data.member.employee_id || 'N/A' }}
                                </td>
                            </template>

                            <template>
                                <td v-if="data.type == '1' && data.member">
                                    {{ data.member.student_phone || 'N/A' }}
                                </td>
                                <td v-if="data.type == '2' && data.member">
                                    {{ data.member.phone || 'N/A' }}
                                </td>
                                <td v-if="data.type == '3' && data.member">
                                    {{ data.member.phone || 'N/A' }}
                                </td>
                            </template>



                            <td>{{ data.membership_date }}</td>
                         
                             <td class="table-center">
                                <a class="action-buttons" @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <a class="link-primary" @click="membershipDetails(data, data.name)"><i
                                            class="fa fa-eye"></i></a>
                                    <a v-if="can('membership_update')" class="link-primary"
                                        @click="editDataInformation(data, data.id)">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a v-if="can('stock_out_delete')" class="link-danger"
                                        @click="deleteInformation(index, data.id)">
                                        <i class="fa fa-trash"></i>
                                    </a>
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
                        <label class="col-form-label">Membership Category</label>
                        <select v-model="formObject.type" class="form-control" v-validate="'required'" name="type"
                            id="type">
                            <option value="">Member Category</option>
                            <option value="1">Student</option>
                            <option value="2">Teacher</option>
                            <option value="3">Staff</option>
                        </select>
                    </div>
                    <template v-if="formObject.type == '1'">

                        <div class="col-md-12">
                            <label class="col-form-label">Student ID</label>
                            <autocomplete v-model="formObject.student_roll" api-url="/api/get-student"
                                placeholder="Enter Student ID" name="student_roll" v-validate="'required'"
                                validation_name="Student ID" @select="fillStudentData" />
                        </div>

                        <div class="col-md-12">
                            <label class="col-form-label">Class</label>
                            <input type="text" v-model="formObject.student_class" name="student_class"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>

                        <div class="col-md-12">
                            <label class="col-form-label">Section</label>
                            <input type="text" v-model="formObject.section_name" name="section_name"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>

                    </template>

                    <template v-if="formObject.type == '2'">

                        <div class="col-md-12">
                            <label class="col-form-label">Employee ID</label>
                            <autocomplete v-model="formObject.employee_id" api-url="/api/get-employee"
                                placeholder="Enter Employee ID" name="employee_id" validation_name="Employee ID"
                                @select="fillEmployeeData" />
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Employee Name</label>
                            <input type="text" v-model="formObject.employee_name" name="employee_name"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>
                    </template>
                    <template v-if="formObject.type == '3'">

                        <div class="col-md-12">
                            <label class="col-form-label">Staff ID</label>
                            <autocomplete v-model="formObject.employee_id" api-url="/api/get-employee"
                                placeholder="Enter Employee ID" name="employee_id" validation_name="Employee ID"
                                @select="fillEmployeeData" />
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Staff Name</label>
                            <input type="text" v-model="formObject.employee_name" name="employee_name"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>
                    </template>
                </div>

                <div class="col-md-6">
                    <div class="col-md-12">
                        <label class="col-form-label">Membership Date</label>
                        <datepicker input_class="form-control" v-model="formObject.membership_date"
                            name="membership_date">
                            <input slot="input" class="form-control" placeholder="Select a date">
                        </datepicker>
                    </div>

                    <template v-if="formObject.type == '1'">
                        <div class="col-md-12">
                            <label class="col-form-label">Student Name</label>
                            <input type="text" v-model="formObject.student_name" name="student_name"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>

                        <div class="col-md-12">
                            <label class="col-form-label">Session</label>
                            <input type="text" v-model="formObject.session_name" name="session_name"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Department</label>
                            <input type="text" v-model="formObject.student_department" name="student_department"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>
                    </template>
                    <template v-if="formObject.type == '2'">
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
                    <template v-if="formObject.type == '3'">
                        <div class="col-md-12">
                            <label class="col-form-label">Staff Department</label>
                            <input type="text" v-model="formObject.employee_department" name="employee_department"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Staff Phone</label>
                            <input type="text" v-model="formObject.employee_phone" name="employee_phone"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>
                    </template>
                </div>
            </div>
        </formModal>
        <general-modal modal-id="detailsModal" modalSize="modal-lg">
            <template v-slot:body>
                <div class="row border_bottom">
                    <label class="col-3">Membership Date</label>
                    <div class="col-md-9">
                        <strong>: </strong> <span>{{ details.membership_date }}</span>
                    </div>
                </div>

                <div class="row border_bottom">
                    <label class="col-3">Member Type</label>
                    <div class="col-md-9">
                        <strong>: </strong> <span>{{ memberTypeCapitalize(details.type) }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Member ID</label>
                    <div class="col-md-9">
                        <strong>: </strong>
                        <span v-if="details.type == '1'">{{ details.member.student_roll }}</span>
                        <span v-if="details.type == '2'">{{ details.member.employee_id }}</span>
                        <span v-if="details.type == '3'">{{ details.member.employee_id }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Member Name</label>
                    <div class="col-md-9">
                        <strong>: </strong><span v-if="details.type == '1'">{{ details.member.student_name_en }}</span>
                        <span v-if="details.type == '2'">{{ details.member.name }}</span>
                        <span v-if="details.type == '3'">{{ details.member.name }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Member Phone</label>
                    <div class="col-md-9">
                        <strong>: </strong>
                        <span v-if="details.type == '1'">{{ details.member.student_phone }}</span>
                        <span v-if="details.type == '2'">{{ details.member.phone }}</span>
                        <span v-if="details.type == '3'">{{ details.member.phone }}</span>
                    </div>
                </div>
            </template>
        </general-modal>

    </div>
</template>

<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";
import GeneralModal from "../../components/generalModal";

export default {
    name: "memberShip",
    components: { PageTop, Pagination, DataTable, formModal, GeneralModal },
    data() {
        return {
            membership_date: null,
            tableHeading: ['Sl', 'Member Name', 'Member Type', 'Member ID', 'Phone', 'Membership Date', 'Status', 'Action'],
            details: {
                membership_date: '',
                type: '',
                member: { student_roll: '', employee_id: '', student_name_en: '', name: '', mobile_no: '' },
            },
            formModalId: 'formModal',
            // studentData: {},
            // teacherData: {},
            // staffData: {},
        }
    },
    created() {
        this.formObject.membership_date = this.getCurrentDate();
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

                this.$toastr('success', 'Student data filled successfully!', 'Success');
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
                this.$set(this.formObject, 'employee_email', employee.email);
                this.$set(this.formObject, 'employee_phone', employee.phone);

                this.$toastr('success', 'Employee data filled successfully!', 'Success');
            } else {
                this.$toastr('error', 'Employee data not found!', 'Error');
            }
        },


        editDataInformation: function (data, id) {
            const _this = this;
            _this.editData(data, id, 'formModal', function (retData) {
                var editUrl = `${_this.urlGenerate()}/${id}/edit`;
                _this.getData(editUrl, 'get', {}, {}, function (retData) {
                    if (retData.status === 1) {
                        const memberData = retData;


                        _this.$set(_this.formObject, 'type', memberData.type);
                        _this.$set(_this.formObject, 'membership_date', memberData.membership_date);

                        switch (memberData.type) {
                            case 1:
                                _this.$set(_this.formObject, 'student_roll', memberData.member.student_roll);
                                _this.$set(_this.formObject, 'student_name', memberData.member.student_name_en);
                                _this.$set(_this.formObject, 'student_class', memberData.member.classes.name);
                                _this.$set(_this.formObject, 'student_department', memberData.member.departments.department_name);
                                _this.$set(_this.formObject, 'section_name', memberData.member.sections.name);
                                _this.$set(_this.formObject, 'session_name', memberData.member.sessions.title);
                                break;
                            case 2:
                                _this.$set(_this.formObject, 'employee_id', memberData.member.employee_id);
                                _this.$set(_this.formObject, 'employee_name', memberData.member.name);
                                _this.$set(_this.formObject, 'employee_department', memberData.member.department.name);
                                _this.$set(_this.formObject, 'employee_email', memberData.member.email);
                                _this.$set(_this.formObject, 'employee_phone', memberData.member.phone);
                                break;
                            case 3:
                                _this.$set(_this.formObject, 'employee_id', memberData.member.employee_id);
                                _this.$set(_this.formObject, 'employee_name', memberData.member.name);
                                _this.$set(_this.formObject, 'employee_department', memberData.member.department.name);
                                _this.$set(_this.formObject, 'employee_phone', memberData.member.phone);
                                break;
                        }
                    } else {
                        _this.$toastr('error', response.message, "Error");
                    }
                });
            });
        },


        membershipDetails: function (data) {
            const _this = this;
            var URL = `${_this.urlGenerate()}/${data.id}`;
            var title = 'Show Library Membership';
            _this.openModal('detailsModal', title, function () {
                _this.getData(URL, "get", {}, {}, function (retData) {
                    _this.details = retData;
                });
            });
        },
        getCurrentDate() {
            const date = new Date();
            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        },
        memberTypeCapitalize(type) {
            switch (type) {
                case '1':
                case 1:
                    return 'Student';
                case '2':
                case 2:
                    return 'Teacher';
                case '3':
                case 3:
                    return 'Staff';
                default:
                    return 'Unknown';
            }
        }
    },
    mounted() {
        this.getDataList();
    }
}
</script>

<style scoped>
.border_bottom {
    border-bottom: 1px solid #ebebeb !important;
    line-height: 32px !important;
}

.border_bottom label {
    margin-bottom: 0 !important;
}

.border_bottom strong {
    margin-right: 5px !important;
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
.table-center .action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}
</style>
