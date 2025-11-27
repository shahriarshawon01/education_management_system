<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :default-object="{ school_id: '', dormitory_id: '', department_id: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Dormitory Member Report"
                    :table-responsive="false" :defaultSearchButton="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Dormitory Member Report" :default-add-button="false">
                            <div @click="printData('printDiv')">
                                <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i>
                                    Print</button>
                            </div>
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.dormitory_id" name="dormitory_id" class="form-control">
                                <option value="">Select Dormitory</option>
                                <template v-for="(eachData, index) in requiredData.dormitory">
                                    <option :value="eachData.id">{{ eachData.dormitory_name }}
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" @click="fetchDormitory" :disabled="loading">
                                {{ loading ? 'Loading...' : 'Get Data' }}
                            </button>
                            <Loader :visible="loading" />
                        </div>
                    </template>
                    <template v-slot:bottom_data>
                        <template v-if="localDataList.data && localDataList.data.length">
                              <div class="main-header mt-3">
                                <div class="row mb-3 mt-3">
                                    <schoolInfo title="Dormitories Member Report" />
                                    <div class="col-12 mt-2">
                                        <div class="report-details-card">
                                            <div class="value-detail" v-if="selectedDormitoryName">
                                                <strong>Dormitory Name:</strong>
                                                <span>{{ selectedDormitoryName || '-' }}</span>
                                            </div>
                                            <div class="value-detail">
                                                <strong>Print Date:</strong>
                                                <span>{{ formatDate(new Date()) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>User Info</th>
                                        <th>Mobile Number</th>
                                        <th>Dormitory Name</th>
                                        <th>Floor Number</th>
                                        <th>Room Number</th>
                                        <th>Seat Number</th>
                                        <th>Dormitory Address</th>
                                        <th>Admit Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in localDataList.data" :key="index">
                                        <td class="fw-medium">{{ index + 1 }}</td>
                                        <td class="student_style">
                                            <!-- Student -->
                                            <template v-if="data.dormitory_user_type == 1 && data.dormitory_user">
                                                <span><strong>Name:</strong> {{ data.dormitory_user.student_name_en ||
                                                    'N/A' }}</span><br />
                                                <span><strong>Student ID:</strong> {{ data.dormitory_user.student_roll
                                                    || 'N/A' }}</span><br />
                                                <span><strong>Department:</strong> {{
                                                    data.dormitory_user.department_name || 'N/A' }}</span>
                                            </template>

                                            <!-- Employee -->
                                            <template v-if="data.dormitory_user_type == 2 && data.dormitory_user">
                                                <span><strong>Name:</strong> {{ data.dormitory_user.name || 'N/A'
                                                    }}</span><br />
                                                <span><strong>Employee ID:</strong> {{ data.dormitory_user.employee_id
                                                    || 'N/A' }}</span><br />
                                                <span><strong>Department:</strong> {{
                                                    data.dormitory_user.department_name || 'N/A' }}</span>
                                            </template>
                                        </td>

                                        <td>
                                            <span v-if="data.dormitory_user_type == 1 && data.dormitory_user">
                                                {{ data.dormitory_user.student_phone || 'N/A' }}
                                            </span>
                                            <span v-if="data.dormitory_user_type == 2 && data.dormitory_user">
                                                {{ data.dormitory_user.phone || 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ data.dormitory_name }}</td>
                                        <td>{{ data.floor_number }}</td>
                                        <td>{{ data.room_number }}</td>
                                        <td>{{ data.seat_number }}</td>
                                        <td>{{ data.location }}</td>
                                        <td style="text-align: center;">{{ data.arrive_date }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <th colspan="5">Total Dormitory Users = {{ totalStudents }}</th>
                                    <th colspan="7">-</th>
                                </tfoot>
                            </table>
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
import schoolInfo from "../../../components/schoolInfo";
import Loader from "../../../components/loader.vue"
import axios from 'axios';
export default {
    name: "dormitoryReport",
    components: { PageTop, DataTable, schoolInfo, Loader },

    data() {
        return {
            localDataList: [],
            loading: false,
             selectedDormitoryName: "",
        };
    },
    methods: {
        fetchDormitory() {
            const { dormitory_id } = this.formFilter;

            if (!dormitory_id) {
                this.$toastr('error', 'Please select the dormitory.', "Validation Error");
                return;
            }

            if (this.loading) return;
            this.loading = true;

            const filterParams = { dormitory_id };

            axios.get("/api/reports/dormitories", { params: filterParams })
                .then(res => {
                    const response = res.data;
                    if (response.status === 5000 || !response.result?.data?.length) {
                        this.localDataList = { data: [] };
                        this.$toastr('warning', response.message || "No Data Found", "No Data Found");
                        return;
                    }

                    const result = response.result || {};
                    const dorm = this.requiredData?.dormitory?.find(d => d.id == dormitory_id);
                    this.selectedDormitoryName = dorm ? dorm.dormitory_name : "";

                    this.localDataList = { data: result.data || [] };
                })
                .catch(err => {
                    this.$toastr("error", "An error occurred while fetching data.", "Error");
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        formatDate(date) {
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = String(d.getFullYear()).slice(-2);
            return `${day}-${month}-${year}`;
        },
    },
    computed: {
        totalStudents() {
            return this.localDataList.data.length;
        },
        totalDormitories() {
            const dormitories = this.localDataList.data.map(data => data.dormitory_id);
            return new Set(dormitories).size;
        }
    },
    mounted() {
        const _this = this;
        _this.getGeneralData(['dormitory', 'department']);
    },
}
</script>
<style scoped>
.table thead th {
    background-color: #cccbcb;
    text-align: center;
}

.table tfoot th {
    background-color: #cccbcb;
    text-align: center;
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

.value-detail {
    margin-bottom: 2px;
    font-size: 16px;
    color: #34495e;
}

.value-detail strong {
    color: #2c3e50;
}

.value-detail span {
    margin-left: 10px;
    font-weight: 600;
    color: #7f8c8d;
}

/* .student_style {
    width: 220px !important;
    text-align: left;
    font-size: 13px !important;
} */
</style>
