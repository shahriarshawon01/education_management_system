<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :defaultSearchButton="false" :table-heading="[]"
                    :default-object="{ section_id: '', department_id: '', session_id: '', class_id: '', date: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Student Attendance"
                    :table-responsive="false">

                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Students Attendance"
                            :default-add-button="false"></page-top>
                    </template>

                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.class_id" name="class_id" class="form-control"
                                @change="getGeneralData({ section: { class_id: formFilter.class_id }, departments: { class_id: formFilter.class_id } })">
                                <option value="">Select Class</option>
                                <template v-for="(eachData, index) in requiredData.class">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="formFilter.session_id" name="session_id" class="form-control">
                                <option value="">Select Session</option>
                                <template v-for="(data, index) in requiredData.session">
                                    <option :value="data.id">{{ data.title }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="formFilter.section_id" name="section_id" class="form-control">
                                <option value="">Select Section</option>
                                <template v-for="(eachData, index) in requiredData.section">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="formFilter.department_id" name="department_id" class="form-control">
                                <option value="">Select Department</option>
                                <template v-for="(eachData, index) in requiredData.departments">
                                    <option :value="eachData.id">{{ eachData.department_name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <datepicker v-model="formFilter.date" name="date" input_class="form-control"
                                aria-placeholder="Select Date">
                            </datepicker>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" @click.prevent.stop="fetchAttendance">Get
                                Attendance</button>
                        </div>
                    </template>

                    <template v-slot:bottom_data>
                        <form @submit.prevent>
                            <template v-if="combinedStudentData.length > 0">
                                <div class="attendance-info">
                                    <!-- <div>
                                        <span class="attendance-label">Class Name :</span>
                                        <span class="attendance-value">{{ combinedStudentData[0].class_name }}</span>
                                    </div>
                                    <div>
                                        <span class="attendance-label">Section Name :</span>
                                        <span class="attendance-value">{{ combinedStudentData[0].name }}</span>
                                    </div>
                                    <div>
                                        <span class="attendance-label">Department Name :</span>
                                        <span class="attendance-value">{{ combinedStudentData[0].department_name
                                            }}</span>
                                    </div> -->
                                    <div>
                                        <span class="attendance-label">Reporting Date :</span>
                                        <span class="attendance-value">{{ attendanceDate }}</span>
                                    </div>
                                </div>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Student Name</th>
                                            <th>Student ID</th>
                                            <th>Class Roll</th>
                                            <th>Class</th>
                                            <th>Section</th>
                                            <th>Attendance</th>
                                            <th>CheckIn</th>
                                            <th>Number</th>
                                            <th>Status</th>
                                            <th>SMS</th>
                                            <th>Date : {{ attendanceDate }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(student, index) in combinedStudentData" :key="student.id">
                                            <td>{{ index + 1 }}</td>
                                            <td style="text-align: left;">{{ student.student_name_en }}</td>
                                            <td style="text-align: center;">{{ student.student_roll }}</td>
                                            <td style="text-align: center;">{{ student.merit_roll }}</td>
                                            <td style="text-align: center;">{{ student.class_name }}</td>
                                            <td style="text-align: center;">{{ student.name }}</td>

                                            <td style="text-align: center;">
                                                <strong
                                                    :style="{ color: (student.status === 'Late' ? 'orange' : (student.status === 'Present' ? 'green' : 'red')) }">
                                                    {{ student.status }}
                                                </strong>
                                            </td>

                                            <td style="text-align: center;">{{ formatTime(student.check_in) }}</td>
                                            <td style="text-align: center; color: darkblue;">{{ student.father_phone ? student.father_phone : student.mother_phone }}
                                            </td>
                                            <td style="text-align: center;">Regular</td>
                                            <td style="text-align: center;">
                                                <button v-if="presentStudentIds.includes(student.id)"
                                                    class="btn btn-small btn btn-inverse disabled">Send SMS</button>

                                                <button v-else class="btn btn-small btn-primary"
                                                    @click="sendSingleSMS(student.id)">Send SMS</button>
                                            </td>
                                            <td style="text-align: center">
                                                <input v-if="presentStudentIds.includes(student.id)" type="checkbox"
                                                    class="present-student-checkbox" disabled checked />
                                                <input v-else type="checkbox" class="absent-student-id"
                                                    :value="student.id" v-model="selectedStudentIds" checked />
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
                                    <button class="btn btn-outline-primary no-print" @click="sendSMSToAllAbsentStudent">
                                        <i class="fa fa-envelope"></i> SMS to all absent student
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
    name: "studentAttendance",
    components: { PageTop, DataTable },

    data() {
        return {
            attendanceDate: '',
            presentStudentIds: [],
            absentStudents: [],
            combinedStudentData: [],
            selectedStudentIds: [],
        };
    },
    methods: {
        formatTime(checkIn) {
            if (!checkIn) return 'N/A';
            const date = new Date(checkIn);
            return date.toLocaleTimeString('en-US');
        },

        // sendSingleSMS(studentId) {
        //     if (!studentId) {
        //         this.$toastr('error', 'Invalid student ID');
        //         return;
        //     }

        //     this.axios.post(`/api/sms_absent_student/${studentId}`)
        //         .then(response => {
        //             this.$toastr('success', `Successfully SMS Sent to ${response.data.student_name_en}`);
        //         })
        //         .catch(error => {
        //             console.error("Error sending SMS:", error);
        //             this.$toastr('error', 'Failed to send SMS');
        //         });
        // },

        sendSingleSMS(studentId) {
            if (!studentId) {
                this.$toastr('error', 'Invalid student ID');
                return;
            }

            this.axios.post(`/api/sms_absent_student/${studentId}`)
                .then(response => {
                    if (response.data.status === 200) {
                        this.$toastr('success', `Successfully SMS Sent to ${response.data.student_name_en}`);
                    } else {
                        this.$toastr('error', response.data.message || 'Failed to send SMS');
                    }
                })
                .catch(error => {
                    console.error("Error sending SMS:", error);
                    this.$toastr('error', error.response?.data?.message || 'An unexpected error occurred while sending SMS');
                });
        },

        sendSMSToAllAbsentStudent() {

            if (this.selectedStudentIds.length === 0) {
                this.$toastr('error', 'No absent students selected');
                return;
            }

            this.axios.post('/api/sms_class_wise_absent_student', {
                student_id: this.selectedStudentIds.map(Number),
            }).then(response => {
                this.$toastr('success', 'SMS sent successfully to absent students');
            }).catch(error => {
                console.error("Error sending SMS to class:", error);
                this.$toastr('error', 'Failed to send SMS to all absent student');
            });
        },

        fetchAttendance() {
            const { class_id, section_id, department_id, session_id, date } = this.formFilter;

            // validation messages
            const validationMessages = {
                class_id: 'Please select the class.',
                session_id: 'Please select the session.',
                section_id: 'Please select the section.',
                department_id: 'Please select the department.',
                date: 'Please select both dates.'
            };

            // Validate fields
            if (!class_id) {
                this.$toastr('error', validationMessages.class_id, "Validation Error");
                return;
            }
            if (!session_id) {
                this.$toastr('error', validationMessages.session_id, "Validation Error");
                return;
            }
            if (!section_id) {
                this.$toastr('error', validationMessages.section_id, "Validation Error");
                return;
            }
            if (!department_id) {
                this.$toastr('error', validationMessages.department_id, "Validation Error");
                return;
            }
            if (!date) {
                this.$toastr('error', validationMessages.date, "Validation Error");
                return;
            }

            this.axios.get('/api/student_attendance', {
                params: {
                    class_id,
                    section_id,
                    department_id,
                    session_id,
                    date
                }
            }).then(response => {
                const result = response.data.result;
                if (!result || !Array.isArray(result.data)) {
                    console.error("Invalid response structure:", result);
                    return;
                }

                const presentStudents = result.presentStudent || [];
                const absenceStudents = result.data || [];
                this.attendanceDate = result.date;
                this.combinedStudentData = [...presentStudents, ...absenceStudents];
                this.presentStudentIds = presentStudents.map(student => student.id);

                this.selectedStudentIds = absenceStudents.map(student => student.id);

                const selectedDate = new Date(date);
                const LATE_THRESHOLD = new Date(selectedDate.setHours(9, 4, 0));

                this.combinedStudentData.forEach(student => {
                    if (this.presentStudentIds.includes(student.id)) {
                        const checkInTime = new Date(student.check_in);
                        student.status = checkInTime > LATE_THRESHOLD ? 'Late' : 'Present';
                    } else {
                        student.status = 'Absent';
                    }
                });

            }).catch(error => {
                console.error("There was an error fetching attendance data:", error);
            });
        },
    },
    mounted() {
        this.getGeneralData(['class', 'session', 'section', 'departments']);
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

.present-student-checkbox {
    opacity: 0.5;
    cursor: not-allowed;
}

@media print {
    .no-print {
        display: none !important;
    }
}
</style>
