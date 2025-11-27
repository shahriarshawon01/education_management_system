<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :default-object="{ school_id: '', created_at: '', updated_at: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="SMS History Report"
                    :table-responsive="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="SMS History Report" :default-add-button="false">
                            <div @click="printData('printDiv')">
                                <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i>
                                    Print</button>

                            </div>
                        </page-top>
                    </template>

                    <template v-slot:filter>
                        <div class="col-md-2">
                            <datepicker class="form-control" v-model="formFilter.created_at" placeholder="From Date"
                                name="created_at">
                            </datepicker>
                        </div>
                        <div class="col-md-2">
                            <datepicker class="form-control" v-model="formFilter.updated_at" placeholder="To Date"
                                name="updated_at"></datepicker>
                        </div>
                    </template>

                    <template v-slot:bottom_data>
                        <template v-if="dataList.data">
                            <div class="main-header mt-3">
                                <div class="row mb-3 mt-3">

                                    <!-- Reusable School Info Component -->
                                    <schoolInfo title="SMS Histories Report" />

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
                                        <th style="width: 1% !important;">Sl</th>
                                        <th style="width: 4% !important;">Mobile</th>
                                        <th style="width: 5% !important;">SMS Date</th>
                                        <th style="width: 30% !important;">SMS</th>
                                        <th style="width: 5% !important;">SMS Type</th>
                                        <th style="width: 5% !important;">SMS Status</th>
                                        <th style="width: 25%;">log</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in dataList.data">
                                        <td class="fw-medium">{{ index + 1 }}</td>
                                        <td style="text-align: left;">{{ data.mobile }}</td>
                                        <td style="text-align: center;">{{ formatDate(data.created_at) }}</td>
                                        <td style="white-space: pre-line; word-break: break-word; max-width: 135px;">{{
                                            data.sms }}</td>
                                        <td style="text-align: center;">{{ data.request_type }}</td>
                                        <td style="text-align: center;">{{ data.server_response }}</td>
                                        <td style="white-space: pre-line; word-break: break-word">{{ data.campaign_title }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="table-success">
                                        <th colspan="3">Total SMS = {{ totalSms }}</th>
                                        <th colspan="4"></th>
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
import PageTop from "../../../components/pageTop";
import schoolInfo from "../../../components/schoolInfo";
export default {
    name: "smsHistoryReport",
    components: { PageTop, DataTable, schoolInfo },

    data() {
        return {
        };
    },
    methods: {
        formatDate(date) {
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = String(d.getFullYear()).slice(-2);
            return `${day}-${month}-${year}`;
        },
    },
    computed: {
        totalSms() {
            return this.dataList.data.length;
        }
    },
    mounted() {
        const _this = this;
        _this.getGeneralData(['designation', 'employeeDepartment']);
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
