<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :defaultSearchButton="false"
                    :default-object="{ from_date: '', to_date: '', section_id: '', class_id: '', invoice_category: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Monthly Student Due Report"
                    :table-responsive="false">

                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Monthly Due Report" :default-add-button="false">
                            <div @click="printData('printDiv')">
                                <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i>
                                    Print</button>
                            </div>
                        </page-top>
                    </template>

                    <template v-slot:filter>
                        <div class="col-md-1">
                            <select v-model="formFilter.class_id" name="class_id" class="form-control"
                                @change="getGeneralData({ section: { class_id: formFilter.class_id }, departments: { class_id: formFilter.class_id } })">
                                <option value="">Class</option>
                                <template v-for="(eachData, index) in requiredData.class">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <select v-model="formFilter.section_id" name="section_id" class="form-control">
                                <option value="">Section</option>
                                <template v-for="(eachData, index) in requiredData.section">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
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
                            <a class="btn btn-primary" @click="fetchMonthlyDue" :disabled="loading">Get Quarterly
                                Due</a>
                        </div>
                        <Loader :visible="loading" />
                    </template>

                    <template v-slot:bottom_data>
                        <template v-if="monthlyDueList.length > 0">

                            <!-- Reusable School Info Component -->
                            <schoolInfo title="Monthly Student Due Report" />

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
                                        <div v-if="fromDate != '-'" class="value-detail">
                                            <strong>Reporting Month:</strong>
                                            <span>{{ reportingMonth(fromDate) }} To {{ reportingMonth(toDate) }}</span>
                                        </div>
                                        <div class="value-detail">
                                            <strong>Print Date:</strong>
                                            <span>{{ formatDate(new Date()) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="overflow-x: auto;">
                                <table class="table table-bordered grand-total-style">
                                    <thead>
                                        <!-- First row: component headers -->
                                        <tr>
                                            <th style="vertical-align: middle; text-align: center;" rowspan="2">SL</th>
                                            <th style="vertical-align: middle; text-align: center;" rowspan="2">Student
                                            </th>
                                            <th v-for="grouped in groupedComponentHeaders"
                                                :key="'head-' + grouped.component" :colspan="grouped.months.length + 1"
                                                style="text-align: center;">
                                                {{ grouped.component }}
                                            </th>
                                            <th rowspan="2" style="vertical-align: middle; text-align: center;">Total
                                                Due</th>
                                        </tr>
                                        <tr>
                                            <template v-for="grouped in groupedComponentHeaders">
                                                <th v-for="month in grouped.months"
                                                    :key="'month-' + grouped.component + '-' + month">
                                                    {{ month }}
                                                </th>
                                                <th rowspan="2">Total</th>
                                            </template>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr v-for="(student, index) in monthlyDueList" :key="'row-' + index">
                                            <td style="vertical-align: middle; text-align: center;">{{ index + 1 }}</td>
                                            <td>
                                                <span>{{ student.name }}</span>
                                                <span><strong>[{{ student.student_roll }}]</strong></span>
                                            </td>

                                            <template v-for="grouped in groupedComponentHeaders">
                                                <td style="text-align: right;" v-for="month in grouped.months"
                                                    :key="'v-' + grouped.component + '-' + month">
                                                    {{ formatAmount(((student.dues[grouped.component] || {})[month]) ||
                                                        0) }}
                                                </td>
                                                <td style="text-align: right; font-weight: bold" class="month-due-style">
                                                    {{
                                                        formatAmount(
                                                            Object.values(student.dues[grouped.component] || {})
                                                                .reduce((a, b) => a + b, 0)
                                                        )
                                                    }}
                                                </td>
                                            </template>

                                            <td style="text-align: right; background-color: #cccbcb; font-weight: bold">
                                                {{ formatAmount(student.totalDue) }}
                                            </td>
                                        </tr>
                                    </tbody>

                                    <tfoot>
                                        <tr class="table-success">
                                            <th colspan="2">Grand Total</th>

                                            <template v-for="grouped in groupedComponentHeaders">
                                                <th v-for="month in grouped.months"
                                                    :key="'gt-' + grouped.component + '-' + month">
                                                    {{ formatAmount(getGroupComponentMonthTotal(grouped.component,
                                                        month)) }}
                                                </th>
                                                <th>
                                                    {{
                                                        formatAmount(
                                                            grouped.months.reduce((sum, month) => {
                                                                return sum + getGroupComponentMonthTotal(grouped.component, month)
                                                            }, 0)
                                                        )
                                                    }}
                                                </th>
                                            </template>
                                            <th>{{ formatAmount(grandTotalDue) }}</th>
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
    name: "monthlyDueReport",
    components: { PageTop, DataTable, schoolInfo, Loader },

    data() {
        return {
            monthlyDueList: [],
            sectionName: '',
            className: '',
            fromDate: '',
            toDate: '',
            componentOrder: [],
            componentMonthHeaders: [],
            loading: false,
        };
    },
    methods: {

        reportingMonth(date) {
            const d = new Date(date);
            return d.toLocaleDateString("en-US", { month: "long" });
        },

        formatDate(date) {
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = String(d.getFullYear()).slice(-2);
            return `${day}-${month}-${year}`;
        },

        formatAmount(amount) {
            return new Intl.NumberFormat("en-US", {
                style: "decimal",
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(amount);
        },

        fetchMonthlyDue() {
            const { class_id, section_id, from_date, to_date, invoice_category } = this.formFilter;

            const validationMessages = {
                class_id: 'Please select the class.',
                section_id: 'Please select the section.',
                from_date: 'Please select the from date.',
                to_date: 'Please select the to date.',
            };

            // Required field validations
            if (!class_id) {
                this.$toastr('error', validationMessages.class_id, "Validation Error");
                return;
            }
            if (!section_id) {
                this.$toastr('error', validationMessages.section_id, "Validation Error");
                return;
            }
            if (!from_date) {
                this.$toastr('error', validationMessages.from_date, "Validation Error");
                return;
            }
            if (!to_date) {
                this.$toastr('error', validationMessages.to_date, "Validation Error");
                return;
            }

            const from = new Date(from_date);
            const to = new Date(to_date);
            if (from > to) {
                this.$toastr('error', 'From date cannot be later than To date.', 'Invalid Date Range');
                return;
            }

            // const monthDiff = (to.getFullYear() - from.getFullYear()) * 12 + (to.getMonth() - from.getMonth());
            // if (monthDiff > 2) {
            //     this.$toastr('error', 'Date range cannot exceed 3 months.', 'Invalid Date Range');
            //     return;
            // }

            const filterParams = {
                class_id,
                section_id,
                from_date,
                to_date,
                invoice_category,
            };
            this.loading = true;
            axios.get("/api/reports/monthly_due_report", { params: filterParams })
                .then(res => {
                    const response = res.data;

                    if (response.status === 5000) {
                        this.monthlyDueList = [];
                        this.$toastr('warning', response.message, "No Data Found");
                        return;
                    }

                    const result = response.result;

                    this.monthlyDueList = result.data || [];
                    this.sectionName = result.section_name || '';
                    this.className = result.class_name || '';
                    this.fromDate = result.from_date || '';
                    this.toDate = result.to_date || '';
                    this.componentOrder = result.component_order || [];
                    this.componentMonthHeaders = result.component_month_headers || [];
                })
                .catch(err => {
                    console.error(err);
                    this.$toastr.error("Error fetching monthly dues");
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        getGroupComponentMonthTotal(component, month) {
            return this.monthlyDueList.reduce((sum, student) => {
                return sum + (((student.dues || {})[component] || {})[month] || 0);
            }, 0);
        },
    },

    computed: {

        groupedComponentHeaders() {
            const grouped = {};
            this.componentMonthHeaders.forEach(({ component, month }) => {
                if (!grouped[component]) grouped[component] = [];
                grouped[component].push(month);
            });

            return Object.entries(grouped).map(([component, months]) => ({
                component,
                months
            }));
        },

        grandTotalDue() {
            return this.monthlyDueList.reduce((total, student) => {
                return total + (parseFloat(student.totalDue) || 0);
            }, 0);
        }
    },

    mounted() {
        const _this = this;
        _this.getGeneralData(['class']);
    },
}
</script>

<style scoped>
.table thead th {
    background-color: #cccbcb;
    text-align: center;
}
.month-due-style {
    background-color:  #cccbcb;
}

.table tfoot th {
    background-color: #cccbcb;
    text-align: center;
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
    margin-left: 2px;
    font-weight: 600;
    color: #7f8c8d;
}
</style>
