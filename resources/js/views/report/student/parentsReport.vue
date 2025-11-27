<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :default-object="{ school_id: '', class_id: '' }" :defaultFilter="false"
                    :default-pagination="false" table-title="Parent Report" :table-responsive="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Parent Report" :default-add-button="false">
                            <div @click="printData('printDiv')">
                                <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i>
                                    Print</button>
                            </div>
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.class_id" name="class_id" class="form-control"
                                @click="getGeneralData({ students: { class_id: formFilter.class_id } })">
                                <option value="">Select Class</option>
                                <template v-for="(eachData, index) in requiredData.class">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.status" name="status">
                                <option value="">Select Status</option>
                                <option value="">All</option>
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                                <option value="2">Dropout</option>
                            </select>
                        </div>
                    </template>

                    <template v-slot:bottom_data>
                        <template v-if="dataList.data">
                            <div class="main-header mt-3">
                                <div class="row mb-3 mt-3">
                                    <!-- Reusable School Info Component -->
                                    <schoolInfo title="Parents Report" />

                                    <div class="row">
                                        <div class="col-6 text-start"></div>
                                        <div class="col-6 text-end">
                                            <strong>Print Date : {{ formatDate(new Date()) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Class Name</th>
                                        <th>Student Name/Roll</th>
                                        <th>Parent Name</th>
                                        <th>Parent Phone</th>
                                        <th>Parent profession</th>
                                        <th>Parent Income</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in groupedData" :key="index">
                                        <td class="fw-medium">{{ index + 1 }}</td>
                                        <td v-if="data.parentRowspan" :rowspan="data.parentRowspan"
                                            style="text-align: left; vertical-align: middle;">
                                            {{ data.class_name }}
                                        </td>
                                        <td>{{ data.student_name_en }} - (<strong>{{ data.student_roll }}</strong>)</td>
                                        <td>{{ data.father_name_en }}</td>
                                        <td>{{ data.father_phone }}</td>
                                        <td>{{ data.profession }}</td>
                                        <td>{{ data.yearly_income }}</td>
                                        <td style="text-align: center;">
                                            <a v-if="parseInt(data.status) === 0">
                                                <span class="badge badge-soft-warning">Inactive</span>
                                            </a>
                                            <a v-if="parseInt(data.status) === 1">
                                                <span class="badge badge-soft-success">Active</span>
                                            </a> 
                                            <a v-if="parseInt(data.status) === 2">
                                                <span class="badge badge-soft-danger">Dropout</span>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="table-success">
                                        <th colspan="3">Total Teacher = {{ totalTeachers }}</th>
                                        <th colspan="7"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </template>
                    </template>
                </data-table>
            </div>
        </div>
    </div>
</template>
<script>
import DataTable from "../../../components/dataTable";
import Pagination from "../../../plugins/pagination/pagination";
import PageTop from "../../../components/pageTop";
import schoolInfo from "../../../components/schoolInfo";
export default {
    name: "classTeacherReport",
    components: { PageTop, Pagination, DataTable, schoolInfo },

    data() {
        return {

        };
    },
    methods: {
        formatDate(date) {
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = String(d.getFullYear()).slice(-2);
            return `${day}-${month}-${year}`;
        },
    },
    computed: {
        groupedData() {
            const grouped = [];
            let currentParent = null;
            let slCounter = 1;
            let rowspanCounter = 0;

            this.dataList.data.forEach((item, index) => {
                if (item.class_name !== currentParent) {
                    if (rowspanCounter > 0) {
                        grouped[grouped.length - rowspanCounter].parentRowspan = rowspanCounter;
                    }
                    currentParent = item.class_name;
                    rowspanCounter = 1;
                } else {
                    rowspanCounter++;
                }
                grouped.push({ ...item, sl: slCounter++ });
            });

            if (rowspanCounter > 0) {
                grouped[grouped.length - rowspanCounter].parentRowspan = rowspanCounter;
            }

            return grouped;
        },
        totalTeachers() {
            return this.dataList.data.length;
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

.table tfoot th {
    background-color: #cccbcb;
    text-align: center;
}

.report-header {
    text-align: center;
    margin-bottom: 6px;
}

.report-header .school-name {
    font-size: 20px;
    font-weight: bold;
    color: #2c3e50;
    margin: 0;
}

.report-header .school-address {
    font-size: 16px;
    color: #7f8c8d;
    margin: 2px 0;
}

.report-header .report-title {
    font-size: 18px;
    font-weight: 600;
    color: #34495e;
    text-transform: uppercase;
    border-top: 1px solid #bdc3c7;
    border-bottom: 1px solid #bdc3c7;
    padding: 6px 0;
    display: inline-block;
    margin-top: 0px;
}
</style>
