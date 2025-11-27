<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :defaultFilter="false" table-title="Manage Mark"
                    :default-object="{ subject_id: '', session_year_id: '', class_id: '', department_id: '', section_id: '', mark_id: '', exam_id: '', exam_type_id: '' }"
                    :default-pagination="false" :table-responsive="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Manage Mark"
                            :default-add-button="false"></page-top>
                    </template>

                    <template v-slot:filter>
                        <div class="col-md-1">
                            <select class="form-control" v-model="formFilter.session_year_id" name="session_year_id">
                                <option value="">Session</option>
                                <template v-for="(data, index) in requiredData.session">
                                    <option :value="data.id">{{ data.title }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <select v-model="formFilter.class_id" name="class_id" class="form-control" @change="getGeneralData({
                                departments: { class_id: formFilter.class_id },
                                section: { class_id: formFilter.class_id },
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
                            <select v-model="formFilter.section_id" name="section_id" class="form-control" @change="getGeneralData({
                                department_subjects: { department_id: formFilter.department_id },
                                section_subjects: { section_id: formFilter.section_id },
                                // manage_mark_subjects: { section_id: formFilter.section_id },
                            })">
                                <option value="">Section</option>
                                <template v-for="(eachData, index) in requiredData.section">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <select v-model="formFilter.subject_id" name="subject_id" class="form-control">
                                <option value="">Subject</option>
                                <template v-for="(eachData, index) in requiredData.section_subjects">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <!-- auth teacher wise -->
                        <!-- <div class="col-md-1">
                            <select v-model="formFilter.subject_id" name="subject_id" class="form-control">
                                <option value="">Subject</option>
                                <template v-for="(eachData, index) in requiredData.manage_mark_subjects">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div> -->

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
                    </template>

                    <template v-slot:bottom_data>
                        <template v-if="dataList.students">
                            <div class="student-info">
                                <div>
                                    <span class="student-label">Subject :</span>
                                    <span class="student-value">{{ dataList.name }}</span>
                                </div>
                                <div>
                                    <span class="student-label">Class :</span>
                                    <span class="student-value">{{ dataList.class_name }}</span>
                                </div>
                                <div>
                                    <span class="student-label">Department :</span>
                                    <span class="student-value">{{ dataList.department_name }}</span>
                                </div>
                                <div>
                                    <span class="student-label">Session :</span>
                                    <span class="student-value">{{ dataList.session_name }}</span>
                                </div>
                            </div>

                            <!-- Rounded Marks Info Message -->

                            <div class="alert alert-info">
                                <strong>Note:</strong> Exam Component marks were <u>rounded</u> to the nearest whole
                                number before submission.
                            </div>

                            <form @submit.prevent="submitData()">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">Sl</th>
                                            <th rowspan="2">Student</th>
                                            <th rowspan="2">Class Roll</th>
                                            <th v-if="dataList.mark_component && dataList.mark_component.length > 0"
                                                :colspan="dataList.mark_component.length">
                                                Exam Component
                                            </th>
                                            <th rowspan="2" style="width: 80px;">CGPA</th>
                                            <th rowspan="2" style="width: 80px;">GRADE</th>
                                        </tr>
                                        <tr>
                                            <th v-for="(head, index) in dataList.mark_component" :key="index"
                                                class="mark-component-header">
                                                {{ head.name }}
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr v-for="(data, index) in dataList.students" :key="data.id">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ data.student_name }} - <strong>{{ data.student_roll }}</strong></td>
                                            <td style="text-align: center;">{{ data.class_roll }}</td>

                                            <td v-for="(thead, heaIndex) in dataList.mark_component" :key="heaIndex"
                                                class="mark-component-cell">
                                                <div class="mark-inputs">
                                                    <input type="text" @input="totalMarkCalculate(data, heaIndex)"
                                                        v-model="data['mark_' + heaIndex]"
                                                        class="form-control mark-input">

                                                    <input type="number" class="form-control converted-mark-input"
                                                        v-model="data['final_result_mark_' + heaIndex]"
                                                        name="final_result_mark" readonly>
                                                </div>
                                            </td>

                                            <td style="width: 80px;"> <input type="number" class="form-control"
                                                    v-model="data.cgpa" name="cgpa" readonly></td>
                                            <td style="width: 80px;"> <input type="text" class="form-control"
                                                    v-model="data.grade" name="grade" readonly></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="text-end mt-4" v-if="dataList.students">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                </div>
                            </form>
                        </template>
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
export default {
    name: "ManageMark",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ["Sl", "Grade Number", "Grade Name", "Grade Point", "Mark From", "Mark To", "Status", "Action"],
            formModalId: "formModal",
        };
    },
    methods: {
        submitData() {
            const _this = this;

            const submittedValue = _this.dataList.students.map(data => {
                const exam_mark_data = {};
                const component_name = {};
                const result_mark = {};
                let subjectTotalMark = '';
                let gradeNumberId = '';
                let examId = '';
                let examTypeId = '';
                let cgpa = '';
                let grade = '';
                let result_status = '';

                _this.dataList.mark_component.forEach((component, index) => {
                    exam_mark_data[index] = data['mark_' + index] || '';

                    let rawResult = parseFloat(data['final_result_mark_' + index]) || 0;
                    result_mark[index] = Math.round(rawResult);

                    component_name[index] = component.name || '';

                    gradeNumberId = component.grade_number_id;
                    subjectTotalMark = component.grade_number;
                    examId = component.exam_id;
                    examTypeId = component.exam_type_id;
                    cgpa = data.cgpa || 0;
                    grade = data.grade || '';
                });

                // _this.dataList.mark_component.forEach((component, index) => {
                //     exam_mark_data[index] = data['mark_' + index] || '';
                //     result_mark[index] = data['final_result_mark_' + index] || '';
                //     component_name[index] = component.name || '';

                //     gradeNumberId = component.grade_number_id;
                //     subjectTotalMark = component.grade_number;
                //     examId = component.exam_id;
                //     examTypeId = component.exam_type_id;
                //     cgpa = data.cgpa || 0;
                //     grade = data.grade || '';
                // });

                result_status = (grade.toLowerCase() === 'f') ? 'fail' : 'pass';

                return {
                    class_id: _this.dataList.class_id,
                    session_year_id: _this.dataList.session_year_id,
                    department_id: _this.dataList.department_id,
                    student_id: data.student_id,
                    exam_id: data.exam_id,
                    subject_id: _this.dataList.subject_id,
                    exam_id: examId,
                    exam_type_id: examTypeId,
                    subject_total_mark: subjectTotalMark,
                    grade_number_id: gradeNumberId,
                    get_mark: data.get_mark,
                    exam_mark_data: exam_mark_data,
                    result_mark: result_mark,
                    component_name: component_name,
                    cgpa: cgpa,
                    grade: grade,
                    result_status: result_status
                };
            });

            const URL = baseUrl + "/api/manage_marks";

            _this.axios.post(URL, submittedValue)
                .then(response => {
                    if (response.data.status === 2000) {
                        if (_this.$route.path !== '/admin/manage_marks') {
                            _this.$router.push({ path: '/admin/manage_marks' });
                        }
                        _this.$toastr('success', response.data.message, "Success");
                    } else {
                        _this.$toastr('error', response.data.message, "Error");
                    }
                })
                .catch(error => {
                    _this.$toastr('error', 'Failed to submit data.', "Error");
                });
        },

        updateAllExamIds(selectedExamId) {
            const _this = this;
            _this.dataList.students.forEach(student => {
                student.exam_id = selectedExamId;
            });
        },

        updateAllGradeNumbers(selectedGradeNumberId) {
            const _this = this;
            _this.dataList.students.forEach(student => {
                student.grade_number_id = selectedGradeNumberId;
            });
        },

        // totalMarkCalculate(data, componentIndex) {
        //     const _this = this;
        //     let totalMark = 0;
        //     let totalConvertedMark = 0;
        //     let error = false;

        //     const totalComponentMarks = this.dataList.mark_component.reduce((sum, component) => {
        //         return sum + parseFloat(component.total_mark || 0);
        //     }, 0);

        //     this.dataList.mark_component.forEach((component, index) => {
        //         const markValue = parseFloat(data['mark_' + index] || 0);

        //         if (markValue > component.exam_mark) {
        //             error = true;
        //             alert(`Error: Component Mark (${markValue}) Must Be Less Than Component Total Mark (${component.exam_mark})`);
        //             this.$set(data, 'mark_' + index, '');
        //             return;
        //         }

        //         // console.log('component.exam_mark : ', component.exam_mark);
        //         // console.log('markValue : ', markValue);
        //         // console.log('component.convert_num : ', component.convert_num);

        //         const resultMark = (markValue / component.exam_mark) * component.convert_num;
        //         // console.log('resultMark : ', resultMark);

        //         const convertedMark = (markValue * component.convert_num) / 100;
        //         this.$set(data, 'final_result_mark_' + index, resultMark.toFixed(2));

        //         totalMark += markValue;
        //         totalConvertedMark += ((convertedMark / totalComponentMarks) * 100);
        //         // console.log('totalMark : ', totalMark);
        //     });

        //     if (error) {
        //         return;
        //     }
        //     this.calculateCgpaAndGrade(data, totalConvertedMark);
        // },

        // this is for old mark calculation remove 


        // totalMarkCalculate(data, componentIndex) {
        //     let totalMark = 0;
        //     let totalConvertedMark = 0;
        //     let error = false;

        //     // If the current input is blank, clear all marks for this student
        //     const currentMark = data['mark_' + componentIndex];

        //     if (currentMark === '' || currentMark === null || currentMark === undefined) {
        //         this.dataList.mark_component.forEach((component, idx) => {
        //             this.$set(data, 'mark_' + idx, '');
        //             this.$set(data, 'final_result_mark_' + idx, '');
        //         });
        //         this.$set(data, 'cgpa', '');
        //         this.$set(data, 'grade', '');
        //         return;
        //     }

        //     // Total possible mark to calculate converted percentage
        //     const totalComponentMarks = this.dataList.mark_component.reduce((sum, component) => {
        //         return sum + parseFloat(component.total_mark || 0);
        //     }, 0);

        //     // Loop through each component to calculate total
        //     this.dataList.mark_component.forEach((component, index) => {
        //         const markValue = parseFloat(data['mark_' + index] || 0);

        //         if (markValue > component.exam_mark) {
        //             error = true;
        //             // alert(`Error: Component Mark (${markValue}) Must Be Less Than Component Total Mark (${component.exam_mark})`);
        //             this.$toastr('warning', `Component Mark (${markValue}) must be less than Component Total Mark (${component.exam_mark})`, 'Warning');
        //             this.$set(data, 'mark_' + index, '');
        //             this.$set(data, 'final_result_mark_' + index, '');
        //             this.$set(data, 'cgpa', '');
        //             this.$set(data, 'grade', '');
        //             return;
        //         }

        //         console.log('component : ', component);
        //         // console.log('component.exam_mark : ', component.exam_mark);
        //         // console.log('markValue : ', markValue);
        //         // console.log('component.convert_num : ', component.convert_num);

        //         const resultMark = (markValue / component.exam_mark) * component.convert_num;
        //         const convertedMark = (markValue * component.convert_num) / 100;
        //         // this.$set(data, 'final_result_mark_' + index, resultMark.toFixed(2));
        //         this.$set(data, 'final_result_mark_' + index, Math.round(resultMark).toFixed(2));


        //         totalMark += markValue;
        //         totalConvertedMark += ((convertedMark / totalComponentMarks) * 100);
        //     });

        //     if (!error) {
        //         this.calculateCgpaAndGrade(data, totalConvertedMark);
        //     }
        // },

        totalMarkCalculate(data, componentIndex) {
            let totalMark = 0;
            let totalConvertedMark = 0;
            let error = false;
            let isComponentFailed = false;
            const currentMark = data['mark_' + componentIndex];

            if (currentMark === '' || currentMark === null || currentMark === undefined) {
                this.dataList.mark_component.forEach((component, idx) => {
                    this.$set(data, 'mark_' + idx, '');
                    this.$set(data, 'final_result_mark_' + idx, '');
                });
                this.$set(data, 'cgpa', '');
                this.$set(data, 'grade', '');
                return;
            }

            console.log('dataList.mark_component : ', this.dataList.mark_component);
            

            const totalComponentMarks = this.dataList.mark_component.reduce((sum, component) => {
                return sum + parseFloat(component.total_mark || 0);
            }, 0);

            this.dataList.mark_component.forEach((component, index) => {
                const markValue = parseFloat(data['mark_' + index] || 0);

                if (markValue > component.exam_mark) {
                    error = true;
                    this.$toastr('warning', `Component Mark (${markValue}) must be less than Component Total Mark (${component.exam_mark})`, 'Warning');
                    this.$set(data, 'mark_' + index, '');
                    this.$set(data, 'final_result_mark_' + index, '');
                    this.$set(data, 'cgpa', '');
                    this.$set(data, 'grade', '');
                    return;
                }

                const passMark = parseFloat(component.pass_mark || 0);
                if (markValue < passMark) {
                    isComponentFailed = true;
                }

                const resultMark = (markValue / component.exam_mark) * component.convert_num;
                const convertedMark = (markValue * component.convert_num) / 100;
                this.$set(data, 'final_result_mark_' + index, Math.round(resultMark).toFixed(2));

                totalMark += markValue;

                //R =  get exam mark data * convertedMark ) / 100;
                // ( final result = R / grade_numbers(total_mark) ) * 100

                
                totalConvertedMark += ((convertedMark / totalComponentMarks) * 100);
            });

            if (!error) {
                if (isComponentFailed) {
                    data.cgpa = 0;
                    data.grade = 'F';
                } else {
                    this.calculateCgpaAndGrade(data, totalConvertedMark);
                }
            }
        },

        calculateCgpaAndGrade(data, totalConvertedMark) {
            // console.log(totalConvertedMark)
            if (totalConvertedMark >= 80) {
                data.cgpa = 5.0;
                data.grade = 'A+';
            } else if (totalConvertedMark >= 70) {
                data.cgpa = 4.0;
                data.grade = 'A';
            } else if (totalConvertedMark >= 60) {
                data.cgpa = 3.50;
                data.grade = 'A-';
            } else if (totalConvertedMark >= 50) {
                data.cgpa = 3.0;
                data.grade = 'B';
            } else if (totalConvertedMark >= 40) {
                data.cgpa = 2.0;
                data.grade = 'C';
            }
            else {
                data.cgpa = 0;
                data.grade = 'F';
            }
        }
    },

    mounted() {
        const _this = this;
        _this.getGeneralData(['session', 'class', 'examName'], function (retData) {
            _this.getGeneralData({
                class: { school_id: _this.Config.school_id },
                class: { class_id: _this.Config.class_id },
            });
        });
    },
};
</script>

<style scoped>
.table thead th {
    background-color: #cccbcb;
    text-align: center;
}

.student-info {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin: 20px 0;
    font-size: 14px;
}

.student-label {
    font-weight: bold;
    margin-right: 5px;
}

.student-value {
    margin-left: 5px;
    color: rgb(0, 0, 0);
}

.mark-inputs {
    display: flex;
    gap: 5px;
}

.mark-input,
.converted-mark-input {
    flex: 1;
    min-width: 0;
}

.mark-component-header,
.mark-component-cell {
    width: 350px;
    text-align: center;
}
</style>
