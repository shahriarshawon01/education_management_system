<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :default-object="{ school_id: '', from_date: '', to_date: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Collection Information Report"
                    :table-responsive="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Collection Information Report"
                            :default-add-button="false">
                            <button @click="printData('printDiv')" class="btn btn-outline-primary"><i
                                    class="fa-sharp fa-solid fa-print"></i> Print</button>

                            <a target="_blank"
                                @click="openFile(`${urlGenerate('collection_info_excel_report')} ? school_id=${formFilter.school_id}&component_id=${formFilter.component_id}&from_date=${formFilter.from_date}&to_date=${formFilter.to_date}`)"
                                class="btn btn-info"><i class="fa fa-file-excel"></i> Download Excel</a>
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select class="form-control" @change="getComponent" v-model="formFilter.school_id"
                                name="school_id">
                                <option value="">Select Institute</option>
                                <template v-for="(data, index) in requiredData.school">
                                    <option :value="data.id">{{ data.title }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <datepicker class="form-control" v-model="formFilter.from_date" placeholder="From Date"
                                name="from_date">
                            </datepicker>
                        </div>
                        <div class="col-md-2">
                            <datepicker class="form-control" v-model="formFilter.to_date" placeholder="To Date"
                                name="to_date"></datepicker>
                        </div>
                        <div class="col-md-11">
                            <div class="row mt-4">
                                <div class="col-md-3" v-for="(component, cIndex) in componentData.data" :key="cIndex">
                                    <input type="checkbox" :value="component.id" name="component_id"
                                        v-model="formFilter.component_id"> <span>{{
                                            component.name }}</span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-slot:reportHeader>
                        <div class="row mb-3 mt-3 report_header">
                            <div class="col-12 mb-3 text-center">
                                <h3> TPSC </h3>
                            </div>
                            <div class="col-12 mb-3 text-center">
                                <h4> Students Collection Information Report </h4>
                            </div>
                            <div class="row">
                                <div class="col-6 text-start">
                                </div>
                                <div class="col-6 text-end">
                                    <strong>Print Date: {{ formatDate(new Date()) }}</strong>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-slot:bottom_data>
                        <template>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label>School Name : {{ dataList.school_name }}</label><br>
                                    <label v-if="dataList.formDate">Form Date : {{ dataList.formDate }}</label><br>
                                    <label v-if="dataList.toDate">To Date : {{ dataList.toDate }}</label>
                                </div>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Sl</th>
                                        <th rowspan="2">Class Name</th>
                                        <th rowspan="2">Total Student</th>
                                        <th rowspan="2">Determined Fee</th>
                                        <th rowspan="2">Target level</th>
                                        <th rowspan="2">Waiver Target</th>
                                        <th rowspan="2">Waiver Next Target</th>
                                        <th colspan="2" class="text-center">Current Month Collect</th>
                                        <th colspan="2" class="text-center">Due Collect</th>
                                        <th colspan="2" class="text-center">Advance Collect</th>
                                        <th colspan="2" class="text-center">Others Collect</th>
                                        <th colspan="2" class="text-center">Total Collect</th>
                                        <th colspan="2" class="text-center">Current Month Due</th>
                                        <th colspan="2" class="text-center">Total Due</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">Student Count</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Number</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Number</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Number</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Number</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Number</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Number</th>
                                        <th class="text-center">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in dataList.data">
                                        <td class="fw-medium">{{ index + 1 }}</td>
                                        <td>{{ data.class_name }}</td>
                                        <td>{{ data.student_count }}</td>
                                        <td>{{ data.paying_amount }}</td>
                                        <td>{{ detarmined(data) }}</td>
                                        <td>{{ data.waiver }}</td>
                                        <td>{{ nextWaiver(data) }}</td>
                                        <td>{{ data.regular_students }}</td>
                                        <td>{{ data.regular_amount }}</td>
                                        <td>{{ data.due_students }}</td>
                                        <td>{{ data.due_amount }}</td>
                                        <td>{{ data.advance_students }}</td>
                                        <td>{{ data.advance_amount }}</td>
                                        <td>{{ data.other_students }}</td>
                                        <td>{{ data.other_amount }}</td>
                                        <td>{{ data.total_students }}</td>
                                        <td>{{ data.total_amount }}</td>
                                        <td>{{ data.current_due_student_count }}</td>
                                        <td>{{ data.current_total_due_amount }}</td>
                                        <td>{{ data.due_student_count }}</td>
                                        <!-- <td>{{ data.total_due_amount }}</td> -->
                                        <td>{{ calculateTotalDueAmount(data) }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="2">Grand Total</th>
                                        <th>{{ grandTotals.studentCount }}</th>
                                        <th>{{ grandTotals.amount }}</th>
                                        <th>{{ grandTotals.detarmined }}</th>
                                        <th>{{ grandTotals.waiver }}</th>
                                        <th>{{ grandTotals.nextWaiver }}</th>
                                        <th>{{ grandTotals.regularStudents }}</th>
                                        <th>{{ grandTotals.regularAmount }}</th>
                                        <th>{{ grandTotals.dueStudents }}</th>
                                        <th>{{ grandTotals.dueAmount }}</th>
                                        <th>{{ grandTotals.advanceStudents }}</th>
                                        <th>{{ grandTotals.advanceAmount }}</th>
                                        <th>{{ grandTotals.otherStudents }}</th>
                                        <th>{{ grandTotals.otherAmount }}</th>
                                        <th>{{ grandTotals.totalStudents }}</th>
                                        <th>{{ grandTotals.totalAmount }}</th>
                                        <th>{{ grandTotals.currentDueStudentCount }}</th>
                                        <th>{{ grandTotals.currentTotalDueAmount }}</th>
                                        <th>{{ grandTotals.dueStudentCount }}</th>
                                        <th>{{ grandTotals.totalDueAmount }}</th>
                                    </tr>
                                </tbody>
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
    name: "collectionInformationReport",
    components: { PageTop, Pagination, DataTable },

    data() {
        return {
            componentData: [],
            component_id: [],
        };

    },
    watch: {
        'formFilter.component_id'(newValue) {
            if (newValue.length > 0 && newValue.length !== this.componentData.data.length) {
                this.formFilter.component_id = this.componentData.data.map(component => component.id);
            }
        },
    },
    methods: {
        calculateTotalDueAmount(data) {
            return data.paying_amount - data.payed;
        },
        detarmined(data) {
            const studentCount = parseFloat(data.student_count) || 0;
            const paying_amount = parseFloat(data.paying_amount) || 0;
            return studentCount * paying_amount;
        },
        nextWaiver(data) {
            const detarminedValue = this.detarmined(data);
            const waiver = parseFloat(data.waiver) || 0;
            return detarminedValue - waiver;
        },
        getComponent() {
            const _this = this;
            var URL = `${_this.urlGenerate('api/reports/get_component_data')}`;
            const paramsData = {
                school_id: _this.formFilter.school_id
            };
            _this.getData(URL, "get", paramsData, {}, function (retData) {
                _this.componentData = retData;
                _this.formFilter.component_id = [];

            });
        },
    },
    computed: {
        grandTotals() {
            if (!Array.isArray(this.dataList.data)) {
                return {};
            }
            return this.dataList.data.reduce((totals, data) => {
                totals.studentCount += parseFloat(data.student_count) || 0;
                totals.amount += parseFloat(data.amount) || 0;
                totals.detarmined += this.detarmined(data);
                totals.waiver += parseFloat(data.waiver) || 0;
                totals.nextWaiver += this.nextWaiver(data);
                totals.regularStudents += parseFloat(data.regular_students) || 0;
                totals.regularAmount += parseFloat(data.regular_amount) || 0;
                totals.dueStudents += parseFloat(data.due_students) || 0;
                totals.dueAmount += parseFloat(data.due_amount) || 0;
                totals.advanceStudents += parseFloat(data.advance_students) || 0;
                totals.advanceAmount += parseFloat(data.advance_amount) || 0;
                totals.otherStudents += parseFloat(data.other_students) || 0;
                totals.otherAmount += parseFloat(data.other_amount) || 0;
                totals.totalStudents += parseFloat(data.total_students) || 0;
                totals.totalAmount += parseFloat(data.total_amount) || 0;
                totals.currentDueStudentCount += parseFloat(data.current_due_student_count) || 0;
                totals.currentTotalDueAmount += parseFloat(data.current_total_due_amount) || 0;
                totals.dueStudentCount += parseFloat(data.due_student_count) || 0;
                totals.totalDueAmount += parseFloat(data.paying_amount - data.payed) || 0;
                return totals;
            }, {
                studentCount: 0,
                amount: 0,
                detarmined: 0,
                waiver: 0,
                nextWaiver: 0,
                regularStudents: 0,
                regularAmount: 0,
                dueStudents: 0,
                dueAmount: 0,
                advanceStudents: 0,
                advanceAmount: 0,
                otherStudents: 0,
                otherAmount: 0,
                totalStudents: 0,
                totalAmount: 0,
                currentDueStudentCount: 0,
                currentTotalDueAmount: 0,
                dueStudentCount: 0,
                totalDueAmount: 0
            });
        }
    },
    mounted() {
        const _this = this;
        _this.getGeneralData(['school', 'session']);

    },
}
</script>
<style scoped></style>
