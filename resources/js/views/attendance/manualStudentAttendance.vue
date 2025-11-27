<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :defaultSearchButton="false"
                    :default-object="{ section_id: '', department_id: '', session_id: '', class_id: '', attendance_date: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Student Attendance"
                    :table-responsive="false">

                    <template v-slot:page_top>
                        <div class="page-header-custom">
                            <page-top :default-object="{}" topPageTitle="Students Attendance"
                                :default-add-button="false">
                                <div @click="printData('printDiv')">
                                    <button class="btn-print-custom">
                                        <i class="fa-sharp fa-solid fa-print"></i>
                                        Print
                                    </button>
                                </div>
                            </page-top>
                        </div>
                    </template>

                    <template v-slot:filter>
                        <div class="filters-wrapper">
                            <div class="filters-grid">
                                <div class="filter-item">
                                    <label class="filter-label">Session</label>
                                    <select class="form-control form-select-custom" v-model="formFilter.session_id"
                                        name="session_id">
                                        <option value="">Select Session</option>
                                        <option v-for="(data, index) in requiredData.session" :key="index"
                                            :value="data.id">
                                            {{ data.title }}
                                        </option>
                                    </select>
                                </div>

                                <div class="filter-item">
                                    <label class="filter-label">Class</label>
                                    <select v-model="formFilter.class_id" name="class_id"
                                        class="form-control form-select-custom" @change="getGeneralData({
                                            section: { class_id: formFilter.class_id },
                                            departments: { class_id: formFilter.class_id }
                                        })">
                                        <option value="">Select Class</option>
                                        <option v-for="(eachData, index) in requiredData.class" :key="index"
                                            :value="eachData.id">
                                            {{ eachData.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="filter-item">
                                    <label class="filter-label">Section</label>
                                    <select v-model="formFilter.section_id" name="section_id"
                                        class="form-control form-select-custom">
                                        <option value="">Select Section</option>
                                        <option v-for="(eachData, index) in requiredData.section" :key="index"
                                            :value="eachData.id">
                                            {{ eachData.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="filter-item">
                                    <label class="filter-label">Department</label>
                                    <select v-model="formFilter.department_id" name="department_id"
                                        class="form-control form-select-custom">
                                        <option value="">Select Department</option>
                                        <option v-for="(eachData, index) in requiredData.departments" :key="index"
                                            :value="eachData.id">
                                            {{ eachData.department_name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="filter-item">
                                    <label class="filter-label">Attendance Date</label>
                                    <datepicker v-model="formFilter.attendance_date" name="attendance_date"
                                        input_class="form-control form-select-custom" aria-placeholder="Select Date">
                                    </datepicker>
                                </div>

                                <div class="filter-item filter-action">
                                    <button class="btn-fetch-custom" @click="fetchData" :disabled="loading">
                                        <i v-if="loading" class="fa fa-spinner fa-spin"></i>
                                        <i v-else class="fa fa-search"></i>
                                        {{ loading ? "Loading..." : "Get Data" }}
                                    </button>
                                </div>
                                <Loader :visible="loading" />

                            </div>
                        </div>
                    </template>

                    <template v-slot:bottom_data>
                        <form @submit.prevent="submitData()" class="attendance-form-wrapper">
                            <div class="bulk-action-section">
                                <div class="bulk-action-header">
                                    <i class="fa fa-users-cog"></i>
                                    <span class="bulk-label">Set attendance for all students:</span>
                                </div>
                                <div class="bulk-action-options">
                                    <label class="radio-option radio-present">
                                        <input type="radio" value="1" @change="setAllAttendance(1)" name="all">
                                        <span class="radio-icon"><i class="fa fa-check-circle"></i></span>
                                        <span class="radio-text">Present</span>
                                    </label>
                                    <label class="radio-option radio-late">
                                        <input type="radio" value="2" @change="setAllAttendance(2)" name="all">
                                        <span class="radio-icon"><i class="fa fa-clock"></i></span>
                                        <span class="radio-text">Late</span>
                                    </label>
                                    <label class="radio-option radio-absent">
                                        <input type="radio" value="3" @change="setAllAttendance(3)" name="all">
                                        <span class="radio-icon"><i class="fa fa-times-circle"></i></span>
                                        <span class="radio-text">Absent</span>
                                    </label>
                                    <label class="radio-option radio-holiday">
                                        <input type="radio" value="4" @change="setAllAttendance(4)" name="all">
                                        <span class="radio-icon"><i class="fa fa-calendar"></i></span>
                                        <span class="radio-text">Holiday</span>
                                    </label>
                                </div>
                            </div>

                            <div class="table-wrapper">
                                <table class="table attendance-table-custom table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="th-sl">SL</th>
                                            <th class="th-name">Student Name</th>
                                            <th class="th-roll">Student ID</th>
                                            <th class="th-roll"> Student Roll</th>
                                            <th class="th-attendance">Attendance Status</th>
                                            <th class="th-remarks">Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(data, index) in studentList" :key="index" class="student-row">
                                            <td class="td-sl">{{ index + 1 }}</td>
                                            <td class="td-name">
                                                <div class="student-info">
                                                    <div class="student-avatar">
                                                        <img class="mouse-action" :src="getImage(data.photo)"
                                                            @click="openFile(getImage(data.photo))">
                                                    </div>
                                                    <span class="student-name-text">{{ data.student_name }}</span>
                                                </div>
                                            </td>
                                            <td class="td-roll">{{ data.student_roll }}</td>
                                            <td class="td-roll">{{ data.merit_roll }}</td>
                                            <td class="td-attendance">
                                                <div class="attendance-options">
                                                    <label class="attendance-radio radio-present-sm">
                                                        <input type="radio" value="1" v-model="data.attendance"
                                                            :name="'attendance-' + index">
                                                        <span class="radio-label">Present</span>
                                                    </label>
                                                    <label class="attendance-radio radio-late-sm">
                                                        <input type="radio" value="2" v-model="data.attendance"
                                                            :name="'attendance-' + index">
                                                        <span class="radio-label">Late</span>
                                                    </label>
                                                    <label class="attendance-radio radio-absent-sm">
                                                        <input type="radio" value="3" v-model="data.attendance"
                                                            :name="'attendance-' + index">
                                                        <span class="radio-label">Absent</span>
                                                    </label>
                                                    <label class="attendance-radio radio-holiday-sm">
                                                        <input type="radio" value="4" v-model="data.attendance"
                                                            :name="'attendance-' + index">
                                                        <span class="radio-label">Holiday</span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="td-remarks">
                                                <input type="text" v-model="data.remarks" :name="'remarks-' + index"
                                                    class="form-control remarks-input-custom"
                                                    placeholder="Add remarks...">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-footer-custom">
                                <button class="btn-submit-custom" type="submit">
                                    <i class="fa-solid fa-save"></i>
                                    <span>Save Attendance</span>
                                </button>
                            </div>
                        </form>
                    </template>
                </data-table>
            </div>
        </div>
    </div>
</template>

<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import Loader from "../../components/loader.vue"
import axios from 'axios';

export default {
    name: "studentAttendance",
    components: { PageTop, Pagination, DataTable,Loader },

    data() {
        return {
            studentList: [],
            loading: false,
        };
    },

    methods: {

        setAllAttendance(value) {
            this.studentList.forEach((student, index) => {
                this.$set(student, 'attendance', value);
            });
        },

        fetchData() {
            const { session_id, class_id, section_id, department_id, attendance_date } = this.formFilter;

            if (!session_id) {
                this.$toastr("error", "Please select a session.", "Validation Error");
                return;
            }

            if (!class_id) {
                this.$toastr("error", "Please select a class.", "Validation Error");
                return;
            }

            if (!section_id) {
                this.$toastr("error", "Please select a section.", "Validation Error");
                return;
            }
            if (!department_id) {
                this.$toastr("error", "Please select a department.", "Validation Error");
                return;
            }

            if (this.loading) return;
            this.loading = true;

            const params = { session_id, class_id, section_id, department_id, attendance_date };

            axios.get("/api/get_manual_student_attendance", { params })
                .then((res) => {
                    const response = res.data;
                    this.studentList = response.data || response.result?.data || response.students || [];

                    if (!this.studentList.length) {
                        this.$toastr("warning", "No Data Found", "No Data");
                    } else {
                        console.log("✅ Loaded studentList:", this.studentList);
                    }
                })
                .catch((err) => {
                    console.error(err);
                    this.$toastr("error", "An error occurred while fetching data.", "Error");
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        submitData() {
            const _this = this;
            const submittedValue = _this.studentList.map(data => ({
                student_id: data.student_roll,
                session_id: _this.formFilter.session_id,
                class_id: _this.formFilter.class_id,
                section_id: _this.formFilter.section_id,
                department_id: _this.formFilter.department_id,
                check_in: _this.formFilter.attendance_date,
                status: data.attendance,
                remarks: data.remarks,
            }));
            const URL = baseUrl + "/api/submit_manual_student_attendance";
            _this.axios.post(URL, submittedValue)
                .then(response => {
                    if (response.data.status === 2000) {
                        _this.$toastr('success', response.data.message, "Success");
                    } else {
                        _this.$toastr('error', response.data.message, "Error");
                    }
                })
                .catch(() => {
                    _this.$toastr('error', 'Failed to submit data.', "Error");
                });
        },
    },
    mounted() {
        const _this = this;
        _this.getGeneralData(['class', 'session']);
    },
}
</script>

<style scoped>
.filters-wrapper {
    background: #ffffff;
    padding: 4px;
    border-radius: 10px;
    margin-bottom: 0px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    border-top-left-radius: 0 !important;
    border-top-right-radius: 0 !important;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    align-items: end;
}

.filter-item {
    display: flex;
    flex-direction: column;
}

.filter-label {
    font-size: 11px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.2px;
}

.form-select-custom {
    height: 32px !important;
    border: 1.5px solid #d1d5db !important;
    border-radius: 8px !important;
    font-size: 13px !important;
    padding: 4px 8px !important;
    transition: all 0.2s ease !important;
}

.form-select-custom:focus {
    border-color: #667eea !important;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
    outline: none !important;
}

.btn-fetch-custom {
    height: 36px;
    width: 100%;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.2);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-fetch-custom:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-fetch-custom:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-print-custom {
    background: white;
    color: #667eea;
    border: 2px solid #667eea;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-print-custom:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.attendance-form-wrapper {
    margin-top: 0px;
}

.bulk-action-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 5px;
    border-radius: 16px;
    margin-bottom: 8px;
    box-shadow: 0 4px 16px rgba(102, 126, 234, 0.25);
}

.bulk-action-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
    color: white;
}

.bulk-action-header i {
    font-size: 20px;
}

.bulk-label {
    font-size: 16px;
    font-weight: 600;
    letter-spacing: 0.3px;
}

.bulk-action-options {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.radio-option {
    display: flex;
    align-items: center;
    gap: 10px;
    background: white;
    padding: 3px 12px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.radio-option:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.radio-option input[type="radio"] {
    display: none;
}

.radio-option input[type="radio"]:checked+.radio-icon {
    transform: scale(1.2);
}

.radio-icon {
    font-size: 20px;
    transition: all 0.3s ease;
}

.radio-text {
    font-weight: 600;
    font-size: 14px;
}

.radio-present {
    color: #28a745;
}

.radio-present:hover {
    background: #e8f5e9;
}

.radio-late {
    color: #ffc107;
}

.radio-late:hover {
    background: #fff8e1;
}

.radio-absent {
    color: #dc3545;
}

.radio-absent:hover {
    background: #ffebee;
}

.radio-holiday {
    color: #17a2b8;
}

.radio-holiday:hover {
    background: #e0f7fa;
}

.table-wrapper {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 24px;
}

.attendance-table-custom {
    width: 100%;
    margin-bottom: 0 !important;
    border-collapse: collapse;
}

.attendance-table-custom thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.attendance-table-custom th {
    color: white !important;
    font-weight: 600 !important;
    text-transform: uppercase;
    font-size: 12px !important;
    letter-spacing: 0.8px;
    padding: 10px 8px !important;
    text-align: center !important;
}

.student-row {
    transition: all 0.2s ease;
    border-bottom: 1px solid #f0f0f0;
}

.student-row:hover {
    background: #f8f9fa;
}

.attendance-table-custom td {
    padding: 8px !important;
    vertical-align: middle !important;
}

.td-sl {
    font-weight: 700;
    color: #6c757d;
    font-size: 14px;
    text-align: center !important;
}

.student-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.student-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 13px;
    flex-shrink: 0;
}

.student-name-text {
    font-weight: 600;
    color: #2c3e50;
}

.td-roll,
.td-class {
    color: #6c757d;
    font-size: 14px;
}

.td-roll,
.td-attendance {
    text-align: center !important;
    vertical-align: middle !important;
}

.td-attendance .attendance-options {
    justify-content: center;
    align-items: center;
}


.attendance-options {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    text-align: center !important;
}

.attendance-radio {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 2px solid transparent;
}

.attendance-radio input[type="radio"] {
    margin: 0;
    cursor: pointer;
}

.radio-label {
    font-size: 13px;
    font-weight: 500;
}

.radio-present-sm {
    background: #e8f5e9;
    color: #28a745;
}

.radio-present-sm:hover {
    border-color: #28a745;
}

.radio-late-sm {
    background: #fff8e1;
    color: #ffc107;
}

.radio-late-sm:hover {
    border-color: #ffc107;
}

.radio-absent-sm {
    background: #ffebee;
    color: #dc3545;
}

.radio-absent-sm:hover {
    border-color: #dc3545;
}

.radio-holiday-sm {
    background: #e0f7fa;
    color: #17a2b8;
}

.radio-holiday-sm:hover {
    border-color: #17a2b8;
}

.remarks-input-custom {
    border: 2px solid #e0e6ed !important;
    border-radius: 8px !important;
    font-size: 13px !important;
    transition: all 0.2s ease !important;
    padding: 8px 12px !important;
}

.remarks-input-custom:focus {
    border-color: #667eea !important;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
    outline: none !important;
}

.form-footer-custom {
    background: white;
    padding: 5px;
    border-radius: 16px;
    display: flex;
    justify-content: center;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.btn-submit-custom {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 16px 48px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-submit-custom:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-submit-custom i {
    font-size: 18px;
}

@media (max-width: 768px) {
    .filters-grid {
        grid-template-columns: 1fr;
    }

    .bulk-action-options {
        flex-direction: column;
    }

    .radio-option {
        width: 100%;
        justify-content: center;
    }

    .attendance-options {
        flex-direction: column;
    }

    .table-wrapper {
        overflow-x: auto;
    }
}
</style>
