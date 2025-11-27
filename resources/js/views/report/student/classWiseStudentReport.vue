<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]"
                    :default-object="{ class_id: '', section_id: '', from_date: '', to_date: '', }"
                    :defaultFilter="false" :default-pagination="false" table-title="Class Wise Student Report"
                    :table-responsive="false" :defaultSearchButton="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Class Wise Student Report"
                            :default-add-button="false">
                            <button @click="printData('printDiv')" class="btn btn-outline-primary">
                                <i class="fa-sharp fa-solid fa-print"></i> Print
                            </button>
                        </page-top>
                    </template>

                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.class_id" name="class_id" class="form-control" @change="
                                getGeneralData({
                                    section: { class_id: formFilter.class_id },
                                })
                                ">
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
                            <datepicker class="form-control" v-model="formFilter.from_date"
                                placeholder="Admission From Date" name="from_date">
                            </datepicker>
                        </div>
                        <div class="col-md-2">
                            <datepicker class="form-control" v-model="formFilter.to_date"
                                placeholder="Admission To Date" name="to_date">
                            </datepicker>
                        </div>

                        <div class="col-md-2">
                            <a class="btn btn-primary" @click="getStudent" :disabled="loading">
                                Get Student
                            </a>
                        </div>
                        <Loader :visible="loading" />
                    </template>

                    <template v-slot:bottom_data>
                        <template v-if="localDataList.data && localDataList.data.length">
                            <div class="main-header mt-3">
                                <div class="row mb-3 mt-3">
                                    <schoolInfo title="Class Wise Student Report" />
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="student-details-card">
                                                <div class="student-detail">
                                                    <strong>Class:</strong>
                                                    <span>
                                                        {{ formFilter.class_id
                                                            ? localDataList.data[0]?.class_name || 'N/A'
                                                            : 'All Classes' }}
                                                    </span>
                                                </div>

                                                <div class="student-detail">
                                                    <strong>Section:</strong>
                                                    <span>
                                                        {{ formFilter.section_id
                                                            ? localDataList.data[0]?.section_name || 'N/A'
                                                            : 'All Sections' }}
                                                    </span>
                                                </div>
                                                <div v-if="formFilter.from_date != '' && formFilter.to_date != ''"
                                                    class="student-detail">
                                                    <strong>Reporting Period:</strong>
                                                    <span>{{ formFilter.from_date }} &nbsp; - <span>{{
                                                        formFilter.to_date
                                                            }}</span></span>
                                                </div>

                                                <div class="student-detail">
                                                    <strong>Print Date:</strong>
                                                    <span>{{ formatDate(new Date()) }}</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <table class="grand-total-style" width="100%">
                                <thead>
                                    <tr>
                                        <th rowspan="3">Sl</th>
                                        <th rowspan="3">Class</th>
                                        <th rowspan="3">Section</th>

                                        <th colspan="2">Students</th>
                                        <th colspan="2">Muslim</th>
                                        <th colspan="2">Hindu</th>

                                        <th rowspan="3">Total</th>
                                        <th rowspan="3">Remarks</th>
                                    </tr>

                                    <tr>
                                        <th>Male</th>
                                        <th>Female</th>

                                        <th>Male</th>
                                        <th>Female</th>

                                        <th>Male</th>
                                        <th>Female</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <template v-for="(data, index) in localDataList.data">
                                        <tr>
                                            <td>{{ index + 1 }}</td>

                                            <td v-if="localDataList.data.findIndex(d => d.class_id === data.class_id) === index"
                                                :rowspan="localDataList.data.filter(d => d.class_id === data.class_id).length">
                                                {{ data.class_name }}
                                            </td>


                                            <td>{{ data.section_name }}</td>


                                            <td>{{ data.total_male }}</td>
                                            <td>{{ data.total_female }}</td>


                                            <td>{{ data.muslim_male }}</td>
                                            <td>{{ data.muslim_female }}</td>


                                            <td>{{ data.hindu_male }}</td>
                                            <td>{{ data.hindu_female }}</td>

                                            <td v-if="localDataList.data.findIndex(d => d.class_id === data.class_id) === index"
                                                :rowspan="localDataList.data.filter(d => d.class_id === data.class_id).length">
                                                {{localDataList.data
                                                    .filter(d2 => d2.class_id === data.class_id)
                                                    .reduce((sum, d2) => sum + (+d2.total_male || 0) + (+d2.total_female ||
                                                        0), 0)}}
                                            </td>

                                            <td></td>
                                        </tr>
                                    </template>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="3">Grand Total</th>
                                        <th>{{ grandTotals.total_male }}</th>
                                        <th>{{ grandTotals.total_female }}</th>
                                        <th>{{ grandTotals.muslim_male }}</th>
                                        <th>{{ grandTotals.muslim_female }}</th>
                                        <th>{{ grandTotals.hindu_male }}</th>
                                        <th>{{ grandTotals.hindu_female }}</th>
                                        <th>{{ grandTotals.total_male + grandTotals.total_female }}</th>
                                        <th></th>
                                    </tr>
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
import Pagination from "../../../plugins/pagination/pagination";
import PageTop from "../../../components/pageTop";
import schoolInfo from "../../../components/schoolInfo";
import Loader from "../../../components/loader.vue";
import axios from 'axios';

