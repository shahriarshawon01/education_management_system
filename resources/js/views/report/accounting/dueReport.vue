<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :defaultSearchButton="false"
                    :default-object="{ from_date: '', to_date: '', section_id: '', class_id: '', invoice_category: '', dormitory_id: '', transport_id: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Student Due Report"
                    :table-responsive="false">

                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Student's Due Report" :default-add-button="false">
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
                        <div v-if="formFilter.invoice_category == 3" class="col-md-2">
                            <select v-model="formFilter.dormitory_id" name="dormitory_id" class="form-control">
                                <option value="">Select Dormitory</option>
                                <template v-for="(eachData, index) in requiredData.dormitory">
                                    <option :value="eachData.id">{{ eachData.dormitory_name }}
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div v-if="formFilter.invoice_category == 4" class="col-md-2">
                            <select class="form-control" v-model="formFilter.transport_id" name="transport_id">
                                <option value="">Select Transport</option>
                                <template v-for="(data, index) in requiredData.transports">
                                    <option :value="data.id">{{ data.transport_name }}
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <a class="btn btn-primary" @click="fetchDue" :disabled="loading">Get Due</a>
                        </div>
                        <Loader :visible="loading" />
                    </template>

                    <template v-slot:bottom_data>
                        <template v-if="groupedData.length > 0">
                            <!-- Reusable School Info Component -->
                            <schoolInfo title="Students Due Report" />

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
                                            <strong>From Date:</strong>
                                            <span>{{ formatDate(fromDate) }}</span>
                                        </div>
                                        <div v-if="toDate != '-'" class="value-detail">
                                            <strong>To Date:</strong>
                                            <span>{{ formatDate(toDate) }}</span>
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
                                        <tr>
                                            <th>Sl</th>
                                            <th>Student</th>
                                            <th v-for="component in uniqueComponents" :key="component">
                                                {{ component }}
                                            </th>
                                            <th>Total Due</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr v-for="(student, index) in groupedData" :key="index">
                                            <td style="vertical-align: middle; text-align: center;">{{ index + 1 }}</td>

                                            <td class="student_style">
                                                <span><strong>Name:</strong> {{ student.name }}</span><br />
                                                <span><strong>Student ID:</strong> {{ student.student_roll
                                                    }}</span><br />
                                                <span><strong>Class Roll:</strong> {{ student.class_roll }}</span><br />
                                                <span><strong>Class:</strong> {{ student.class }}</span><br />
                                                <span><strong>Section:</strong> {{ student.section }}</span><br />
                                                <!-- <strong>Category :</strong> {{ getInvoiceCategory(student.invoice_category) }} -->


                                            </td>

                                            <td style="vertical-align: middle; text-align: right;"
                                                v-for="component in uniqueComponents">
                                                <div>
                                                    {{ formatAmount(student.due[component] || 0) }}
                                                    <span v-if="student.dueCount && student.dueCount[component]">
                                                        ({{ student.dueCount[component] }}x)
                                                    </span>
                                                </div>
                                            </td>
                                            <th
                                                style="vertical-align: middle; text-align: center; background-color: #cccbcb;">
                                                {{ formatAmount(student.totalDue) }}
                                            </th>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-success">
                                            <th colspan="2" class="fw-bold">Grand Total</th>
                                            <th v-for="component in uniqueComponents">
                                                {{ formatAmount(getComponentTotal(component)) }}
                                            </th>
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
    name: "dueReport",
    components: { PageTop, DataTable, schoolInfo, Loader },


    data() {
        return {
            dueList: [],
            sectionName: '',
            className: '',
            fromDate: '',
            toDate: '',
            componentOrder: [],
            loading: false,
        };
    },
    methods: {

        fetchDue() {
            const { class_id, section_id, from_date, to_date, invoice_category,dormitory_id,transport_id } = this.formFilter;

            const validationMessages = {
                class_id: 'Please select the class.',
                section_id: 'Please select the section.',
            };

            if (!class_id) {
                this.$toastr('error', validationMessages.class_id, "Validation Error");
                return;
            }
            if (!section_id) {
                this.$toastr('error', validationMessages.section_id, "Validation Error");
                return;
            }

            const from = new Date(from_date);
            const to = new Date(to_date);
            if (from > to) {
                this.$toastr('error', 'From date cannot be later than To date.', 'Invalid Date Range');
                return;
            }

            const filterParams = {
                class_id,
                section_id,
                from_date,
                to_date,
                invoice_category,
                dormitory_id,
                transport_id,
            };
            this.loading = true;
            axios.get("/api/reports/due_report", { params: filterParams })
                .then(res => {
                    const response = res.data;

                    if (response.status === 5000) {
                        this.dueList = [];
                        this.$toastr('warning', response.message, "No Data Found");
                        return;
                    }

                    const result = response.result;
                    this.dueList = result.data || [];
                    this.sectionName = result.section_name || '';
                    this.className = result.class_name || '';
                    this.fromDate = result.from_date || '';
                    this.toDate = result.to_date || '';
                    this.componentOrder = result.component_order || [];
                })
                .catch(err => {
                    console.error(err);
                    this.$toastr("error", "An error occurred while fetching data.", "Error");
                })
                .finally(() => {
                    this.loading = false;
                });

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

        formatAmount(amount) {
            return new Intl.NumberFormat("en-US", {
                style: "decimal",
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(amount);
        },

        getComponentTotal(component) {
            return this.dueList.reduce((total, entry) => {
                return total + (entry.component_name === component ? entry.due_amount : 0);
            }, 0);
        }
    },

    computed: {
        totalStudent() {
            return this.dueList.length;
        },

        groupedData() {
            const grouped = {};
            this.dueList.forEach((entry) => {
                const roll = entry.student_roll;
                const comp = entry.component_name;

                if (!grouped[roll]) {
                    grouped[roll] = {
                        name: entry.student_name,
                        student_roll: roll,
                        class_roll: entry.class_roll,
                        section: entry.section_name,
                        class: entry.class_name,
                        date: entry.date,
                        due: {},
                        dueCount: {},
                        totalDue: 0,
                    };
                }

                if (!grouped[roll].dueCount[comp]) {
                    grouped[roll].dueCount[comp] = 0;
                    grouped[roll].due[comp] = 0;
                }

                grouped[roll].dueCount[comp] += 1;
                grouped[roll].due[comp] += entry.due_amount;
                grouped[roll].totalDue += entry.due_amount;
            });
            return Object.values(grouped).sort((a, b) => {
                return Number(a.class_roll) - Number(b.class_roll);
            });
        },

        uniqueComponents() {
            const existing = new Set(this.dueList.map(entry => entry.component_name));
            return this.componentOrder.filter(name => existing.has(name));
        },

        grandTotalComponents() {
            const totals = {};
            this.dueList.forEach((entry) => {
                if (!totals[entry.component_name]) {
                    totals[entry.component_name] = 0;
                }
                totals[entry.component_name] += entry.due_amount;
            });
            return totals;
        },

        grandTotalDue() {
            return this.dueList.reduce((total, entry) => total + entry.due_amount, 0);
        }
    },

    mounted() {
        const _this = this;
        _this.getGeneralData(['dormitory', 'class', 'transports']);
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
    font-size: 13px !important;
}
</style>
