<template>
    <data-table :table-heading="[]" table-title="Result List" :defaultFilter="false" :default-object="{exam_id:''}">
        <template v-slot:page_top>
            <page-top topPageTitle="Result List"  :default-add-button="false">
                <div @click="printData('printDiv')">
                    <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i> Print</button>
                </div>
            </page-top>
        </template>
        <template v-slot:filter>
            <div class="col-md-2">
                <select class="form-control" v-model="formFilter.exam_id" name="exam_id">
                    <option value="">Select Exam</option>
                    <template v-for="(eachData,index) in requiredData.examination">
                        <option :value="eachData.id">{{eachData.name}}</option>
                    </template>
                </select>
            </div>
        </template>
        <template v-slot:bottom_data>
            <template>
                <div class="row mb-4" v-if="dataList.data">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-4"><strong>Name</strong></div>
                            <div class="col-md-1"><strong>:</strong></div>
                            <div class="col-md-6"><p>{{ dataList.student_name }}</p></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><strong>Roll</strong></div>
                            <div class="col-md-1"><strong>:</strong></div>
                            <div class="col-md-6"><p>{{ dataList.student_roll }}</p></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><strong>Class</strong></div>
                            <div class="col-md-1"><strong>:</strong></div>
                            <div class="col-md-6"><p>{{ dataList.class_name }}</p></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><strong>Department</strong></div>
                            <div class="col-md-1"><strong>:</strong></div>
                            <div class="col-md-6"><p>{{ dataList.department_name }}</p></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><strong>Session</strong></div>
                            <div class="col-md-1"><strong>:</strong></div>
                            <div class="col-md-6"><p>{{ dataList.session_name }}</p></div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <h3>{{ dataList.school_name }}</h3>
                        <strong>{{ dataList.ein_name }}</strong><br>
                        <strong>{{ dataList.address }}</strong>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="row gradeshow">
                            <div class="col-md-6 text-start">
                                <label>Number</label><br>
                                <strong>80 % - 100 %</strong><br>
                                <strong>70 % - 79 %</strong><br>
                                <strong>60 % - 69 %</strong><br>
                                <strong>50 % - 59 %</strong><br>
                                <strong>40 % - 49 %</strong><br>
                                <strong>33 % - 39 %</strong><br>
                                <strong>0 % - 32 %</strong><br>
                            </div>
                            <div class="col-md-3 text-start">
                                <label>Point</label><br>
                                <strong>5.00</strong><br>
                                <strong>4.00</strong><br>
                                <strong>3.50</strong><br>
                                <strong>3.00</strong><br>
                                <strong>2.00</strong><br>
                                <strong>1.00</strong><br>
                                <strong>0.00</strong><br>
                            </div>
                            <div class="col-md-3 text-start">
                                <label>Letter</label><br>
                                <strong>A + </strong><br>
                                <strong>A </strong><br>
                                <strong>A - </strong><br>
                                <strong> B</strong><br>
                                <strong> C</strong><br>
                                <strong> D</strong><br>
                                <strong> F</strong><br>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr class="bagrouthed">
                            <th>Sl</th>
                            <th>Subject Name</th>
                            <th>Exam Number</th>
                            <th>Exam Name</th>
                            <th v-for="(head, index) in dataList.component" :key="index">
                                {{ head.name }}
                            </th>
                            <th>Total Mark</th>
                            <th>Grade</th>
                            <th>Grade Point</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(data,index) in dataList.data">
                            <td>{{index+1}}</td>
                            <td>{{ data.subject_name }}</td>
                            <td>{{ data.grade_number }}</td>
                            <td>{{ data.exam_name }}</td>
                            <td  v-for="(mark, dIndex) in dataList.examMarkData" :key="dIndex">
                                {{ mark.name }}
                            </td>
                            <td>{{ data.total_mark }}</td>
                            <td>{{ data.grade_name }}</td>
                            <td>{{ data.grade_point }}</td>
                            <td>{{ dataList.exam_status }} - [ {{ dataList.failed_components }} ]</td>
                        </tr>
                    </tbody>
                </table>
            </template>
        </template>
    </data-table>
</template>

<script>
    import DataTable from "../components/dataTable";
    import PageTop from "../components/pageTop";

    export default {
        name: "resultList",
        components: {PageTop, DataTable},
        data() {
            return {
                tableHeading: ["Sl", "Subject Name", "Subject Code", "Teacher", "Subject Credit"],
            };
        },
        mounted() {
            // this.getDataList();
            this.getGeneralDataStudent(['examination']);
        },
    };
</script>

<style scoped>
    .bagrouthed{
        background: #f4f4f4;
    }
    .gradeshow{
        border: 1px solid;
    }
</style>
