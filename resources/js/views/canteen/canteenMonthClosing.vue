<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :default-object="{ month: '', years: '' }" :defaultFilter="false"
                    :default-pagination="false" table-title="Canteen Month Closing" :table-responsive="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Canteen Month Closing"
                            :default-add-button="false"></page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.canteen_month" class="form-control" name="canteen_month">
                                <option value="">Select Month</option>
                                <option v-for="(month, index) in months" :key="index"
                                    :value="String(index + 1).padStart(2, '0')">
                                    {{ month }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="years" v-model="formFilter.years" class="form-control">
                                <option value="">Select Year</option>
                                <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.member_type" name="member_type">
                                <option value="" selected>Select member</option>
                                <option value="1">Student</option>
                                <option value="2">Employee</option>
                            </select>
                        </div>

                    </template>
                    <template v-slot:bottom_data>
                        <!-- <form @submit.prevent="submitData()"> -->
                        <form @submit.prevent="submitData()">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="fw-bold text-primary m-0 d-flex align-items-center">
                                        <span ref="calendarIcon" class="emoji me-1">📅</span>
                                        <span>Invoice on:</span>
                                    </h5>
                                    <span class="roster-on px-3 py-2 fs-6 shadow-sm">
                                        {{ dataList.month }}
                                    </span>
                                </div>
                                <div v-if="dataList.data">
                                    <button class="generate-invoice-btn" type="submit"
                                        v-if="can('canteen_month_closing_add')">
                                        <i class="fa fa-file-invoice-dollar"></i> Generate Invoice
                                    </button>
                                </div>
                                <Loader :visible="loading" />
                            </div>

                            <table v-if="dataList.data && dataList.data.length"
                                class="table table-bordered align-middle">
                                <thead>
                                    <tr class="background-style">
                                        <th style="width: 2%;">SL</th>
                                        <th style="width: 10%;">Member Type</th>
                                        <th style="width: 28%;">Member</th>
                                        <th style="width: 20%;">Class / Designation</th>
                                        <th v-for="(meal, index) in mealTimes" :key="index"
                                            style="width: 10%; text-transform: capitalize;">
                                            {{ meal }}
                                        </th>
                                        <th style="width: 10%;">Total Amount</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="(data, index) in dataList.data" :key="index">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ data.member_type }}</td>
                                        <td>
                                            <strong>{{ data.member_name || '—' }}</strong><br>
                                            <small class="text-muted">
                                                ({{ data.student_roll || data.employee_id || 'N/A' }})
                                            </small>
                                        </td>

                                        <td>{{ data.student_class || data.employee_designation || '—' }}</td>

                                        <td v-for="(meal, index) in mealTimes" :key="index">
                                            <div class="component-display">
                                                {{ data[meal] }}
                                            </div>
                                        </td>

                                        <td class="text-right">
                                            <div class="component-display">
                                                {{ data.total_amount }}
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div v-else class="text-center text-muted py-3 border rounded">
                                No records found for the selected month.
                            </div>
                            <div class="text-end mt-1" v-if="dataList.data && dataList.data.length > 0">
                                <button class="generate-invoice-btn" type="submit"
                                    v-if="can('canteen_month_closing_add')">
                                    <i class="fa fa-file-invoice-dollar"></i> Generate Invoice
                                </button>
                            </div>
                        </form>
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
    name: "canteenMonthClosing",
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
            mealTimes: ['breakfast', 'lunch', 'dinner'],
        };
    },
    methods: {

        submitData() {
            const data = this.dataList?.data || [];

            if (!Array.isArray(data) || data.length === 0) {
                this.$toastr('error', "No data available to generate invoice.", "Error");
                return;
            }

            if (!this.formFilter.canteen_month || !this.formFilter.years) {
                this.$toastr('error', "Please select month and year first.", "Error");
                return;
            }

            const payload = data.map(item => {

                return {
                    member_id: item.member_id,
                    member_type: item.member_type === 'Student' ? 1 : 2, // 1=Student, 2=Employee
                    month: this.formFilter.canteen_month,
                    year: this.formFilter.years,
                    total_amount: parseFloat(item.total_amount || 0)
                };
            });

            this.loading = true;
            this.axios.post(baseUrl + '/api/canteen_month_closing', { data: payload })
                .then(response => {
                    const { status, message } = response.data;

                    if (status === 2000) {
                        this.$toastr('success', message, "Success");
                        this.$router.push({ path: '/admin/canteen_month_closing' });
                    } else {
                        this.$toastr('error', message || "Something went wrong!", "Error");
                    }
                })
                .catch(error => {
                    console.error('Error submitting data:', error);
                    this.$toastr('error', "Server error while generating invoice.", "Error");
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        assign() {
            this.formFilter.member_type = '';
            this.formFilter.canteen_month = this.formFilter.canteen_month || String(new Date().getMonth() + 1).padStart(2, '0');
            this.formFilter.years = this.formFilter.years || new Date().getFullYear();
            this.formObject.amount = '';
        },
    },

    mounted() {
        const today = new Date();
        const currentMonth = String(today.getMonth() + 1).padStart(2, '0');
        const currentYear = today.getFullYear();
        this.formFilter.canteen_month = currentMonth;
        this.formFilter.years = currentYear;

        for (let i = currentYear - 1; i <= currentYear + 5; i++) {
            this.years.push(i);
        }

        this.assign();
    },
};
</script>

<style scoped>
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
}

.generate-invoice-btn:active {
    transform: scale(0.97);
}

.generate-invoice-btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.component-display {
    background-color: #f1f5f9;
    color: #111827;
    text-align: center;
    padding: 6px 10px;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    font-weight: 500;
}
</style>
