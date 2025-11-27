<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]"
                    :default-object="{ school_id: '', department_id: '', designation_id: '', employee_type: '', job_status: '' }"
                    :defaultSearchButton="false" :defaultFilter="false" :default-pagination="false"
                    table-title="Employee Report" :table-responsive="false">

                    <!-- Page Top Slot -->
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Employee Report" :default-add-button="false">
                            <div @click="printData('printDiv')">
                                <button class="btn btn-outline-primary">
                                    <i class="fa-sharp fa-solid fa-print"></i> Print
                                </button>
                            </div>
                        </page-top>
                    </template>

                    <!-- Filter Slot -->
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.department_id" name="department_id">
                                <option value="">Select Department</option>
                                <option v-for="data in requiredData.employeeDepartment" :key="data.id" :value="data.id">
                                    {{ data.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.designation_id" name="designation_id">
                                <option value="">Select Designation</option>
                                <option v-for="data in requiredData.designation" :key="data.id" :value="data.id">
                                    {{ data.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.employee_type" name="employee_type">
                                <option value="">Employee Type</option>
                                <option value="1">Teacher</option>
                                <option value="2">Staff</option>
                                <option value="3">Support Staff</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.job_status" name="job_status">
                                <option value="">All Job Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                                <option value="2">Resigned</option>
                                <option value="3">Terminated</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" @click="getEmployee" :disabled="loading">
                                {{ loading ? 'Loading...' : 'Get Employee' }}
                            </button>
                        </div>
                        <Loader :visible="loading" />

                    </template>

                    <!-- Bottom Data Slot -->
                    <template v-slot:bottom_data>
                        <div id="printDiv">
                            <template v-if="empList.length > 0">
                                <div class="main-header mt-3">
                                    <div class="row mb-3 mt-3">
                                        <schoolInfo title="Employee Report" />
                                        <div class="row">
                                            <div class="col-6 text-start"></div>
                                            <div class="col-6 text-end">
                                                <strong>Print Date : {{ formatDate(new Date()) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Employee Name</th>
                                            <th>Employee ID</th>
                                            <th>Employee Type</th>
                                            <th>Designation</th>
                                            <th>Department</th>
                                            <th>Mobile</th>
                                            <th>E-mail</th>
                                            <th>Gender</th>
                                            <th>Job Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(data, index) in empList" :key="index">
                                            <td class="fw-medium">{{ index + 1 }}</td>
                                            <td>{{ data.name }}</td>
                                            <td class="text-center">{{ data.employee_id }}</td>
                                            <td style="text-align: center;"> {{ showEmployeeType(data.employee_type) }}
                                            </td>
                                            <td>{{ data.designation_name }}</td>
                                            <td>{{ data.department_name }}</td>
                                            <td>{{ data.phone }}</td>
                                            <td>{{ data.email }}</td>
                                            <td>{{ getGender(data.gender) }}</td>
                                            <td class="text-center">
                                                <span v-if="parseInt(data.status) === 0"
                                                    class="badge badge-soft-warning">Inactive</span>
                                                <span v-if="parseInt(data.status) === 1"
                                                    class="badge badge-soft-success">Active</span>
                                                <span v-if="parseInt(data.status) === 2"
                                                    class="badge badge-soft-danger">Resigned</span>
                                                <span v-if="parseInt(data.status) === 3"
                                                    class="badge badge-soft-warning">Terminated</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-success">
                                            <th colspan="3">Total Employee = {{ empList.length }}</th>
                                            <th colspan="7"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </template>
                        </div>
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
import axios from "axios";

export default {
    name: "employeeReport",
    components: { PageTop, DataTable, schoolInfo },
    data() {
        return {
            empList: [],
            loading: false
        };
    },
    methods: {
        showEmployeeType(type) {
            switch (type) {
                case 1: return 'Teacher';
                case 2: return 'Staff';
                case 3: return 'Support Staff';
                default: return '-';
            }
        },
        getEmployee() {
            const { department_id, designation_id, employee_type, job_status, from_date, to_date } = this.formFilter;
            if (this.loading) return;
            this.loading = true;

            const filterParams = {
                department_id,
                designation_id,
                employee_type,
                job_status,
                from_date,
                to_date
            };
            this.loading = true;
            axios.get("/api/reports/employee", { params: filterParams })
                .then(res => {
                    const response = res.data;
                    this.empList = response.status === 5000 ? [] : (response.result?.data || []);
                    if (response.status === 5000) {
                        this.$toastr("warning", response.message, "No Data Found");
                    }
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
            const day = String(d.getDate()).padStart(2, "0");
            const month = String(d.getMonth() + 1).padStart(2, "0");
            const year = String(d.getFullYear()).slice(-2);
            return `${day}-${month}-${year}`;
        },

        getGender(gender) {
            if (gender === 1) return "Male";
            if (gender === 2) return "Female";
            return "";
        },
    },

    mounted() {
        this.getGeneralData(['designation', 'employeeDepartment']);
    }
};
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
</style>
