<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :default-object="{ month: '', years: '', dormitory_user_type: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Dormitory Monthly Invoice Generate"
                    :table-responsive="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Dormitory Monthly Invoice Generate"
                            :default-add-button="false"></page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.dormitory_month" class="form-control" name="dormitory_month">
                                <option value="">Select Month</option>
                                <option v-for="(month, index) in months" :key="index"
                                    :value="String(index + 1).padStart(2, '0')">
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
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.dormitory_user_type"
                                name="dormitory_user_type">
                                <option value="" selected>Select Type</option>
                                <option value="1">Student</option>
                                <option value="2">Employee</option>
                                <option value="3">Guest</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.dormitory_id"
                                name="transport">
                                <option value="">Select Dormitory</option>
                                <template v-for="(data, index) in requiredData.dormitory">
                                    <option :value="data.id">{{ data.dormitory_name }}
                                    </option>
                                </template>
                            </select>
                        </div>
                    </template>
                    <template v-slot:bottom_data>
                        <div>
                            <form @submit.prevent="submitData()">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="fw-bold text-primary m-0 d-flex align-items-center">
                                            <span ref="calendarIcon" class="emoji me-1">📅</span>
                                            <span>Invoice on:</span>
                                        </h5>
                                        <span class="roster-on px-3 py-2 fs-6 shadow-sm">
                                            {{ getMonthName(dataList.month) }}
                                        </span>
                                    </div>
                                    <div v-if="dataList.data">
                                        <button class="generate-invoice-btn" type="submit">
                                            <i class="fa fa-file-invoice-dollar"></i> Generate Invoice
                                        </button>
                                    </div>
                                    <Loader :visible="loading" />
                                </div>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="background-style">
                                            <th style="width: 5%;">SL</th>
                                            <th style="width: 8%;">Type</th>
                                            <th style="width: 30%;">Dormitory User</th>
                                            <th style="width: 12%;">Semester/Designation</th>
                                            <th style="width: 35%;">Dormitory</th>
                                            <th style="width: 30%;" v-for="(head, index) in dataList.component">
                                                {{ head.name }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(data, index) in dataList.data" :key="data.id">
                                            <td style="text-align: center;">{{ index + 1 }}</td>
                                            <td>{{ getRosterTypeLabel(data.dormitory_user_type) }}</td>
                                            <td>
                                                {{ data.student_name || data.employee_name || data.guest_name }}
                                                <strong>[{{
                                                    data.student_roll || data.employee_id || data.guest_id
                                                }}]</strong>
                                            </td>

                                            <td>
                                                {{ data.student_class || data.employee_designation ||
                                                    data.guest_designation }}
                                            </td>
                                            <td>{{ data.dormitory_name }}</td>
                                            <td v-for="(head, headIndex) in dataList.component" :key="headIndex"
                                                class="text-center">
                                                <input type="number" v-model.number="data['component_' + head.id]"
                                                    class="form-control text-center" placeholder="0" readonly>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                                <div class="text-end mt-1" v-if="dataList.data && dataList.data.length > 0">
                                    <button class="generate-invoice-btn" type="submit">
                                        <i class="fa fa-file-invoice-dollar"></i> Generate Invoice
                                    </button>
                                </div>
                            </form>
                        </div>
                    </template>
                </data-table>
            </div>
        </div>
    </div>
</template>
<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";
import Loader from "../../components/loader.vue"
export default {
    name: "dormitoryMonthClosing",
    components: { PageTop, Pagination, DataTable, formModal, Loader },
    data() {
        return {
            tableHeading: [],
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
        getRosterTypeLabel(type) {
            switch (type) {
                case 1: return 'Student';
                case 2: return 'Employee';
                case 3: return 'Guest';
                default: return 'Unknown';
            }
        },

        submitData() {
            const _this = this;

            const submittedValue = _this.dataList.data.map(data => {

                const component_amount = {};
                _this.dataList.component.forEach(component => {
                    component_amount[component.id] = data['component_' + component.id] || 0;
                });

                return {
                    school_id: data.school_id,
                    dormitory_month: _this.formFilter.dormitory_month,
                    years: _this.formFilter.years,
                    student_id: data.student_primary_id,
                    employee_id: data.employee_primary_id,
                    guest_id: data.guest_primary_id,
                    dormitory_user_type: data.dormitory_user_type,
                    total_amount: data.net_amount || 0,
                    total_waiver: (data.net_amount || 0) - (data.payable_amount || 0),
                    total_payable: data.payable_amount || 0,
                    component_amount
                }
            });

            this.loading = true;
            const URL = baseUrl + "/api/dormitory_month_closing";

            _this.axios.post(URL, submittedValue, {
                validateStatus: () => true
            })
                .then(response => {
                    const { status, message } = response.data;

                    if (status === 2000) {
                        _this.$toastr('success', message, "Success");
                        if (_this.$route.path !== '/admin/dormitory_month_closing') {
                            _this.$router.push({ path: '/admin/dormitory_month_closing' });
                        }
                    } else {
                        _this.$toastr('error', message || "Something went wrong.", "Error");
                    }
                })
                .catch(error => {
                    console.error("Axios Error:", error);
                    _this.$toastr('error', 'Failed to submit data.', "Error");
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        getMonthName(monthNumber) {
            const months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];

            if (!monthNumber) {
                const today = new Date();
                return months[today.getMonth()];
            }

            const index = parseInt(monthNumber, 10) - 1;
            return months[index] || 'Unknown';
        },

        calculateTotal(data) {
            let total = 0;
            for (let component of this.dataList.component) {
                if (data.component_amount && data.component_amount[component.id]) {
                    total += parseFloat(data.component_amount[component.id]);
                }
            }
            return total.toFixed(2);
        },

        assign() {
            this.formFilter.dormitory_user_type = '';
            this.formFilter.dormitory_id = '';
            this.formFilter.dormitory_month = this.formFilter.dormitory_month || String(new Date().getMonth() + 1).padStart(2, '0');
            this.formFilter.years = this.formFilter.years || new Date().getFullYear();
            this.formObject.amount = '';
        }
    },

    computed: {
        totalMembers() {
            return this.dataList.data ? this.dataList.data.length : 0;
        },

        grandTotalAmount() {
            let grandTotal = 0;
            for (let data of this.dataList.data) {
                for (let component of this.dataList.component) {
                    if (data.component_amount && data.component_amount[component.id]) {
                        grandTotal += parseFloat(data.component_amount[component.id]);
                    }
                }
            }

            return grandTotal.toFixed(2);
        }
    },

    mounted() {
        const today = new Date();
        const currentMonth = String(today.getMonth() + 1).padStart(2, '0');
        const currentYear = today.getFullYear();
        this.formFilter.dormitory_month = currentMonth;
        this.formFilter.years = currentYear;

        for (let i = currentYear - 1; i <= currentYear + 5; i++) {
            this.years.push(i);
        }

        this.assign();
        this.getGeneralData(['dormitory']);
    },
};
</script>

<style scoped>
input[readonly],
textarea[readonly],
select[disabled] {
    cursor: not-allowed !important;
    background-color: #f9f9f9 !important;
    color: #666 !important;
}

.background-style {
    background: #cccbcb;
    text-align: center;
}

.table tfoot th {
    background-color: #cccbcb;
    text-align: center;
}

.roster-on {
    background-color: #3b6e67;
    border-radius: 0.375rem;
    color: white;
}

.generate-invoice-btn {
    background-color: #4a8a66;
    border: 1px solid #0d6efd;
    color: #fff;
    padding: 5px 10px;
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
</style>
