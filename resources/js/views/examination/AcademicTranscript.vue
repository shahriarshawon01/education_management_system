<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :defaultSearchButton="false" :table-heading="[]" :defaultFilter="false"
                    table-title="Exam Result"
                    :default-object="{ session_year_id: '', class_id: '', exam_id: '', department_id: '', section_id: '', exam_type_id: '' }"
                    :default-pagination="false" :table-responsive="false" :show-search-button="false">

                    <template v-slot:filter>
                        <div class="col-md-1" style="margin-left: 14px">
                            <select class="form-control" v-model="formFilter.session_year_id" name="session_year_id">
                                <option value="">Session</option>
                                <template v-for="(data, index) in requiredData.session">
                                    <option :value="data.id">{{ data.title }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <select v-model="formFilter.class_id" name="class_id" class="form-control" @change="getGeneralData({
                                examination: { class_id: formFilter.class_id },
                                departments: { class_id: formFilter.class_id },
                                section: { class_id: formFilter.class_id }
                            })">
                                <option value="">Class</option>
                                <template v-for="(eachData, index) in requiredData.class">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <select v-model="formFilter.department_id" name="department_id" class="form-control">
                                <option value="">Department</option>
                                <template v-for="(eachData, index) in requiredData.departments">
                                    <option :value="eachData.id">{{ eachData.department_name }}</option>
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

                        <div class="col-md-1">
                            <select v-model="formFilter.exam_id" name="exam_id" class="form-control" @change="
                                getGeneralData({
                                    examinationType: { exam_id: formFilter.exam_id },
                                    examClass: { class_id: formFilter.class_id },
                                })
                                ">
                                <option value="">Exam Name</option>
                                <template v-for="(eachData, index) in requiredData.examName">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <select v-model="formFilter.exam_type_id" name="exam_type_id" class="form-control">
                                <option value="">Exam Type</option>
                                <template v-for="(eachData, index) in requiredData.examinationType">
                                    <option :value="eachData.id">{{ eachData.type_name }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <input type="text" class="form-control" placeholder="Search by Roll"
                                v-model="formFilter.student_roll" name="student_roll">
                        </div>

                        <div class="col-md-2">
                            <a class="btn btn-primary" @click="fetchTranscript">Get Transcript</a>
                        </div>

                    </template>
                    <template v-slot:bottom_data>
                        <div v-html="reportData.html"></div>
                        <div class="button-container">
                            <div class="button-group">
                                <div @click="printData('printDiv')">
                                    <button class="btn btn-outline-primary print-btn no-print">
                                        <i class="fa-sharp fa-solid fa-print"></i> Print
                                    </button>
                                </div>
                                <div>
                                    <button class="btn btn-outline-primary no-print excel-btn" @click="exportToExcel">
                                        <i class="fa fa-file-excel"></i> Export Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                </data-table>
            </div>
        </div>
    </div>
</template>
<script>
import DataTable from "../../components/dataTable";
import PageTop from "../../components/pageTop";
import axios from 'axios';
import * as XLSX from "xlsx";

export default {
    name: "AcademicTranscript",
    components: { PageTop, DataTable },
    data() {
        return {
            rawDataList: {},
            subjects: [],
            subject_name: '',
            subject_total_mark: '',
            reportData: {},
        };
    },
    methods: {
        fetchTranscript() {
            axios.get('/api/academic_transcript', { params: this.createRequestParams() })
            
                .then(response => {
                    console.log('response', response.data);
                    if (response.data.result) {
                        this.rawDataList = response.data.result.data || {};
                        this.subject_name = response.data.result.subject_name;
                        this.subject_total_mark = response.data.result.subject_total_mark;
                        this.reportData = response.data.result;
                    } else {
                        this.$toastr('warning', response.data.message, "Warning");
                    }
                })
                .catch(error => {
                    // alert('Error fetching exam results. Please try again.');
                    this.$toastr('error', `Error fetching exam results. Please try again.`, 'Error');
                    console.error('Error:', error);
                });
        },

        createRequestParams() {
            return {
                session_year_id: this.formFilter.session_year_id,
                class_id: this.formFilter.class_id,
                department_id: this.formFilter.department_id,
                section_id: this.formFilter.section_id,
                exam_id: this.formFilter.exam_id,
                exam_type_id: this.formFilter.exam_type_id,
                student_roll: this.formFilter.student_roll,
            };
        },

        exportToExcel() {
            const ws = XLSX.utils.table_to_sheet(document.getElementById("academicTranscriptId"));
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Academic Transcript");
            XLSX.writeFile(wb, "academic_transcript.xlsx");
        }
    },

    mounted() {
        const _this = this;
        _this.getGeneralData(['examGradeNumber', 'session', 'class', 'examName']);
    },
};
</script>

<style scoped>
.button-container {
    display: flex;
    justify-content: center;
    margin-bottom: 50px;
}

.button-group {
    display: flex;
    justify-content: center;
    gap: 10px;
}

@media print {
    body {
        background-color: #fff;
    }

    th,
    td {
        font-size: 12px;
    }
}
</style>
