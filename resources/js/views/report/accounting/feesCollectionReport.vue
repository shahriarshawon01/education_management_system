<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]"
                    :default-object="{ school_id: '', student_id: '', session_year_id: '', class_id: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Fees Collection Report"
                    :table-responsive="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Fees Collection Report"
                            :default-add-button="false">
                            <div @click="printData('printDiv')">
                                <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i>
                                    Print</button>
                            </div>
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.class_id" name="class_id" class="form-control"
                                @click="getGeneralData({ students: { class_id: formFilter.class_id } })">
                                <option value="">Select Class</option>
                                <template v-for="(eachData, index) in requiredData.class">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
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
                            <input type="text" class="form-control" v-model="formFilter.roll"
                                placeholder="Search By Roll" name="roll">
                        </div>
                    </template>

                    <template v-slot:bottom_data>
                        <template v-if="dataList.data">

                            <!-- Reusable School Info Component -->
                            <schoolInfo title="Student Fees Collection Report" />

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label v-if="dataList.student_name"><strong>Name :</strong> {{
                                        dataList.student_name
                                    }} - ({{ dataList.student_roll }})</label><br>
                                    <label><strong>Class :</strong> {{ dataList.class_name }}</label><br>
                                    <label><strong>Session :</strong> {{ dataList.session_name }}</label><br>
                                    <label><strong>Section :</strong> {{ dataList.section_name }}</label><br>
                                </div>
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Student Name</th>
                                        <th>Student ID</th>
                                        <th>Date</th>
                                        <th>Payable Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Due Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in dataList.data">
                                        <td class="fw-medium">{{ index + 1 }}</td>
                                        <td>{{ data.student_name }}</td>
                                        <td style="text-align: center;">{{ data.student_roll }}</td>
                                        <td style="text-align: center;">{{ data.date }}</td>
                                        <td style="text-align: right;">{{ data.total_payable }}</td>
                                        <td style="text-align: right;">{{ data.total_payed }}</td>
                                        <td style="text-align: right;">{{ data.due_amount }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="table-success">
                                        <th colspan="4">Total</th>
                                        <th v-if="dataList.option !== undefined">{{ dataList.option.total_payable }}
                                        </th>
                                        <th v-if="dataList.option !== undefined">{{ dataList.option.total_payed }}</th>
                                        <th v-if="dataList.option !== undefined">{{ dataList.option.total_due }}</th>
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
export default {
    name: "dueReportComponent",
    components: { PageTop, Pagination, DataTable, schoolInfo },

    data() {
        return {

        };
    },
    computed: {

    },
    methods: {

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
