<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :defaultSearchButton="false" :table-heading="[]"
                    :default-object="{ designation_id: '', department_id: '', from_date: '', to_date: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Teacher Attendance Report"
                    :table-responsive="false">

                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Teacher Attendance Report"
                            :default-add-button="false">
                        </page-top>
                    </template>

                    <template v-slot:filter>
                        <div class="col-md-1">
                            <select v-model="formFilter.department_id" name="department_id" class="form-control">
                                <option value="">Department</option>
                                <template v-for="(eachData, index) in requiredData.employeeDepartment">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <select v-model="formFilter.designation_id" name="designation_id" class="form-control">
                                <option value="">Designation</option>
                                <template v-for="(eachData, index) in requiredData.designation">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <datepicker class="form-control" v-model="formFilter.from_date" placeholder="From Date"
                                name="from_date">
                            </datepicker>
                        </div>
                        <div class="col-md-1">
                            <datepicker class="form-control" v-model="formFilter.to_date" placeholder="To Date"
                                name="to_date"></datepicker>
                        </div>

                        <!-- <div class="col-md-2">
                            <select v-model="weekends" name="weekend" multiple class="form-control">
                                <option value="">Weekend</option>
                                <option value="5">Saturday</option>
                                <option value="6">Sunday</option>
                            </select>
                        </div> -->

                        <!-- <div class="col-md-2">
                            <select v-model="formFilter.holidays" name="holidays" class="form-control" multiple>
                                <option value="" disabled>Select Holiday Dates</option>
                                <option v-for="(date, index) in attendanceDates" :key="index"
                                    :value="date.date_formatted">
                                    {{ date.date_formatted }}
                                </option>
                            </select>
                        </div> -->

                        <div class="col-md-2">
                            <button class="btn btn-primary" @click.prevent.stop="getTeacherAttendance">Get
                                Attendance</button>
                        </div>
                    </template>

                    <template v-slot:bottom_data>
                        <form @submit.prevent>
                            <template v-if="combinedEmployeeData.teachers && combinedEmployeeData.teachers.length > 0">

                                <!-- Reusable School Info Component -->
                                <schoolInfo title="Teacher Attendance Report" />

                                <div class="attendance-info">
                                    <span class="attendance-label"><strong>Reporting Date :</strong> {{
                                        reportingDateRange }}</span>
                                </div>

                                <div style="overflow-x: auto;">

                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="sticky-column">SL</th>
                                                <th rowspan="2" class="sticky-column">Teacher</th>
                                                <th v-for="(date, dateIndex) in attendanceDates" :key="dateIndex">
                                                    {{ date.toFormat('dd') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(teacher, index) in combinedEmployeeData.teachers"
                                                :key="teacher.id">
                                                <td class="sticky-column">{{ index + 1 }}</td>
                                                <td class="sticky-column" style="text-align: left;">
                                                    <strong>Name : </strong>{{ teacher.name }}<br>
                                                    <strong>ID : </strong>{{ teacher.id }}
                                                </td>
                                                <template v-for="(dateT, dateIndex) in attendanceDates">
                                                    <td v-if="dateT.holiday" style="background: red;" :key="dateIndex">W
                                                    </td>
                                                    <td v-else-if="teacher.checkInStatus[dateT.date_formatted]">
                                                        <div>
                                                            <span style="font-weight: bold;"
                                                                :style="{ color: getStatusColor(teacher.checkInStatus[dateT.date_formatted].presentStatus) }">
                                                                {{
                                                                    teacher.checkInStatus[dateT.date_formatted].presentStatus.charAt(0)
                                                                }}
                                                            </span><br>
                                                            <span
                                                                v-if="teacher.checkInStatus[dateT.date_formatted].check_in"
                                                                :style="{ color: getStatusColor(teacher.checkInStatus[dateT.date_formatted].presentStatus) }">
                                                                CI : {{
                                                                    formatTime(teacher.checkInStatus[dateT.date_formatted].check_in)
                                                                }}
                                                            </span> <br>
                                                            <span
                                                                v-if="teacher.checkInStatus[dateT.date_formatted].check_out"
                                                                :style="{ color: getStatusColor(teacher.checkInStatus[dateT.date_formatted].presentStatus) }">
                                                                CO : {{
                                                                    formatTime(teacher.checkInStatus[dateT.date_formatted].check_out)
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
                                        ML = Medical Leave, CL = Casual Leave, MAL = Maternity Leave, PAL = Paternity
                                        Leave
                                    </strong>
                                </span><br>
                                <span>
                                    <strong>
                                        <span style="color: green;">Present = P</span><br>
                                        <span style="color: orange;">Late = L</span><br>
                                        <span style="color: red;">Absent = A</span><br>
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
                        </form>
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
    name: "teacherAttendanceReport",
    components: { DataTable, PageTop, schoolInfo },
    data() {
        return {
            attendanceDates: [],
            combinedEmployeeData: [],
            weekends: [5],
            holidays: []
        };
    },

    methods: {
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

        getTeacherAttendance() {
            const { designation_id, department_id, from_date, to_date } = this.formFilter;

            const fromDate = new Date(from_date);
            const toDate = new Date(to_date);
            const timeDiff = toDate - fromDate;
            const oneMonthInMilliseconds = 30 * 24 * 60 * 60 * 1000;

            if (timeDiff > oneMonthInMilliseconds) {
                this.$toastr('error', "Date range can't exceed one month", "Validation Error", { status: 5000 });
                return;
            }

            if (!from_date || !to_date) {
                this.$toastr('error', 'Please select both dates.', "Validation Error");
                return;
            }

            this.axios.get('/api/reports/teacher_attendance', {
                params: { designation_id, department_id, from_date, to_date }
            }).then(response => {
                const result = response.data.result || {};

                this.$set(this, 'combinedEmployeeData', result)

                if (Array.isArray(this.combinedEmployeeData.teachers) && this.combinedEmployeeData.teachers.length === 0) {
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
            const headers = ["SL", "teacher", ...this.attendanceDates.map(date => date.toFormat("dd"))];

            const data = this.combinedEmployeeData.teachers.map((teacher, index) => {
                const row = [index + 1, `${teacher.name} (${teacher.id})`];

                this.attendanceDates.forEach(date => {
                    const status = teacher.checkInStatus[date.date_formatted];
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
            XLSX.utils.book_append_sheet(workbook, worksheet, "Teacher Attendance");

            XLSX.writeFile(workbook, "teacher_attendance_report.xlsx");
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
        this.getGeneralData(['designation', 'employeeDepartment']);
    },
}
</script>

<style scoped>
.sticky-column {
    position: sticky;
    left: 0;
    background-color: white;
    z-index: 1;
    box-shadow: 1px 0 0 #ddd;
}

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

.present-teacher-checkbox {
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
