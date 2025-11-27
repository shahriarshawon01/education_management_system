<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]"
                    :default-object="{ school_id: '', transport_id: '', month: '', year: '',user_type:'' }" :defaultFilter="false"
                    :default-pagination="false" table-title="Transports Monthly Invoice Report"
                    :table-responsive="false" :defaultSearchButton="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Transports Monthly Invoice Report"
                            :default-add-button="false">
                            <div @click="printData('printDiv')">
                                <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i>
                                    Print</button>
                            </div>
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                              <select v-model="formFilter.month" class="form-control" name="month">
                                <option value="">Select Month</option>
                                <option v-for="(month, index) in months" :key="index"
                                    :value="String(index + 1).padStart(2, '0')">
                                    {{ month }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                              <select name="years" v-model="formFilter.years" class="form-control">
                                <option value="">Select Year</option>
                                <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select v-model="formFilter.transport_id" name="transport_id" class="form-control">
                                <option value="">Select transport</option>
                                <template v-for="(eachData, index) in requiredData.transports">
                                    <option :value="eachData.id">{{ eachData.transport_name }} - {{
                                        eachData.route_name }}</option>
                                </template>
                            </select>
                        </div>
                         <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.user_type" name="user_type">
                                <option value="" selected>Select user</option>
                                <option value="1">Student</option>
                                <option value="2">Employee</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-primary" @click="fetchTransport" :disabled="loading">
                                {{ loading ? 'Loading...' : 'Get Data' }}
                            </button>
                        </div>
                    </template>

                    <template v-slot:bottom_data>
                        <template v-if="localDataList.data && localDataList.data.length">
                            <div class="main-header mt-3">
                                <div class="row mb-3 mt-3">
                                    <schoolInfo title="Transports Monthly Invoice Report" />
                                    <div class="col-12 mt-2">
                                        <div class="report-details-card">
                                            <div class="value-detail" v-if="selectedTransportName">
                                                <strong>Transport Name:</strong>
                                                <span>{{ selectedTransportName }}</span>
                                            </div>
                                            <div class="value-detail">
                                                <strong>Reporting Month:</strong>
                                                <span>{{ selectedMonthName || '-' }}</span>
                                            </div>
                                            <div class="value-detail">
                                                <strong>Reporting Year:</strong>
                                                <span>{{ formFilter.year }}</span>
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
                                        <th>Transport Info</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in localDataList.data" :key="index">
                                        <td style="vertical-align: middle; text-align: center;">{{ index + 1 }}</td>

                                        <td>
                                            <!-- Student -->
                                            <template v-if="data.transport_user_type == 1 && data.transport_user">
                                                <span><strong>Name:</strong> {{ data.transport_user.student_name_en ||
                                                    'N/A' }} ({{ data.transport_user.student_roll
                                                        || 'N/A' }})</span><br />
                                                <span><strong>Merit Roll:</strong> {{ data.transport_user.merit_roll
                                                    || 'N/A' }}</span><br />
                                                <span><strong>Type:</strong> {{ data.transport_user.type || 'N/A'
                                                    }}</span><br />
                                            </template>

                                            <!-- Employee -->
                                            <template v-if="data.transport_user_type == 2 && data.transport_user">
                                                <span><strong>Name:</strong> {{ data.transport_user.name || 'N/A'
                                                    }} ({{ data.transport_user.employee_id
                                                        || 'N/A' }})</span><br />
                                                <span><strong>Designation:</strong> {{
                                                    data.transport_user.designation_name || 'N/A'
                                                    }}</span><br />
                                                <span><strong>Type:</strong> {{ data.transport_user.type || 'N/A'
                                                    }}</span>
                                            </template>
                                        </td>

                                        <td class="student_style">
                                            <template>
                                                <span><strong>Name:</strong> {{ data.transport_name }}</span><br>
                                                <span><strong>Number:</strong> {{ data.transport_no }}</span><br>
                                                <span><strong>Drop Point:</strong> {{ data.drop_point }}</span><br>
                                            </template>
                                        </td>
                                        <td
                                            style="vertical-align: middle; text-align: right; background-color: #cccbcb;">
                                            {{ formatAmount(data.invoice_amount) }}
                                        </td>

                                    </tr>

                                </tbody>
                                <tfoot>
                                    <th colspan="3"
                                        style="vertical-align: middle; text-align: center; background-color: #cccbcb;">
                                        Total Transports
                                        Users = {{ totalStudents }}</th>
                                    <th class="text-end">Total Payable = {{ formatAmount(localDataList.total_payable) }}
                                    </th>
                                    <!-- <th colspan="7"></th> -->
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
import axios from 'axios';
export default {
    name: "dormitoryReport",
    components: { PageTop, DataTable, schoolInfo },

    data() {
        const today = new Date();
        const currentMonth = today.getMonth() + 1;
        const currentYear = today.getFullYear();
        return {
            localDataList: [],
            loading: false,
            selectedTransportName: "",
            selectedMonthName: "",
            selectedYear: "",
             years: [],
             months: [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ],
        };
    },
    methods: {
        fetchTransport() {
            const { transport_id, month, year,user_type } = this.formFilter;
            this.selectedTransportName = "";
            if (this.loading) return;
            this.loading = true;


            const filterParams = { transport_id };
            if (month) filterParams.month = month;
            if (year) filterParams.year = year;
            if (user_type) filterParams.user_type = user_type;

            axios.get("/api/reports/transports_monthly_invoice_report", { params: filterParams })
                .then(res => {
                    const response = res.data;

                    if (response.status === 5000 || !response.result?.data?.length) {

                        this.dataList = { data: [], total_payable: 0 };
                        this.$toastr('warning', response.message || "No Data Found", "No Data Found");
                        return;
                    }

                    const result = response.result || {};

                    this.localDataList = {
                        data: result.data || [],
                        total_payable: result.total_payable || 0,
                    };
                    const dorm = this.requiredData?.transports?.find(d => d.id == transport_id);
                    this.selectedTransportName = dorm ? `${dorm.transport_name} - ${dorm.route_name}` : "";
                 
                     this.selectedMonthName = this.getMonthName(month);
                    this.selectedYear = year;
                })
                .catch(err => {
                    this.$toastr("error", "An error occurred while fetching data.", "Error");
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        formatAmount(amount) {
            if (!amount) return "0.00";
            return parseFloat(amount).toFixed(2);
        },
        getMonthName(month) {
            const monthNames = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];
            return monthNames[parseInt(month, 10) - 1] || "-";
        },

        formatDate(date) {
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = String(d.getFullYear()).slice(-2);
            return `${day}-${month}-${year}`;
        },
         assign() {
            this.formFilter.month = this.formFilter.month || String(new Date().getMonth() + 1).padStart(2, '0');
            this.formFilter.years = this.formFilter.years || new Date().getFullYear();
        },
    },
    computed: {
        totalStudents() {
            return this.localDataList.data.length;
        },
        totalDormitories() {
            const dormitories = this.localDataList.data.map(data => data.transport_id);
            return new Set(dormitories).size;
        }
    },
    mounted() {
        const _this = this;
        _this.getGeneralData(['transports', 'department']);
        const today = new Date();
        const currentMonth = String(today.getMonth() + 1).padStart(2, '0');
        const currentYear = today.getFullYear();
        _this.formFilter.month = currentMonth;
        _this.formFilter.year = currentYear;

         for (let i = currentYear - 1; i <= currentYear + 5; i++) {
            _this.years.push(i);
        }
        _this.assign();
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

.report-details-card {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-top: 10px;
    font-size: 15px;
}

.report-details-card .value-detail {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 6px;
}
</style>
