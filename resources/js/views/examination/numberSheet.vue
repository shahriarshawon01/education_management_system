<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :defaultFilter="false" table-title="Number Sheet"
                    :default-object="{ subject_id: '', session_id: '', class_id: '', section_id: '', department_id: '', mark_id: '', exam_id: '', exam_type_id: '1' }"
                    :default-pagination="false" :table-responsive="false" :defaultSearchButton="false">

                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Number Sheet" :default-add-button="false">
                            <button @click="printData('printDiv')" class="btn btn-outline-primary"><i
                                    class="fa-sharp fa-solid fa-print"></i> Print</button>
                        </page-top>
                    </template>

                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.session_id" name="session_id">
                                <option value="">Select Session</option>
                                <template v-for="(data, index) in requiredData.session">
                                    <option :value="data.id">{{ data.title }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="formFilter.class_id" name="class_id" class="form-control"
                                @change="getGeneralData({ section: { class_id: formFilter.class_id }, departments: { class_id: formFilter.class_id } })">
                                <option value="">Select Class</option>
                                <template v-for="(eachData, index) in requiredData.class">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="formFilter.section_id" name="section_id" class="form-control">
                                <option value="">Select Section</option>
                                <template v-for="(eachData, index) in requiredData.section">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="formFilter.department_id" name="department_id" class="form-control">
                                <option value="">Select Department</option>
                                <template v-for="(eachData, index) in requiredData.departments">
                                    <option :value="eachData.id">{{ eachData.department_name }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select v-model="formFilter.exam_type_id" name="exam_type_id" class="form-control">
                                <template v-for="(eachData, index) in requiredData.examinationType">
                                    <option :value="eachData.id">{{ eachData.type_name }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-primary" @click.prevent.stop="getNumberSheet">Submit</button>
                        </div>
                    </template>

                    <template v-slot:bottom_data>
                        <template v-if="Object.keys(numberSheet).length > 0">
                            <div class="report-header">
                                <h3 class="school-name">TMSS Public School & College</h3>
                                <p class="school-address">Joypur Para, Bogura</p>
                                <h4 class="report-title">......................... {{ exam_type }}-{{ session_name }}
                                </h4>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="student-details-card">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="student-detail">
                                                    <strong>Class:</strong>
                                                    <span>{{ class_name }}</span>
                                                </div>
                                                <div class="student-detail">
                                                    <strong>Section:</strong>
                                                    <span>{{ section_name }}</span>
                                                </div>

                                                <div class="student-detail">
                                                    <strong>Subject Teacher Name:
                                                        .................................</strong>
                                                </div>
                                            </div>

                                            <div class="col-md-6" style="float: right;">
                                                <div class="student-detail">
                                                    <strong>Subject: .................................</strong>
                                                </div>

                                                <div class="student-detail">
                                                    <strong>Class Teacher Name:
                                                        .................................</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td style="text-align: center;">Total Marks</td>
                                                <td style="text-align: center;"></td>
                                                <td style="text-align: center;">CQ</td>
                                                <td style="text-align: center;"></td>
                                                <td style="text-align: center;">MCQ</td>
                                                <td style="text-align: center;"></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: center;">Height Marks</td>
                                                <td style="text-align: center;"></td>
                                                <td style="text-align: center;">CQ</td>
                                                <td style="text-align: center;"></td>
                                                <td style="text-align: center;">MCQ</td>
                                                <td style="text-align: center;"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div style="overflow-x: auto;">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th rowspan="3" style="width: 2% !important;">Sl</th>
                                            <th rowspan="3" style="width: 4%;">Class Roll</th>
                                            <th rowspan="3" style="width: 25%;">Students Name</th>
                                            <th colspan="9" style="text-align: center;">Statement of Marks Obtained</th>
                                            <th rowspan="3" style="width: 6%;">Grade Letter</th>
                                            <th rowspan="3" style="width: 6%;">GPA</th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" style="width: 10%; text-align: center;">1st Tutorial</th>
                                            <th colspan="2" style="width: 10%; text-align: center;">1st Term</th>
                                            <th colspan="2" style="width: 10%; text-align: center;">2nd Tutorial</th>
                                            <th colspan="2" style="width: 10%; text-align: center;">2nd Term</th>
                                            <th rowspan="2" style="width: 6%; text-align: center;">Total</th>
                                        </tr>
                                        <tr>
                                            <th style="width: 5% !important;">CQ</th>
                                            <th style="width: 5%;">MCQ</th>
                                            <th style="width: 5%;">CQ</th>
                                            <th style="width: 5%;">MCQ</th>
                                            <th style="width: 5%;">CQ</th>
                                            <th style="width: 5%;">MCQ</th>
                                            <th style="width: 5%;">CQ</th>
                                            <th style="width: 5%;">MCQ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(data, index) in numberSheet">
                                            <td class="fw-medium" style="text-align: center;">{{ index + 1 }}</td>
                                            <td style="text-align: center;">{{ data.class_roll }}</td>
                                            <td>{{ data.student_name }} <strong>({{ data.student_roll }})</strong></td>
                                            <td style="text-align: center;"></td>
                                            <td style="text-align: center;"></td>
                                            <td style="text-align: center;"></td>
                                            <td style="text-align: center;"></td>
                                            <td style="text-align: center;"></td>
                                            <td style="text-align: center;"></td>
                                            <td style="text-align: center;"></td>
                                            <td style="text-align: center;"></td>
                                            <td style="text-align: center;"></td>
                                            <td style="text-align: center;"></td>
                                            <td style="text-align: center;"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </template>
                        <p class="mt-4 text-end signature">Subject Teacher Signature</p>
                    </template>
                </data-table>
            </div>
        </div>
    </div>
</template>

<script>
import DataTable from "../../components/dataTable";
import PageTop from "../../components/pageTop";
export default {
    name: "numberSheet",
    components: { PageTop, DataTable },
    data() {
        return {
            numberSheet: [],
            exam_type: '',
            session_name: '',
            class_name: '',
            section_name: '',
        };
    },
    methods: {
        getNumberSheet() {
            const { class_id, session_id, section_id, department_id, exam_type_id } = this.formFilter;

            const validationMessages = {
                session_id: 'Please select the session.',
                class_id: 'Please select the class.',
                section_id: 'Please select the section.',
                department_id: 'Please select the department.',
                exam_type_id: 'Please select exam.',
            };

            // Validate fields
            if (!session_id) {
                this.$toastr('error', validationMessages.session_id, "Validation Error");
                return;
            }
            if (!class_id) {
                this.$toastr('error', validationMessages.class_id, "Validation Error");
                return;
            }

            if (!section_id) {
                this.$toastr('error', validationMessages.section_id, "Validation Error");
                return;
            }
            if (!department_id) {
                this.$toastr('error', validationMessages.department_id, "Validation Error");
                return;
            }
            if (!exam_type_id) {
                this.$toastr('error', validationMessages.exam_type_id, "Validation Error");
                return;
            }

            this.axios.get('/api/number_sheet', {
                params: { class_id, session_id, section_id, department_id, exam_type_id }
            }).then(response => {
                const result = response.data.result;

                if (!result.data || result.data.length === 0) {
                    this.$toastr('error', 'No Student Found', "Validation Error");
                    return;
                }

                this.numberSheet = result.data;
                this.exam_type = result.examName.type_name;
                this.session_name = result.sessionName.session_name;
                this.class_name = result.className.class_name;
                this.section_name = result.sectionName.section_name;

                // this.school_name = result.school_name;
                // this.school_address = result.school_address;

            }).catch(error => {
                console.error("Error fetching admit card and seat plan:", error.response ? error.response.data : error.message);
                this.$toastr('error', 'Failed to fetch data.', "Error");
            });
        },
    },

    mounted() {
        const _this = this;
        _this.getGeneralData(['examGradeNumber', 'session', 'class', 'examName', 'examinationType']);
    },
};
</script>

<style scoped>

.signature {
    font-weight: 800;
    border-bottom: 1px solid #000;
    width: 180px;
    float: right;
}

.table thead th {
    background-color: #cccbcb;
    text-align: center;
}

.sub-total {
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

.student-details-card {
    margin-bottom: -20px;
}

.student-detail {
    margin-bottom: 2px;
    font-size: 16px;
    color: #34495e;
}

.student-detail strong {
    color: #2c3e50;
}

.student-detail span {
    margin-left: 10px;
    font-weight: 600;
    color: #7f8c8d;
}

@media print {
    .student-details-card {
        margin-bottom: -20px;
        margin-left: 15px;
    }
}
</style>
