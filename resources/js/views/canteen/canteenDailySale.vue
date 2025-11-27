<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Canteen Daily Sale"
                    :default-object="{ member_type: '', meal_time_id: '' }">
                    <template v-slot:page_top>
                        <page-top :default-object="{ member_type: '', meal_time_id: '' }"
                            topPageTitle="Canteen Daily Sale" :default-add-button="false">
                            <template #default>
                                <button v-if="can('canteen_daily_sale_add')"
                                    @click="openModal('canteenSaleModal', '💵 Canteen Daily Sale', false, false, {})"
                                    class="btn btn-primary">
                                    <i class="fa fa-plus"></i> 💵 Canteen Sale
                                </button>

                            </template>
                        </page-top>
                    </template>

                    <template v-slot:filter>
                        <div class="col-md-2">
                            <datepicker v-model="formFilter.date" placeholder="Select Date" name="date"
                                input_class="form-control">
                            </datepicker>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.sale_type" name="sale_type">
                                <option value="" selected>Sale Type</option>
                                <option value="1">Cash Sale</option>
                                <option value="2">Invoice Sale</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.member_type" name="member_type">
                                <option value="" selected>Member Type</option>
                                <option value="1">Student</option>
                                <option value="2">Employee</option>
                            </select>
                        </div>
                    </template>
                    <template v-slot:data>
                        <table class="table table-bordered">
                            <thead>
                                <tr class="background-style">
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Meal Time</th>
                                    <th>Date</th>
                                    <th>Member Type</th>
                                    <th>Sale Type</th>
                                    <th>Invoice Generate</th>
                                    <th>Menus</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, index) in dataList.data">
                                    <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                                    <td>{{ data.name }}</td>
                                    <td>{{ data.phone ?? '-' }}</td>


                                    <td>{{ data.meal_time ? data.meal_time.name : '-' }}</td>
                                    <td style="text-align: center;">{{ data.date }}</td>
                                    <td style="text-align: center;">{{ getMemberType(data.member_type) }}</td>
                                    <td style="text-align: center;">{{ getSaleType(data.sale_type) }}</td>
                                    <td style="text-align: center;">
                                        <span class="badge"
                                            :class="data.invoice_generate == 1 ? 'bg-success' : 'bg-danger'"
                                            style="font-size: 12px; padding: 4px 6px;">
                                            {{ data.invoice_generate == 1 ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span v-for="menu in data.menus" :key="menu.id" class="badge bg-info me-1"
                                            style="font-size: 12px; padding: 4px 6px;">
                                            {{ menu.item_name }}
                                        </span>
                                    </td>
                                    <td style="text-align: right;">{{ data.amount ?? '-' }}</td>
                                    <td style="display:flex; justify-content: center">
                                        <div class="hstack gap-3 fs-15">
                                            <a v-if="can('canteen_daily_sale_view')" class="link-primary"
                                                @click="viewDetails(data)"><i class="fa fa-file-invoice"></i></a>
                                            <a v-if="can('canteen_daily_sale_delete')" class="link-danger"
                                                @click="deleteInformation(index, data.id)"><i
                                                    class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>

                            <tfoot>
                                <tr class="table-footer">
                                    <th colspan="9" class="total-text">
                                        <i class="fa fa-wallet"></i>
                                        Total Amount
                                    </th>
                                    <th><span class="highlight">{{ totalAmount }} </span></th>
                                    <th class="text-center">—</th>
                                </tr>

                                <tr class="table-footer">
                                    <th colspan="9" class="total-text">
                                        <i class="fa fa-file-invoice-dollar"></i>
                                        Total Invoice Amount
                                    </th>
                                    <th><span class="highlight">{{ totalInvoiceAmount }} </span></th>
                                    <th class="text-center">—</th>
                                </tr>

                                <tr class="table-footer">
                                    <th colspan="9" class="total-text">
                                        <i class="fa fa-wallet"></i>
                                        Total Cash Sale On
                                        <span class="highlight">{{ formFilter.date }}</span>
                                    </th>
                                    <th><span class="highlight">{{ totalCashAmount }} </span></th>
                                    <th class="text-center">—</th>
                                </tr>
                            </tfoot>

                        </table>

                        <div class="text-end mt-4">
                            <button @click="generateCashInvoice"
                                :disabled="!dataList.data || dataList.data.length === 0"
                                v-if="can('canteen_daily_sale_add')" class="generate-invoice-btn">
                                <i class="fa fa-file-invoice-dollar"></i> Generate Invoice
                            </button>
                        </div>
                        <Loader :visible="loading" />
                    </template>
                </data-table>
            </div>
        </div>

        <formModal modalSize="modal-lg" modal-id="canteenSaleModal" @submit="submitSaleData">
            <div class="row">

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Member</label>
                        <select v-model="formObject.member_type" name="member_type" class="form-control"
                            v-validate="'required'">
                            <option value="">Select member</option>
                            <option :value="1">Student</option>
                            <option :value="2">Employee</option>
                            <option :value="3">Guest</option>
                        </select>
                    </div>
                </div>

                <!-- Student Form Section -->
                <template v-if="formObject.member_type == 1">
                    <div class="col-md-3">
                        <label class="form-label">Student ID</label>
                        <autocomplete v-model="formObject.student_roll" api-url="/api/get-student"
                            placeholder="Enter Student ID" name="student_roll" v-validate="'required'"
                            validation_name="Student ID" @select="fillStudentData" />
                    </div>
                </template>

                <!-- Employee Form Section -->
                <template v-if="formObject.member_type == 2">
                    <div class="col-md-3">
                        <label class="form-label">Employee ID</label>
                        <autocomplete v-model="formObject.employee_id" api-url="/api/get-employee"
                            placeholder="Enter Employee ID" name="employee_id" validation_name="Employee ID"
                            @select="fillEmployeeData" />
                    </div>
                </template>

                <div class="col-md-3">
                    <label class="form-label">Payment Date</label>
                    <datepicker v-model="formObject.date" name="date" input_class="form-control"
                        :disabled-dates="{ to: new Date(new Date().setDate(new Date().getDate() - 1)) }"
                        v-validate="'required'" placeholder="Payment Date">
                    </datepicker>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Meal Time</label>
                    <select v-model="formObject.meal_time_id" class="form-control" v-validate="'required'"
                        name="meal_time_id">
                        <option value="">Select Time</option>
                        <template v-for="(data, index) in requiredData.mealTimes">
                            <option :value="data.id">{{ data.name }}</option>
                        </template>
                    </select>
                </div>

                <!-- Student Profile -->
                <template v-if="formObject.member_type == 1">
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div v-if="selectedStudent"
                                class="profile-card shadow-sm d-flex align-items-center p-1 mb-1 bg-white rounded-4">
                                <div
                                    class="avatar bg-primary text-white d-flex align-items-center justify-content-center me-3">
                                    <i class="fa fa-user-graduate"></i>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="text-muted fw-bold">
                                        <span class="fs-8 text-dark">{{ selectedStudent.name }}</span>
                                        <span class="fs-9">({{ selectedStudent.student_roll }})</span>
                                    </div>
                                    <div class="text-muted small mt-1">
                                        <span class="fw-semibold text-dark">Class:</span>
                                        {{ selectedStudent.class_name }} <br>
                                        <span class="fw-semibold text-dark">Is canteen Member:</span>
                                        <span class="status-indicator ms-1"
                                            :class="selectedStudent.canteen_member_status == 1 ? 'bg-success' : 'bg-danger'">
                                            {{ getMemberStatus(selectedStudent.canteen_member_status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Employee Profile -->
                <template v-if="formObject.member_type == 2">
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div v-if="selectedEmployee"
                                class="profile-card shadow-sm d-flex align-items-center p-1 mb-1 bg-white rounded-4">
                                <div
                                    class="avatar bg-success text-white d-flex align-items-center justify-content-center me-3">
                                    <i class="fa fa-user-tie"></i>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="text-muted fw-bold">
                                        <span class="fs-8 text-dark">{{ selectedEmployee.name }}</span>
                                        <span class="fs-9">({{ selectedEmployee.employee_id }})</span>
                                    </div>
                                    <div class="text-muted small mt-1">
                                        <span class="fw-semibold text-dark">Designation:</span>
                                        {{ selectedEmployee.designation }} <br>
                                        <span class="fw-semibold text-dark">Is canteen Member:</span>
                                        <span class="status-indicator ms-1"
                                            :class="selectedEmployee.canteen_member_status == 1 ? 'bg-success' : 'bg-danger'">
                                            {{ getMemberStatus(selectedEmployee.canteen_member_status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Guest Form Section -->
                <template v-if="formObject.member_type == 3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" v-model="formObject.name" name="name" class="form-control"
                            v-validate="'required'" placeholder="Enter Name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" v-model="formObject.phone" name="phone" class="form-control"
                            placeholder="Enter Phone">
                    </div>
                </template>
            </div>

            <!-- Sale Type ) -->
            <template v-if="[1, 2, 3].includes(formObject.member_type)">
                <div class="col-md-6 mt-1">
                    <label class="form-label fw-semibold text-dark">Sale Type</label>
                    <div class="d-flex align-items-center gap-3 bg-light p-2 rounded-3 border">
                        <label
                            class="d-flex align-items-center bg-white shadow-sm px-2 py-2 rounded-3 border cursor-pointer flex-grow-1 transition-all"
                            :class="{
                                'border-success shadow-lg bg-success bg-opacity-10': formObject.sale_type == 1,
                                'border-light': formObject.sale_type != 1
                            }">
                            <input type="radio" v-model="formObject.sale_type" :value="1"
                                class="me-2 form-check-input" />
                            <i class="fa fa-money-bill-wave text-success me-2"></i>
                            Cash Sale
                        </label>
                        <label v-if="formObject.canteen_member_status == 1"
                            class="d-flex align-items-center bg-white shadow-sm px-2 py-2 rounded-3 border cursor-pointer flex-grow-1 transition-all"
                            :class="{
                                'border-primary shadow-lg bg-primary bg-opacity-10': formObject.sale_type == 2,
                                'border-light': formObject.sale_type != 2
                            }">
                            <input type="radio" v-model="formObject.sale_type" :value="2"
                                class="me-2 form-check-input" />
                            <i class="fa fa-file-invoice-dollar text-primary me-2"></i>
                            Invoice Sale
                        </label>
                    </div>
                </div>
            </template>

            <table class="table table-bordered set-menu-table mt-2">
                <thead>
                    <tr class="background-style">
                        <th style="width: 30% !important; text-align: center;">Menu Item</th>
                        <th style="width: 15% !important; text-align: center;">Amount</th>
                        <th style="width: 15% !important; text-align: center;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="(row, index) in formObject.set_canteen_menu"
                        :key="'menu-row-' + index + '-' + menuItemsKey">
                        <td>
                            <select v-select2 v-model="row.menu_item_id" name="menu_item_id" v-validate="'required'"
                                data-vv-name="menu_item_id" class="form-control" @change="updateMenuPrice(row)"
                                :key="'select-' + index + '-' + menuItemsKey">
                                <option value="" disabled>Menu Item</option>
                                <template v-for="eachData in requiredData.menuItem">
                                    <option :value="eachData.id">{{ eachData.item_name }}</option>
                                </template>
                            </select>
                        </td>

                        <td>
                            <input type="number" v-model="row.amount" name="amount" class="form-control text-center"
                                placeholder="Enter Amount" />
                        </td>


                        <td class="text-center">
                            <a class="btn btn-outline-success"
                                :class="{ 'disabled': isEditMode, 'no-pointer': isEditMode }" :disabled="isEditMode"
                                @click="addRow(formObject.set_canteen_menu, {
                                    menu_item_id: '',
                                    amount: '',
                                })">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </a>
                            <a class="btn btn-outline-danger"
                                :class="{ 'disabled': isEditMode, 'no-pointer': isEditMode }" :disabled="isEditMode"
                                v-if="formObject.set_canteen_menu.length > 1"
                                @click="deleteRow(formObject.set_canteen_menu, index)">
                                <i class="fa fa-close" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </formModal>

        <general-modal modal-id="detailsModal" modalSize="modal-lg">
            <template v-slot:title>
                <span>Canteen Payment Receipt</span>
            </template>
            <template v-slot:body>
                <a class="btn btn-outline-warning print-receipt" style="text-align: right !important;"
                    @click="printData('printReceipt')">
                    <i class="fa fa-print"></i> Print
                </a><br><br>
                <div id="printReceipt">
                    <template v-if="paymentsDetails.payment">
                        <div class="receipt-container">
                            <!-- Student Copy -->
                            <div class="receipt-section">
                                <div class="report-header">
                                    <div class="school-info">
                                        <div class="logo">
                                            <img :src="getImage('uploads/logo.png')" class="school-logo" />
                                        </div>
                                        <h3 class="school-name">
                                            {{ paymentsDetails.payment.school?.title  }}
                                        </h3>

                                        <p class="school-address">
                                            {{ paymentsDetails.payment.school?.address }}
                                        </p>

                                        <p class="school-contact">
                                            <strong>E-mail:</strong>
                                            {{ paymentsDetails.payment.school?.email }}
                                        </p>

                                        <p class="school-contact">
                                            <strong>Phone:</strong>
                                            {{ paymentsDetails.payment.school?.phone }}
                                        </p>
                                        <h4 class="report-title">Canteen Payment Receipt</h4><br>
                                        <h4 class="report-copy">Customer Copy</h4>
                                    </div>
                                </div>

                                <strong>Invoice Number</strong>: {{ paymentsDetails.payment?.voucher_number }}<br>
                                <strong>Name</strong>: {{ paymentsDetails.payment?.name }}<br>
                                <strong>Phone</strong>: {{ paymentsDetails.payment?.phone }}<br>
                                <strong>Payment Date</strong>: {{ paymentsDetails.payment?.date }}<br>
                                <strong>Meal Time</strong>: {{ paymentsDetails.payment?.meal_time?.name }}<br>
                                <table class="table table-bordered mt-1">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Menu Item</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(menu, index) in paymentsDetails.menus" :key="menu.id">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ menu.item_name }}</td>
                                            <td style="text-align: right;">{{
                                                amountFormat(paymentsDetails.payment.amount) }}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-center">Total</th>
                                            <th style="text-align: right;">{{
                                                amountFormat(paymentsDetails.payment.amount) }}</th>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- Signatures -->
                                <div class="signature-section">
                                    <div class="signature-box">
                                        <p>Customer Signature</p>
                                    </div>
                                    <div class="signature-box">
                                        <p>Cashier Signature</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Canteen Copy -->
                            <div class="receipt-section">
                                <div class="report-header">
                                    <div class="school-info">
                                        <div class="logo">
                                            <img :src="getImage('uploads/logo.png')" class="school-logo" />
                                        </div>
                                       <h3 class="school-name">
                                            {{ paymentsDetails.payment.school?.title  }}
                                        </h3>

                                        <p class="school-address">
                                            {{ paymentsDetails.payment.school?.address }}
                                        </p>

                                        <p class="school-contact">
                                            <strong>E-mail:</strong>
                                            {{ paymentsDetails.payment.school?.email }}
                                        </p>

                                        <p class="school-contact">
                                            <strong>Phone:</strong>
                                            {{ paymentsDetails.payment.school?.phone }}
                                        </p>
                                        <h4 class="report-title">Canteen Payment Receipt</h4><br>
                                        <h4 class="report-copy">Canteen Copy</h4>
                                    </div>
                                </div>

                                <strong>Invoice Number</strong>: {{ paymentsDetails.payment?.voucher_number }}<br>
                                <strong>Name</strong>: {{ paymentsDetails.payment?.name }}<br>
                                <strong>Phone</strong>: {{ paymentsDetails.payment?.phone }}<br>
                                <strong>Payment Date</strong>: {{ paymentsDetails.payment?.date }}<br>
                                <strong>Meal Time</strong>: {{ paymentsDetails.payment?.meal_time?.name }}<br>
                                <table class="table table-bordered mt-1">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Menu Item</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(menu, index) in paymentsDetails.menus" :key="menu.id">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ menu.item_name }}</td>
                                            <td style="text-align: right;">{{
                                                amountFormat(paymentsDetails.payment.amount) }}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-center">Total</th>
                                            <th style="text-align: right;">{{
                                                amountFormat(paymentsDetails.payment.amount) }}</th>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- Signatures -->
                                <div class="signature-section">
                                    <div class="signature-box">
                                        <p>Customer Signature</p>
                                    </div>
                                    <div class="signature-box">
                                        <p>Cashier Signature</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
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
import Loader from "../../components/loader.vue"
import axios from 'axios';

export default {
    name: "canteenDailySale",
    components: { PageTop, Pagination, DataTable, formModal, GeneralModal, Loader },
    data() {
        return {
            tableHeading: [],
            formModalId: "formModal",
            paymentsDetails: {
                payment: null,
                menus: []
            },
            totalPayable: 0,
            loading: false,
            menuItemsKey: 0,
            set_canteen_menu: [
                {
                    menu_item_id: '',
                    amount: '',
                },
            ],
            isEditMode: false,
            selectedEmployee: null,
            selectedStudent: null,
            canteen_member_status: null
        };
    },

    created() {
        this.formFilter.date = this.getCurrentDate();
    },

    watch: {
        'formObject.set_canteen_menu': {
            handler(newValue) {
                if (!newValue || newValue.length === 0) {
                    this.formObject.set_canteen_menu = [{
                        menu_item_id: '',
                        amount: '',
                    }];
                }
            },
            deep: true,
        },

        'formObject.member_type'(newVal) {
            if (newVal == 3) {
                this.fillGuestData({});
            }
        },

        'formObject.date'(newVal) {
            this.fetchMenuItems();
        },
        'formObject.meal_time_id'(newVal) {
            this.fetchMenuItems();
        },
    },

    methods: {

        getMemberType(type) {
            if (type == 1) return 'Student';
            if (type == 2) return 'Employee';
            if (type == 3) return 'Guest';
            return '-';
        },

        getSaleType(type) {
            if (type == 1) return 'Cash Sale';
            if (type == 2) return 'Invoice Sale';
            return '-';
        },

        fillStudentData(student) {
            if (student && student.id) {
                this.$set(this.formObject, 'student_id', student.id);
                this.$set(this.formObject, 'student_roll', student.student_roll);
                this.$set(this.formObject, 'name', student.student_name_en);
                this.$set(this.formObject, 'phone', student.student_phone);
                this.$set(this.formObject, 'student_class', student.class_name);
                this.$set(this.formObject, 'student_department', student.department_name);
                this.$set(this.formObject, 'section_name', student.section_name);
                this.$set(this.formObject, 'session_name', student.session_name);
                this.$set(this.formObject, 'school_name', student.school_name);
                this.$set(this.formObject, 'school_id', student.school_id);
                this.$set(this.formObject, 'canteen_member_status', student.is_canteen_member);
                this.$set(this.formObject, 'sale_type', student.is_canteen_member == 1 ? 2 : 1);

                this.selectedStudent = {
                    name: student.student_name_en,
                    student_roll: student.student_roll,
                    class_name: student.class_name,
                    canteen_member_status: student.is_canteen_member
                };

                this.$toastr('success', 'Student data filled successfully!', 'Success');
            } else {
                this.$toastr('error', 'Student data not found!', 'Error');
            }
        },

        fillEmployeeData(employee) {
            if (employee && employee.id) {
                this.$set(this.formObject, 'employee_primary_id', employee.id);
                this.$set(this.formObject, 'employee_id', employee.employee_id);
                this.$set(this.formObject, 'name', employee.name);
                this.$set(this.formObject, 'employee_department', employee.department);
                this.$set(this.formObject, 'employee_designation', employee.designation);
                this.$set(this.formObject, 'employee_email', employee.email);
                this.$set(this.formObject, 'phone', employee.phone);
                this.$set(this.formObject, 'school_name', employee.school_name);
                this.$set(this.formObject, 'school_id', employee.school_id);
                this.$set(this.formObject, 'canteen_member_status', employee.is_canteen_member);
                this.$set(this.formObject, 'sale_type', employee.is_canteen_member == 1 ? 2 : 1);

                this.selectedEmployee = {
                    name: employee.name,
                    employee_id: employee.employee_id,
                    designation: employee.designation,
                    canteen_member_status: employee.is_canteen_member
                };

                this.$toastr('success', 'Employee data filled successfully!', 'Success');
            } else {
                this.$toastr('error', 'Employee data not found!', 'Error');
            }
        },

        fillGuestData(guest) {
            if (guest) {
                this.$set(this.formObject, 'name', guest.name || '');
                this.$set(this.formObject, 'phone', guest.phone || '');
                this.$set(this.formObject, 'member_type', 3);
                this.$set(this.formObject, 'canteen_member_status', 0);
                this.$set(this.formObject, 'sale_type', 1);

                this.selectedGuest = {
                    name: guest.name || 'Guest Customer',
                    phone: guest.phone || '',
                    canteen_member_status: 0
                };

                this.$toastr('success', 'Guest selected — For Cash Sale.', 'Success');
            } else {
                this.$toastr('error', 'Guest data not found!', 'Error');
            }
        },

        getMemberStatus(status) {
            return status == 1 ? 'Yes' : 'No';
        },

        fetchMenuItems() {
            if (!this.formObject.date || !this.formObject.meal_time_id) {
                this.$set(this.requiredData, 'menuItem', []);
                return;
            }

            const params = {
                menu_date: this.formObject.date,
                meal_time_id: this.formObject.meal_time_id
            };

            axios.get(baseUrl + '/api/canteen_menu_items_price', { params })
                .then(response => {
                    if (response.data.status === 2000) {
                        this.$set(this.requiredData, 'menuItem', response.data.menuItems || []);
                        this.formObject.set_canteen_menu.forEach(row => {
                            row.menu_item_id = '';
                            row.amount = '';
                        });
                        this.$nextTick(() => {
                            this.menuItemsKey++;
                        });

                        if (this.requiredData.menuItem.length > 0) {
                            this.$toastr('success', `${this.requiredData.menuItem.length} menu items loaded!`, 'Success');
                        } else {
                            this.$toastr('error', 'No menu items found for selected date and meal time.', 'Error');
                        }
                    }
                })
                .catch((error) => {
                    console.error('Error fetching menu items:', error);
                    this.$set(this.requiredData, 'menuItem', []);
                    this.$toastr('error', 'Failed to load menu items.', 'Error');
                });
        },

        updateMenuPrice(row) {
            const selectedItem = this.requiredData.menuItem.find(
                item => item.id === row.menu_item_id
            );
            if (selectedItem) {
                this.$set(row, 'amount', selectedItem.price || 0);
            } else {
                this.$set(row, 'amount', 0);
            }
        },

        submitSaleData() {
            const _this = this;
            const validationMessages = {
                name: 'Please enter the name.',
                date: 'Please select the date.',
                amount: 'Please enter a valid amount.',
            };

            const totalAmount = _this.formObject.set_canteen_menu.reduce((sum, item) => sum + parseFloat(item.amount || 0), 0);
            if (!_this.formObject.name || _this.formObject.name.trim() === '') {
                _this.$toastr('error', validationMessages.name, "Validation Error");
                return;
            }

            if (!_this.formObject.date) {
                _this.$toastr('error', validationMessages.date, "Validation Error");
                return;
            }

            if (totalAmount <= 0) {
                _this.$toastr('error', validationMessages.amount, "Validation Error");
                return;
            }

            let payload = {
                name: _this.formObject.name,
                phone: _this.formObject.phone,
                date: _this.formObject.date,
                meal_time_id: _this.formObject.meal_time_id,
                member_type: _this.formObject.member_type,
                student_id: _this.formObject.student_id || null,
                employee_primary_id: _this.formObject.employee_primary_id || null,
                sale_type: _this.formObject.sale_type,
                is_canteen_member: _this.formObject.canteen_member_status,
                amount: _this.formObject.set_canteen_menu.reduce((sum, item) => sum + parseFloat(item.amount || 0), 0),
                menu_id: JSON.stringify(_this.formObject.set_canteen_menu.map(item => item.menu_item_id)),
            };

            const URL = `${baseUrl}/api/canteen_daily_sale`;

            _this.axios
                .post(URL, payload)
                .then((response) => {
                    if (response.data.status === 2000) {
                        _this.$toastr('success', response.data.message, "Success");
                        _this.closeModal('canteenSaleModal');
                        _this.assign();
                    } else {
                        _this.$toastr('error', response.data.message, "Error");
                    }
                })
                .catch((error) => {
                    _this.$toastr('error', 'Failed to submit cash payment.', "Error");
                });
        },

        amountFormat(amount) {
            return parseFloat(amount).toFixed(2);
        },

        randomBadgeColor(id) {
            const colors = ['bg-primary', 'bg-success', 'bg-warning', 'bg-info', 'bg-danger'];
            return colors[id % colors.length];
        },

        generateCashInvoice() {
            const _this = this;

            if (!_this.dataList.data || _this.dataList.data.length === 0) {
                _this.$toastr('warning', 'No data selected.', 'Warning');
                return;
            }
            const payload = _this.dataList.data.map(item => ({
                id: item.id,
                name: item.name,
                amount: item.amount,
                date: item.date,
                sale_type: item.sale_type,
                invoice_generate: item.invoice_generate,
            }));
            this.loading = true;
            const URL = baseUrl + "/api/cash_sale_invoice_generate";

            _this.axios.post(URL, payload)
                .then(response => {
                    if (response.data.status === 2000) {
                        _this.$toastr('success', response.data.message, "Success");
                    } else {
                        _this.$toastr('error', response.data.message, "Error");
                    }
                })
                .catch(error => {
                    _this.$toastr('error', 'Failed to generate invoice.', "Error");
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        viewDetails(data) {
            const _this = this;
            var URL = `${_this.urlGenerate()}/${data.id}`;
            _this.getData(URL, "get", {}, {}, function (retData) {
                _this.paymentsDetails = retData; // retData must have payment & menus
                _this.totalPayable = retData.payment?.amount || 0;
                _this.openModal('detailsModal', false);
            });
        },

        assign() {
            const _this = this;
            _this.$set(_this.formObject, 'date', this.getCurrentDate());
            _this.$set(_this.formObject, 'meal_time_id', '');
            _this.$set(_this.formObject, 'member_type', '');
            _this.$set(_this.formObject, 'amount', '');

            _this.$set(_this.formObject, 'set_canteen_menu', [{
                menu_item_id: '',
                amount: '',
            }]);
            _this.$set(_this.formFilter, 'sale_type', '');
            _this.$set(_this.formFilter, 'member_type', '');

            this.requiredData.menuItem = [];
            this.selectedStudent = null;
            this.selectedEmployee = null;
        },

        getCurrentDate() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
    },

    computed: {
        totalAmount() {
            if (!this.dataList.data) return 0;
            return this.dataList.data.reduce((sum, item) => {
                return sum + (parseFloat(item.amount) || 0);
            }, 0);
        },
        totalCashAmount() {
            if (!this.dataList.data) return 0;
            return this.dataList.data
                .filter(item => item.sale_type == 1)
                .reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
        },

        totalInvoiceAmount() {
            if (!this.dataList.data) return 0;
            return this.dataList.data
                .filter(item => item.sale_type == 2)
                .reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
        },

        totalNetPayment() {
            return this.totalCashAmount - this.totalInvoiceAmount;
        }
    },


    mounted() {
        const _this = this;
        _this.getDataList();
        _this.assign();
        _this.getGeneralData(["mealTimes"]);
    },
};
</script>

<style scoped>
.table-center .action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.background-style {
    background: #cccbcb;
    text-align: center;
}

.table-footer {
    background: #cccbcb;
}

.table-footer th {
    color: #555;
    font-size: 1em;
    font-weight: 600;
    text-align: right;
}

.table thead th {
    background-color: #cccbcb;
    text-align: center;
}

.total-text .highlight {
    color: #1d6a3e;
    font-weight: 700;
}

.table-footer .fa-wallet {
    color: #777;
    margin-right: 5px;
}

.generate-invoice-btn {
    background-color: #4a8a66;
    border: 1px solid #0d6efd;
    color: #fff;
    padding: 10px 20px;
    font-size: 15px;
    font-weight: 500;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease-in-out;
}

.generate-invoice-btn:hover {
    background-color: #024425;
    border-color: #0a58ca;
}

.generate-invoice-btn:active {
    background-color: #0a58ca;
    border-color: #0a53be;
    transform: scale(0.97);
}

.generate-invoice-btn:focus {
    outline: none;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.5);
}

.generate-invoice-btn:disabled {
    background-color: #0d6efd;
    border-color: #0d6efd;
    opacity: 0.65;
    cursor: not-allowed;
    box-shadow: none;
}

.main_component {
    font-family: "Poppins", sans-serif
}

.report-header {
    text-align: center;
    margin-bottom: 1px;
}

.report-header .school-name {
    font-size: 18px;
    font-weight: bold;
    color: #2c3e50;
    margin: 0;
    margin-top: -20px;
}

.report-header .school-address {
    font-size: 15px;
    color: #7f8c8d;
    margin: 2px 0;
}

.report-header .school-contact {
    margin-top: 0;
    margin-bottom: 0px !important;
}

.report-header .report-title {
    font-size: 16px;
    font-weight: 500;
    color: #34495e;
    text-transform: uppercase;
    border-top: 1px solid #bdc3c7;
    border-bottom: 1px solid #bdc3c7;
    padding: 2px 0;
    display: inline-block;
    margin-top: 0px;
}

.report-header .report-copy {
    font-size: 15px;
    font-weight: 500;
    color: #34495e;
    text-transform: uppercase;
    border-top: 1px solid #bdc3c7;
    border-bottom: 1px solid #bdc3c7;
    padding: 1px 0;
    display: inline-block;
    margin-top: 0px;
}

.school-logo {
    width: 60px;
    height: 60px;
    border: 2px solid #fff;
    border-radius: 50%;
    margin-bottom: 0px;
    margin-top: -35px;
}

.receipt-container {
    display: flex;
    gap: 10px;
    justify-content: space-between;
    position: relative;
}

.receipt-section {
    width: 49%;
    border: 1px solid #ddd;
    padding: 10px;
    background: #fff;
    position: relative;
}

.receipt-container::before {
    content: '\2702';
    font-size: 20px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 0 4px;
}

.receipt-container::after {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    width: 2px;
    height: 100%;
}

.report-header {
    text-align: center;
    margin-bottom: 1px;
}

.school-name {
    font-size: 20px;
    font-weight: bold;
}

.school-address {
    font-size: 14px;
}

.table {
    width: 100%;
}

.signature-section {
    display: flex;
    justify-content: space-between;
    margin-top: 40px;
}

.signature-box {
    width: 40%;
    text-align: center;
    border-top: 1px solid black;
    padding-top: 1px;
    font-weight: bold;
}

.profile-card {
    background: linear-gradient(145deg, #f9fafb, #ffffff);
    border: 1px solid #e9ecef;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.profile-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
}

.avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    font-size: 18px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.status-indicator {
    display: inline-block;
    color: #fff;
    font-size: 12px;
    font-weight: 500;
    padding: 1px 4px;
    border-radius: 50px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.status-indicator.bg-success {
    background-color: #28a745 !important;
}

.status-indicator.bg-danger {
    background-color: #dc3545 !important;
}

.profile-card .fw-bold {
    font-size: 15px;
}

.profile-card .small {
    font-size: 13px;
    opacity: 0.9;
}

.transition-all {
    transition: all 0.2s ease-in-out;
}
</style>
