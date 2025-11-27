<template>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3>Invoice Add Form</h3>
                </div>
                <div class="col-md-6 text-end">
                    <router-link to="/admin/generate_invoice" class="btn btn-primary">
                        <i class="fa fa-arrow-left"></i> Back
                    </router-link>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form @submit.prevent="submitAttestationForm()">
                <div class="modal-content-wrapper" style="max-height: 70vh; overflow-y: auto; padding: 15px;">

                    <div class="row align-items-end g-3">
                        <!-- Session -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Session</label>
                            <select class="form-control" v-model="formObject.session_year_id" name="session_year_id"
                                v-validate="'required'" @change="studentCount()" :disabled="isEditMode">
                                <option value="">Select Session</option>
                                <template v-for="(data, index) in requiredData.session">
                                    <option :value="data.id">{{ data.title }}</option>
                                </template>
                            </select>
                        </div>

                        <!-- Class -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Class</label>
                            <select class="form-control" v-model="formObject.class_id" name="class_id"
                                v-validate="'required'" :disabled="isEditMode" @change="
                                    getGeneralData({
                                        students: {
                                            class_id: formObject.class_id,
                                            school_id: Config.school_id,
                                        },
                                    }),
                                    studentCount()
                                    ">
                                <option value="">Select Class</option>
                                <template v-for="(data, index) in requiredData.account_student_class">
                                    <option :value="data.id">{{ data.name }}</option>
                                </template>
                            </select>
                        </div>

                        <!-- Invoice Date -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Invoice Date</label>
                            <datepicker v-model="formObject.date" name="date" input_class="form-control"
                                v-validate="'required'">
                            </datepicker>
                        </div>

                        <!-- Invoice Month -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Invoice Month</label>
                            <input type="text" class="form-control" v-model="formObject.month" readonly
                                placeholder="Auto-filled" />
                        </div>

                        <!-- Invoice Type -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Invoice Type</label>
                            <select class="form-control" v-model="formObject.is_advance" name="is_advance"
                                @change="studentCount()" disabled>
                                <option value="0">Regular</option>
                                <option value="1">Advance</option>
                            </select>
                        </div>

                        <!-- Total Students (aligned right side) -->
                        <div class="col-md-2" v-if="totalStudent">
                            <label class="form-label fw-semibold text-secondary d-block">Total Students</label>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary p-2 px-3 fs-6">
                                    {{ totalStudentsCurrentFilter }}
                                </span>
                                <a class="btn btn-sm btn-outline-primary" @click="editInvoiceListByStudent">
                                    <i class="fa fa-pencil-alt"></i> Change
                                </a>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3" />
                    <div class="col-md-12">
                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-2 p-2 rounded shadow-sm"
                            style="background: linear-gradient(90deg, #357f78, #58a99d);">
                            <h5 class="mb-0 fw-bold text-white">
                                Component Details
                            </h5>
                            <small class="px-3 py-1 rounded-pill text-dark fw-semibold shadow-sm" style="font-size: 0.85rem; 
                                background: linear-gradient(90deg, #58a99d, #357f78); 
                                border: 1px solid #357f78;">
                                ⚠ Waiver calculation will be handled automatically on the back-end.
                            </small>

                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 2%; text-align:center;">Sl</th>
                                    <th style="width: 50%; text-align:center;">Component</th>
                                    <th style="width: 18%; text-align:center;">Amount</th>
                                    <th style="width: 15%; text-align:center;">Waiver</th>
                                    <th style="width: 15%; text-align:center;">Net Payable</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, index) in formObject.components">
                                    <td class="text-center">{{ index + 1 }}</td>
                                    <td>
                                        <template v-if="formType === 1">{{ showData(data, 'name') }}</template>
                                        <template v-if="formType === 2">{{ data.component_name }}</template>
                                    </td>
                                    <td>
                                        <input class="form-control form-control-sm text-end" v-model.number="data.value"
                                            @focus="clearZero(data, 'value')" @blur="restoreZero(data, 'value')"
                                            placeholder="Enter amount" />
                                    </td>

                                    <td>
                                        <input class="form-control form-control-sm text-end" v-model="data.waiver"
                                            @input="calculateNetPay(data)" readonly :disabled="isEditMode" />
                                    </td>
                                    <td><input class="form-control form-control-sm text-end" readonly
                                            v-model="data.net_pay" :disabled="isEditMode"></td>
                                </tr>

                                <template v-if="formObject.components && formObject.components.length">
                                    <tr class="fw-bold text-center">
                                        <th colspan="2">Total</th>
                                        <th class="text-center">{{ formObject.totalValue }}</th>
                                        <th class="text-center">{{ formObject.totalWaiver }}</th>
                                        <th class="text-center">{{ formObject.totalNetPay }}</th>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fa fa-check-circle me-1"></i> Submit
                        </button>
                    </div>
                </div>
            </form>



            <!-- Modal for student list -->
            <form-modal modal-id="editInvoiceModal" modalSize="modal-lg" :reset-form-data="false"
                :resetFormDataOnCloseIcon="false" color-class="bg-warning" :is-enable-print-submit="false">
                <div class="modal-header">
                    <h5 class="modal-title">Student List</h5>
                    <span>
                        Total Students: {{ totalClassStudents }}
                        <span v-if="selectedStudentCount > 0">
                            (Selected: {{ selectedStudentCount }})
                        </span>
                    </span>
                </div>
                <div class="modal-body">
                    <!-- Search bar -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <input type="text" class="form-control" v-model="searchQuery"
                                placeholder="Search by Student Name or ID" />
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" v-model="formObject.is_advance_status"
                                @change="studentCount()">
                                <option value="include">Include Advance</option>
                                <option value="exclude">Exclude Advance</option>
                            </select>
                        </div>
                    </div>

                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 5%; text-align: center;">
                                    <input type="checkbox" class="checkbox" @click="toggleSelectAll($event)"
                                        :checked="areAllSelected" />
                                </th>
                                <th>Student Name</th>
                                <th>Student ID</th>
                                <th style="width: 20%;">Waiver Status</th>
                            </tr>
                        </thead>
                        <tbody v-if="filteredStudentList.length">
                            <tr v-for="(student, index) in filteredStudentList" :key="index">
                                <td style="text-align: center;">
                                    <input type="checkbox" class="checkbox" v-model="student.checked"
                                        @change="updateSelectedStudents(student)" />
                                </td>
                                <td>{{ student.student_name_en }}</td>
                                <td>{{ student.student_roll }}</td>
                                <td style="text-align: center;">
                                    <span
                                        :class="{ 'text-danger': student.waiver === 'Waiver', 'text-success': student.waiver === 'No Waiver' }">
                                        {{ student.waiver }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No students found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </form-modal>
        </div>
    </div>
</template>

<script>
import FormModal from "../../components/formModal";
import PageTop from "../../components/pageTop";
import GeneralModal from "../../components/generalModal"
import formModal from "../../components/formModal"
import formComponent from "../parent/formComponent";

export default {
    name: "addStudentGenerateInvoice",
    components: { PageTop, FormModal, GeneralModal, formModal, formComponent },
    data() {
        return {
            details: [],
            invoiceDetails: [],
            totalValue: 0,
            totalWaiver: 0,
            totalNetPay: 0,
            totalStudent: "",
            studentList: [],
            isEditMode: false,
            searchQuery: "",
            totalClassStudents: 0,
        };
    },
    computed: {
        totalStudentsCurrentFilter() {
            return this.formObject && this.formObject.students ? this.formObject.students.length + ' Students' : ''
        },

        // For check student list
        areAllSelected() {
            return this.studentList.length > 0 && this.studentList.every((student) => student.checked);
        },

        filteredStudentList() {
            if (!this.searchQuery) return this.studentList;

            const query = this.searchQuery.toLowerCase();
            return this.studentList.filter(student =>
                student.student_name_en.toLowerCase().includes(query) ||
                student.student_roll.toLowerCase().includes(query)
            );
        },

        selectedStudentCount() {
            return this.studentList.filter(student => student.checked).length;
        }
    },

    watch: {
        'formObject.components': {
            deep: true,
            handler(newVal) {
                const _this = this;
                _this.$set(_this.formObject, 'totalValue', 0);
                _this.$set(_this.formObject, 'totalWaiver', 0);
                _this.$set(_this.formObject, 'totalNetPay', 0);
                $.each(_this.formObject.components, function (index, value) {
                    _this.$set(_this.formObject, 'totalValue', parseInt(_this.formObject.totalValue) + parseInt(value.value));
                    _this.$set(_this.formObject, 'totalWaiver', parseInt(_this.formObject.totalWaiver) + parseInt(value.waiver));
                    _this.$set(_this.formObject, 'totalNetPay', parseInt(_this.formObject.totalNetPay) + parseInt(value.net_pay));
                    _this.calculateNetPay(value);
                });
            }
        },

        // For check student list
        studentList: {
            deep: true,
            handler(newList) {
                this.formObject.students = newList
                    .filter((student) => student.checked)
                    .map((student) => student.id);
            },
        },

        'formObject.date'(newDate) {
            if (newDate) {
                const selectedDate = new Date(newDate);
                const selectedMonthName = selectedDate.toLocaleString('default', { month: 'long' });
                this.formObject.month = selectedMonthName;

                const currentDate = new Date();
                const currentMonth = currentDate.getMonth();
                const selectedMonth = selectedDate.getMonth();

                // Compare months
                if (selectedMonth > currentMonth) {
                    this.formObject.is_advance = "1"; // Advance
                } else {
                    this.formObject.is_advance = "0"; // Regular
                }
            } else {
                this.formObject.month = '';
            }
        }

    },

    methods: {

        clearZero(data, field) {
            if (data[field] === 0 || data[field] === "0") {
                data[field] = "";
            }
        },
        restoreZero(data, field) {
            if (data[field] === "" || data[field] === null || isNaN(data[field])) {
                data[field] = 0;
            }
        },

        submitAttestationForm: function () {
            const _this = this;
            _this.submitForm(_this.formObject, false, function (retData) {
                if (parseInt(retData.status) === 2000) {
                    _this.$router.push({ path: '/admin/generate_invoice' });
                }
            });
        },

        // / For check student list
        toggleSelectAll(event) {
            const isChecked = event.target.checked;
            this.studentList.forEach((student) => {
                student.checked = isChecked;
            });

            if (isChecked) {
                this.formObject.students = this.studentList.map((student) => student.id);
            } else {
                this.formObject.students = [];
            }
        },

        updateSelectedStudents(student) {
            if (student.checked) {
                if (!this.formObject.students.includes(student.id)) {
                    this.formObject.students.push(student.id);
                }
            } else {
                const index = this.formObject.students.indexOf(student.id);
                if (index > -1) {
                    this.formObject.students.splice(index, 1);
                }
            }
        },
        // ./ For check student list

        addWaiverInfoToStudents() {
            const _this = this;
            const URL = `${_this.urlGenerate("api/get_waiver_student_invoice")}`;
            _this.studentList.forEach((student) => {
                const paramsData = { student_id: student.id };

                _this.getData(URL, "get", paramsData, {}, function (retData) {
                    const waiver = retData.find((item) => item.waiver_student_id === student.id);
                    _this.$set(student, "waiver", waiver ? "Waiver" : "No Waiver");
                });
            });
        },

        // /new code for edit 
        getInstituteWiseComponent() {
            const _this = this;
            if (_this.isEditMode) return;
            var URL = `${_this.urlGenerate("api/getComponentBySchoolId")}`;
            const paramsData = { school_id: _this.formObject.school_id };
            _this.getData(URL, "get", paramsData, {}, function (retData) {
                _this.$set(_this.formObject, "components", retData);
            });
        },

        // ./new code for edit 

        calculateNetPay(data) {
            const value = parseFloat(data.value);
            const waiver = parseFloat(data.waiver);
            const netPay = value - waiver;
            data.net_pay = isNaN(netPay) ? 0 : netPay;
        },

        calculateTotals() {
            if (!Array.isArray(this.formObject.components)) return;

            this.formObject.totalValue = 0;
            this.formObject.totalWaiver = 0;
            this.formObject.totalNetPay = 0;

            this.formObject.components.forEach((value) => {
                this.formObject.totalValue += parseInt(value.value) || 0;
                this.formObject.totalWaiver += parseInt(value.waiver) || 0;
                this.formObject.totalNetPay += parseInt(value.net_pay) || 0;
            });
        },

        getEditData: function (id) {
            const _this = this;
            _this.isEditMode = true; // Set editing flag
            var editUrl = `${_this.urlGenerate()}/${id}/edit`;
            _this.getData(editUrl, "get", {}, {}, function (retData) {
                const invoice = retData.invoice;
                if (!invoice) {
                    console.error("Invoice data missing");
                    return;
                }

                _this.$store.commit('updateId', id);
                _this.$store.commit('formType', 2);

                // Set basic invoice data
                _this.$set(_this.formObject, "id", invoice.id);
                _this.$set(_this.formObject, "date", invoice.date);
                _this.$set(_this.formObject, "student_id", invoice.student.id);
                _this.$set(_this.formObject, "student_name", invoice.student.student_name_en || "");
                _this.$set(_this.formObject, "student_roll", invoice.student.student_roll || "");
                _this.$set(_this.formObject, "class_id", invoice.student.class_id || "");
                _this.$set(_this.formObject, "session_year_id", invoice.student.session_year_id || "");
                _this.$set(_this.formObject, "invoice_code", invoice.invoice_code || "");
                _this.$set(_this.formObject, "is_advance", Number(invoice.is_advance));
                _this.$set(_this.formObject, "total_amount", invoice.total_amount || 0);
                _this.$set(_this.formObject, "total_waiver", invoice.total_waiver || 0);
                _this.$set(_this.formObject, "total_payable", invoice.total_payable || 0);

                const components = invoice.components.map(component => ({
                    id: component.id,
                    component_id: component.components_id,
                    value: component.value || 0,
                    waiver: component.waiver || 0,
                    net_pay: component.net_pay || 0,
                    component_name: component.components?.name || ""
                }));
                _this.$set(_this.formObject, "components", components);
                _this.getGeneralData({
                    students: { class_id: invoice.student.class_id }
                }, () => {
                    _this.$set(_this.formObject, "student_id", invoice.student.id);
                });
            });
        },

        studentCount() {
            const _this = this;
            const URL = baseUrl + "/api/classWiseStudent";
            const params = {
                class_id: _this.formObject.class_id,
                school_id: _this.formObject.school_id,
                session_year_id: _this.formObject.session_year_id,
                student_id: _this.formObject.student_id,
                is_advance: _this.formObject.is_advance,
                month: _this.formObject.month,
                date: _this.formObject.date,
                is_advance_status: _this.formObject.is_advance_status,
            };

            _this.getData(URL, "get", params, {}, function (retData) {
                _this.totalStudent = retData.student_count;
                _this.$set(_this.formObject, "invoice_code", retData.invoice);
                _this.studentList = retData.students;
                _this.$set(_this.formObject, "students", retData.studentIds);

                _this.totalClassStudents = retData.class_total_students;
                _this.$set(_this.formObject, "totalClassStudents", _this.totalClassStudents);

                _this.addWaiverInfoToStudents();
            });
        },

        editInvoiceListByStudent() {
            const _this = this;
            _this.openModal("editInvoiceModal", "Edit Invoice");
        },
    },

    mounted() {
        const _this = this;
        _this.getDataList();
        _this.getGeneralData(["session"], function (retData) {
            _this.getGeneralData({
                session: { school_id: _this.Config.school_id },
                account_student_class: { school_id: _this.Config.school_id },
            });
            _this.getInstituteWiseComponent();
        });

        // Set current date and month automatically
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0];
        const currentMonthName = today.toLocaleString('default', { month: 'long' });

        _this.$set(_this.formObject, "date", formattedDate);
        _this.$set(_this.formObject, "month", currentMonthName);

        _this.$set(_this.formObject, "session_year_id", "");
        _this.$set(_this.formObject, "class_id", "");
        _this.$set(_this.formObject, "student_id", "");
        // _this.$set(_this.formObject, "date", "");
        _this.$set(_this.formObject, "is_advance", "0");
        _this.$set(_this.formObject, "is_advance_status", "include");

        if (this.$route.params.id !== undefined) {
            this.getEditData(this.$route.params.id);
        }
    },
}
</script>

