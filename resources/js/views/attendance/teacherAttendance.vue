<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :defaultSearchButton="false" :table-heading="[]"
                    :default-object="{ designation_id: '', department_id: '', date: '' }" :defaultFilter="false"
                    :default-pagination="false" table-title="Teacher Attendance" :table-responsive="false">

                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Teacher Attendance" :default-add-button="false">
                        </page-top>
                    </template>

                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.department_id" name="department_id" class="form-control">
                                <option value="">Select Department</option>
                                <template v-for="(eachData, index) in requiredData.employeeDepartment">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="formFilter.designation_id" name="designation_id" class="form-control">
                                <option value="">Select Designation</option>
                                <template v-for="(eachData, index) in requiredData.designation">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <datepicker v-model="formFilter.date" name="date" input_class="form-control"
                                placeholder="Select Date"></datepicker>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" @click.prevent.stop="fetchAttendance">Get Attendance</button>
                        </div>
                    </template>

                    <template v-slot:bottom_data>
                        <form @submit.prevent>
                            <template v-if="combinedEmployeeData.length > 0">
                                <div class="attendance-info">
                                    <div>
                                        <span class="attendance-label">Reporting Date :</span>
                                        <span class="attendance-value">{{ attendanceDate }}</span>
                                    </div>
                                </div>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Teacher Name</th>
                                            <th>Teacher ID</th>
                                            <th>Department</th>
                                            <th>Designation</th>
                                            <th>Phone</th>
                                            <th>Attendance</th>
                                            <th>CheckIn</th>
                                            <th>Status</th>
                                            <th>Date : {{ attendanceDate }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(teacher, index) in combinedEmployeeData" :key="teacher.id">
                                            <td>{{ index + 1 }}</td>
                                            <td style="text-align: left;">{{ teacher.name }}</td>
                                            <td style="text-align: center;">{{ teacher.teacher_bio_id }}</td>
                                            <td style="text-align: left;">{{ teacher.department_name }}</td>
                                            <td style="text-align: left;">{{ teacher.designation_name }}</td>
                                            <td style="text-align: center; color: darkblue;">{{ teacher.phone }}</td>

                                            <!-- <td v-if="presentEmployeeIds.includes(teacher.id)"
                                                style="color: green; text-align: center;"><strong>Present</strong></td>
                                            <td v-else style="color: red; text-align: center"><strong>Absent</strong>
                                            </td> -->

                                            <td style="text-align: center;">
                                                <strong
                                                    :style="{ color: (teacher.status === 'Late' ? 'orange' : (teacher.status === 'Present' ? 'green' : 'red')) }">
                                                    {{ teacher.status }}
                                                </strong>
                                            </td>

                                            <td>{{ formatTime(teacher.check_in) }}</td>
                                            <td>
                                                <button v-if="presentEmployeeIds.includes(teacher.id)"
                                                    class="btn btn-small btn-inverse disabled">Send SMS</button>
                                                <button v-else class="btn btn-small btn-primary"
                                                    @click="sendSingleTeacherSMS(teacher.id)">Send SMS</button>
                                            </td>
                                            <td style="text-align: center">
                                                <input v-if="presentEmployeeIds.includes(teacher.id)" type="checkbox"
                                                    class="present-teacher-checkbox" disabled checked />
                                                <input v-else type="checkbox" class="absent-teacher-id"
                                                    :value="teacher.id" v-model="selectedTeacherIds" checked />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="button-group">
                                    <div @click="printData('printDiv')">
                                        <button class="btn btn-outline-primary no-print">
                                            <i class="fa-sharp fa-solid fa-print"></i> Print
                                        </button>
                                    </div>
                                    <button @click="printData('printDiv')" class="btn btn-outline-primary no-print">
                                        <i class="fa fa-file-pdf"></i> Export PDF
                                    </button>
                                    <button class="btn btn-outline-primary no-print" @click="sendSMSToAllAbsentTeachers">
                                        <i class="fa fa-envelope"></i> SMS to Teacher
                                    </button>
                                </div>
                            </template>
                        </form>
                    </template>
                </data-table>
            </div>
        </div>
    </div>
</template>

<script>
import DataTable from "../../components/dataTable";
import PageTop from "../../components/pageTop";

export default {
    name: "teacherAttendance",
    components: { DataTable, PageTop },
    data() {
        return {
            attendanceDate: '',
            presentEmployeeIds: [],
            combinedEmployeeData: [],
            selectedTeacherIds: [],
        };
    },
    methods: {
        formatTime(checkIn) {
            if (!checkIn) return 'N/A';
            const date = new Date(checkIn);
            return date.toLocaleTimeString('en-US');
        },

        sendSingleTeacherSMS(teacherId) {
            if (!teacherId) {
                this.$toastr('error', 'Invalid teacher ID');
                return;
            }

            this.axios.post(`/api/sms_single_absent_teacher/${teacherId}`)
                .then(response => {
                    this.$toastr('success', `Successfully SMS Sent to ${response.data.teacher_name}`);
                })
                .catch(error => {
                    console.error("Error sending SMS:", error);
                    this.$toastr('error', 'Failed to send SMS');
                });
        },

        sendSMSToAllAbsentTeachers() {
            if (this.selectedTeacherIds.length === 0) {
                this.$toastr('error', 'No absent teachers selected');
                return;
            }

            this.axios.post('/api/sms_multiple_absent_teacher', {
                teacher_id: this.selectedTeacherIds.map(Number),
            }).then(response => {
                this.$toastr('success', 'SMS sent successfully to absent teachers');
            }).catch(error => {
                console.error("Error sending SMS to multiple teachers:", error);
                this.$toastr('error', 'Failed to send SMS to teachers');
            });
        },

        fetchAttendance() {
            const { designation_id, department_id, date } = this.formFilter;

            // Validate date input
            if (!date) {
                this.$toastr('error', 'Please select a date.', "Validation Error");
                return;
            }

            this.axios.get('/api/teacher_attendance', {
                params: {
                    designation_id,
                    department_id,
                    date
                }
            }).then(response => {
                const result = response.data.result;
                const presentEmployees = result.presentTeacher || [];
                const absentEmployees = result.data || [];
                this.attendanceDate = result.date;
                this.combinedEmployeeData = [...presentEmployees, ...absentEmployees];
                this.presentEmployeeIds = presentEmployees.map(emp => emp.id);
                this.selectedTeacherIds = absentEmployees.map(emp => emp.id);

                // Define late threshold based on the selected date
                const selectedDate = new Date(date);
                const LATE_THRESHOLD = new Date(selectedDate.setHours(8, 36, 0));

                this.combinedEmployeeData.forEach(teacher => {
                    if (this.presentEmployeeIds.includes(teacher.id)) {
                        const checkInTime = new Date(teacher.check_in);
                        teacher.status = checkInTime > LATE_THRESHOLD ? 'Late' : 'Present';
                    } else {
                        teacher.status = 'Absent';
                    }
                });
            }).catch(error => {
                console.error("Error fetching attendance:", error);
                this.$toastr('error', 'Failed to fetch attendance data.', "Error");
            });
        },
    },
    mounted() {
        this.getGeneralData(['designation', 'employeeDepartment']);
    },
}
</script>

<style scoped>
.main_component {
    padding: 20px;
}

.table thead th {
    background-color: #cccbcb;
}

.table th,
.table td {
    text-align: center;
    color: black;
    font-size: 14px
}

.button-group {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
}

.attendance-info {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin: 20px 0;
    font-size: 14px;
}

.attendance-label {
    font-weight: bold;
    margin-right: 5px;
}

.attendance-value {
    margin-left: 5px;
    color: rgb(0, 0, 0);
}

.present-teacher-checkbox {
    opacity: 0.5;
    cursor: not-allowed;
}
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