export default {
    name: "classWiseStudentReport",
    components: { PageTop, Pagination, DataTable, schoolInfo, Loader },

    data() {
        return {
            loading: false,
            localDataList: {
                data: []
            },
        };
    },

    computed: {

        grandTotals() {
            return this.localDataList.data.reduce((totals, d) => {
                totals.total_male += +d.total_male || 0;
                totals.total_female += +d.total_female || 0;
                totals.muslim_male += +d.muslim_male || 0;
                totals.muslim_female += +d.muslim_female || 0;
                totals.hindu_male += +d.hindu_male || 0;
                totals.hindu_female += +d.hindu_female || 0;
                return totals;
            }, {
                total_male: 0,
                total_female: 0,
                muslim_male: 0,
                muslim_female: 0,
                hindu_male: 0,
                hindu_female: 0
            });
        },


        classGrandTotals() {
            const totals = {};
            this.localDataList.data.forEach(d => {
                if (!totals[d.class_id]) {
                    totals[d.class_id] = 0;
                }
                totals[d.class_id] += (+d.total_male || 0) + (+d.total_female || 0);
            });
            return totals;
        }
    },
    methods: {
        getStudent() {
            const filterParams = {};
            if (this.formFilter.class_id) filterParams.class_id = this.formFilter.class_id;
            if (this.formFilter.section_id) filterParams.section_id = this.formFilter.section_id;
            if (this.formFilter.from_date) filterParams.from_date = this.formFilter.from_date;
            if (this.formFilter.to_date) filterParams.to_date = this.formFilter.to_date;

            this.loading = true;

            axios.get("/api/reports/class_wise_students", { params: filterParams })
                .then(res => {
                    const response = res.data;

                    if (response.status === 404 || !response.result.data.length) {
                        this.localDataList.data = [];
                        this.$toastr('warning', 'No records found for the selected class/section.', 'Warning');
                        return;
                    }

                    this.localDataList.data = response.result.data || [];
                })
                .catch(error => {
                    console.error("Failed to fetch data:", error);
                    this.$toastr('error', 'Failed to fetch data from the server.', 'Error');
                })
                .finally(() => this.loading = false);
        },


        formatDate(date) {
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = String(d.getFullYear()).slice(-2);
            return `${day}-${month}-${year}`;
        },

    },

    mounted() {
        this.getGeneralData(["class"]);
    },
};
</script>

<style scoped>
.table thead th,
.table tfoot th {
    background-color: #cccbcb;
    text-align: center;
}

.report-header {
    text-align: center;
    margin-bottom: 6px;
}

.grand-total-style,
.grand-total-style th,
.grand-total-style td {
    border: 2px solid #000;
    border-collapse: collapse;
    text-align: center;
    padding: 4px;
    font-size: 16px;
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

.print-grand-total {
    display: none;
}

</style>
