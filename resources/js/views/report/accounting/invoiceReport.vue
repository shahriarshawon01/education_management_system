<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]"
                    :default-object="{ school_id: '', invoice_type_id: '', class_id: '', session_id: '', class_id: '', invoice_id: '', invoice_category: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Invoice Wise Report"
                    :table-responsive="false" :defaultSearchButton="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Invoice Wise Report" :default-add-button="false">
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
                            <select v-select2 class="form-control" v-model="formFilter.student_id" name="student_id"
                                @change="getGeneralData({ invoice: { invoice_type_id: formFilter.student_id } })">
                                <option value="">Select Student</option>
                                <template v-for="(data, index) in requiredData.students">
                                    <option :value="data.id">{{ data.student_name_en }} {{ data.student_roll }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.invoice_id" name="invoice_id">
                                <option value="">Invoice</option>
                                <template v-for="(data, index) in requiredData.invoice">
                                    <option :value="data.id">{{ data.invoice_code }} [Payable : {{ data.total_payable
                                    }}]
                                    </option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <a class="btn btn-primary" @click="fetchInvoiceData" :disabled="loading">
                                Get Invoice
                            </a>
                        </div>
                        <!-- loader -->
                        <Loader :visible="loading" />
                    </template>

                    <template v-slot:bottom_data>
                        <template v-if="localDataList.length > 0">
                            <!-- Reusable School Info Component -->
                            <schoolInfo title="Student Invoice Report" />
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="report-details-card">
                                        <div class="value-detail">
                                            <strong>Class:</strong>
                                            <span>{{ className }}</span>
                                        </div>
                                        <div class="value-detail">
                                            <strong>Section:</strong>
                                            <span>{{ sectionName }}</span>
                                        </div>
                                        <div class="value-detail">
                                            <strong>Print Date:</strong>
                                            <span>{{ formatDate(new Date()) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-bordered grand-total-style">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th style="width: 300px;">Invoice</th>
                                        <th>Component</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Waiver</th>
                                        <th>Net Pay</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in localDataList">
                                        <td style="vertical-align: middle; text-align: center;">{{ index + 1 }}</td>
                                        <td class="student_style">
                                            <strong>Code : </strong>{{ data.invoice_code }}<br />
                                            <strong>Type :</strong> {{ getInvoiceType(data.invoice_type) }} <br />
                                            <strong>Category :</strong> {{ getInvoiceCategory(data.invoice_category) }}
                                        </td>
                                        <td style="vertical-align: middle; text-align: left;">{{ data.component_name }}
                                        </td>
                                        <td style="vertical-align: middle; text-align: center;">{{ data.date }}</td>
                                        <td style="vertical-align: middle; text-align: right;">{{ data.invoice_amount }}
                                        </td>
                                        <td style="vertical-align: middle; text-align: right;">{{ data.waiver_amount }}
                                        </td>
                                        <td style="vertical-align: middle; text-align: right;">{{ data.payable_amount }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot v-if="totalData">
                                    <tr>
                                        <th colspan="4">Total</th>
                                        <th style="vertical-align: middle; text-align: right;">{{ totalData.total_amount
                                            }}</th>
                                        <th style="vertical-align: middle; text-align: right;">{{ totalData.total_waiver
                                            }}</th>
                                        <th style="vertical-align: middle; text-align: right;">{{
                                            totalData.total_payable }}</th>
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
import Loader from "../../../components/loader.vue"
import axios from 'axios';


export default {
    name: "invoiceReport",
    components: { PageTop, Pagination, DataTable, schoolInfo, Loader },

    data() {
        return {
            localDataList: {},
            totalData: {},
            loading: false,
            sectionName: '',
            className: '',
        };
    },

    methods: {

        fetchInvoiceData() {
            const { class_id, student_id, invoice_id, invoice_category } = this.formFilter;

            const validationMessages = {
                class_id: 'Please select the class.',
                student_id: 'Please select the student.',
                invoice_id: 'Please select the invoice.'
            };

            if (!class_id) {
                this.$toastr('error', validationMessages.class_id, "Validation Error");
                return;
            }
            if (!student_id) {
                this.$toastr('error', validationMessages.section_id, "Validation Error");
                return;
            }
            if (!invoice_id) {
                this.$toastr('error', validationMessages.invoice_id, "Validation Error");
                return;
            }

            const filterParams = {
                class_id,
                student_id,
                invoice_id,
                invoice_category,
            };
            this.loading = true;
            axios.get("/api/reports/invoices", { params: filterParams })
                .then(res => {
                    const response = res.data;


                    if (response.status === 5000) {
                        this.localDataList = [];
                        this.$toastr('warning', response.message, "No Data Found");
                        return;
                    }

                    const result = response.result;
                    this.localDataList = result.data || [];
                    this.totalData = result || [];
                    this.invoiceData = res.data.data;
                    this.sectionName = result.section_name || '';
                    this.className = result.class_name || '';
                })
                .catch((error) => {
                    console.error("Failed to fetch collection data:", error);
                    this.$toastr('error', 'Failed to fetch data from the server.', 'Error');
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        getInvoiceType(value) {
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

    },
    mounted() {
        const _this = this;
        _this.$set(_this.formFilter, 'student_id', '');
        _this.getGeneralData(['school', 'class']);


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
    font-size: 12px !important;
}
</style>
