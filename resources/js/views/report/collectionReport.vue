<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]"
                    :default-object="{ department_id: '', student_id: '', session_year_id: '', class_id: '', invoice_id: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Collection Report"
                    :table-responsive="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Collection Report" :default-add-button="false">
                            <div @click="printData('printDiv')">
                                <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i>
                                    Print</button>
                            </div>
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.class_id" name="class_id" class="form-control" @change="getGeneralData({
                                students: { class_id: formFilter.class_id },
                                departments: { class_id: formFilter.class_id }
                            })">
                                <option value="">Select Class</option>
                                <template v-for="(eachData, index) in requiredData.class">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
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
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.session_year_id" name="session_year_id">
                                <option value="">Select Session</option>
                                <template v-for="(data, index) in requiredData.session">
                                    <option :value="data.id">{{ data.title }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-select2 class="form-control" v-model="formFilter.student_id" name="student_id"
                                @change="getGeneralData({ invoice: { student_id: formFilter.student_id } })">
                                <option value="">Select Student</option>
                                <template v-for="(data, index) in requiredData.students">
                                    <option :value="data.id">{{ data.student_name_en }} - {{ data.roll }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.invoice_id" name="invoice_id">
                                <option value="">Invoice</option>
                                <template v-for="(data, index) in requiredData.invoice">
                                    <option :value="data.id">{{ data.invoice_code }} [Payable : {{ data.total_payable }}]
                                    </option>
                                </template>
                            </select>
                        </div>
                    </template>
                    <template v-slot:reportHeader>
                        <div class="row mb-3 mt-3 report_header">
                            <div class="col-12 mb-3 text-center">
                                <h4>{{ dataList.school_name }}</h4>
                                <span>School STEB : {{ dataList.school_steb }} , School Registration : {{
                                    dataList.registration_no }}</span><br>
                                <span>{{ dataList.school_address }}</span>
                                <h3 style="margin-top: 15px;"> Student Collection Report </h3>
                            </div>
                            <div class="row">
                                <div class="col-6 text-start">
                                </div>
                                <div class="col-6 text-end">
                                    <strong>Print Date : {{ formatDate(new Date()) }}</strong>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-slot:bottom_data>
                        <template v-if="dataList.componentWiseData">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label>School Name : {{ dataList.school_name }}</label><br>
                                    <label>Session : {{ dataList.session_name }}</label><br>
                                    <label>Class : {{ dataList.class_name }}</label><br>
                                </div>
                                <div class="col-md-6 text-end">
                                    <label v-if="dataList.student_name">Student Name : {{ dataList.student_name
                                        }}</label><br>
                                </div>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th style="text-align: center;">Component Name</th>
                                        <th style="text-align: center;">Payment Date</th>
                                        <th style="text-align: center;">Amount</th>
                                        <th style="text-align: center;">Waiver</th>
                                        <th style="text-align: center;">Payed Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in dataList.componentWiseData">
                                        <td class="fw-medium">{{ index + 1 }}</td>
                                        <td>{{ data.component_name }}</td>
                                        <td style="text-align: center;">{{ data.date }}</td>
                                        <td style="text-align: right;">{{ data.amount }}</td>
                                        <td style="text-align: right;">{{ data.waiver }}</td>
                                        <td style="text-align: right;">{{ data.payed }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="table-success">
                                        <th colspan="3">Total</th>
                                        <th>{{ dataList.total_amount.toFixed(2) }}</th>
                                        <th>{{ dataList.total_waiver.toFixed(2) }}</th>
                                        <th>{{ dataList.total_payed.toFixed(2) }}</th>
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
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
export default {
    name: "collectionReport",
    components: { PageTop, Pagination, DataTable },

    data() {
        return {
        };
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
</style>
