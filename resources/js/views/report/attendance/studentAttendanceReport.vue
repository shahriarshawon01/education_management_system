<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :defaultSearchButton="false" :table-heading="[]"
                    :default-object="{ section_id: '', department_id: '', session_id: '', class_id: '', from_date: '', to_date: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Student Attendance Report"
                    :table-responsive="false">

                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Students Attendance Report"
                            :default-add-button="false">
                        </page-top>
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
                            <select v-model="formFilter.section_id" name="section_id" class="form-control">
                                <option value="">Select Section</option>
                                <template v-for="(eachData, index) in requiredData.section">
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
                            <select v-model="formFilter.department_id" name="department_id" class="form-control">
                                <option value="">Select Department</option>
                                <template v-for="(eachData, index) in requiredData.departments">
                                    <option :value="eachData.id">{{ eachData.department_name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <datepicker v-model="formFilter.from_date" name="from_date" input_class="form-control"
                                aria-placeholder="From Date">
                            </datepicker>
                        </div>
                        <div class="col-md-1">
                            <datepicker v-model="formFilter.to_date" name="to_date" input_class="form-control"
                                aria-placeholder="To Date">
                            </datepicker>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" @click.prevent.stop="getStudentAttendance">Get
                                Attendance</button>
                        </div>
                    </template>
                    <template v-slot:bottom_data>
                        <template v-if="combinedStudentData.students && combinedStudentData.students.length > 0">
                            <!-- Reusable School Info Component -->
                            <schoolInfo title="Student Attendance Report" />
                            <div class="attendance-info">
                                <span class="attendance-label"><strong>Reporting Date :</strong> {{ reportingDateRange
                                    }}</span>
                            </div>

                            <div style="overflow-x: auto;">

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="sticky-column">SL</th>
                                            <th rowspan="2" class="sticky-column">Student</th>
                                            <th v-for="(date, dateIndex) in attendanceDates" :key="dateIndex">
                                                {{ date.toFormat('dd') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(student, index) in combinedStudentData.students" :key="student.id">
                                            <td class="sticky-column">{{ index + 1 }}</td>
                                            <td class="sticky-column" style="text-align: left;">
                                                <strong>Name : </strong>{{ student.name }}<br>
                                                <strong>ID : </strong>{{ student.id }}
                                            </td>
                                            <template v-for="(dateT, dateIndex) in attendanceDates">
                                                <td v-if="dateT.holiday" style="background: red;" :key="dateIndex">W
                                                </td>
                                                <td
                                                    v-else-if="student.checkInStatus && student.checkInStatus[dateT.date_formatted]">
                                                    <div>
                                                        <span style="font-weight: bold;"
                                                            :style="{ color: getStatusColor(student.checkInStatus[dateT.date_formatted].presentStatus) }">
                                                            {{
                                                                student.checkInStatus[dateT.date_formatted].presentStatus.charAt(0)
                                                            }}
                                                        </span><br>
                                                        <span
                                                            v-if="student.checkInStatus[dateT.date_formatted].check_in"
                                                            :style="{ color: getStatusColor(student.checkInStatus[dateT.date_formatted].presentStatus) }">
                                                            CI : {{
                                                                formatTime(student.checkInStatus[dateT.date_formatted].check_in)
                                                            }}
                                                        </span>
                                                        <span
                                                            v-if="student.checkInStatus[dateT.date_formatted].check_out !== '0000-00-00 00:00:00'"
                                                            :style="{ color: getStatusColor(student.checkInStatus[dateT.date_formatted].presentStatus) }">
                                                            CO : {{
                                                                formatTime(student.checkInStatus[dateT.date_formatted].check_out)
                                                            }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td v-else>
                                                    <span style="color: red;">A</span>
                                                </td>
                                            </template>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>
                            <span>
                                <strong>
                                    <span style="color: green;">Present = P; </span>
                                    <span style="color: orange;"> Late = L; </span>
                                    <span style="color: red;"> Absent = A;</span>
                                    <span style="color: red;"> Weekend (Friday) = W</span>
                                </strong>
                            </span>
                            <div class="button-group">
                                <div @click="printData('printDiv')">
                                    <button class="btn btn-outline-primary no-print">
                                        <i class="fa-sharp fa-solid fa-print"></i> Print PDF
                                    </button>
                                </div>
                                <button class="btn btn-outline-primary no-print" @click="exportToExcel">
                                    <i class="fa fa-file-excel"></i> Export Excel
                                </button>
                            </div>
                        </template>
                    </template>

                </data-table>
            </div>
        </div>
    </div>
</template>

<script>
import DataTable from "../../../components/dataTable";
import PageTop from "../../../components/pageTop";
import { DateTime } from "luxon";
import * as XLSX from "xlsx";
import schoolInfo from "../../../components/schoolInfo";

export default {
    name: "studentAttendanceReport",
    components: { PageTop, DataTable, schoolInfo },

    data() {
        return {
            attendanceDates: [],
            combinedStudentData: [],

            weekends: [5],
            holidays: []
        };
    },
    methods: {
        formatDate(date) {
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = String(d.getFullYear()).slice(-2);
            return `${day}-${month}-${year}`;
        },

        formatTime(dateString) {
            if (!dateString) return 'N/A';
            const options = { hour: '2-digit', minute: '2-digit', hour12: true };
            const date = new Date(dateString);
            return date.toLocaleTimeString([], options);
        },

        getStatusColor(status) {
            return {
                'L': 'orange',
                'P': 'green',
                'A': 'red'
            }[status] || 'black';
        },

        getStudentAttendance() {
            const { class_id, session_id, section_id, department_id, from_date, to_date } = this.formFilter;

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
            if (!from_date || !to_date) {
                this.$toastr('error', validationMessages.date, "Validation Error");
                return;
            }

            this.axios.get('/api/reports/student_attendance', {
                params: { class_id, session_id, section_id, department_id, from_date, to_date }
            }).then(response => {
                const result = response.data.result || {};

                this.$set(this, 'combinedStudentData', result)

                if (this.combinedStudentData.length === 0) {
                    this.$toastr('error', 'No attendance data found for the selected date range.', "Validation Error");
                }

                this.attendanceDates = this.generateDateRange(from_date, to_date);

            }).catch(error => {
                console.error("Error fetching attendance:", error.response ? error.response.data : error.message);
                this.$toastr('error', 'Failed to fetch attendance data.', "Error");
            });
        },

        generateDateRange(fromDate, toDate) {
            let startDate = DateTime.fromISO(fromDate);
            const endDate = DateTime.fromISO(toDate);
            const dateArray = [];

            while (startDate <= endDate) {
                let dateString = startDate.toFormat('dd-MM-yyyy')
                startDate.holiday = this.weekends.indexOf(startDate.weekday) != -1 || this.holidays.indexOf(dateString) != -1
                startDate.date_formatted = dateString
                dateArray.push(startDate);
                startDate = DateTime.fromISO(startDate.toFormat('yyyy-MM-dd')).plus({ days: 1 });
            }

            return dateArray;
        },

        exportToExcel() {
            const headers = ["SL", "Student", ...this.attendanceDates.map(date => date.toFormat("dd"))];

            const data = this.combinedStudentData.students.map((student, index) => {
                const row = [index + 1, `${student.name} (${student.id})`];

                this.attendanceDates.forEach(date => {
                    const status = student.checkInStatus[date.date_formatted];
                    const checkInTime = status && status.check_in ? this.formatTime(status.check_in) : "N/A";
                    const checkOutTime = status && status.check_out !== "0000-00-00 00:00:00" ? this.formatTime(status.check_out) : "N/A";
                    if (status) {
                        const attendanceStatus = status.presentStatus.charAt(0);
                        row.push(`${attendanceStatus} (CI: ${checkInTime}, CO: ${checkOutTime})`);
                    } else {
                        row.push("A");
                    }
                });

                return row;
            });

            const worksheet = XLSX.utils.aoa_to_sheet([headers, ...data]);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Student Attendance");

            XLSX.writeFile(workbook, "student_attendance_report.xlsx");
        }
    },

    computed: {
        reportingDateRange() {
            const { from_date, to_date } = this.formFilter;
            if (from_date && to_date) {
                const startDate = DateTime.fromISO(from_date).toFormat("dd-MM-yyyy");
                const endDate = DateTime.fromISO(to_date).toFormat("dd-MM-yyyy");
                return `${startDate} To ${endDate}`;
            }
            return '';
        }
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

.report-header {
    margin-bottom: 6px;
    text-align: center;
}

.report-header .school-name {
    font-size: 20px;
    font-weight: bold;
    color: #2c3e50;
    margin: 0;
}

.report-header .school-address {
    font-size: 16px;
    color: #7f8c8d;
    margin: 2px 0;
}

.report-header .report-title {
    font-size: 18px;
    font-weight: 600;
    color: #34495e;
    text-transform: uppercase;
    border-top: 1px solid #bdc3c7;
    border-bottom: 1px solid #bdc3c7;
    padding: 6px 0;
    display: inline-block;
    margin-top: 0px;
}

@media print {
    .no-print {
        display: none !important;
    }
}
</style>
