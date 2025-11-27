<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :default-object="{}" :defaultFilter="false" :default-pagination="false"
                    table-title="Transport Monthly Invoice Generate" :table-responsive="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Transport Monthly Invoice Generate"
                            :default-add-button="false">
                        </page-top>
                    </template>

                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.transport_month" class="form-control" name="transport_month">
                                <option value="">Select Month</option>
                                <option v-for="(month, index) in months" :key="index"
                                    :value="String(index + 1).padStart(2, '0')">
                                    {{ month }}
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select v-model="formFilter.roster_year" class="form-control" name="roster_year">
                                <option value="">Select Year</option>
                                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.roster_type" name="roster_type">
                                <option value="" selected>Select Type</option>
                                <option value="1">Student</option>
                                <option value="2">Employee</option>
                                <option value="3">Guest</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.transport_id"
                                name="transport">
                                <option value="">Select Transport</option>
                                <template v-for="(data, index) in requiredData.transports">
                                    <option :value="data.id">{{ data.transport_name }}
                                    </option>
                                </template>
                            </select>
                        </div>
                    </template>
                    <template v-slot:bottom_data>
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
                                        <th style="width: 3%;">SL</th>
                                        <th style="width: 10%;">User Type</th>
                                        <th style="width: 27%;">Transport User</th>
                                        <th style="width: 20%;">Semester/Designation</th>
                                        <th style="width: 20%;">Transport</th>
                                        <th style="width: 20%;" v-for="(head, index) in dataList.component">
                                            {{ head.name }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in dataList.data" :key="data.id">
                                        <td style="text-align: center;">{{ index + 1 }}</td>
                                        <td>{{ getTransferUser(data.transport_user_type) }}</td>
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

                                        <td>{{ data.transport_name }}</td>
                                        <td v-for="(head, headIndex) in dataList.component" :key="headIndex"
                                            class="text-center">
                                            <input type="number" v-model.number="data['component_' + head.id]"
                                                class="form-control text-center" placeholder="0" readonly>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="text-end mt-1" v-if="dataList.data">
                                <button class="generate-invoice-btn" type="submit">
                                    <i class="fa fa-file-invoice-dollar"></i> Generate Invoice
                                </button>
                            </div>
                            <Loader :visible="loading" />
                        </form>
                    </template>
                </data-table>
            </div>
        </div>
    </div>
</template>
<script>
import DataTable from "../../components/dataTable";
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";
import Loader from "../../components/loader.vue"
export default {
    name: "transportMonthClosing",
    components: { PageTop, DataTable, formModal, Loader },
    data() {
        const today = new Date();
        const currentMonth = String(today.getMonth() + 1).padStart(2, '0');
        const currentYear = today.getFullYear();

        const years = Array.from({ length: 10 }, (_, i) => currentYear - i);
        return {
            years: [],
            months: [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ],
            tableHeading: [],
            formModalId: "formModal",
            loading: false,
        };
    },

    methods: {

        getTransferUser(type) {
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
                    roster_year: _this.formFilter.roster_year,
                    transport_month: _this.formFilter.transport_month,
                    transport_user_type: data.transport_user_type, // ✅ needed
                    student_id: data.student_primary_id || null,
                    employee_id: data.employee_primary_id || null,
                    guest_id: data.guest_primary_id || null,
                    school_id: data.school_id || null,
                    total_amount: data.net_amount || 0,
                    total_waiver: (data.net_amount || 0) - (data.payable_amount || 0),
                    total_payable: data.payable_amount || 0,
                    component_amount
                };
            });

            this.loading = true;
            const URL = baseUrl + "/api/transport_month_closing";

            _this.axios.post(URL, submittedValue)
                .then(response => {
                    if (response.data.status === 2000) {
                        _this.getDataList();
                        _this.$toastr('success', response.data.message, "Success");
                    } else {
                        _this.$toastr('error', response.data.message || 'Failed to save.', "Error");
                    }
                })
                .catch(error => {
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

        assign() {
            this.formFilter.roster_type = '';
            this.formFilter.transport_id = '';
            this.formFilter.transport_month = this.formFilter.transport_month || String(new Date().getMonth() + 1).padStart(2, '0');
            this.formFilter.roster_year = this.formFilter.roster_year || new Date().getFullYear();
            this.formObject.amount = '';
        }
    },

    mounted() {
        const today = new Date();
        const currentMonth = String(today.getMonth() + 1).padStart(2, '0');
        const currentYear = today.getFullYear();
        this.formFilter.transport_month = currentMonth;
        this.formFilter.roster_year = currentYear;

        for (let i = currentYear - 1; i <= currentYear + 5; i++) {
            this.years.push(i);
        }

        this.assign();
        this.getGeneralData(['transports']);
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
