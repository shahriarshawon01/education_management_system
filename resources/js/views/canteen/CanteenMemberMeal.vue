<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Assign To Canteen">
                    <template v-slot:page_top>
                        <page-top :default-object="{ consumer_type: '' }" topPageTitle="Assign To Canteen"
                            :default-add-button="can('daily_meal_members_add')"></page-top>
                    </template>
                    <template v-slot:filter></template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>
                                <span v-if="data.consumer_type === 'guest'">
                                    {{ data.guests.guest_name }}
                                </span>
                                <span v-else-if="data.consumer_type === 'teacher' && data.teachers">
                                    {{ data.teachers.name }}
                                </span>
                                <span v-else-if="data.consumer_type === 'student' && data.students">
                                    {{ data.students.student_name_en }}
                                </span>
                                <span v-else-if="data.consumer_type === 'staff' && data.staffs">
                                    {{ data.staffs.name }}
                                </span>
                            </td>
                            <td style="text-align: left">
                                <span v-if="data.consumer_type === 'guest'">Guest</span>
                                <span v-else-if="data.consumer_type === 'student'">Student</span>
                                <span v-else-if="data.consumer_type === 'teacher'">Teacher</span>
                                <span v-else-if="data.consumer_type === 'staff'">Staff</span>
                            </td>


                            <td style="text-align: center">{{ formatMealTime(data.meal_time) }}</td>
                            <td style="text-align: center">{{ data.meal_date }}</td>
                            <td style="text-align: center">{{ data.meal_day }}</td>
                            <td style="text-align: center">{{ data.meal_count }}</td>

                            <td style="display:flex;justify-content: center">
                                <div class="hstack gap-3 fs-15">
                                    <!-- <a v-if="can('daily_meal_members_update')" class="link-primary"
                                        @click="editDataInformation(data, data.id)"><i class="fa fa-edit"></i></a> -->
                                    <a v-if="can('daily_meal_members_delete')" class="link-danger"
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
                            <!-- Consumer Type Dropdown -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-form-label">Meal Assign</label>
                                    <select v-model="formObject.consumer_type" class="form-control"
                                        v-validate="'required'">
                                        <option value="">Select Consumer</option>
                                        <option value="student">Student</option>
                                        <option value="teacher">Teacher</option>
                                        <option value="staff">Staff</option>
                                        <option value="guest">Guest</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Datepicker and Dynamic Fields -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-form-label">Meal Dates</label>
                                    <date-picker v-model="formObject.meal_dates" type="date" format="YYYY-MM-DD"
                                        multiple placeholder="Select multiple dates" :editable="false" :clearable="true"
                                        class="form-control" input_class="form-control" />
                                </div>

                                <template
                                    v-if="['student', 'teacher', 'staff', 'guest'].includes(formObject.consumer_type)">
                                    <div class="form-group">
                                        <label class="col-form-label">Meal Times</label>
                                        <div class="form-check" v-for="meal in mealTimes" :key="meal.id">
                                            <input type="checkbox" class="form-check-input" :id="'meal-' + meal.id"
                                                :value="meal.id" v-model="formObject.meal_time" name="meal_time[]" />
                                            <label class="form-check-label" :for="'meal-' + meal.id">{{ meal.name
                                            }}</label>
                                        </div>
                                    </div>
                                </template>
                            </div>

                        </div>

                        <!-- Student Form Section -->
                        <template v-if="formObject.consumer_type === 'student'">
                            <div class="row" style="margin-top: -110px !important;">
                                <div class="col-md-6">
                                    <div class="student-roll">
                                        <label class="col-form-label">Student ID</label>
                                        <input type="text" v-model="formObject.student_roll" v-validate="'required'"
                                            name="student_roll" class="form-control" placeholder="Enter Student ID"
                                            @change="getStudent">
                                    </div>

                                    <div class="student-name">
                                        <label class="col-form-label">Student Name</label>
                                        <input type="text" v-model="formObject.student_name" name="student_name"
                                            class="form-control" placeholder="Student Name" readonly>
                                    </div>

                                    <div class="student-class">
                                        <label class="col-form-label">Class</label>
                                        <input type="text" v-model="formObject.student_class" name="student_class"
                                            class="form-control" placeholder="Student Class" readonly>
                                    </div>

                                    <div class="student-department">
                                        <label class="col-form-label">Department</label>
                                        <input type="text" v-model="formObject.student_department"
                                            name="student_department" class="form-control"
                                            placeholder="Student Department" readonly>
                                    </div>

                                    <div class="student-section">
                                        <label class="col-form-label">Section</label>
                                        <input type="text" v-model="formObject.section_name" name="section_name"
                                            class="form-control" placeholder="Student Section" readonly>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Teacher Form Section -->
                        <template v-if="formObject.consumer_type === 'teacher'">
                            <div class="row" style="margin-top: -110px !important;">
                                <div class="col-md-6">
                                    <div class="teacher-id">
                                        <label class="col-form-label">Teacher ID</label>
                                        <input type="text" v-model="formObject.teacher_id" v-validate="'required'"
                                            name="teacher_id" class="form-control" placeholder="Enter Teacher ID"
                                            @change="getTeacher">
                                    </div>

                                    <div class="teacher-name">
                                        <label class="col-form-label">Teacher Name</label>
                                        <input type="text" v-model="formObject.teacher_name" name="teacher_name"
                                            class="form-control" placeholder="Teacher Name" readonly>
                                    </div>
                                    <div class="teacher-department">
                                        <label class="col-form-label">Department</label>
                                        <input type="text" v-model="formObject.teacher_department"
                                            name="teacher_department" class="form-control"
                                            placeholder="Teacher Department" readonly>
                                    </div>
                                    <div class="teacher-email">
                                        <label class="col-form-label">Email</label>
                                        <input type="text" v-model="formObject.teacher_email" name="teacher_email"
                                            class="form-control" placeholder="Teacher E-mail" readonly>
                                    </div>
                                    <div class="teacher-phone">
                                        <label class="col-form-label">Phone</label>
                                        <input type="text" v-model="formObject.teacher_phone" name="teacher_phone"
                                            class="form-control" placeholder="Teacher Phone" readonly>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Staff Form Section -->
                        <template v-if="formObject.consumer_type === 'staff'">
                            <div class="row" style="margin-top: -110px !important;">
                                <div class="col-md-6">
                                    <div class="staff-id">
                                        <label class="col-form-label">Staff ID</label>
                                        <input type="text" v-model="formObject.staff_id" v-validate="'required'"
                                            name="staff_id" class="form-control" placeholder="Enter Staff ID"
                                            @change="getStaff">
                                    </div>
                                    <div class="staff-name">
                                        <label class="col-form-label">Staff Name</label>
                                        <input type="text" v-model="formObject.staff_name" name="staff_name"
                                            class="form-control" placeholder="Staff Name" readonly>
                                    </div>
                                    <div class="staff-department">
                                        <label class="col-form-label">Department</label>
                                        <input type="text" v-model="formObject.staff_department" name="staff_department"
                                            class="form-control" placeholder="Staff Department" readonly>
                                    </div>
                                    <div class="staff-email">
                                        <label class="col-form-label">Email</label>
                                        <input type="text" v-model="formObject.staff_email" name="staff_email"
                                            class="form-control" placeholder="Staff E-mail" readonly>
                                    </div>
                                    <div class="staff-phone">
                                        <label class="col-form-label">Phone</label>
                                        <input type="text" v-model="formObject.staff_phone" name="staff_phone"
                                            class="form-control" placeholder="Staff Phone" readonly>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Guest Form Section -->
                        <template v-if="formObject.consumer_type === 'guest'">
                            <div class="row" style="margin-top: -110px !important;">
                                <div class="col-md-6">
                                    <div class="guest-name">
                                        <label class="col-form-label">Guest Name</label>
                                        <input type="text" v-model="formObject.guest_name" name="guest_name"
                                            class="form-control" placeholder="Enter Guest Name">
                                    </div>
                                    <div class="guest-email">
                                        <label class="col-form-label">Email</label>
                                        <input type="email" v-model="formObject.email" v-validate="'required|email'"
                                            name="email" class="form-control" placeholder="Enter Email Address">
                                    </div>
                                    <div class="guest-institute">
                                        <label class="col-form-label">Institute</label>
                                        <input type="text" v-model="formObject.guest_institute" name="guest_institute"
                                            class="form-control" placeholder="Enter Guest Institute">
                                    </div>
                                    <div class="guest-phone">
                                        <label class="col-form-label">Phone</label>
                                        <input type="text" v-model="formObject.guest_phone" name="guest_phone"
                                            class="form-control" placeholder="Enter Guest Phone">
                                    </div>
                                    <div class="guest-nid">
                                        <label class="col-form-label">NID</label>
                                        <input type="number" v-model="formObject.guest_nid" name="guest_nid"
                                            class="form-control" placeholder="Enter Guest NID">
                                    </div>
                                    <div class="guest-designation">
                                        <label class="col-form-label">Designation</label>
                                        <input type="text" v-model="formObject.guest_designation"
                                            name="guest_designation" class="form-control"
                                            placeholder="Enter Guest Designation">
                                    </div>
                                    <div class="guest-department">
                                        <label class="col-form-label">Department</label>
                                        <input type="text" v-model="formObject.guest_department" name="guest_department"
                                            class="form-control" placeholder="Enter Guest Department">
                                    </div>
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
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';


