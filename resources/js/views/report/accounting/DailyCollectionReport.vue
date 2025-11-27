<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :defaultSearchButton="false"
                    :default-object="{ school_id: '', class_id: '', from_date: '', to_date: '', payment_type: '', collection_by: '', invoice_category: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Parent Report"
                    :table-responsive="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Daily Collection Report"
                            :default-add-button="false">
                            <div @click="printData('printDiv')">
                                <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i>
                                    Print</button>
                            </div>
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <datepicker class="form-control" v-model="formFilter.from_date" placeholder="From Date"
                                name="from_date" required>
                            </datepicker>
                        </div>
                        <div class="col-md-2">
                            <datepicker class="form-control" v-model="formFilter.to_date" placeholder="To Date"
                                name="to_date" required></datepicker>
                        </div>
                        <div class="col-md-2">
                            <select name="payment_type" v-model="formFilter.payment_type" class="form-control">
                                <option value="">Payment Type</option>
                                <option value="1">Cash</option>
                                <option value="2">Bank</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="collection_by" v-model="formFilter.collection_by" class="form-control">
                                <option value="">Collection By</option>
                                <template v-for="(eachData, index) in requiredData.collectBy">
                                    <option :value="eachData.id">{{ eachData.username }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="formFilter.invoice_category" name="invoice_category" class="form-control">
                                <option value="">Select Invoice Category</option>
                                <option value="1">Academic</option>
                                <option value="2">Canteen</option>
                                <option value="5">Canteen Cash</option>
                                <option value="3">Dormitory</option>
                                <option value="4">Transport</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" @click="fetchCollectionData" :disabled="loading">
                                Get Collection
                            </button>
                        </div>
                        <!-- loader -->
                        <Loader :visible="loading" />
                    </template>

                    <template v-slot:bottom_data>
                        <template v-if="localDataList.data.length > 0">

                            <!-- Reusable School Info Component -->
                            <schoolInfo title="Daily Collection Report" />
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="value-detail d-flex align-items-center mb-2">
                                        <i class="fa fa-calendar-alt me-2 text-primary"></i>
                                        <strong class="me-2">Reporting Date:</strong>
                                        <span>{{ formatDate(formFilter.from_date) }}</span>
                                    </div>
                                    <div class="value-detail d-flex align-items-center mb-2">
                                        <i class="fa fa-print me-2 text-success"></i>
                                        <strong class="me-2">Print Date:</strong>
                                        <span>{{ formatDate(new Date()) }}</span>
                                    </div>
                                    <div class="value-detail d-flex align-items-center">
                                        <i class="fa fa-user-check me-2 text-info"></i>
                                        <strong class="me-2">Collection By:</strong>
                                        <span v-if="formFilter.collection_by" class="badge bg-success text-dark">
                                            {{requiredData.collectBy.find(u => u.id ==
                                                formFilter.collection_by)?.username || 'All User'}}
                                        </span>
                                        <span v-else class="badge bg-secondary">All</span>
                                    </div>
                                </div>
                            </div>

                            <div style="overflow-x: scroll;">
                                <table class="grand-total-style">
                                    <thead v-if="localDataList.data.length > 0">
                                        <tr>
                                            <th rowspan="2" style="width: 2%;">SI</th>
                                            <th rowspan="2" style="width: 25%;">Paid By</th>
                                            <th rowspan="2" style="width: 5%;">Date</th>
                                            <th :colspan="uniqueComponents.length || 1">Component</th>
                                            <th rowspan="2" style="width: 8%;">Receipt</th>
                                            <th rowspan="2" style="width: 8%;">Receipt By</th>
                                        </tr>
                                        <tr>
                                            <th v-for="component in uniqueComponents" :key="component">
                                                {{ component }}
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr v-if="localDataList.data.length === 0">
                                            <td :colspan="5 + (uniqueComponents.length || 1)"
                                                style="text-align: center;">No data available</td>
                                        </tr>
                                        <template v-else>
                                            <tr v-for="(item, index) in localDataList.data" :key="index">
                                                <td>{{ index + 1 }}</td>
                                                <template v-if="item.paid_by_type == 1">
                                                    <td class="student_style">
                                                        <span><strong>Name:</strong> {{ item.student_name_en }} ({{
                                                            item.student_roll }})</span><br />
                                                        <span><strong>Class:</strong> {{ item.class_name }}</span><br />
                                                        <span><strong>Session:</strong> {{ item.session_name
                                                        }}</span><br />
                                                        <span><strong>Roll:</strong> {{ item.merit_roll }}</span><br />
                                                        <strong>Category :</strong> {{
                                                            getInvoiceCategory(item.invoice_category) }}<br />
                                                        <strong>Type :</strong> {{ getType(item.paid_by_type) }}
                                                    </td>
                                                </template>
                                                <template v-else-if="item.paid_by_type == 2">
                                                    <td class="student_style">
                                                        <span><strong>Name:</strong> {{ item.employee_name }} ({{
                                                            item.employee_id }})</span><br />
                                                        <span><strong>Designation:</strong> {{ item.designation_name
                                                        }}</span><br />
                                                        <strong>Category :</strong> {{
                                                            getInvoiceCategory(item.invoice_category) }}<br />
                                                        <strong>Type :</strong> {{ getType(item.paid_by_type) }}
                                                    </td>
                                                </template>
                                                <template v-else>
                                                    <td class="student_style">
                                                        <span>Guest</span>
                                                    </td>
                                                </template>
                                                <td>{{ item.payment_date }}</td>

                                                <td v-for="component in uniqueComponents" :key="component">
                                                    <span
                                                        v-if="item.invoice_details.some(detail => detail.component_name === component)">
                                                        {{
                                                            item.invoice_details.find(detail => detail.component_name ===
                                                                component)?.paid_amount || 0
                                                        }}
                                                    </span>
                                                    <span v-else>0</span>
                                                </td>

                                                <td>{{ item.total_payed }}</td>
                                                <td>
                                                    <span v-if="item.payment_type == 1">Cash</span>
                                                    <span v-if="item.payment_type == 2">Bank</span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                    <tfoot v-if="localDataList.data.length > 0">
                                        <tr class="component-total">
                                            <td :colspan="3"><strong>Total Collect Money</strong></td>
                                            <td v-for="component in uniqueComponents" :key="component">
                                                {{ componentTotals()[component] > 0 ? componentTotals()[component] : ''
                                                }}
                                            </td>
                                            <td><strong>{{ totalCollectMoney }}</strong></td>
                                            <td>-</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
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
import schoolInfo from "../../../components/schoolInfo.vue";
import Loader from "../../../components/loader.vue"
import axios from 'axios';
export default {
    name: "DailyCollectionReport",
    components: { PageTop, Pagination, DataTable, schoolInfo, Loader },

    data() {
        return {
            localDataList: {
                data: []
            },
            uniqueComponents: [],
            loading: false,
        };
    },
    methods: {

        formatAmount(amount) {
            return parseFloat(amount).toFixed(2);
        },

        formatDate(dateString) {
            if (!dateString) return '-';

            const date = new Date(dateString);
            if (isNaN(date)) return '-';

            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        },

        getInvoiceCategory(value) {
            switch (parseInt(value)) {
                case 1:
                    return "Academic";
                case 2:
                    return "Canteen";
                case 3:
                    return "Dormitory";
                case 4:
                    return "Transport";
                case 5:
                    return "Canteen Cash";
                default:
                    return "Unknown";
            }
        },

        getType(value) {
            switch (parseInt(value)) {
                case 1:
                    return "Student";
                case 2:
                    return "Employee";
                case 3:
                    return "Guest";
                default:
                    return "Unknown";
            }
        },

        componentTotals() {
            const totals = {};

            this.localDataList.data.forEach(item => {
                if (Array.isArray(item.invoice_details)) {
                    item.invoice_details.forEach(detail => {
                        const componentName = detail.component_name;
                        const payableAmount = parseFloat(detail.paid_amount) || 0;

                        if (!totals[componentName]) {
                            totals[componentName] = 0;
                        }

                        totals[componentName] += payableAmount;
                    });
                }
            });

            return totals;
        },

        fetchCollectionData() {
            if (!this.formFilter.from_date) {
                this.$toastr('warning', "Please select 'From Date' first.", "Warning");
                return;
            }

            if (!this.formFilter.to_date) {
                this.$toastr('warning', "Please select 'To Date' first.", "Warning");
                return;
            }

            const from = new Date(this.formFilter.from_date);
            const to = new Date(this.formFilter.to_date);
            if (from > to) {
                this.$toastr('warning', "From Date cannot be greater than To Date.", "Warning");
                return;
            }

            if (from.toDateString() !== to.toDateString()) {
                this.$toastr('warning', "Please select the same date in both 'From Date' and 'To Date'.", "Warning");
                return;
            }

            this.loading = true;
            axios.get("/api/reports/daily_collection", { params: this.formFilter })
                .then((response) => {
                    const result = response.data.result;
                    this.localDataList.data = result.data || [];

                    if (this.localDataList.data.length === 0) {
                        this.$toastr('warning', 'No payment records found for the selected date.', 'No Data');
                        this.uniqueComponents = [];
                        return;
                    }

                    this.uniqueComponents = [
                        ...new Set(
                            this.localDataList.data.flatMap(item =>
                                item.invoice_details?.map(detail => detail?.component_name).filter(Boolean)
                            )
                        ),
                    ];
                })
                .catch((error) => {
                    console.error("Failed to fetch collection data:", error);
                    this.$toastr('error', 'Failed to fetch data from the server.', 'Error');
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    },

    computed: {

        totalCollectMoney() {
            return this.localDataList.data.reduce((sum, item) => sum + (item.total_payed || 0), 0);
        },

        groupedData() {
            const grouped = [];
            let currentTeacher = null;
            let slCounter = 1;
            let rowspanCounter = 0;

            this.localDataList.data.forEach((item, index) => {
                if (item.class_name !== currentTeacher) {
                    if (rowspanCounter > 0) {
                        grouped[grouped.length - rowspanCounter].teacherRowspan = rowspanCounter;
                    }
                    currentTeacher = item.class_name;
                    rowspanCounter = 1;
                } else {
                    rowspanCounter++;
                }
                grouped.push({ ...item, sl: slCounter++ });
            });

            if (rowspanCounter > 0) {
                grouped[grouped.length - rowspanCounter].teacherRowspan = rowspanCounter;
            }

            return grouped;
        },

        totalStudents() {
            return this.localDataList.data.length;
        }
    },
    mounted() {
        const _this = this;
        _this.getGeneralData(['class', 'collectBy']);
    },
}
</script>

<style scoped>
.report-details-card {
    margin-bottom: -20px;
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

.student_style {
    width: 220px !important;
    text-align: left;
    font-size: 13px !important;
}

.table thead th {
    background-color: #cccbcb;
    text-align: center;
}

.table tfoot th {
    background-color: #cccbcb;
    text-align: center;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 13px;
    text-align: center;
}

th,
td {
    border: 1px solid black;
    padding: 5px;
}

th {
    background-color: #f2f2f2;
}

.main-header {
    text-align: center;
    font-weight: bold;
    margin-bottom: 5px;
}

.sub-header {
    text-align: center;
    font-weight: bold;
    margin-bottom: 10px;
}

.total-row {
    font-weight: bold;
    background-color: #cccbcb;
}

.component-total {
    font-weight: bold;
    background-color: #cccbcb;
}
</style>
