<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :default-object="{ month: '', years: '', member_id: '' }"
                    :defaultFilter="false" :defaultSearchButton="false" :default-pagination="false"
                    table-title="Canteen Ledger Report" :table-responsive="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Canteen Ledger Report" :default-add-button="false">
                            <div @click="printData('printDiv')">
                                <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i>
                                    Print</button>
                            </div>
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.canteen_month" class="form-control" name="canteen_month">
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
                            <select v-select2 class="form-control" v-model="formFilter.member_id">
                                <option value="">Select member</option>
                                <option v-for="m in members" :key="m.member_id" :value="m.member_id">
                                    {{ m.member_name }} ({{ m.member_code }})
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-primary" @click="fetchCanteenLedger" :disabled="loading">
                                {{ loading ? 'Loading...' : 'Get Data' }}
                            </button>
                        </div>
                    </template>

                    <template v-slot:bottom_data>
                        <div v-if="localDataList.rows && localDataList.rows.length">
                            <!-- Reusable School Info Component -->
                            <schoolInfo title="Canteen Monthly Meal Report" />
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="report-details-card">
                                        <div class="value-detail" v-if="localDataList.member_info">
                                            <strong>Name:</strong>
                                            <span>{{ localDataList.member_info.name }}</span>
                                        </div>
                                        <div class="value-detail" v-if="localDataList.member_info.roll">
                                            <strong>Roll:</strong>
                                            <span>{{ localDataList.member_info.roll }}</span>
                                        </div>
                                        <div class="value-detail" v-if="localDataList.member_info.class_name">
                                            <strong>Class:</strong>
                                            <span>{{ localDataList.member_info.class_name }}</span>
                                        </div>
                                        <div class="value-detail" v-if="localDataList.member_info.employee_id">
                                            <strong>Employee ID:</strong>
                                            <span>{{ localDataList.member_info.employee_id }}</span>
                                        </div>
                                        <div class="value-detail" v-if="localDataList.member_info.designation_name">
                                            <strong>Designation:</strong>
                                            <span>{{ localDataList.member_info.designation_name }}</span>
                                        </div>
                                        <div class="value-detail">
                                            <strong>Month:</strong>
                                            <span>{{ localDataList.month }}</span>
                                        </div>
                                        <div class="value-detail">
                                            <strong>Print Date:</strong>
                                            <span>{{ formatDate(new Date()) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr class="background-style">
                                        <th>SL</th>
                                        <th>Date</th>
                                        <th v-for="(meal, index) in localDataList.meal_columns" :key="index">
                                            {{ meal }}
                                        </th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, index) in localDataList.rows" :key="index">
                                        <td style="text-align: center;">{{ index + 1 }}</td>
                                        <td style="text-align: center;">{{ row.date }}</td>
                                        <td style="text-align: right;" v-for="(meal, i) in localDataList.meal_columns"
                                            :key="i">
                                            {{ formatAmount(row[meal] || 0) }}
                                        </td>
                                        <td style="text-align: right;">{{ formatAmount(row.total_amount) }}</td>
                                    </tr>
                                </tbody>

                                <tfoot>
                                    <tr class="table-success">
                                        <th colspan="2" class="fw-bold">Grand Total</th>
                                        <th v-for="(meal, index) in localDataList.meal_columns" :key="index"
                                            style="text-align: center;">
                                            {{ formatAmount(mealTotals.totals[meal] || 0) }}
                                        </th>
                                        <th style="text-align: center;">{{ formatAmount(mealTotals.grandTotal || 0) }}
                                        </th>

                                    </tr>
                                </tfoot>

                            </table>
                        </div>
                        <div v-else class="text-center text-muted py-3 border rounded">
                            No records found for the selected month.
                        </div>
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
import formModal from "../../../components/formModal";
import Loader from "../../../components/loader.vue"
import schoolInfo from "../../../components/schoolInfo";
import axios from 'axios';

export default {
    name: "canteenLedgerReport",
    components: { PageTop, Pagination, DataTable, formModal, Loader, schoolInfo },
    data() {
        return {
            tableHeading: [],
            localDataList: { data: [] },
            formModalId: "formModal",
            years: [],
            months: [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ],
            loading: false,
            mealTimes: ['breakfast', 'lunch', 'dinner'],
            loading: false,
            members: [],
        };
    },

    methods: {

        fetchMembers() {
            axios.get("/api/get-canteen-member")
                .then(res => {
                    this.members = res.data.result ?? [];

                    this.$nextTick(() => {
                        $(this.$el).find('select[v-select2]').trigger('change');
                    });
                })
                .catch(() => {
                    this.members = [];
                });
        },

        fetchCanteenLedger() {
            const selected = this.members.find(m => m.member_id == this.formFilter.member_id);

            const params = {
                month: this.formFilter.canteen_month,
                year: this.formFilter.years,
                member_id: selected?.member_id || '',
                consumer_type: selected?.consumer_type || ''
            };

            this.loading = true;
            axios.get("/api/reports/canteen_ledger_report", { params })
                .then(res => {
                    if (res.data.status === 2000 && res.data.result) {
                        this.localDataList = {
                            meal_columns: res.data.result.meal_columns || [],
                            rows: res.data.result.rows || [],
                            member_info: res.data.result.member_info || {}, // use backend member info
                            month: res.data.result.month || '',             // use backend month name
                            year: res.data.result.year || '',
                            print_date: res.data.result.print_date || new Date().toLocaleDateString()
                        };
                    } else {
                        this.localDataList = {
                            meal_columns: [],
                            rows: [],
                            member_info: {},
                            month: '',
                            year: '',
                            print_date: new Date().toLocaleDateString()
                        };
                    }
                })
                .finally(() => this.loading = false);
        },

        formatDate(date) {
            if (!date || date === '-') {
                return '-';
            }
            const d = new Date(date);
            if (isNaN(d)) {
                return '-';
            }
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = String(d.getFullYear()).slice(-2);
            return `${day}-${month}-${year}`;
        },

        formatAmount(amount) {
            return new Intl.NumberFormat("en-US", {
                style: "decimal",
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(amount);
        },

        assign() {
            this.formFilter.canteen_month = this.formFilter.canteen_month || String(new Date().getMonth() + 1).padStart(2, '0');
            this.formFilter.years = this.formFilter.years || new Date().getFullYear();
            this.formObject.amount = '';
        },
    },

    computed: {
        mealTotals() {
            const totals = {};
            let grandTotal = 0;

            // Initialize totals for each meal
            if (this.localDataList.meal_columns && this.localDataList.meal_columns.length) {
                this.localDataList.meal_columns.forEach(meal => {
                    totals[meal] = 0;
                });
            }

            // Sum up per row
            if (this.localDataList.rows && this.localDataList.rows.length) {
                this.localDataList.rows.forEach(row => {
                    this.localDataList.meal_columns.forEach(meal => {
                        totals[meal] += row[meal] || 0;
                    });
                    grandTotal += row.total_amount || 0;
                });
            }

            return { totals, grandTotal };
        }
    },

    mounted() {
        const today = new Date();
        const currentMonth = String(today.getMonth() + 1).padStart(2, '0');
        const currentYear = today.getFullYear();
        this.formFilter.canteen_month = currentMonth;
        this.formFilter.years = currentYear;

        for (let i = currentYear - 1; i <= currentYear + 5; i++) {
            this.years.push(i);
        }
        this.fetchMembers();

        this.assign();
    },
};
</script>

<style scoped>
.background-style {
    background: #cccbcb;
    text-align: center;
}

.table tfoot th {
    background-color: #cccbcb;
    text-align: center;
}
</style>
