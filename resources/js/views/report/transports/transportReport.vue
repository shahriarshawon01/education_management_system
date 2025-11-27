<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :default-object="{ school_id: '', transport_id: '', department_id: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Transport Member Report"
                    :table-responsive="false" :defaultSearchButton="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Transport Member Report" :default-add-button="false">
                            <div @click="printData('printDiv')">
                                <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i>
                                    Print</button>
                            </div>
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.transport_id" name="transport_id">
                                <option value="">Select Transport</option>
                                <template v-for="(data, index) in requiredData.transports">
                                    <option :value="data.id">{{ data.transport_name }}
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" @click="fetchTransport" :disabled="loading">
                                {{ loading ? 'Loading...' : 'Get Data' }}
                            </button>
                            <Loader :visible="loading" />
                        </div>
                    </template>
                    <template v-slot:bottom_data>
                        <template v-if="dataList.data && dataList.data.length">
                            <!-- <div class="main-header mt-3">
                                <div class="row mb-3 mt-3">
                                    <schoolInfo title="Transport Report" />

                                    <div class="row">
                                        <div class="col-6 text-start"></div>
                                        <div class="col-6 text-end">
                                            <strong>Print Date : {{ formatDate(new Date()) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                             <div class="main-header mt-3">
                                <div class="row mb-3 mt-3">
                                    <schoolInfo title="Transports Member Report" />
                                    <div class="col-12 mt-2">
                                        <div class="report-details-card">
                                            <div class="value-detail" v-if="selectedTransportName">
                                                <strong>Transport Name:</strong>
                                                <span>{{ selectedTransportName }}</span>
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
                                        <th>Transport Name</th>
                                        <th>Transport Number</th>
                                        <th>Transport Address</th>
                                        <th>Admit Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in dataList.data" :key="index">
                                        <td class="fw-medium">{{ index + 1 }}</td>
                                        <td class="student_style">
                                            <!-- Student -->
                                            <template v-if="data.transport_user_type == 1 && data.transport_user">
                                                <span><strong>Name:</strong> {{ data.transport_user.student_name_en ||
                                                    'N/A' }}</span><br />
                                                <span><strong>Student ID:</strong> {{ data.transport_user.student_roll
                                                    || 'N/A' }}</span><br />
                                                <span><strong>Department:</strong> {{
                                                    data.transport_user.department_name || 'N/A' }}</span>
                                            </template>

                                            <!-- Employee -->
                                            <template v-if="data.transport_user_type == 2 && data.transport_user">
                                                <span><strong>Name:</strong> {{ data.transport_user.name || 'N/A'
                                                    }}</span><br />
                                                <span><strong>Employee ID:</strong> {{ data.transport_user.employee_id
                                                    || 'N/A' }}</span><br />
                                                <span><strong>Department:</strong> {{
                                                    data.transport_user.department_name || 'N/A' }}</span>
                                            </template>
                                        </td>

                                        <td>
                                            <span v-if="data.transport_user_type == 1 && data.transport_user">
                                                {{ data.transport_user.student_phone || 'N/A' }}
                                            </span>
                                            <span v-if="data.transport_user_type == 2 && data.transport_user">
                                                {{ data.transport_user.phone || 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ data.transport_name }}</td>
                                        <td>{{ data.transport_no }}</td>
                                        <td>{{ data.address }}</td>
                                        <td style="text-align: center;">{{ data.assign_date }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <th colspan="5">Total Transport Users = {{ totalStudents }}</th>
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
    name: "transportReport",
    components: { PageTop, DataTable, schoolInfo, Loader },

    data() {
        return {
            dataList: [],
            loading: false,
            selectedTransportName: "",
        };
    },
    methods: {
        fetchTransport() {
            const { transport_id } = this.formFilter;

            if (!transport_id) {
                this.$toastr('error', 'Please select the transport.', "Validation Error");
                return;
            }

            if (this.loading) return;
            this.loading = true;

            const filterParams = { transport_id };

            axios.get("/api/reports/transports", { params: filterParams })
                .then(res => {
                    const response = res.data;

                    if (response.status === 5000 || !response.result?.data?.length) {
                        this.dataList = { data: [] };
                        this.$toastr('warning', response.message || "No Data Found", "No Data Found");
                        return;
                    }

                    const result = response.result || {};
                     const dorm = this.requiredData?.transports?.find(d => d.id == transport_id);
                    this.selectedTransportName = dorm ? `${dorm.transport_name} - ${dorm.route_name}` : "";
                    this.dataList = { data: result.data || [] };
                })
                .catch(err => {
                    console.error(err);
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
            return this.dataList.data.length;
        },
    },
    mounted() {
        const _this = this;
        _this.getGeneralData(['transports', 'department']);
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
