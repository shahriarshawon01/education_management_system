<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]"
                    :default-object="{ session_year_id: '', class_id: '', from_date: '', to_date: '', section_id: '', department_id: '', activityStatus: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Student Details Report"
                    :table-responsive="false" :defaultSearchButton="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Student Details Report"
                            :default-add-button="false">
                            <div @click="printData('printDiv')">
                                <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i>
                                    Print</button>
                            </div>
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.class_id" name="class_id" class="form-control" @change="
                                getGeneralData({
                                    students: { class_id: formFilter.class_id },
                                    section: { class_id: formFilter.class_id },
                                    departments: { class_id: formFilter.class_id },
                                })
                                ">
                                <option value="">Select Class</option>
                                <template v-for="(eachData, index) in requiredData.class">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div>
                                <select v-model="formFilter.section_id" name="section_id" class="form-control">
                                    <option value="">Select Section</option>
                                    <template v-for="(eachData, index) in requiredData.section">
                                        <option :value="eachData.id">{{ eachData.name }}</option>
                                    </template>
                                </select>
                            </div>
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
                            <select class="form-control" v-model="formFilter.session_year_id" name="session_year_id">
                                <option value="">Select Session</option>
                                <template v-for="(data, index) in requiredData.session">
                                    <option :value="data.id">{{ data.title }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.activityStatus" name="activityStatus">
                                <option value="">Select Status</option>
                                <option :value="1">Active</option>
                                <option :value="0">In Active</option>
                                <option :value="2">Dropout</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <a class="btn btn-primary" @click="getStudent" :disabled="loading">
                                Get Student
                            </a>
                        </div>
                        <!-- loader -->
                        <Loader :visible="loading" />
                    </template>

                    <template v-slot:bottom_data>
                        <div class="row mb-3 no-print">
                            <div class="col">
                                <h5>Select Columns to Display</h5>
                                <div class="d-flex flex-wrap">
                                    <div v-for="(header, index) in tableHeaders" :key="index" class="form-check me-3">
                                        <input type="checkbox" class="form-check-input" :id="`column-${index}`"
                                            :value="header.key" v-model="selectedColumns" />
                                        <label class="form-check-label" :for="`column-${index}`">{{ header.label
                                        }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <template v-if="dataList.data" id="printDiv">

                            <!-- Reusable School Info Component -->
                            <schoolInfo title="Student Details Report" />

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="student-details-card">
                                        <div class="student-detail">
                                            <strong>Class:</strong>
                                            <span>{{ dataList.data[0]?.class_name || 'N/A' }}</span>
                                        </div>
                                        <div class="student-detail">
                                            <strong>Section:</strong>
                                            <span>{{ dataList.data[0]?.section_name || 'N/A' }}</span>
                                        </div>
                                        <div class="student-detail">
                                            <strong>Session:</strong>
                                            <span>{{ dataList.data[0]?.session_name || 'N/A' }}</span>
                                        </div>
                                        <div class="student-detail">
                                            <strong>Print Date:</strong>
                                            <span>{{ formatDate(new Date()) }}</span>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div style="overflow-x: auto;">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th v-for="(header, index) in tableHeaders" :key="index"
                                                v-if="isColumnVisible(header.key)">
                                                {{ header.label }}
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr v-for="(data, index) in dataList.data" :key="index"
                                            class="text-align-middle">
                                            <td v-for="header in tableHeaders" :key="header.key"
                                                v-if="isColumnVisible(header.key)">

                                                <!-- SL -->
                                                <span v-if="header.key === 'index'">{{ index + 1 }}</span>

                                                <!-- Admission Date -->
                                                <span v-else-if="header.key === 'admission_date'">{{
                                                    formatDate(data.admission_date) }}</span>

                                                <!-- Student Roll -->
                                                <span v-else-if="header.key === 'student_id'">{{ data.student_roll
                                                    }}</span>

                                                <!-- Class Roll -->
                                                <span v-else-if="header.key === 'class_roll'">{{ data.merit_roll
                                                    }}</span>

                                                <!-- Class / Section / Session / Department -->
                                                <span v-else-if="header.key === 'class'">{{ data.class_name }}</span>
                                                <span v-else-if="header.key === 'section'">{{ data.section_name
                                                    }}</span>
                                                <span v-else-if="header.key === 'session'">{{ data.session_name
                                                    }}</span>
                                                <span v-else-if="header.key === 'department'">{{ data.department_name ||
                                                    '' }}</span>

                                                <!-- Birth Certificate -->
                                                <span v-else-if="header.key === 'birth_certificate_no'">{{
                                                    data.birth_certificate_no }}</span>

                                                <!-- DOB -->
                                                <span v-else-if="header.key === 'date_of_birth'">{{
                                                    formatDate(data.date_of_birth) }}</span>

                                                <!-- Blood, Gender, Religion -->
                                                <span v-else-if="header.key === 'blood_group'">{{ data.blood_group
                                                    }}</span>
                                                <span v-else-if="header.key === 'gender'">{{ getGenderName(data.gender)
                                                    }}</span>
                                                <span v-else-if="header.key === 'religion'">{{
                                                    getReligionName(data.religion) }}</span>

                                                <!-- Father / Mother -->
                                                <span v-else-if="header.key === 'father_name_en'">{{ data.father_name_en
                                                    || '' }}</span>
                                                <span v-else-if="header.key === 'father_name_bn'">{{ data.father_name_bn
                                                    || '' }}</span>
                                                <span v-else-if="header.key === 'father_nid'">{{ data.father_nid || ''
                                                    }}</span>
                                                <span v-else-if="header.key === 'father_phone'">{{ data.father_phone
                                                    }}</span>

                                                <span v-else-if="header.key === 'mother_name_en'">{{ data.mother_name_en
                                                    || '' }}</span>
                                                <span v-else-if="header.key === 'mother_name_bn'">{{ data.mother_name_bn
                                                    || '' }}</span>
                                                <span v-else-if="header.key === 'mother_nid'">{{ data.mother_nid || ''
                                                    }}</span>
                                                <span v-else-if="header.key === 'mother_phone'">{{ data.mother_phone
                                                    }}</span>

                                                <!-- Address -->
                                                <span v-else-if="header.key === 'address'">{{ data.village_name || ''
                                                    }}</span>

                                                <!-- Status -->
                                                <span v-else-if="header.key === 'status'"
                                                    style="display: inline-block; text-align:center;"
                                                    :class="statusBadgeClass(data.status)">
                                                    {{ getStatusLabel(data.status) }}
                                                </span>

                                                <!-- Photo -->
                                                <span v-else-if="header.key === 'photo'">
                                                    <template v-if="data.photo">
                                                        <img class="stamp-image" :src="getImage(data.photo)"
                                                            @click="openFile(getImage(data.photo))" />
                                                    </template>
                                                    <template v-else>N/A</template>
                                                </span>

                                                <!-- Fallback (default display by key) -->
                                                <span v-else>{{ data[header.key] }}</span>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

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
import Pagination from "../../../plugins/pagination/pagination";
import PageTop from "../../../components/pageTop";
import schoolInfo from "../../../components/schoolInfo";
import Loader from "../../../components/loader.vue"
import axios from 'axios';
export default {
    name: "studentReport",
    components: { PageTop, Pagination, DataTable, schoolInfo, Loader },

    data() {
        return {
            tableHeaders: [
                { key: 'index', label: 'Sl' },
                { key: 'admission_date', label: 'Admission Date' },
                { key: 'student_name_en', label: 'Name En' },
                { key: 'student_name_bn', label: 'Name Bn' },
                { key: 'student_id', label: 'Student ID' },
                { key: 'class_roll', label: 'Roll' },
                { key: 'class', label: 'Class' },
                { key: 'section', label: 'Section' },
                { key: 'session', label: 'Session' },
                { key: 'department', label: 'Department' },
                { key: 'birth_certificate_no', label: 'Birth Certificate No' },
                { key: 'date_of_birth', label: 'Birth Date' },
                { key: 'blood_group', label: 'Blood Group' },
                { key: 'gender', label: 'Gender' },
                { key: 'religion', label: 'Religion' },

                { key: 'father_name_en', label: 'Father Name En' },
                { key: 'father_name_bn', label: 'Father Name Bn' },
                { key: 'father_nid', label: 'Father NID' },
                { key: 'father_phone', label: 'Father Mobile' },

                { key: 'mother_name_en', label: 'Mother Name En' },
                { key: 'mother_name_bn', label: 'Mother Name Bn' },
                { key: 'mother_nid', label: 'Mother NID' },
                { key: 'mother_phone', label: 'Mother Phone' },

                { key: 'address', label: 'Address' },
                { key: 'photo', label: 'Photo' },
                { key: 'status', label: 'Status' },
            ],
            selectedColumns: ['index', 'student_name_en', 'student_id', 'class_roll', 'class', 'status'],
            loading: false,
        };

    },
    methods: {

        getStudent() {
            const { class_id, section_id, department_id, session_year_id, activityStatus } = this.formFilter;

            const filterParams = {
                class_id,
                section_id,
                department_id,
                session_year_id,
                activityStatus
            };
            this.loading = true;

            axios.get("/api/reports/students", { params: filterParams })
                .then(res => {
                    const response = res.data;

                    if (response.status === 5000) {
                        this.dataList.data = [];
                        this.$toastr('warning', response.message, "No Data Found");
                        return;
                    }

                    const result = response.result;
                    this.dataList.data = result.data || [];
                    this.totalData = result || [];

                    this.sectionName = result.section_name || '';
                    this.className = result.class_name || '';
                    this.departmentName = result.department_name || '';
                    this.sessionName = result.session_name || '';
                })

                .catch((error) => {
                    console.error("Failed to fetch collection data:", error);
                    this.$toastr('error', 'Failed to fetch data from the server.', 'Error');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        toggleSelectAll(event) {
            if (event.target.checked) {
                this.selectedColumns = this.tableHeaders.map((header) => header.key);
            } else {
                this.selectedColumns = [];
            }
        },

        isColumnVisible(columnKey) {
            return this.selectedColumns.includes(columnKey);
        },

        formatDate(date) {
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = String(d.getFullYear()).slice(-2);
            return `${day}-${month}-${year}`;
        },

        getReligionName(religion) {
            switch (parseInt(religion)) {
                case 1:
                    return 'Islam';
                case 2:
                    return 'Hindu';
                case 3:
                    return 'Buddhist';
                case 4:
                    return 'Christian';
                default:
                    return 'Others';
            }
        },
        getGenderName(religion) {
            switch (parseInt(religion)) {
                case 1:
                    return 'Male';
                case 2:
                    return 'Female';
                case 3:
                    return 'Other';
                default:
                    return 'Unknown';
            }
        },

        getStatusLabel(status) {
            switch (Number(status)) {
                case 1:
                    return "Active";
                case 0:
                    return "Inactive";
                case 2:
                    return "Dropout";
                default:
                    return "Unknown";
            }
        },

        statusBadgeClass(status) {
            switch (Number(status)) {
                case 1:
                    return "badge-status bg-success text-dark";
                case 0:
                    return "badge-status bg-danger text-dark";
                case 2:
                    return "badge-status bg-warning text-dark";
                default:
                    return "badge-status bg-secondary text-dark";
            }
        }
    },
    computed: {
        totalStudent() {
            return this.dataList.data.length;
        }
    },
    mounted() {
        const _this = this;
        _this.getGeneralData(['class', 'session']);

    },
}
</script>
<style scoped>
.table thead th {
    background-color: #cccbcb;
    text-align: center;
}

.text-align-middle {
    vertical-align: middle;
}

.stamp-cell {
    width: 60px !important;
    height: 60px !important;
    text-align: center !important;
}

.stamp-image {
    width: 60px !important;
    height: 80px !important;
    object-fit: cover !important;
    border-radius: 4px;
    border: 1px solid #ccc;
    display: block;
    margin: auto;
}

.report-header {
    text-align: center;
    margin-bottom: 6px;
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

.student-details-card {
    margin-bottom: -20px;
}

.student-detail {
    margin-bottom: 2px;
    font-size: 16px;
    color: #34495e;
}

.student-detail strong {
    color: #2c3e50;
}

.student-detail span {
    margin-left: 10px;
    font-weight: 600;
    color: #7f8c8d;
}

.badge-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 60px;
    padding: 2px 10px;
    font-weight: 600;
    border-radius: 20px;
    font-size: 13px;
    text-transform: capitalize;
    letter-spacing: 0.3px;
    transition: all 0.2s ease-in-out;
}

.badge-status:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

@media print {
    .no-print {
        display: none !important;
    }

    .student-details-card {
        margin-bottom: -20px;
        margin-left: 15px;
    }

    .table,
    .table th,
    .table td {
        border: 1px solid #000;
        border-collapse: collapse;
    }
}
</style>
