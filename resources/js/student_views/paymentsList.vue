<template>
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-white">📄 Student Payment History</h5>
        </div>

        <div class="card-body">
            <data-table :default-object="{ month: '', years: '' }" table-title="Payments List" :defaultFilter="false"
                :defaultSearchButton="false">
                <template v-slot:page_top>
                    <page-top :default-object="{}" topPageTitle="Payment history" :default-add-button="false">
                        <div @click="printData('printDiv')">
                            <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i>
                                Print</button>
                        </div>
                    </page-top>
                </template>
                <template v-slot:filter>
                    <div class="col-md-2">
                        <select v-model="formFilter.payment_month" class="form-control" name="payment_month">
                            <option value="">All payment</option>
                            <option v-for="(month, index) in months" :key="index"
                                :value="String(index + 1).padStart(2, '0')">
                                {{ month }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="years" v-model="formFilter.years" class="form-control">
                            <option value="">All payment</option>
                            <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary" @click="fetchPaymentData" :disabled="loading">
                            {{ loading ? 'Loading...' : 'Get Data' }}
                        </button>
                    </div>
                </template>

                <template v-slot:data>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Payment Date</th>
                                <th>Payment Category</th>
                                <th>Total Amount</th>
                                <th>Waiver</th>
                                <th>Payable Amount</th>
                                <th>Paid Amount</th>
                                <th>Due Amount</th>
                                <th>Payment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(payment, index) in localDataList.data" :key="index">
                                <td class="fw-semibold">{{ index + 1 }}</td>
                                <td style="text-align: center;">{{ showData(payment, "payment_date") }}</td>
                                 <td style="text-align: center;">{{ getInvoiceCategory(payment.invoice_category) }}</td>
                                <td class="amount">{{ formatAmount(showData(payment, "total_amount")) }}</td>
                                <td class="amount">{{ formatAmount(showData(payment, "total_waiver")) }}</td>
                                <td class="amount">{{ formatAmount(showData(payment, "total_payable")) }}</td>
                                <td class="amount">{{ formatAmount(showData(payment, "total_payed")) }}</td>
                                <td class="amount">{{ formatAmount(showData(payment, "due_amount")) }}</td>
                                <td style="text-align: center;">
                                    <span class="badge" :class="payment.due_amount > 0 ? 'bg-danger' : 'bg-success'">
                                        {{ payment.due_amount > 0 ? 'Due' : 'Paid' }}
                                    </span>
                                </td>

                            </tr>
                        </tbody>

                        <tfoot>
                            <tr class="table-success">
                                <th colspan="3">Grand Total</th>
                                <th>{{ getColumnTotal("total_amount") }}</th>
                                <th>{{ getColumnTotal("total_waiver") }}</th>
                                <th>{{ getColumnTotal("total_payable") }}</th>
                                <th>{{ getColumnTotal("total_payed") }}</th>
                                <th>{{ getColumnTotal("due_amount") }}</th>
                                <th>-</th>
                            </tr>
                        </tfoot>
                    </table>

                </template>
            </data-table>
        </div>
    </div>
</template>

<script>
import DataTable from "../components/dataTable";
import PageTop from "../components/pageTop";
import Pagination from "../plugins/pagination/pagination";
import Loader from "../components/loader.vue"
import axios from 'axios';

export default {
    name: "PaymentsList",
    components: { PageTop, DataTable, Pagination, Loader },
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
        };
    },

    methods: {

        fetchPaymentData() {
            const params = {
                month: this.formFilter.payment_month,
                year: this.formFilter.years,
            };

            this.loading = true;
            axios.get("/payments", { params })
                .then(res => {
                    if (res.data.status === 2000 && res.data.result) {
                        this.localDataList = {
                            data: res.data.result.data || [],
                            option: res.data.result.option || {},
                        };
                    } else {
                        this.localDataList = { data: [], option: {} };
                    }
                })
                .finally(() => this.loading = false);
        },

        formatAmount(amount) {
            return new Intl.NumberFormat("en-US", {
                style: "decimal",
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(amount);
        },

        getColumnTotal(columnKey) {
            if (!this.localDataList || !Array.isArray(this.localDataList.data)) return this.formatAmount(0);

            const total = this.localDataList.data.reduce((sum, item) => {
                const val = Number(item[columnKey]) || 0;
                return sum + val;
            }, 0);

            return this.formatAmount(total);
        },


        assign() {
            this.formFilter.payment_month = this.formFilter.payment_month || String(new Date().getMonth() + 1).padStart(2, '0');
            this.formFilter.years = this.formFilter.years || new Date().getFullYear();
            this.formObject.amount = '';
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

    },

    mounted() {
        this.getDataList();
        const today = new Date();
        const currentMonth = String(today.getMonth() + 1).padStart(2, '0');
        const currentYear = today.getFullYear();
        this.formFilter.payment_month = currentMonth;
        this.formFilter.years = currentYear;

        for (let i = currentYear - 1; i <= currentYear + 5; i++) {
            this.years.push(i);
        }
        this.assign();
    },
};
</script>

<style scoped>
.card-header {
    border-bottom: 1px solid #ddd;
}

.table thead th {
    background-color: #cccbcb;
    text-align: center;
}

.table tfoot th {
    background-color: #cccbcb;
    text-align: center;
}

.amount {
    text-align: right;
}
</style>
