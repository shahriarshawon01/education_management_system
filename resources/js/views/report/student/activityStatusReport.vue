<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]"
                    :default-object="{ school_id: '', class_id: '', from_date: '', to_date: '', session_year_id: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Activity Status Report"
                    :table-responsive="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Activity Status Report"
                            :default-add-button="false">
                            <button @click="printData('printDiv')" class="btn btn-outline-primary"><i
                                    class="fa-sharp fa-solid fa-print"></i> Print</button>
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
                        <!-- <div class="col-md-2">
                            <datepicker class="form-control" v-model="formFilter.from_date" placeholder="From Date"
                                name="from_date">
                            </datepicker>
                        </div>
                        <div class="col-md-2">
                            <datepicker class="form-control" v-model="formFilter.to_date" placeholder="To Date"
                                name="to_date"></datepicker>
                        </div> -->
                    </template>
                    <template v-slot:bottom_data>
                        <template v-if="dataList.data">
                            <div class="main-header mt-3">
                                <div class="row mb-3 mt-3">

                                    <!-- Reusable School Info Component -->
                                    <schoolInfo title="Activity Status Report" />

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
                                    <tr style="vertical-align: middle;">
                                        <th rowspan="3">Sl</th>
                                        <th rowspan="3">Class Name</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-center">Active Student</th>
                                        <th colspan="3" class="text-center">InActive Student</th>
                                        <th colspan="3" class="text-center">DropOut Student</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">Male</th>
                                        <th class="text-center">Female</th>
                                        <th class="text-center">Total</th>

                                        <th class="text-center">Male</th>
                                        <th class="text-center">Female</th>
                                        <th class="text-center">Total</th>

                                        <th class="text-center">Male</th>
                                        <th class="text-center">Female</th>
                                        <th class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in dataList.data">
                                        <td class="fw-medium">{{ index + 1 }}</td>
                                        <td style="text-align: center;">{{ data.class_name }}</td>

                                        <td style="text-align: center;">{{ activeMale(data) }}</td>
                                        <td style="text-align: center;">{{ activeFeMale(data) }}</td>
                                        <td style="text-align: center;">{{ activeTotal(data) }}</td>

                                        <td style="text-align: center;">{{ data.inActiveMale }}</td>
                                        <td style="text-align: center;">{{ data.inActiveFemale }}</td>
                                        <td style="text-align: center;">{{ data.inActiveTotal }}</td>

                                        <td style="text-align: center;">{{ data.drop_male }}</td>
                                        <td style="text-align: center;">{{ data.drop_female }}</td>
                                        <td style="text-align: center;">{{ data.drop_total }}</td>

                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="table-success">
                                        <th colspan="2">Grand Total</th>
                                        <th>{{ grandTotals.activeMale }}</th>
                                        <th>{{ grandTotals.activeFeMale }}</th>
                                        <th>{{ grandTotals.activeTotal }}</th>

                                        <th>{{ grandTotals.inActiveMale }}</th>
                                        <th>{{ grandTotals.inActiveFemale }}</th>
                                        <th>{{ grandTotals.inActiveTotal }}</th>

                                        <th>{{ grandTotals.drop_male }}</th>
                                        <th>{{ grandTotals.drop_female }}</th>
                                        <th>{{ grandTotals.drop_total }}</th>

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
    name: "activityStatusReport",
    components: { PageTop, Pagination, DataTable, schoolInfo },
    data() {
        return {

        };
    },
    computed: {
        grandTotals() {
            if (!Array.isArray(this.dataList.data)) {
                return {};
            }
            return this.dataList.data.reduce((totals, data) => {
                totals.drop_male += parseFloat(data.drop_male) || 0;
                totals.drop_female += parseFloat(data.drop_female) || 0;
                totals.drop_total += parseFloat(data.drop_total) || 0;

                totals.activeMale += this.activeMale(data);
                totals.activeFeMale += this.activeFeMale(data);
                totals.activeTotal += this.activeTotal(data);

                totals.inActiveMale += parseFloat(data.inActiveMale) || 0;
                totals.inActiveFemale += parseFloat(data.inActiveFemale) || 0;
                totals.inActiveTotal += parseFloat(data.inActiveTotal) || 0;

                return totals;
            }, {
                drop_male: 0,
                drop_female: 0,
                drop_total: 0,

                activeMale: 0,
                activeFeMale: 0,
                activeTotal: 0,

                inActiveMale: 0,
                inActiveFemale: 0,
                inActiveTotal: 0,
            });
        }
    },
    methods: {
        activeMale(data) {
            return data.activeMale - data.drop_male - data.inActiveMale;
        },

        activeFeMale(data) {
            return data.activeFemale - data.drop_female - data.inActiveMale;
        },

        activeTotal(data) {
            return this.activeMale(data) + this.activeFeMale(data);
        },
    },
    mounted() {
        const _this = this;
        _this.getGeneralData(['school', 'class', 'session']);

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
