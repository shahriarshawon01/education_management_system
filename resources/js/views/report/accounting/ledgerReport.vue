<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :defaultSearchButton="false" :table-heading="[]"
                    :default-object="{ student_id: '', class_id: '', invoice_id: '', from_date: '', to_date: '', invoice_category: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Student's Ledger Report"
                    :table-responsive="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Student Ledger Report" :default-add-button="false">
                            <button @click="printData('printDiv')" class="btn btn-outline-primary"><i
                                    class="fa-sharp fa-solid fa-print"></i> Print</button>
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <autocomplete v-model="formFilter.student_roll" :api-url="`/api/get-student`"
                                placeholder="Enter Student ID" name="student_roll" validation_name="Student ID"
                                @select="fillStudentData" />
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
                            <a class="btn btn-primary" @click="fetchLedgerData" :disabled="loading">Get Ledger</a>
                        </div>
                        <Loader :visible="loading" />
                    </template>

                    <template v-slot:bottom_data>
                        <template v-if="ledgerDataList.length > 0">

                            <!-- Reusable School Info Component -->
                            <schoolInfo title="Student Ledger Report" />

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="student-details-card">
                                        <div class="student-detail">
                                            <strong>Student Name:</strong>
                                            <span>{{ studentInfo.student_name }}</span>
                                        </div>
                                        <div class="student-detail">
                                            <strong>Student ID:</strong>
                                            <span>{{ studentInfo.student_id }}</span>
                                        </div>
                                        <div class="student-detail">
                                            <strong>Class:</strong>
                                            <span>{{ studentInfo.class_name }}</span>
                                        </div>
                                        <div class="student-detail">
                                            <strong>Guardian Phone:</strong>
                                            <span>{{ studentInfo.guardian_phone }}</span>
                                        </div>
                                        <div v-if="studentInfo.from_date != '-' && studentInfo.to_date != '-'"
                                            class="student-detail">
                                            <strong>Reporting Period:</strong>
                                            <span>{{ formatDate(studentInfo.from_date) }} &nbsp; - <span>{{
                                                formatDate(studentInfo.to_date)
                                                    }}</span></span>
                                        </div>

                                        <div class="student-detail">
                                            <strong>Admission Date:</strong>
                                            <span>{{ studentInfo.admission_date }}</span>
                                        </div>
                                        <div class="student-detail">
                                            <strong>Print Date:</strong>
                                            <span>{{ formatDate(new Date()) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="overflow-x: auto;">
                                <table class="table table-bordered grand-total-style">
                                    <thead>
                                        <tr>
                                            <th>Invoice Code</th>
                                            <th>Invoice Date</th>
                                            <th>Particulars</th>
                                            <th>Debit</th>
                                            <th>Credit</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-for="invoiceGroup in groupedLedgerData">
                                            <template v-for="(paymentGroup, pIndex) in invoiceGroup.paymentGroups">
                                                <tr v-for="(item, index) in paymentGroup.items"
                                                    :key="`${invoiceGroup.invoice_code}-${paymentGroup.payment_date}-${index}`">

                                                    <td v-if="pIndex === 0 && index === 0"
                                                        :rowspan="invoiceGroup.totalRows" class="align-middle">
                                                        {{ invoiceGroup.invoice_code }}
                                                    </td>
                                                    <td v-if="pIndex === 0 && index === 0"
                                                        :rowspan="invoiceGroup.totalRows"
                                                        class="text-center align-middle">
                                                        {{ formatDate(invoiceGroup.invoice_date) }}
                                                    </td>
                                                    <td>{{ item.particulars }}</td>
                                                    <td style="text-align: right;">{{ formatAmount(item.debit) }}</td>
                                                    <td style="text-align: right;">{{ formatAmount(item.credit) }}</td>
                                                    <td style="text-align: right;">{{ formatAmount(item.balance) }}</td>
                                                </tr>
                                            </template>
                                        </template>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-right">Grand Total</th>
                                            <th class="text-right">{{ formatAmount(grandTotal.debit) }}</th>
                                            <th class="text-right">{{ formatAmount(grandTotal.credit) }}</th>
                                            <th class="text-right">{{ formatAmount(grandTotal.balance) }}</th>
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
import PageTop from "../../../components/pageTop";
import schoolInfo from "../../../components/schoolInfo";
import Loader from "../../../components/loader.vue"

import axios from 'axios';
export default {
    name: "ledgerReport",
    components: { PageTop, DataTable, schoolInfo, Loader },

    data() {
        return {
            ledgerDataList: [],
            debitData: [],
            creditData: [],
            grandTotal: {},
            studentInfo: {},
            loading: false,
        };
    },

    methods: {
        fillStudentData(student) {
            if (student && student.id) {
                this.$set(this.formFilter, 'student_id', student.id);
                this.$set(this.formFilter, 'student_roll', student.student_roll);
                this.$set(this.formFilter, 'student_name', student.student_name_en);
                this.$set(this.formFilter, 'school_id', student.school_id);
                this.$set(this.formFilter, 'id', student.id);
            }
        },

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

        fetchLedgerData() {
            if (!this.formFilter.student_id || this.formFilter.student_id === "") {
                this.$toastr('error', 'Please enter a valid Student ID', 'Validation Error');
                return;
            }
            this.loading = true;
            axios.get("/api/reports/ledgers", { params: this.formFilter })
                .then((response) => {
                    const result = response.data.result;

                    if (!result || !result.ledger_data || Object.keys(result.ledger_data).length === 0) {
                        this.ledgerDataList = [];
                        this.studentInfo = {};
                        this.grandTotal = 0;
                        this.$toastr('warning', 'No ledger records found for the selected filter.', 'No Data');
                        return;
                    }

                    const transactions = [];
                    Object.values(result.ledger_data).forEach(invoice => {
                        transactions.push(...invoice);
                    });

                    this.ledgerDataList = transactions;

                    this.studentInfo = {
                        student_name: result.student_name,
                        student_id: result.student_id,
                        admission_date: result.admission_date,
                        class_name: result.class_name,
                        guardian_phone: result.guardian_phone,
                        from_date: result.from_date,
                        to_date: result.to_date,
                    };

                    this.grandTotal = result.grand_total;
                })
                .catch((error) => {
                    console.error("Failed to fetch ledger data:", error);
                    this.$toastr('error', 'Failed to fetch data from the server.', 'Error');
                })
                .finally(() => {
                    this.loading = false;
                });
        },
    },

    computed: {
        groupedLedgerData() {
            const grouped = {};
            this.ledgerDataList.forEach(transaction => {
                const invoiceKey = transaction.invoice_code;
                if (!grouped[invoiceKey]) {
                    grouped[invoiceKey] = {
                        invoice_code: transaction.invoice_code,
                        invoice_date: transaction.invoice_date,
                        invoice_category: transaction.invoice_category,
                        paymentGroups: {},
                        totalRows: 0,
                    };
                }

                const invoiceGroup = grouped[invoiceKey];
                const paymentKey = transaction.payment_date;

                if (!invoiceGroup.paymentGroups[paymentKey]) {
                    invoiceGroup.paymentGroups[paymentKey] = {
                        payment_date: transaction.payment_date,
                        items: [],
                    };
                }
                invoiceGroup.paymentGroups[paymentKey].items.push(transaction);
                invoiceGroup.totalRows++;
            });

            return Object.values(grouped).map(group => ({
                ...group,
                paymentGroups: Object.values(group.paymentGroups)
            }));
        }
    },

    mounted() {
        const _this = this;
        _this.$set(_this.formFilter, 'student_id', '');
        _this.getGeneralData(['class']);
    },
}
</script>

<style scoped>
table.table tr th:first-child,
table.table tr td:first-child {
    width: initial !important;
}

.table thead th {
    background-color: #cccbcb;
    text-align: center;
}

.table tfoot th {
    background-color: #cccbcb;
    text-align: center;
}

.sub-total {
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

@media print {
    .student-details-card {
        margin-bottom: -20px;
        margin-left: 15px;
    }

    .table,
    .table th,
    .table td {
        border: 1px solid #000;
        border-collapse: collapse;
    }
}
</style>
