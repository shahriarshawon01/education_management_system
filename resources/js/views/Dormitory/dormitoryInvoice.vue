<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Transport Invoice"
                    :default-object="{ month: '', years: '' }">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Dormitory Invoice" page-modal-title="Dormitory Invoice"
                            :default-add-button="false">
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select name="month" v-model="formFilter.month" class="form-control">
                                <option value="">Select Month</option>
                                <option v-for="(month, index) in getMonths()" :key="index" :value="index + 1">
                                    {{ month }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="month" v-model="formFilter.years" class="form-control">
                                <option value="">Select Year</option>
                                <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                            </select>
                        </div>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>
                                <strong>Date : </strong>{{ data.to_date }}<br />
                                <strong>Month : </strong>{{ capitalizeFirst(data.month) }}<br />
                            </td>
                            <td>
                                <strong>Code : </strong>{{ data.invoice_code }}<br />
                                <strong>Payer :</strong> {{ getInvoiceType(data.invoice_type) }} <br />
                                <strong>Category :</strong> {{ getInvoiceCategory(data.invoice_category) }}
                            </td>
                            <template v-if="data.invoice_type == '1'">
                                <td>
                                    <strong>Name : </strong>{{ data.student_name_en }}<br />
                                    <strong>ID : </strong>{{ data.student_roll }}<br />
                                    <strong>Class : </strong>{{ data ? data.class_name : "" }}<br />
                                    <strong>Session : </strong>{{ data ? data.session_title : "" }}<br />

                                </td>
                            </template>
                            <template v-if="data.invoice_type == '2'">
                                <td>
                                    <strong>Name : </strong>{{ data.employee_name }}<br />
                                    <strong>ID : </strong>{{ data.employee_id }}<br />
                                    <strong>Department : </strong>{{ data ? data.department_name : "" }}<br />
                                    <strong>Designation : </strong>{{ data ? data.designation_name : "" }}<br />
                                    <strong>Employee Type : </strong>{{ getEmployeeType(data.employee_type) }}<br />
                                </td>
                            </template>
                            <template v-if="data.invoice_type == '3'">
                                <td>
                                    <strong>Name : </strong>Guest<br />
                                </td>
                            </template>
                            <td style="text-align: left">
                                {{ data.created_by_username }}
                            </td>
                            <td style="text-align: right">
                                {{ showData(data, "total_amount") }}
                            </td>
                            <td style="text-align: right">
                                {{ showData(data, "total_waiver") }}
                            </td>
                            <td style="text-align: right">
                                {{ showData(data, "total_payable") }}
                            </td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <a class="link-primary" @click="viewDetails(data)"><i class="fa fa-eye"></i></a>
                                    <template v-if="data.payments_count === 0">
                                        <a v-if="can('generate_invoice_delete')" class="link-danger"
                                            @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>

        <general-modal modal-id="detailsModal" modalSize="modal-lg">
            <template v-slot:title>
                <span class="modal-title">Invoice Details</span>
            </template>
            <template v-slot:body>
                <div class="modal-actions mb-3 text-right">
                    <a class="btn btn-outline-warning btn-print" @click="printData('printInvoice')">
                        <i class="fa fa-print"></i> Print
                    </a>
                </div>
                <div id="printInvoice" class="invoice-container" v-if="invoiceDetails && invoiceDetails.invoice">
                    <div class="invoice-header mb-3 p-3 border rounded shadow-sm bg-light">
                        <div>
                            <span><strong>Invoice Code:</strong> {{ invoiceDetails.invoice.invoice_code }}</span><br />
                            <span><strong>Invoice Date:</strong> {{ invoiceDetails.invoice.to_date }}</span><br />
                            <span><strong>Invoice Month:</strong> {{ capitalizeFirst(invoiceDetails.invoice.month)
                                }}</span><br />
                            <span><strong>Payer Type:</strong> {{ getInvoiceType(invoiceDetails.invoice.invoice_type)
                                }}</span><br />
                            <span><strong>Invoice Category:</strong> {{
                                getInvoiceCategory(invoiceDetails.invoice.invoice_category)
                                }}</span><br />
                        </div>
                        <template v-if="invoiceDetails.invoice.invoice_type == 1">
                            <hr />
                            <span><strong>Name:</strong> {{ invoiceDetails.invoice.student_name_en
                                }}</span><br />
                            <span><strong>Session:</strong> {{ invoiceDetails.invoice.session_title }}</span><br />
                            <span><strong>ID:</strong> {{ invoiceDetails.invoice.student_roll }}</span>
                        </template>

                        <template v-else-if="invoiceDetails.invoice.invoice_type == 2">
                            <hr />
                            <span><strong>Name:</strong> {{ invoiceDetails.invoice.employee_name
                                }}</span><br />
                            <span><strong>Department:</strong> {{ invoiceDetails.invoice.department_name
                                }}</span><br />
                            <span><strong>Designation:</strong> {{ invoiceDetails.invoice.designation_name
                            }}</span><br>
                            <span><strong>Type:</strong> {{
                                getEmployeeType(invoiceDetails.invoice.employee_type)
                            }}</span>
                        </template>
                        <template v-else-if="invoiceDetails.invoice.invoice_type == 3">
                            <hr />
                            <span><strong>Guest Name:</strong> Guest</span>
                        </template>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped shadow-sm">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th style="width: 5%">SL</th>
                                    <th>Component</th>
                                    <th style="width: 15%">Amount</th>
                                    <th style="width: 15%">Waiver</th>
                                    <th style="width: 15%">Net Pay</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, index) in invoiceDetails.invoice_details" :key="index">
                                    <td class="text-center">{{ index + 1 }}</td>
                                    <td>{{ data.component_name }}</td>
                                    <td class="text-right">{{ data.invoice_amount }}</td>
                                    <td class="text-right">{{ data.waiver_amount }}</td>
                                    <td class="text-right">{{ data.payable_amount }}</td>
                                </tr>
                                <tr class="bg-light font-weight-bold">
                                    <td colspan="2" class="text-right">Total</td>
                                    <td class="text-right">{{ showData(invoiceDetails.invoice, "total_amount") }}</td>
                                    <td class="text-right">{{ showData(invoiceDetails.invoice, "total_waiver") }}</td>
                                    <td class="text-right">{{ showData(invoiceDetails.invoice, "total_payable") }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </general-modal>
    </div>
</template>

<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";
import GeneralModal from "../../components/generalModal";
export default {
    name: "dormitoryInvoice",
    components: { GeneralModal, PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ["Sl", "Generate", "Invoice", "Payer Details", "Created By", "Total Amount", "Waiver", "Payable", "Action"],
            invoiceDetails: [],
            years: [],
        };
    },

    methods: {

        getMonths() {
            return [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December"
            ];
        },

        viewDetails(data) {
            const _this = this;
            var URL = `${_this.urlGenerate()}/${data.id}`;
            _this.getData(URL, "get", {}, {}, function (retData) {
                _this.invoiceDetails = retData;
                console.log('_this.invoiceDetails : ', _this.invoiceDetails);

                _this.openModal("detailsModal", false);
            });
        },

        capitalizeFirst(value) {
            if (!value) return '';
            return value.charAt(0).toUpperCase() + value.slice(1);
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

        getEmployeeType(value) {
            switch (parseInt(value)) {
                case 1:
                    return "Teacher";
                case 2:
                    return "Staff";
                case 3:
                    return "Support Staff";
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

        generateYears() {
            const currentYear = new Date().getFullYear();
            for (let year = currentYear; year >= 2020; year--) {
                this.years.push(year);
            }
        },
    },

    mounted() {
        const _this = this;
        _this.getDataList();
        _this.generateYears();
        _this.getGeneralData(['class']);
    },
};
</script>

<style scoped>
.table-center .action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.total-section {
    text-align: center;
}

.modal-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 0px;
}

.btn-print {
    font-size: 0.9rem;
    font-weight: bold;
}

.invoice-container {
    padding: 5px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
}

.invoice-header {
    font-size: 1rem;
    margin-bottom: 5px;
    line-height: 1.5;
}

.table {
    margin: 0;
    background-color: #fff;
    border-collapse: collapse;
    border: 1px solid #ddd;
}

.table thead th {
    background-color: #4caf50;
    color: white;
    text-align: center;
    font-weight: bold;
    padding: 10px;
}

.table tbody td {
    padding: 10px;
    font-size: 0.9rem;
}

.table .text-right {
    text-align: right;
}

.total-section {
    background-color: #f2f2f2;
    font-weight: bold;
}

.total-section th {
    font-weight: bold;
    color: #333;
}
</style>
