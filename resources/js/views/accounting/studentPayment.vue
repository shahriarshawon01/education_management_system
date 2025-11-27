<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <div style="overflow-x: auto;">
                    <data-table :table-heading="tableHeading" table-title="Student Payment"
                        :default-object="{ class_id: '', section_id: '', invoice_category: '', payment_method: '' }">
                        <template v-slot:page_top>
                            <page-top topPageTitle="Student Payment" page-modal-title="Student Payment"
                                :default-object="{ date: '', invoice_id: '', payment_for: '', payment_category: '', invoice_category: '', payment_type: '', class_id: '', section_id: '', session_year_id: '', student_id: '', components: {}, totalValue: 0, totalWaiver: 0, totalPayable: 0, totalWaiver: 0, totalPaid: 0 }"></page-top>
                        </template>
                        <template v-slot:filter>
                            <div class="col-md-2">
                                <select v-model="formFilter.class_id" name="class_id" class="form-control" @change="
                                    getGeneralData({
                                        section: { class_id: formFilter.class_id },
                                    })
                                    ">
                                    <option value="">Select Class</option>
                                    <template v-for="(eachData, index) in requiredData.class">
                                        <option :value="eachData.id">{{ eachData.name }}</option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select v-model="formFilter.section_id" name="section_id" class="form-control">
                                    <option value="">Select Section</option>
                                    <template v-for="(eachData, index) in requiredData.section">
                                        <option :value="eachData.id">{{ eachData.name }}</option>
                                    </template>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select class="form-control" v-model="formFilter.invoice_category"
                                    name="invoice_category">
                                    <option value="">Select Invoice Category</option>
                                    <option value="1">Academic</option>
                                    <option value="2">Canteen</option>
                                    <option value="3">Dormitory</option>
                                    <option value="4">Transport</option>
                                    <option value="5">Cash payment</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select class="form-control" v-model="formFilter.payment_method" name="payment_method">
                                    <option value="">Select Payment Type</option>
                                    <option value="1">Cash</option>
                                    <option value="2">Bank</option>
                                </select>
                            </div>
                        </template>

                        <template v-slot:data>
                            <tr v-for="(data, index) in dataList.data">
                                <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>

                                <template v-if="data.paid_by_type == 1">
                                    <td>
                                        <strong>Name : </strong>{{ data.student_name_en }} <br />
                                        <strong>Student ID : </strong>{{ data.student_roll }} <br />
                                        <strong>Class : </strong>{{ data.class_name }} <br />
                                        <strong>Session : </strong>{{ data.session_title }} <br />
                                        <strong>Section : </strong>{{ data.section_name }} <br />
                                        <strong>Department : </strong>{{ data.student_department }}
                                    </td>
                                </template>

                                <template v-if="data.paid_by_type == 2">
                                    <td>
                                        <strong>Name : </strong>{{ data.employee_name }}<br />
                                        <strong>Employee ID : </strong>{{ data.employee_id }}<br />
                                        <strong>Department : </strong>{{ data ? data.employee_department : "" }}<br />
                                        <strong>Designation : </strong>{{ data ? data.employee_designation : "" }}
                                        <br />
                                        <strong>Phone : </strong>{{ data ? data.employee_phone : "" }}
                                        <br />
                                        <strong>Employee Type : </strong>{{ getEmployeeType(data.employee_type) }}
                                    </td>
                                </template>
                                <template v-if="data.paid_by_type == 3">
                                    <td>
                                        <strong>Name : </strong>Guest<br />
                                    </td>
                                </template>

                                <template>
                                    <td>
                                        <strong>Code : </strong>{{ data.invoice_code }}<br />
                                        <strong>Voucher Number : </strong>{{ data.voucher_number }}<br />
                                        <strong>Category :</strong> {{ getInvoiceCategory(data.invoice_category)
                                        }}<br />
                                        <strong>Payer :</strong> {{ getPaidByType(data.paid_by_type) }} <br />
                                        <strong>Type :</strong> {{ getInvoiceType(data.is_advance) }}
                                    </td>
                                </template>

                                <td style="text-align: right;">{{ getRemainingPayable(data) }}</td>
                                <td style="text-align: right;">{{ amountFormat(showData(data, 'total_payed')) }}</td>
                                <td style="text-align: right;">{{ getDueAmount(data) }}</td>
                                <td style="text-align: center;">{{ data.date }}</td>

                                <td style="text-align: left;">{{ data ? data.created_by : '-' }}</td>
                                <!-- <td style="text-align: left;">{{ showData(data.updated_user, 'username') }}</td> -->

                                <td style="text-align: center;">{{ getPaymentStatus(data.payment_type) }}</td>
                                <td class="table-center">
                                    <div class="hstack gap-3 fs-15 action-buttons">
                                        <a v-if="can('student_payment_view')" class="link-primary"
                                            @click="viewDetails(data)"><i class="fa fa-file-invoice"></i></a>
                                        <a v-if="can('student_payment_update')" class="link-primary"
                                            @click="editPayment(data.id)"><i class="fa fa-edit"></i></a>
                                        <a v-if="can('student_payment_delete')" class="link-danger"
                                            @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </data-table>
                </div>
            </div>
        </div>

        <formModal modalSize="modal-lg" @submit="submitForm(formObject, 'formModal')">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <label class="form-label">Payment Category</label>
                    <select @change="handlePaymentCategoryChange" v-model="formObject.payment_category"
                        name="payment_category" class="form-control" :disabled="isEditMode" v-validate="'required'">
                        <option value="">Select Category</option>
                        <option value="1">Academic</option>
                        <option value="2">Canteen</option>
                        <option value="3">Dormitory</option>
                        <option value="4">Transport</option>
                        <option value="5">Cash payment</option>
                    </select>
                </div>

                <template v-if="formObject.payment_category != 5">

                    <div class="col-md-4 mb-2">
                        <label class="form-label">Payment For</label>
                        <select v-model="formObject.payment_for" name="payment_for" class="form-control"
                            :disabled="isEditMode" v-validate="'required'">
                            <option value="">Select Type</option>
                            <option value="1">Student</option>
                            <option v-if="formObject.payment_category != 1" value="2">Employee</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-2" v-if="formObject.payment_for == '1'">
                        <label class="form-label">Student ID</label>
                        <autocomplete v-model="formObject.student_roll" :api-url="`/api/get-student`"
                            placeholder="Enter Student ID" name="student_roll" validation_name="Student ID"
                            @select="fillStudentData" :disabled="isEditMode" />
                    </div>

                    <div class="col-md-4 mb-2" v-if="formObject.payment_for == '2'">
                        <label class="form-label">Employee ID</label>
                        <autocomplete v-model="formObject.employee_id" api-url="/api/get-employee"
                            placeholder="Enter Employee ID" name="employee_id" validation_name="Employee ID"
                            @select="fillEmployeeData" :disabled="isEditMode" />
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="form-label">Invoice</label>
                        <select class="form-control" v-model="formObject.invoice_id" @change="loadInvoiceData"
                            :disabled="isEditMode">
                            <option value="">Select Invoice</option>
                            <template v-for="(data, index) in requiredData.invoice">
                                <option :value="data.id">{{ data.invoice_code }} [ Payable: {{ data.total_payable }}]
                                </option>
                            </template>
                        </select>
                    </div>
                </template>

                <!-- Always show these (Cash & Others) -->
                <div class="col-md-4 mb-2">
                    <label class="form-label">Payment Date</label>
                    <datepicker v-model="formObject.date" name="date" input_class="form-control"
                        v-validate="'required|not_past_date'" placeholder="Payment Date">
                    </datepicker>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label">Payment Type</label>
                    <select class="form-control" v-model="formObject.payment_type" name="payment_type"
                        v-validate="'required'" @change="revalidateDate">
                        <option value="">Select Payment Type</option>
                        <option value="1">Cash</option>
                        <option value="2">Bank</option>
                    </select>
                </div>

                <div v-if="formObject.payment_type == 2" class="col-md-4 mb-2">
                    <label class="form-label">Bank Account</label>
                    <select class="form-control" v-model="formObject.bank_id" name="bank_id" v-validate="'required'">
                        <option value="">Select Bank Account</option>
                        <template v-for="(data, index) in requiredData.bank">
                            <option :value="data.id">{{ data.name }} - <strong>{{ data.account_number }}</strong>
                            </option>
                        </template>
                    </select>
                </div>

                <template v-if="formObject.payment_category != 5">

                    <div class="col-12 col-md-6" v-if="formObject.payment_for == '1' && formObject.student_name">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-header bg-primary text-white py-1 border-0">
                                <small class="fw-bold d-flex align-items-center">
                                    <i class="fas fa-user-graduate me-2"></i> Student Information
                                </small>
                            </div>
                            <div class="card-body py-1 px-2 small d-flex flex-wrap align-items-center gap-1">
                                <div><b>Name:</b> {{ formObject.student_name }} ({{ formObject.student_roll }})</div>
                                <div><b>Class:</b> {{ formObject.student_class || 'N/A' }}</div>
                                <div><b>Session:</b> {{ formObject.session_name || 'N/A' }}</div>
                                <div><b>Department:</b> {{ formObject.student_department || 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6" v-if="formObject.payment_for == '2' && formObject.employee_name">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-header bg-success text-white py-1 border-0">
                                <small class="fw-bold d-flex align-items-center">
                                    <i class="fas fa-briefcase me-2"></i> Employee Information
                                </small>
                            </div>
                            <div class="card-body py-1 px-2 small d-flex flex-wrap align-items-center gap-1">
                                <div><b>Name:</b> {{ formObject.employee_name }} <strong>({{ formObject.employee_id
                                }})</strong></div>
                                <div><b>Designation:</b> {{ formObject.employee_designation || 'N/A' }}</div>
                                <div><b>Department:</b> {{ formObject.employee_department || 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <!-- Add late fine  -->
            <div v-if="formObject.payment_category != 5" class="card mb-4 shadow-sm">
                <div class="card-header bg-primary d-flex justify-content-between align-items-center py-2 px-3">
                    <h5 class="mb-0 text-white fw-semibold">💳 Payment Collection</h5>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="addFineCheckbox"
                            v-model="formObject.late_fine" @change="addLateFine"
                            :disabled="$store.state.formType === 2">
                        <label class="form-check-label text-white ms-2" for="addFineCheckbox">
                            Add Late Fine
                        </label>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle table-sm">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th style="width: 2%;">Sl</th>
                                    <th style="width: 38%;">Component</th>
                                    <th style="width: 15%;">Amount</th>
                                    <th style="width: 15%;">Waiver</th>
                                    <th style="width: 15%;">Paid</th>
                                    <th style="width: 15%;">Payable</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, index) in formObject.components" :key="index">
                                    <td class="text-center">{{ index + 1 }}</td>
                                    <td>{{ data.component_name }}</td>

                                    <td><input class="form-control form-control-sm component-amount" readonly
                                            v-model="data.invoice_amount" :readonly="data.components_id !== 28"
                                            @input="updatePayableAmount(data)"></td>

                                    <td><input class="form-control form-control-sm component-amount" readonly
                                            v-model="data.waiver_amount"></td>
                                    <td><input class="form-control form-control-sm component-amount" readonly
                                            v-model="data.paid"></td>
                                    <td>
                                        <input :name="`payable_amount_${index}`" class="form-control form-control-sm"
                                            style="text-align: center;"
                                            v-validate="'decimal|max_value:' + parseInt(data.actual_payable)"
                                            v-model="data.payable_amount" :readonly="data.components_id == 28">
                                    </td>
                                </tr>
                                <template v-if="formObject.components && formObject.components.length">
                                    <tr class="fw-bold text-end text-nowrap total-style">
                                        <td colspan="2" class="text-center">Total</td>
                                        <td>{{ formObject.totalValue }}</td>
                                        <td>{{ formObject.totalWaiver }}</td>
                                        <td>{{ formObject.totalPaid }}</td>
                                        <td>{{ formObject.totalPayable }}</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- cash payment  -->
            <div v-if="formObject.payment_category == 5" class="card mb-4 shadow-sm">
                <div class="card-header bg-primary py-2 px-3">
                    <h5 class="mb-0 text-white fw-semibold">💵 Canteen Cash Invoices Collection</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle table-sm mb-0">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th>Sl</th>
                                    <th>Component</th>
                                    <th>Invoice Date</th>
                                    <th>Amount</th>
                                    <th>Paid</th>
                                    <th>Payable</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(comp, index) in combinedComponents" :key="index">
                                    <td class="text-center">{{ index + 1 }}</td>
                                    <td>{{ comp.components.name }}</td>
                                    <td class="text-center">{{ comp.invoice_date }}</td>
                                    <td class="component-amount">{{ comp.invoice_amount }}</td>
                                    <td class="component-amount">{{ comp.paid }}</td>
                                    <td class="component-amount">{{ comp.payable_amount }}</td>
                                </tr>

                                <!-- Grand Total -->
                                <tr class="fw-bold text-end text-nowrap total-style">
                                    <td colspan="3" class="text-center">Total</td>
                                    <td>{{ grandTotal.totalValue }}</td>
                                    <td>{{ grandTotal.totalPaid }}</td>
                                    <td>{{ grandTotal.totalPayable }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </formModal>

        <general-modal modal-id="detailsModal" modalSize="modal-xl">
            <template v-slot:title>
                <span>Student Payment Receipt</span>
            </template>
            <template v-slot:body>
                <a class="btn btn-outline-warning print-receipt" style="text-align: right !important;"
                    @click="printData('printReceipt')">
                    <i class="fa fa-print"></i> Print
                </a><br><br>
                <div id="printReceipt">
                    <template v-if="paymentsDetails.payment !== undefined">
                        <div class="receipt-container">
                            <!-- Student Copy -->
                            <div class="receipt-section">
                                <div class="report-header">
                                    <div class="school-info">
                                        <div class="logo">
                                            <img :src="getImage('uploads/logo.png')" class="school-logo" />
                                        </div>
                                        <h3 class="school-name">{{ paymentsDetails.payment.school_title }}</h3>
                                        <p class="school-address">{{ paymentsDetails.payment.school_address }}</p>
                                        <p class="school-contact"><strong>E-mail:</strong> {{
                                            paymentsDetails.payment.school_email }} <strong>Phone:</strong> {{
                                                paymentsDetails.payment.school_phone }}</p>
                                        <h4 class="report-title">{{
                                            getInvoiceCategory(paymentsDetails.payment.invoice_category) }} Payment
                                            Receipt</h4><br>
                                        <h4 class="report-copy">{{ getPaidByType(paymentsDetails.payment.paid_by_type)
                                        }} Copy ({{ getPaymentType(paymentsDetails.payment.payment_type) }})</h4>
                                    </div>
                                </div>

                                <strong>Voucher Number</strong>: {{ paymentsDetails.payment.voucher_number }}<br>

                                <template v-if="paymentsDetails.payment.paid_by_type == 1">
                                    <strong>Name</strong>: {{ paymentsDetails.payment.student_name_en }} ({{
                                        paymentsDetails.payment.student_roll }})<br>
                                    <strong>Class</strong>: {{ paymentsDetails.payment.class_name }}<br>
                                    <strong>Session</strong>: {{ paymentsDetails.payment.session_title }}<br>
                                    <strong>Roll</strong>: {{ paymentsDetails.payment.merit_roll }}<br>
                                </template>
                                <template v-if="paymentsDetails.payment.paid_by_type == 2">
                                    <strong>Name</strong>: {{ paymentsDetails.payment.employee_name }}<br>
                                    <strong>Designation</strong>: {{ paymentsDetails.payment.employee_designation }}<br>
                                    <strong>Employee ID</strong>: {{ paymentsDetails.payment.employee_id }}<br>
                                </template>
                                <template v-if="paymentsDetails.payment.paid_by_type == 3">
                                    <strong>Name</strong>: Guest Customer<br>
                                </template>
                                <strong>Payment Date</strong>: {{ paymentsDetails.payment.date }}<br>
                                <strong>Invoice Month</strong>: {{ monthFormate(paymentsDetails.payment.invoice_month)
                                }}<br>
                                <table class="table table-bordered mt-1">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Particulars</th>
                                            <th>Paid Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(data, index) in paymentsDetails.paymentComp" :key="index">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ data.component_name }}</td>
                                            <td style="text-align: right;">{{ amountFormat(data.payed) }}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-center">Total</th>
                                            <th style="text-align: right;">{{ amountFormat(totalPayable) }}</th>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="amount-words">
                                    <strong>In Words:</strong>
                                    <span class="underline-amount text-muted">
                                        {{ numberToWords(totalPayable).replace(' Taka Only', '') }}
                                    </span>
                                    <span class="text-muted"> Taka Only</span>
                                </div>

                                <!-- Signatures -->
                                <div class="signature-section">
                                    <div class="signature-box">
                                        <p>{{ getPaidByType(paymentsDetails.payment.paid_by_type) }} Signature</p>
                                    </div>
                                    <div class="signature-box">
                                        <p>Cashier Signature</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Office Copy -->
                            <div class="receipt-section">
                                <div class="report-header">
                                    <div class="school-info">
                                        <div class="logo">
                                            <img :src="getImage('uploads/logo.png')" class="school-logo" />
                                        </div>
                                        <h3 class="school-name">{{ paymentsDetails.payment.school_title }}</h3>
                                        <p class="school-address">{{ paymentsDetails.payment.school_address }}</p>
                                        <p class="school-contact"><strong>E-mail:</strong> {{
                                            paymentsDetails.payment.school_email }} <strong>Phone:</strong> {{
                                                paymentsDetails.payment.school_phone }}</p>
                                        <h4 class="report-title">{{
                                            getInvoiceCategory(paymentsDetails.payment.invoice_category) }} Payment
                                            Receipt</h4><br>
                                        <h4 class="report-copy">Office Copy ({{
                                            getPaymentType(paymentsDetails.payment.payment_type) }})</h4>
                                    </div>
                                </div>

                                <strong>Voucher Number</strong>: {{ paymentsDetails.payment.voucher_number }}<br>
                                <template v-if="paymentsDetails.payment.paid_by_type == 1">
                                    <strong>Name</strong>: {{ paymentsDetails.payment.student_name_en }} ({{
                                        paymentsDetails.payment.student_roll }})<br>
                                    <strong>Class</strong>: {{ paymentsDetails.payment.class_name }}<br>
                                    <strong>Session</strong>: {{ paymentsDetails.payment.session_title }}<br>
                                    <strong>Roll</strong>: {{ paymentsDetails.payment.merit_roll }}<br>
                                </template>
                                <template v-if="paymentsDetails.payment.paid_by_type == 2">
                                    <strong>Name</strong>: {{ paymentsDetails.payment.employee_name }}<br>
                                    <strong>Designation</strong>: {{ paymentsDetails.payment.employee_designation }}<br>
                                    <strong>Employee ID</strong>: {{ paymentsDetails.payment.employee_id }}<br>
                                </template>
                                <template v-if="paymentsDetails.payment.paid_by_type == 3">
                                    <strong>Name</strong>: Guest Customer<br>
                                </template>

                                <strong>Payment Date</strong>: {{ paymentsDetails.payment.date }}<br>
                                <strong>Invoice Month</strong>: {{ monthFormate(paymentsDetails.payment.invoice_month)
                                }}<br>
                                <table class="table table-bordered mt-1">
                                    <thead>
                                        <tr class="background-style">
                                            <th>SL</th>
                                            <th>Particulars</th>
                                            <th>Paid Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(data, index) in paymentsDetails.paymentComp" :key="index">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ data.component_name }}</td>
                                            <td style="text-align: right;">{{ amountFormat(data.payed) }}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-center">Total</th>
                                            <th style="text-align: right;">{{ amountFormat(totalPayable) }}</th>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="amount-words">
                                    <strong>In Words:</strong>
                                    <span class="underline-amount text-muted">
                                        {{ numberToWords(totalPayable).replace(' Taka Only', '') }}
                                    </span>
                                    <span class="text-muted"> Taka Only</span>
                                </div>

                                <!-- Signatures -->
                                <div class="signature-section">
                                    <div class="signature-box">
                                        <p>{{ getPaidByType(paymentsDetails.payment.paid_by_type) }} Signature</p>
                                    </div>
                                    <div class="signature-box">
                                        <p>Cashier Signature</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Bank Copy -->
                            <div class="receipt-section">
                                <div class="report-header">
                                    <div class="school-info">
                                        <div class="logo">
                                            <img :src="getImage('uploads/logo.png')" class="school-logo" />
                                        </div>
                                        <h3 class="school-name">{{ paymentsDetails.payment.school_title }}</h3>
                                        <p class="school-address">{{ paymentsDetails.payment.school_address }}</p>
                                        <p class="school-contact"><strong>E-mail:</strong> {{
                                            paymentsDetails.payment.school_email }} <strong>Phone:</strong> {{
                                                paymentsDetails.payment.school_phone }}</p>
                                        <h4 class="report-title">{{
                                            getInvoiceCategory(paymentsDetails.payment.invoice_category) }} Payment
                                            Receipt</h4><br>
                                        <h4 class="report-copy">Bank Copy ({{
                                            getPaymentType(paymentsDetails.payment.payment_type) }})</h4>
                                    </div>
                                </div>

                                <strong>Voucher Number</strong>: {{ paymentsDetails.payment.voucher_number }}<br>
                                <template v-if="paymentsDetails.payment.paid_by_type == 1">
                                    <strong>Name</strong>: {{ paymentsDetails.payment.student_name_en }} ({{
                                        paymentsDetails.payment.student_roll }})<br>
                                    <strong>Class</strong>: {{ paymentsDetails.payment.class_name }}<br>
                                    <strong>Session</strong>: {{ paymentsDetails.payment.session_title }}<br>
                                    <strong>Roll</strong>: {{ paymentsDetails.payment.merit_roll }}<br>
                                </template>
                                <template v-if="paymentsDetails.payment.paid_by_type == 2">
                                    <strong>Name</strong>: {{ paymentsDetails.payment.employee_name }}<br>
                                    <strong>Designation</strong>: {{ paymentsDetails.payment.employee_designation }}<br>
                                    <strong>Employee ID</strong>: {{ paymentsDetails.payment.employee_id }}<br>
                                </template>
                                <template v-if="paymentsDetails.payment.paid_by_type == 3">
                                    <strong>Name</strong>: Guest Customer<br>
                                </template>
                                <strong>Payment Date</strong>: {{ paymentsDetails.payment.date }}<br>
                                <strong>Invoice Month</strong>: {{ monthFormate(paymentsDetails.payment.invoice_month)
                                }}<br>
                                <table class="table table-bordered mt-1">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Particulars</th>
                                            <th>Paid Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(data, index) in paymentsDetails.paymentComp" :key="index">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ data.component_name }}</td>
                                            <td style="text-align: right;">{{ amountFormat(data.payed) }}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-center">Total</th>
                                            <th style="text-align: right;">{{ amountFormat(totalPayable) }}</th>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="amount-words">
                                    <strong>In Words:</strong>
                                    <span class="underline-amount text-muted">
                                        {{ numberToWords(totalPayable).replace(' Taka Only', '') }}
                                    </span>
                                    <span class="text-muted"> Taka Only</span>
                                </div>

                                <!-- Signatures -->
                                <div class="signature-section">
                                    <div class="signature-box">
                                        <p>{{ getPaidByType(paymentsDetails.payment.paid_by_type) }} Signature</p>
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
import { Validator } from 'vee-validate';
import axios from 'axios';

export default {
    name: "studentPayment",
    components: { GeneralModal, PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ['Sl', 'Payer Info', 'Invoice Details', 'Payable Amount', 'Paid Amount', 'Due Amount', 'Payment Date', 'Created By', 'Payment Status', 'Action'],
            paymentsDetails: [],
            totalAmount: 0,
            totalWaiver: 0,
            totalPayable: 0,
            invoiceComponent: [],

            addFine: true,

            isEditMode: false,
            cashInvoices: [],
            combinedComponents: [],

            grandTotal: {
                totalValue: 0,
                totalWaiver: 0,
                totalPaid: 0,
                totalPayable: 0,
            },
        }
    },

    watch: {
        'formObject.payment_type'(newVal) {
            this.$nextTick(() => {
                this.$validator.validate('date');
            });
        },
        'formObject.components': {
            deep: true,
            handler(newVal) {
                const _this = this;
                _this.$set(_this.formObject, 'totalValue', 0);
                _this.$set(_this.formObject, 'totalWaiver', 0);
                _this.$set(_this.formObject, 'totalPayable', 0);
                _this.$set(_this.formObject, 'totalPaid', 0);
                $.each(_this.formObject.components, function (index, value) {
                    _this.$set(_this.formObject, 'totalValue', parseInt(_this.formObject.totalValue) + parseInt(value.invoice_amount));
                    _this.$set(_this.formObject, 'totalWaiver', parseInt(_this.formObject.totalWaiver) + parseInt(value.waiver_amount));
                    _this.$set(_this.formObject, 'totalPaid', parseInt(_this.formObject.totalPaid) + parseInt(value.paid));
                    _this.$set(_this.formObject, 'totalPayable', parseInt(_this.formObject.totalPayable) + parseInt(value.payable_amount));
                });
            }
        },

        'formObject.payment_category'(newVal) {
            if (newVal == 5) { // Cash Payment selected
                this.formObject.invoice_id = '';
                this.formObject.components = [];
                this.formObject.totalValue = 0;
                this.formObject.totalWaiver = 0;
                this.formObject.totalPaid = 0;
                this.formObject.totalPayable = 0;

                this.loadCashPaymentInvoice();
            }
        }
    },

    methods: {

        handlePaymentCategoryChange() {
            const category = this.formObject.payment_category;

            if (category === '1') {
                this.formObject.payment_for = '1';

                if (this.formObject.student_roll) {
                    this.fillStudentData(this.formObject);
                }
            } else {
                this.formObject.payment_for = '';
                this.formObject.student_roll = '';
                this.formObject.student_id = '';
                this.formObject.employee_id = '';
            }
        },

        fillStudentData(student) {
            this.$set(this.requiredData, 'invoice', {});
            if (student && student.id) {
                this.$set(this.formObject, 'student_id', student.id);
                this.$set(this.formObject, 'student_roll', student.student_roll);
                this.$set(this.formObject, 'student_name', student.student_name_en);
                this.$set(this.formObject, 'class_id', student.class_id);
                this.$set(this.formObject, 'student_class', student.class_name);
                this.$set(this.formObject, 'student_department', student.department_name);
                this.$set(this.formObject, 'section_name', student.section_name);
                this.$set(this.formObject, 'session_name', student.session_name);
                this.$set(this.formObject, 'session_id', student.session_id);
                this.$set(this.formObject, 'school_name', student.school_name);
                this.$set(this.formObject, 'school_id', student.school_id);
                this.$set(this.formObject, 'id', student.id);

                this.fetchInvoices(student.id, 1);
            }
        },

        fillEmployeeData(employee) {
            this.$set(this.requiredData, 'invoice', {});
            if (employee && employee.id) {
                this.$set(this.formObject, 'employee_primary_id', employee.id);
                this.$set(this.formObject, 'employee_id', employee.employee_id);
                this.$set(this.formObject, 'employee_name', employee.name);
                this.$set(this.formObject, 'employee_department', employee.department);
                this.$set(this.formObject, 'employee_designation', employee.designation);
                this.$set(this.formObject, 'employee_email', employee.email);
                this.$set(this.formObject, 'employee_phone', employee.phone);
                this.$set(this.formObject, 'school_name', employee.school_name);
                this.$set(this.formObject, 'school_id', employee.school_id);
                this.$set(this.formObject, 'id', employee.id);

                this.fetchInvoices(employee.id, 2);
            }
        },

        fetchInvoices(userId, paymentFor) {
            const URL = this.urlGenerate('api/get_invoices');
            const invoiceCategory = this.formObject.payment_category;
            this.getData(URL, 'get', { user_id: userId, payment_for: paymentFor, invoice_category: invoiceCategory }, {}, (retData) => {
                if (retData && retData.length) {
                    this.$set(this.requiredData, 'invoice', retData);
                    this.$set(this.formObject, 'invoice_id', '');
                } else {
                    this.$toast.error('No invoices found for this consumer!');
                }
            });
        },

        loadInvoiceData() {
            if (this.isEditMode) return;
            if (!this.formObject.invoice_id) {
                this.$toast.error('Please select an Invoice!');
                return;
            }

            const params = {
                invoice_id: this.formObject.invoice_id,
                payment_for: this.formObject.payment_for,
                invoice_type_id: this.formObject.payment_for == 1 ? this.formObject.student_id
                    : this.formObject.payment_for == 2 ? this.formObject.employee_primary_id
                        : this.formObject.payment_for == 3 ? this.formObject.employee_primary_id
                            : this.formObject.guest_id
            };

            const URL = this.urlGenerate('api/user_wise_invoice');
            this.getData(URL, 'get', params, {}, (retData) => {
                if (retData && retData.length) {
                    this.$set(this.formObject, 'components', retData);
                } else {
                    this.$toast.error('No invoice data found!');
                }
            });
        },

        loadCashPaymentInvoice() {
            axios.get(`/api/get_cash_payment_invoice`)
                .then(response => {
                    if (response.data.status === 5000) {
                        this.cashInvoices = [];
                        this.formObject.components = [];
                        this.grandTotal = { totalValue: 0, totalWaiver: 0, totalPaid: 0, totalPayable: 0 };
                        this.$toastr('warning', response.data.message, 'No Data');
                        return;
                    }

                    let data = response.data.result || [];
                    this.cashInvoices = Array.isArray(data) ? data : [data];
                    this.combinedComponents = [];
                    this.grandTotal = { totalValue: 0, totalWaiver: 0, totalPaid: 0, totalPayable: 0 };

                    this.cashInvoices.forEach(invoice => {
                        if (invoice.components && invoice.components.length) {
                            invoice.components.forEach(comp => {
                                comp.invoice_date = invoice.date;
                                comp.invoice_code = invoice.invoice_code;
                                this.combinedComponents.push(comp);
                                this.grandTotal.totalValue += parseInt(comp.invoice_amount) || 0;
                                this.grandTotal.totalWaiver += parseInt(comp.waiver_amount) || 0;
                                this.grandTotal.totalPaid += parseInt(comp.paid) || 0;
                                this.grandTotal.totalPayable += parseInt(comp.payable_amount) || 0;
                            });
                        }
                    });
                    this.formObject.components = [...this.combinedComponents];
                    this.formObject.totalValue = this.grandTotal.totalValue;
                    this.formObject.totalWaiver = this.grandTotal.totalWaiver;
                    this.formObject.totalPaid = this.grandTotal.totalPaid;
                    this.formObject.totalPayable = this.grandTotal.totalPayable;
                })
                .catch(error => {
                    console.error('Error fetching cash payment invoices:', error);
                });
        },

        monthFormate(month) {
            if (!month) return '';
            month = month.toLowerCase();
            return month.charAt(0).toUpperCase() + month.slice(1);
        },

        updatePayableAmount(data) {
            if (data.components_id === 28) {
                data.payable_amount = parseInt(data.invoice_amount) || 0;
                data.actual_payable = data.payable_amount;
            }
        },

        addLateFine() {
            const LATE_FINE_ID = 28;

            if (this.formObject.late_fine) {
                const exists = this.formObject.components.some(
                    comp => comp.components_id === LATE_FINE_ID
                );

                if (!exists) {
                    this.formObject.components.push({
                        id: null,
                        invoice_id: this.formObject.invoice_id,
                        invoice_category: this.formObject.payment_category,
                        components_id: LATE_FINE_ID,
                        invoice_amount: "0",
                        waiver_amount: "0",
                        payable_amount: 0,
                        actual_payable: 0,
                        paid: "0",
                        created_at: null,
                        updated_at: null,
                        id: LATE_FINE_ID,
                        component_name: "Fine"
                    });
                }
            } else {
                this.formObject.components = this.formObject.components.filter(
                    comp => comp.components_id !== LATE_FINE_ID
                );
            }
        },

        amountFormat(amount) {
            return parseFloat(amount).toFixed(2);
        },

        getRemainingPayable(data) {
            const totalInvoiceAmount = parseFloat(data.total_payable || 0);
            const previousPayments = parseFloat(data.previous_payments || 0);

            const remainingPayable = totalInvoiceAmount - previousPayments;
            const amount = remainingPayable > 0 ? remainingPayable : 0;
            return this.amountFormat(amount);
        },

        getDueAmount(data) {
            const totalPayable = parseFloat(data.total_payable || 0);
            const totalPaid = parseFloat(data.invoice_total_paid || 0);
            const due = totalPayable - totalPaid;
            return due > 0 ? due.toFixed(2) : '0.00';
        },

        getPaymentStatus(paymentType) {
            return paymentType === 1 ? 'Cash' : 'Bank';
        },

        studentInvoiceComponent() {
            const _this = this;
            const invoiceId = {
                invoice_id: _this.formObject.invoice_id,
            };
            var URL = `${_this.urlGenerate('api/student_wise_invoice')}`;
            _this.getData(URL, "get", invoiceId, {}, function (retData) {
                _this.$set(_this.formObject, 'components', retData);

                _this.$set(_this.requiredData, 'components', retData);
            });
        },

        editPayment(id) {
            const _this = this;
            _this.isEditMode = true;

            const URL = `${_this.urlGenerate()}/${id}/edit`;

            _this.$store.commit('updateId', id);
            _this.$store.commit('formType', 2);

            _this.getData(URL, "get", {}, {}, function (retData) {
                const payment = retData.payment;
                const components = retData.components || [];
                let payment_for = null;
                if (payment.paid_by_type === 1) payment_for = '1';
                else if (payment.paid_by_type === 2) payment_for = '2';
                else if (payment.paid_by_type === 3) payment_for = '3';
                let payment_category = payment.invoice_category ? payment.invoice_category.toString() : null;
                const formObject = {
                    ...payment,
                    payment_category: payment_category,
                    payment_for: payment_for,
                    student_name: payment.student_name || null,
                    student_roll: payment.student_roll || null,
                    student_department: payment.student_department || null,
                    student_id: payment.student_id || null,
                    student_class: payment.student_class || null,
                    session_name: payment.session_name || null,
                    employee_name: payment.employee_name || null,
                    employee_id: payment.employee_id || null,
                    employee_primary_id: payment.employee_primary_id || null,
                    employee_designation: payment.employee_designation || null,
                    employee_department: payment.employee_department || null,
                    school_name: payment.school_name || null,
                    school_phone: payment.school_phone || null,
                    school_email: payment.school_email || null,
                    school_address: payment.school_address || null,
                    components: components.map(c => ({
                        ...c,
                        invoice_amount: c.invoice_amount,
                        paid: c.paid,
                        payable_amount: c.payable_amount,
                        actual_payable: c.actual_payable,
                        waiver_amount: c.waiver_amount,
                        component_name: c.component_name
                    }))
                };

                _this.$store.commit("formObject", formObject);
                _this.requiredData.invoice = _this.requiredData.invoice || [];
                const invoiceExists = _this.requiredData.invoice.find(i => i.id === formObject.invoice_id);
                if (!invoiceExists && formObject.invoice_id) {
                    _this.requiredData.invoice.push({
                        id: formObject.invoice_id,
                        invoice_code: formObject.invoice_code || 'N/A',
                        total_payable: formObject.total_payed || 0
                    });
                }
                _this.openModal('formModal', 'Edit Payments');
            });
        },

        viewDetails(data) {
            const _this = this;
            var URL = `${_this.urlGenerate()}/${data.id}`;
            _this.getData(URL, "get", {}, {}, function (retData) {
                _this.paymentsDetails = retData;
                _this.totalPayable = retData.payment.total_payed;
                _this.openModal('detailsModal', false);
            });
        },

        revalidateDate() {
            this.$validator.validate('date');
        },

        getPaidByType(value) {
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

        getPaymentType(value) {
            switch (parseInt(value)) {
                case 1:
                    return "Cash";
                case 2:
                    return "Bank";
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

        getInvoiceType(value) {
            return parseInt(value) === 1 ? "Advance" : "Regular";
        },

        numberToWords(num) {
            const a = [
                '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
                'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen',
                'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
            ];
            const b = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

            const inWords = (n) => {
                if (n < 20) return a[n];
                if (n < 100) return b[Math.floor(n / 10)] + (n % 10 ? ' ' + a[n % 10] : '');
                if (n < 1000) return a[Math.floor(n / 100)] + ' Hundred ' + (n % 100 ? inWords(n % 100) : '');
                if (n < 100000) return inWords(Math.floor(n / 1000)) + ' Thousand ' + (n % 1000 ? inWords(n % 1000) : '');
                if (n < 10000000) return inWords(Math.floor(n / 100000)) + ' Lakh ' + (n % 100000 ? inWords(n % 100000) : '');
                return inWords(Math.floor(n / 10000000)) + ' Crore ' + (n % 10000000 ? inWords(n % 10000000) : '');
            };

            return inWords(num).trim() + ' Taka Only';
        },
    },

    mounted() {
        const _this = this;

        _this.getDataList();
        _this.getGeneralData(
            ['class', 'session', 'account_student_class', 'school', 'authLoginUser', 'bank'],
            () => {
                const authUser = _this.requiredData?.authLoginUser || {};
                const roleName = authUser.role_name || '';
                Validator.extend('not_past_date', {
                    getMessage: () => `This payment date is not allowed for your role!`,
                    validate: (value) => {
                        const vm = _this;
                        const bypassRoles = ['SuperUser', 'ChiefAccount', 'ItHead'];
                        const oneMonthRoles = ['AccountInvoice'];
                        const selected = new Date(value);
                        const today = new Date();
                        const oneMonthAgo = new Date();
                        selected.setHours(0, 0, 0, 0);
                        today.setHours(0, 0, 0, 0);
                        oneMonthAgo.setMonth(today.getMonth() - 1);
                        oneMonthAgo.setHours(0, 0, 0, 0);

                        if (!value) return false;

                        const isBank = parseInt(_this.formObject.payment_type) === 2;
                        const userRole = _this.requiredData?.authLoginUser?.role_name || '';

                        if (bypassRoles.includes(userRole)) {
                            return true;
                        }
                        if (vm.isEditMode) {
                            if (oneMonthRoles.includes(userRole)) {
                                return selected >= oneMonthAgo && selected <= today;
                            }
                            return true;
                        }

                        if (oneMonthRoles.includes(userRole)) {
                            return selected >= oneMonthAgo && selected <= today;
                        }

                        if (isBank) {
                            return selected >= oneMonthAgo && selected <= today;
                        }
                        return selected >= today;
                    }
                });
            }
        );
        _this.$set(_this.formObject, 'invoice_id', '');
        _this.$set(_this.formObject, 'payment_category', '');
        _this.$set(_this.formObject, 'payment_for', '');
        _this.$set(_this.formObject, 'payment_type', '1');
        _this.$set(_this.formObject, 'date', '');
        _this.$set(_this.formObject, 'bank_id', '');
    }
}
</script>

<style scoped>
.component-amount {
    background-color: #f1f5f9;
    color: #111827;
    text-align: center;
    padding: 6px 10px;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    font-weight: 500;
}

.table-center .action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.total-style {
    font-weight: bold;
    background-color: #cccbcb;
}

.table thead th {
    background-color: #cccbcb;
    text-align: center;
}

.main_component {
    font-family: "Poppins", sans-serif
}

.report-header {
    text-align: center;
    margin-bottom: 1px;
}

.report-header .school-name {
    font-size: 16px;
    font-weight: bold;
    color: #2c3e50;
    margin: 0;
    margin-top: -25px;
}

.report-header .school-address {
    font-size: 13px;
    color: #7f8c8d;
    margin: 2px 0;
}

.report-header .school-contact {
    margin-top: 0;
    margin-bottom: 0px !important;
}

.report-header .report-title {
    font-size: 13px;
    font-weight: 500;
    color: #34495e;
    text-transform: uppercase;
    border-top: 1px solid #bdc3c7;
    border-bottom: 1px solid #bdc3c7;
    padding: 1px 0;
    display: inline-block;
    margin-top: 0px;
}

.report-header .report-copy {
    font-size: 13px;
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
    width: 55px;
    height: 55px;
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
    font-size: 16px;
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
}

.amount-words {
    background: #f8f9fa;
    padding: 1px 2px;
    margin-top: -8px;
    font-weight: 600;
    font-size: 11px;
}

.underline-amount {
    text-decoration: underline;
}

input[readonly],
textarea[readonly],
select[disabled] {
    cursor: not-allowed !important;
    background-color: #f9f9f9 !important;
    color: #666 !important;
}
</style>
