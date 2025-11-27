<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Assign Canteen">
                    <template v-slot:page_top>
                        <page-top :default-object="{ consumer_type: '' ,member_type: ''}" topPageTitle="Assign Canteen"
                            :default-add-button="can('assign_canteen_add')"></page-top>
                    </template>
                    <template v-slot:filter>
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

                            <template v-if="data.consumer_type == 1">
                                <td>
                                    <strong>Name : </strong>{{ data.consumer.student_name_en }}<br />
                                    <strong>ID : </strong>{{ data.consumer.student_roll }}<br />
                                    <strong>Type : </strong>{{ getCanteenUserType(data.consumer_type)
                                    }}<br />
                                    <strong>Phone : </strong>{{ data.consumer.student_phone || 'N/A'
                                    }}
                                </td>
                            </template>
                            <template v-if="data.consumer_type == 2">
                                <td>
                                    <strong>Name : </strong>{{ data.consumer.name }}<br />
                                    <strong>ID : </strong>{{ data.consumer.employee_id }}<br />
                                    <strong>Type : </strong>{{ getCanteenUserType(data.consumer_type)
                                    }}<br />
                                    <strong>Phone : </strong>{{ data.consumer.phone || 'N/A'
                                    }}
                                </td>
                            </template>
                            <template v-else-if="data.consumer_type == 3">
                                <td>
                                    <strong>Name : </strong>Guest User<br />
                                </td>
                            </template>
                            <td style="text-align: center">{{ data.assign_date }}</td>
                            <td style="text-align: center">
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <!-- <a v-if="can('assign_canteen_update')" class="link-primary"
                                        @click="editDataInformation(data, data.id)"><i class="fa fa-edit"></i></a> -->
                                    <a v-if="can('assign_canteen_delete')" class="link-danger"
                                        @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
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
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Member Category</label>
                                    <select v-model="formObject.consumer_type" class="form-control"
                                        v-validate="'required'" :disabled="isEditMode" name="consumer_type">
                                        <option value="">Select Member</option>
                                        <option :value="1">Student</option>
                                        <option :value="2">Employee</option>
                                        <!-- <option value="guest">Guest</option> -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-label">Assign Date</label>
                                    <datepicker input_class="form-control" v-model="formObject.assign_date"
                                        v-validate="'required'" name="assign_date">
                                        <input slot="input" class="form-control" placeholder="Select a date">
                                    </datepicker>
                                </div>
                            </div>
                        </div>

                        <!-- Student Form Section -->
                        <template v-if="formObject.consumer_type === 1">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="col-form-label">Student ID</label>
                                    <autocomplete v-model="formObject.student_roll" api-url="/api/get-student"
                                        placeholder="Enter Student ID" name="student_roll" v-validate="'required'"
                                        validation_name="Student ID" @select="fillStudentData" :disabled="isEditMode" />
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">Class</label>
                                    <input type="text" v-model="formObject.student_class" name="student_class"
                                        class="form-control" placeholder="Auto-filled" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">Student Name</label>
                                    <input type="text" v-model="formObject.student_name" name="student_name"
                                        class="form-control" placeholder="Auto-filled" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="col-form-label">Department</label>
                                    <input type="text" v-model="formObject.student_department" name="student_department"
                                        class="form-control" placeholder="Auto-filled" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">Section</label>
                                    <input type="text" v-model="formObject.section_name" name="section_name"
                                        class="form-control" placeholder="Auto-filled" readonly>
                                </div>
                            </div>
                        </template>

                        <!-- employee Form Section -->
                        <template v-if="formObject.consumer_type === 2">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="col-form-label">Employee ID</label>
                                    <autocomplete v-model="formObject.employee_id" api-url="/api/get-employee"
                                        placeholder="Enter Employee ID" name="employee_id" validation_name="Employee ID"
                                        @select="fillEmployeeData" :disabled="isEditMode" />
                                </div>

                                <div class="col-md-6">
                                    <label class="col-form-label">Employee Department</label>
                                    <input type="text" v-model="formObject.employee_department"
                                        name="employee_department" class="form-control" placeholder="Auto-filled"
                                        readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">Employee Name</label>
                                    <input type="text" v-model="formObject.employee_name" name="employee_name"
                                        class="form-control" placeholder="Auto-filled" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">Employee Designation</label>
                                    <input type="text" v-model="formObject.employee_designation"
                                        name="employee_designation" class="form-control" placeholder="Auto-filled"
                                        readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">Phone</label>
                                    <input type="text" v-model="formObject.employee_phone" name="employee_phone"
                                        class="form-control" placeholder="Employee Phone" readonly>
                                </div>
                            </div>
                        </template>
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
    name: "AssignCanteen",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ["Sl", "Consumer Details", "Assign Date", "Status", "Action"],
            assign_date: null,
            formModalId: "formModal",
            details: {},
            isEditMode: false,
        };
    },

    created() {
        this.formObject.assign_date = this.getCurrentDate();
    },

    methods: {

        getCanteenUserType(type) {
            switch (type) {
                case 1: return 'Student';
                case 2: return 'Employee';
                case 3: return 'Guest';
                default: return 'Unknown';
            }
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
                this.$set(this.formObject, 'school_name', student.school_name);
                this.$set(this.formObject, 'school_id', student.school_id);

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
                this.$set(this.formObject, 'school_name', employee.school_name);
                this.$set(this.formObject, 'school_id', employee.school_id);

                this.$toastr('success', 'Employee details successfully retrieved.', 'Success');
            } else {
                this.$toastr('error', 'Employee data not found!', 'Error');
            }
        },

        editDataInformation: function (data, id) {
            const _this = this;
            _this.isEditMode = true;
            _this.editData(data, id, 'formModal', function (retData) {
                var editUrl = `${_this.urlGenerate()}/${id}/edit`;
                _this.getData(editUrl, 'get', {}, {}, function (retData) {
                    if (retData.status === 1) {
                        const canteenData = retData;
                        _this.$set(_this.formObject, 'assign_date', canteenData.assign_date);
                        _this.$set(_this.formObject, 'consumer_id', canteenData.consumer_id);

                        switch (canteenData.consumer_type) {
                            case 3:
                                _this.$set(_this.formObject, 'guest_name', canteenData.consumer.guest_name);
                                _this.$set(_this.formObject, 'guest_phone', canteenData.consumer.phone);
                                _this.$set(_this.formObject, 'guest_nid', canteenData.consumer.nid);
                                _this.$set(_this.formObject, 'guest_designation', canteenData.consumer.guest_designation);
                                _this.$set(_this.formObject, 'guest_department', canteenData.consumer.guest_department);
                                break;
                            case 1:
                                _this.$set(_this.formObject, 'student_roll', canteenData.consumer.student_roll);
                                _this.$set(_this.formObject, 'student_name', canteenData.consumer.student_name_en);
                                _this.$set(_this.formObject, 'student_class', canteenData.consumer.class_name);
                                _this.$set(_this.formObject, 'department_name', canteenData.consumer.department_name);

                                _this.$set(_this.formObject, 'section_name', canteenData.consumer.section_name);
                                break;

                            case 2:
                                _this.$set(_this.formObject, 'employee_id', canteenData.consumer.employee_id);
                                _this.$set(_this.formObject, 'employee_name', canteenData.consumer.name);
                                _this.$set(_this.formObject, 'employee_department', canteenData.consumer.employee_department); // ✅ fixed
                                _this.$set(_this.formObject, 'employee_designation', canteenData.consumer.designation); // ✅ fixed
                                _this.$set(_this.formObject, 'employee_phone', canteenData.consumer.phone);
                                break;
                        }
                    } else {
                        _this.$toastr('error', 'Error fetching data', "Error");
                    }
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
    },
    mounted() {
        const _this = this;
        _this.$set(_this.formFilter, 'member_type', '');
        _this.getDataList();

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