export default {
    name: "CanteenMemberMeal",
    components: { PageTop, Pagination, DataTable, formModal, DatePicker },
    data() {
        return {
            tableHeading: ["Sl", "Consumer", "Consumer Type", "Meal Time", "Meal Date", "Meal Day", "Total Meal", "Action"],
            meal_date: null,
            formModalId: "formModal",
            details: {},
            teacherData: {},
            student_roll: '',
            teacher_id: '',
        };
    },

    created() {
        this.formObject.meal_date = this.getCurrentDate();
        this.generateWeekDates();
    },

    methods: {

        formatMealTime(mealTime) {
            let ids = mealTime;
            if (typeof ids === 'string') {
                try {
                    ids = JSON.parse(ids);
                } catch (e) {
                    ids = ids.split(',').map(v => Number(v.trim())).filter(v => !isNaN(v));
                }
            }

            if (!Array.isArray(ids)) {
                return '[Invalid]';
            }
            ids = ids.map(v => Number(v)).filter(v => !isNaN(v));
            const slots = [0, 0, 0];
            ids.forEach(id => {
                const idx = id - 1;
                if (idx >= 0 && idx < slots.length) {
                    slots[idx] = 1;
                }
            });

            return `[${slots.join(' + ')}]`;
        },

        generateWeekDates() {
            const today = new Date();
            const dates = [];

            for (let i = 0; i < 7; i++) {
                const d = new Date();
                d.setDate(today.getDate() + i);
                const formatted = `${d.getFullYear()}-${(d.getMonth() + 1).toString().padStart(2, '0')}-${d.getDate().toString().padStart(2, '0')}`;
                dates.push({ date: formatted });
            }

            this.availableDates = dates;
        },

        getStudent() {
            const _this = this;
            const URL = `${baseUrl}/api/get_student?student_roll=${_this.formObject.student_roll}`;
            _this.axios.get(URL)
                .then(response => {
                    const studentData = response.data.result;
                    if (studentData) {
                        _this.$set(_this.formObject, 'consumer_id', studentData.id);
                        _this.$set(_this.formObject, 'student_name', studentData.student_name_en);
                        _this.$set(_this.formObject, 'student_class', studentData.class_name);
                        _this.$set(_this.formObject, 'student_department', studentData.department_name);
                        _this.$set(_this.formObject, 'section_name', studentData.section_name);

                        _this.$toastr('success', response.data.message, "Success");
                    } else {
                        _this.$toastr('error', response.data.message, "Error");
                    }
                })
        },

        getTeacher() {
            const _this = this;
            const URL = `${baseUrl}/api/get_teacher?teacher_id=${_this.formObject.teacher_id}`;
            _this.axios.get(URL)
                .then(response => {
                    const teacherData = response.data.result;
                    if (teacherData) {
                        _this.$set(_this.formObject, 'consumer_id', teacherData.id);
                        _this.$set(_this.formObject, 'teacher_name', teacherData.name);
                        _this.$set(_this.formObject, 'teacher_department', teacherData.department);
                        _this.$set(_this.formObject, 'teacher_email', teacherData.email);
                        _this.$set(_this.formObject, 'teacher_phone', teacherData.phone);

                        _this.$toastr('success', response.data.message, "Success");
                    } else {
                        _this.$toastr('error', response.data.message, "Error");
                    }
                })
        },

        getStaff() {
            const _this = this;
            const URL = `${baseUrl}/api/get_staff?staff_id=${_this.formObject.staff_id}`;
            _this.axios.get(URL)
                .then(response => {
                    const staffData = response.data.result;
                    if (staffData) {
                        _this.$set(_this.formObject, 'consumer_id', staffData.id);
                        _this.$set(_this.formObject, 'staff_name', staffData.name);
                        _this.$set(_this.formObject, 'staff_department', staffData.department);
                        _this.$set(_this.formObject, 'staff_email', staffData.email);
                        _this.$set(_this.formObject, 'staff_phone', staffData.phone);

                        _this.$toastr('success', response.data.message, "Success");
                    } else {
                        _this.$toastr('error', response.data.message, "Error");
                    }
                })
                .catch(error => {
                    console.error('Error fetching product data:', error);
                });
        },

        fetchMealTimes() {
            const _this = this;
            _this.axios.get(`${baseUrl}/api/meal-times`)
                .then(res => {
                    if (res.data.status === 1) {
                        _this.mealTimes = res.data.data;
                    }
                });
        },

        editDataInformation: function (data, id) {
            const _this = this;
            _this.editData(data, id, 'formModal', function (retData) {
                var editUrl = `${_this.urlGenerate()}/${id}/edit`;
                _this.getData(editUrl, 'get', {}, {}, function (retData) {
                    if (retData.status === 1) {
                        const canteenData = retData;

                        _this.$set(_this.formObject, 'meal_date', canteenData.meal_date);
                        _this.$set(_this.formObject, 'consumer_id', canteenData.consumer_id);
                        _this.$set(_this.formObject, 'comments', canteenData.comments);

                        switch (canteenData.consumer_type) {
                            case 'guest':
                                _this.$set(_this.formObject, 'guest_name', canteenData.guests.guest_name);
                                _this.$set(_this.formObject, 'email', canteenData.guests.email);
                                _this.$set(_this.formObject, 'guest_institute', canteenData.guests.guest_institute);
                                _this.$set(_this.formObject, 'guest_phone', canteenData.guests.phone);
                                _this.$set(_this.formObject, 'guest_nid', canteenData.guests.nid);
                                _this.$set(_this.formObject, 'guest_designation', canteenData.guests.guest_designation);
                                _this.$set(_this.formObject, 'guest_department', canteenData.guests.guest_department);
                                break;
                            case 'student':
                                _this.$set(_this.formObject, 'student_roll', canteenData.consumer.student_roll);
                                _this.$set(_this.formObject, 'student_name', canteenData.consumer.student_name_en);
                                _this.$set(_this.formObject, 'student_class', canteenData.consumer.classes.name);
                                _this.$set(_this.formObject, 'student_department', canteenData.consumer.departments.department_name);
                                _this.$set(_this.formObject, 'section_name', canteenData.consumer.sections.name);
                                break;
                            case 'teacher':
                                _this.$set(_this.formObject, 'teacher_id', canteenData.consumer.employee_id);
                                _this.$set(_this.formObject, 'teacher_name', canteenData.consumer.name);
                                _this.$set(_this.formObject, 'teacher_department', canteenData.consumer.department.name);
                                _this.$set(_this.formObject, 'teacher_email', canteenData.consumer.email);
                                _this.$set(_this.formObject, 'teacher_phone', canteenData.consumer.phone);
                                break;
                            case 'staff':
                                _this.$set(_this.formObject, 'staff_id', canteenData.consumer.employee_id);
                                _this.$set(_this.formObject, 'staff_name', canteenData.consumer.name);
                                _this.$set(_this.formObject, 'staff_department', canteenData.consumer.department.name);
                                _this.$set(_this.formObject, 'staff_email', canteenData.consumer.email);
                                _this.$set(_this.formObject, 'staff_phone', canteenData.consumer.phone);
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
        _this.fetchMealTimes();
        _this.getDataList();
        _this.formObject.meal_time = this.formObject.meal_time || [];
    },
};
</script>

<style scoped>
.meal-day-box {
    background: linear-gradient(to right, #f0f4ff, #e0ecff);
    border-left: 5px solid #007bff;
    transition: all 0.3s ease;
}

.meal-day-box:hover {
    box-shadow: 0 0 10px rgba(0, 123, 255, 0.2);
}

.vue2-datepicker {
    width: 100%;
    font-family: 'Segoe UI', sans-serif;
    font-size: 14px;
}

.vue2-datepicker .mx-input {
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
}

.vue2-datepicker .mx-calendar {
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}
</style>
