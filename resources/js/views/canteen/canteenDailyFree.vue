<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :default-object="{ date: '' }" :defaultFilter="false"
                    :default-pagination="false" table-title="Canteen Daily Fee" :table-responsive="false">
                    <!-- <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Canteen Daily Fee" :default-add-button="false">
                            <template #default>
                                <button v-if="can('canteen_sale_add')"
                                    @click="openModal('cashPaymentModal', '💵 Cash Payment', false, false, {})"
                                    class="btn btn-primary">
                                    <i class="fa fa-plus"></i> 💵 Cash Payment
                                </button>

                            </template>
                        </page-top>
                    </template> -->

                    <template v-slot:filter>
                        <div class="col-md-2">
                            <datepicker v-model="formFilter.date" placeholder="Select Date" name="date"
                                v-validate="'required'" input_class="form-control"></datepicker>
                        </div>

                        <div class="col-md-3">
                            <select class="form-control" v-model="formFilter.customer_type" name="customer_type">
                                <option value="" selected>Select Consumer Type</option>
                                <option value="student">Student</option>
                                <option value="employee">Employee</option>
                                <option value="guest">Guest</option>
                            </select>
                        </div>
                    </template>
                    <template v-slot:bottom_data>
                        <div>
                            <form @submit.prevent="submitData()">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center px-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <h5 class="fw-bold text-primary m-0 d-flex align-items-center">
                                                    <span class="emoji me-1">📅</span>
                                                    <span>Roster on:</span>
                                                </h5>
                                                <span class="roster-on px-3 py-2 fs-6 shadow-sm">
                                                    {{ dataList.date }}
                                                </span>
                                            </div>
                                            <div v-if="dataList.data">
                                                <button class="submit_btn" type="submit">
                                                    <i class="fa fa-plus"></i>Submit
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="background-style">
                                            <th style="width: 15px; ">SL</th>
                                            <th style="width: 70px;">Type</th>
                                            <th style="width: 200px;">Name</th>
                                            <th style="width: 150px;">Semester/Designation</th>
                                            <th style="width: 100px;">Institute</th>
                                            <th v-for="(head, index) in dataList.component"
                                                :style="{ width: head.isWide ? '220px' : '110px' }">
                                                {{ head.name }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(data, index) in dataList.data" :key="data.id">
                                            <td style="text-align: center; width: 2%;">{{ index + 1 }}</td>
                                            <td style="width: 5%;">{{ data.roster_type }}</td>
                                            <td style="width: 15%;">
                                                {{ data.student_name || data.employee_name || data.guest_name }}
                                                <strong>[{{
                                                    data.student_roll || data.employee_id || data.guest_id
                                                }}]</strong>
                                            </td>

                                            <td style="width: 8%;">
                                                {{ data.student_class || data.employee_designation ||
                                                    data.guest_designation }}
                                            </td>
                                            <td style="width: 30%;">{{ data.school_name }}</td>
                                            <td style="width: 10%;" v-for="(head, headIndex) in dataList.component"
                                                :key="headIndex" class="text-center">
                                                <input type="number" v-model="data['component_' + head.id]"
                                                    class="form-control text-center" placeholder="Enter value">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="text-end mt-2" v-if="dataList.data">
                                    <button class="submit_btn" type="submit">
                                        <i class="fa fa-plus"></i>Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </template>
                </data-table>
            </div>
        </div>

        <!-- <formModal modalSize="modal-lg" modal-id="cashPaymentModal" @submit="submitCashPayment">
            <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Customer</label>
                        <select v-model="formObject.customer_type" class="form-control" v-validate="'required'">
                            <option value="">Select Customer Type</option>
                            <option value="student">Student</option>
                            <option value="employee">Employee</option>
                            <option value="guest">Guest</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label">Payment Date</label>
                    <datepicker v-model="formObject.payment_date" name="payment_date" input_class="form-control"
                        :disabled-dates="{ to: new Date(new Date().setDate(new Date().getDate() - 1)) }"
                        v-validate="'required'" placeholder="Payment Date">
                    </datepicker>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Meal Time</label>
                    <select v-model="formObject.meal_time_id" class="form-control" v-validate="'required'"
                        name="meal_time_id">
                        <option value="">Select Time</option>
                        <template v-for="(data, index) in requiredData.mealTimes">
                            <option :value="data.id">{{ data.name }}</option>
                        </template>
                    </select>
                </div>

                Student Form Section
                <template v-if="formObject.customer_type === 1">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="col-form-label">Student ID</label>
                            <autocomplete v-model="formObject.student_roll" api-url="/api/get-canteen-student"
                                placeholder="Enter Student ID" name="student_roll" v-validate="'required'"
                                validation_name="Student ID" @select="fillStudentData" />
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Student Name</label>
                            <input type="text" v-model="formObject.name" name="name" class="form-control readonly-field"
                                placeholder="Auto-filled" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Phone</label>
                            <input type="text" v-model="formObject.phone" name="phone"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Institute</label>
                            <input type="text" v-model="formObject.school_name" name="school_name"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>
                    </div>
                </template>

                Employee Form Section
                <template v-if="formObject.customer_type === 2">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="col-form-label">Employee ID</label>
                            <autocomplete v-model="formObject.employee_id" api-url="/api/get-canteen-employee"
                                placeholder="Enter Employee ID" name="employee_id" validation_name="Employee ID"
                                @select="fillEmployeeData" />
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Employee Name</label>
                            <input type="text" v-model="formObject.name" name="name" class="form-control readonly-field"
                                placeholder="Auto-filled" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Phone</label>
                            <input type="text" v-model="formObject.phone" name="phone"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Institute</label>
                            <input type="text" v-model="formObject.school_name" name="school_name"
                                class="form-control readonly-field" placeholder="Auto-filled" readonly>
                        </div>
                    </div>
                </template>

                Guest Form Section
                <template v-if="formObject.customer_type === 'guest'">
                    <div class="col-md-4">
                        <label class="form-label">Name</label>
                        <input type="text" v-model="formObject.name" name="name" class="form-control"
                            v-validate="'required'" placeholder="Enter Name">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Phone</label>
                        <input type="text" v-model="formObject.phone" name="phone" class="form-control"
                            placeholder="Enter Phone">
                    </div>
                </template>
            </div>

            <table class="table table-bordered set-menu-table mt-2">
                <thead>
                    <tr class="background-style">
                        <th style="width: 30% !important; text-align: center;">Menu Item</th>
                        <th style="width: 15% !important; text-align: center;">Amount</th>
                        <th style="width: 15% !important; text-align: center;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="(row, index) in formObject.set_canteen_menu" :key="index">
                        <td>
                            <select v-select2 v-model="row.menu_item_id" name="menu_item_id" v-validate="'required'"
                                class="form-control">
                                <option value="" disabled>Menu Item</option>
                                <template v-for="(eachData, index) in requiredData.menuItem">
                                    <option :value="eachData.id">{{ eachData.item_name }}</option>
                                </template>
                            </select>
                        </td>

                        <td>
                            <input type="number" v-model="row.amount" name="amount" class="form-control"
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
        </formModal> -->

    </div>
</template>
<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";
export default {
    name: "CanteenDailyFee",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: [],
            formModalId: "formModal",
            set_canteen_menu: [
                {
                    menu_item_id: '',
                    amount: '',
                },
            ],
            isEditMode: false,
        };
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
    },

    created() {
        this.formObject.payment_date = this.getCurrentDate();
    },

    methods: {

        submitData() {
            const _this = this;

            const submittedValue = _this.dataList.data.map(data => {
                const component_amount = {};
                _this.dataList.component.forEach(component => {
                    component_amount[component.id] = data['component_' + component.id] || '';
                });
                let student_id = null;
                let employee_id = null;
                let school_id = null;
                let guest_id = null;
                let roster_type = null;

                if (data.student_primary_id) {
                    student_id = data.student_primary_id;
                    school_id = data.school_id;
                    roster_type = 'student';
                } else if (data.employee_primary_id) {
                    employee_id = data.employee_primary_id;
                    school_id = data.school_id;
                    roster_type = 'employee';
                }
                else if (data.guest_primary_id) {
                    guest_id = data.guest_primary_id;
                    roster_type = 'guest';
                }

                return {
                    date: _this.dataList.date,
                    student_id: student_id,
                    employee_id: employee_id,
                    school_id: school_id,
                    guest_id: guest_id,
                    roster_type: roster_type,
                    component_amount: component_amount
                };
            });

            const URL = baseUrl + "/api/canteen_daily_fee";

            _this.axios.post(URL, submittedValue)
                .then(response => {
                    if (response.data.status === 2000) {
                        _this.getDataList();
                        _this.$toastr('success', response.data.message, "Success");
                    } else {
                        _this.$toastr('error', response.data.message, "Error");
                    }
                })
                .catch(error => {
                    _this.$toastr('error', 'Failed to submit data.', "Error");
                });
        },

        fillStudentData(student) {
            console.log(student);

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
                this.$set(this.formObject, 'employee_email', employee.email);
                this.$set(this.formObject, 'phone', employee.phone);
                this.$set(this.formObject, 'school_name', employee.school_name);
                this.$set(this.formObject, 'school_id', employee.school_id);

                this.$toastr('success', 'Employee data filled successfully!', 'Success');
            } else {
                this.$toastr('error', 'Employee data not found!', 'Error');
            }
        },

        submitCashPayment() {
            const _this = this;
            const validationMessages = {
                name: 'Please enter the name.',
                payment_date: 'Please select the payment date.',
                amount: 'Please enter a valid amount.',
            };

            const totalAmount = _this.formObject.set_canteen_menu.reduce((sum, item) => sum + parseFloat(item.amount || 0), 0);
            if (!_this.formObject.name || _this.formObject.name.trim() === '') {
                _this.$toastr('error', validationMessages.name, "Validation Error");
                return;
            }

            if (!_this.formObject.payment_date) {
                _this.$toastr('error', validationMessages.payment_date, "Validation Error");
                return;
            }

            if (totalAmount <= 0) {
                _this.$toastr('error', validationMessages.amount, "Validation Error");
                return;
            }

            let payload = {
                name: _this.formObject.name,
                phone: _this.formObject.phone,
                payment_date: _this.formObject.payment_date,
                meal_time_id: _this.formObject.meal_time_id,
                amount: _this.formObject.set_canteen_menu.reduce((sum, item) => sum + parseFloat(item.amount || 0), 0),
                menu_id: JSON.stringify(_this.formObject.set_canteen_menu.map(item => item.menu_item_id)),
            };

            const URL = baseUrl + "/api/canteen_sale";

            _this.axios
                .post(URL, payload)
                .then((response) => {
                    if (response.data.status === 2000) {
                        _this.$toastr('success', response.data.message, "Success");
                        _this.closeModal('cashPaymentModal');
                        _this.assign();
                    } else {
                        _this.$toastr('error', response.data.message, "Error");
                    }
                })
                .catch((error) => {
                    _this.$toastr('error', 'Failed to submit cash payment.', "Error");
                });
        },

        assign() {
            const _this = this;
            _this.$set(_this.formFilter, 'customer_type', '');
            _this.$set(_this.formObject, 'payment_date', this.getCurrentDate());
            _this.$set(_this.formObject, 'meal_time_id', '');
            _this.$set(_this.formObject, 'customer_type', '');
            _this.$set(_this.formObject, 'amount', '');
            _this.$set(_this.formObject, 'set_canteen_menu', [{
                menu_item_id: '',
                amount: '',
            }]);
        },

        getCurrentDate() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
    },
    mounted() {
        const _this = this;
        _this.assign();
        _this.getGeneralData(["mealTimes", "menuItem"]);
        _this.$set(_this.formObject, 'set_canteen_menu', [{
            menu_item_id: '',
            amount: '',
        }]);
    },
};
</script>

<style scoped>
.background-style {
    background: #cccbcb;
    text-align: center;
}

.readonly-field {
    background-color: #f1f3f5;
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
    color: #495057;
    cursor: not-allowed;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
    font-size: 0.95rem;
}

.readonly-field::placeholder {
    color: #868e96;
}

input[readonly].form-control {
    background-color: #f1f3f5;
    cursor: not-allowed;
    color: #495057;
}

.roster-on {
    background-color: #1E293B;
    border-radius: 0.375rem;
    color: white;
}

.submit_btn {
    background-color: #1E293B;
    color: #fff;
    padding: 5px 10px;
    font-size: 15px;
    font-weight: 500;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease-in-out;
    min-width: 160px;
    min-height: 38px;
}
</style>
