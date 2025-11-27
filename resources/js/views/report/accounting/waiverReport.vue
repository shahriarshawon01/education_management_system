<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :defaultSearchButton="false"
                    :default-object="{ session_year_id: '', section_id: '', class_id: '', department_id: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Student's Waiver Report"
                    :table-responsive="false">

                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Student's Waiver Report"
                            :default-add-button="false">
                            <div @click="printData('printDiv')">
                                <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i>
                                    Print</button>
                            </div>
                        </page-top>
                    </template>

                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.class_id" name="class_id" class="form-control" @change="getGeneralData({
                                section: { class_id: formFilter.class_id },
                                departments: { class_id: formFilter.class_id }
                            })">
                                <option value="">Class</option>
                                <template v-for="(eachData, index) in requiredData.class">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="formFilter.section_id" name="section_id" class="form-control">
                                <option value="">Section</option>
                                <template v-for="(eachData, index) in requiredData.section">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select v-model="formFilter.department_id" name="department_id" class="form-control">
                                <option value="">Department</option>
                                <template v-for="(eachData, index) in requiredData.departments">
                                    <option :value="eachData.id">{{ eachData.department_name }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.session_year_id" name="session_year_id">
                                <option value="">Session</option>
                                <template v-for="(data, index) in requiredData.session">
                                    <option :value="data.id">{{ data.title }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <a class="btn btn-primary" @click="fetchStudentWaiver" :disabled="loading">Get Student
                                Waiver</a>
                        </div>
                        <!-- loader -->
                        <Loader :visible="loading" />
                    </template>

                    <template v-slot:bottom_data>
                        <template v-if="getWaiverList.length > 0">
                            <schoolInfo title="Student's Waiver Report" />
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
                                        <div class="value-detail">
                                            <strong>Department:</strong>
                                            <span>{{ sectionName }}</span>
                                        </div>
                                        <div class="value-detail">
                                            <strong>Session:</strong>
                                            <span>{{ sectionName }}</span>
                                        </div>
                                        <div class="value-detail">
                                            <strong>Print Date:</strong>
                                            <span>{{ formatDate(new Date()) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="overflow-x: auto;">
                                <table class="table table-bordered table-striped grand-total-style">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" style="text-align:center;">SL</th>
                                            <th rowspan="2" style="text-align:center;">Student Info</th>
                                            <th rowspan="2" style="text-align:center;">Document</th>
                                            <th rowspan="2" style="text-align:center;">Amount</th>
                                            <th colspan="6" style="text-align:center;">Student Waiver</th>
                                            <th rowspan="2" style="text-align:center;">Total</th>
                                        </tr>
                                        <tr>
                                            <th style="text-align:center;">Debtor Member</th>
                                            <th style="text-align:center;">TPSC Staff</th>
                                            <th style="text-align:center;">HEM Staff</th>
                                            <th style="text-align:center;">Merit Position</th>
                                            <th style="text-align:center;">Child Merit</th>
                                            <th style="text-align:center;">General</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr v-for="(item, index) in getWaiverList"
                                            :key="item.student_id + '-' + (item.reason || index)">
                                            <td style="text-align:center;">{{ index + 1 }}</td>
                                            <td style="text-align: left !important;">
                                                <strong>Name:</strong> {{ item.student_name }}<br>
                                                <strong>Student ID:</strong> {{ item.student_roll }}<br>
                                                <strong>Class:</strong> {{ item.class_name }}<br>
                                                <strong>Roll No:</strong> {{ item.class_roll }}<br>
                                                <strong>Session:</strong> {{ item.session_name }}
                                            </td>
                                            <td class="text-center">
                                                <a v-if="item.file" :href="getFileUrl(item.file)" target="_blank"
                                                    download
                                                    class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-1">
                                                    <i class="fa fa-download"></i>
                                                    <span>Download</span>
                                                </a>
                                                <!-- <img v-if="item.file" class="mouse-action" :src="getImage(item.file)"
                                                    @click="openFile(getImage(item.file))">
                                                <span v-else class="text-muted">No File</span> -->
                                            </td>

                                            <td style="text-align:right;">{{ formatNumber(item.total_amount) }}</td>

                                            <!-- Student Waiver columns (based on reason or remarks) -->
                                            <td style="text-align:center;">{{ getWaiverAmount(item, 'Debtor Member') }}
                                            </td>
                                            <td style="text-align:center;">{{ getWaiverAmount(item, 'TPSC Staff') }}
                                            </td>
                                            <td style="text-align:center;">{{ getWaiverAmount(item, 'HEM Staff') }}</td>
                                            <td style="text-align:center;">{{ getWaiverAmount(item, 'Merit Position') }}
                                            </td>
                                            <td style="text-align:center;">{{ getWaiverAmount(item, 'Child Merit') }}
                                            </td>
                                            <td style="text-align:center;">{{ getWaiverAmount(item, 'General') }}</td>

                                            <td style="text-align:right;">{{ formatNumber(item.total_waiver) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="total-style">
                                            <th colspan="2" style="text-align: center;">Grand Total:</th>
                                            <th style="text-align:right;">{{ sumField('total_amount') }}</th>
                                            <th></th>
                                            <th style="text-align:center;">{{ sumReason('Debtor Member') }}</th>
                                            <th style="text-align:center;">{{ sumReason('TPSC Staff') }}</th>
                                            <th style="text-align:center;">{{ sumReason('HEM Staff') }}</th>
                                            <th style="text-align:center;">{{ sumReason('Merit Position') }}</th>
                                            <th style="text-align:center;">{{ sumReason('Child Merit') }}</th>
                                            <th style="text-align:center;">{{ sumReason('General') }}</th>
                                            <th style="text-align:right;">{{ sumField('total_waiver') }}</th>
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
import axios from 'axios';
import Loader from "../../../components/loader.vue";

export default {
    name: "waiverReport",
    components: { PageTop, DataTable, schoolInfo, Loader },

    data() {
        return {
            getWaiverList: [],
            sectionName: '',
            className: '',
            sessionName: '',
            loading: false,
        };
    },

    methods: {
        // Map waiver reasons to readable text
        getReasonText(reason) {
            const reasons = {
                1: 'TPSC Staff',
                2: 'HEM Staff',
                3: 'Merit Position',
                4: 'Child Merit',
                5: 'Debtor Member',
                6: 'General',
            };
            return reasons[reason] || 'No Document';
        },

        // Fetch data from API
        fetchStudentWaiver() {
            const { class_id, section_id, session_year_id } = this.formFilter;

            const validationMessages = {
                class_id: 'Please select the class.',
                section_id: 'Please select the section.',
                session_year_id: 'Please select the session.'
            };

            // Validation checks
            if (!class_id) {
                this.$toastr('error', validationMessages.class_id, "Validation Error");
                return;
            }
            if (!section_id) {
                this.$toastr('error', validationMessages.section_id, "Validation Error");
                return;
            }
            if (!session_year_id) {
                this.$toastr('error', validationMessages.session_year_id, "Validation Error");
                return;
            }

            const filterParams = { class_id, section_id, session_year_id };

            this.loading = true;
            axios.get("/api/reports/waiver_report", { params: filterParams })
                .then(res => {
                    const response = res.data;
                    console.log('Response:', response);
                    if (response.status === 5000) {
                        this.getWaiverList = [];
                        this.$toastr('warning', response.message, "No Data Found");
                        this.loading = false;
                        return;
                    }

                    const result = response.result;
                    this.getWaiverList = result.data || [];
                    this.sectionName = result.section_name || '';
                    this.className = result.class_name || '';
                    this.sessionName = result.session_name || '';
                })
                .catch(err => {
                    console.error(err);
                    this.$toastr.error("Error fetching student waiver report");
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        // Format date (dd-mm-yy)
        formatDate(date) {
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = String(d.getFullYear()).slice(-2);
            return `${day}-${month}-${year}`;
        },

        // Format number with comma separators
        formatNumber(value) {
            if (!value) return '-';
            return Number(value).toLocaleString();
        },

        // Return waiver amount by reason label
        getWaiverAmount(item, label) {
            const reasonMap = {
                1: "TPSC Staff",
                2: "HEM Staff",
                3: "Merit Position",
                4: "Child Merit",
                5: "Debtor Member",
                6: "General"
            };
            return reasonMap[item.reason] === label ? this.formatNumber(item.total_waiver) : '-';
        },

        // Sum numeric field (e.g., total_amount, total_waiver)
        sumField(field) {
            return this.formatNumber(
                this.getWaiverList.reduce((acc, cur) => acc + (Number(cur[field]) || 0), 0)
            );
        },

        // Sum waiver totals by reason (for footer row)
        sumReason(label) {
            const reasonMap = {
                1: "TPSC Staff",
                2: "HEM Staff",
                3: "Merit Position",
                4: "Child Merit",
                5: "Debtor Member",
                6: "General"
            };
            const total = this.getWaiverList.reduce((acc, cur) => {
                return reasonMap[cur.reason] === label
                    ? acc + (Number(cur.total_waiver) || 0)
                    : acc;
            }, 0);
            return this.formatNumber(total);
        },

        getFileUrl(file) {
            if (!file) return '#';
            if (file.startsWith('http')) return file;
            // ✅ stored inside storage/app/public/uploads
            if (file.startsWith('uploads/')) return `/storage/${file}`;
            if (file.startsWith('storage/')) return `/${file}`;
            return `/storage/uploads/${file}`;
        }
    },

    computed: {
        // Optionally transform list if needed
        groupedWaiverList() {
            return this.getWaiverList.map(item => ({
                ...item,
                components: item.components
                    ? item.components.split(', ')
                    : [],
                value: item.total_value ?? item.value
            }));
        },
    },

    mounted() {
        this.getGeneralData(['class', 'session']);
    },
};
</script>


<style scoped>
.table thead th {
    background-color: #cccbcb;
    text-align: center;
    vertical-align: middle !important;
    font-weight: 600;
    font-size: 14px;
    color: #2c3e50;
    border: 1px solid #b5b5b5;
    padding: 8px 6px;
}

.table tfoot th {
    background-color: #cccbcb;
    text-align: center;
    vertical-align: middle !important;
    font-weight: 700;
}

.table td {
    vertical-align: middle !important;
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