<style scoped>
table.table tr th:first-child,
table.table tr td:first-child {
    width: initial !important;
}

.select-style {
    margin-left: 15px !important;
}

.table thead th {
    background-color: #cccbcb;
    text-align: center;
}

.total-section {
    background-color: #cccbcb;
    text-align: center;
}

.modal-header {
    padding: 0px;
    background-color: #ffc107;
    border-bottom: 1px solid #ddd;
}

.modal-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
}

.table {
    margin: 0;
    background-color: #fff;
    border-collapse: collapse;
    border: 1px solid #ddd;
}

.table-hover tbody tr:hover {
    background-color: #f9f9f9;
}

.table th {
    background-color: #f1f1f1;
    font-weight: bold;
    text-align: center;
    padding: 10px;
    border-bottom: 2px solid #ccc;
}

.table td {
    padding: 10px;
    text-align: left;
    vertical-align: middle;
}

.checkbox {
    cursor: pointer;
}

.text-danger {
    color: red;
    font-weight: bold;
}

.text-success {
    color: green;
    font-weight: bold;
}

.text-muted {
    color: #6c757d;
}

.table tbody tr td:first-child {
    text-align: center;
}

.table tbody tr:last-child {
    border-bottom: none;
}

input[readonly],
textarea[readonly],
select[disabled] {
    cursor: not-allowed !important;
    background-color: #f9f9f9 !important;
    color: #666 !important;
}

@media (max-width: 768px) {
    .modal-title {
        font-size: 1.25rem;
    }

    .table th,
    .table td {
        font-size: 0.9rem;
        padding: 8px;
    }
}
</style>
