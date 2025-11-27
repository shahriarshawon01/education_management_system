<template>
    <div class="main_component">
        <table class="table table-bordered ">

            <thead>

                <tr>
                    <th>Class</th>
                    <th>Session</th>
                    <th>Section</th>
                    <th>Depertment</th>
                    <!--                <th>-->
                    <!--                    From SL-->
                    <!--                </th>-->
                    <!--                <th>-->

                    <!--                    To-->
                    <!--                </th>-->
                    <!-- <th>
                    Subject
                </th> -->
                    <th>
                        Applicant ID
                    </th>
                    <th>
                        Action
                    </th>
                </tr>

            </thead>
            <tbody>
                <tr>
                    <td class="col-2" style="width: 16.66666667% !important">
                        <select class="form-control" name="class_id" v-model="formData.class_id">
                            <option value="" selected disabled>select Class</option>
                            <option :value="classInfo_list.id" v-for="(classInfo_list, index) in formData.classinfos"
                                :key="index" v-text="classInfo_list.name"></option>
                        </select>
                    </td>
                    <td class="col-2">
                        <select class="form-control" name="session_id" v-model="formData.session_id">
                            <option value="" selected disabled>select Session</option>
                            <option :value="session_list.id" v-for="(session_list, index) in formData.session_info"
                                :key="index" v-text="session_list.title">
                            </option>
                        </select>
                    </td>
                    <!-- <td v-if="showData(Config.school, 'institution_type_id') ==1">
                    <select class="form-control" v-model="formData.group_id">
                        <option :value="group_list.id" v-for="(group_list, index) in formData.groups"
                                v-text="group_list.name"></option>
                    </select>
                </td> -->
                    <td class="col-2">
                        <select class="form-control" name="section_id" v-model="formData.section_id">
                            <option value="" selected disabled>select Section</option>
                            <option :value="section_list.id" v-for="(section_list, index) in formData.section_info"
                                :key="index" v-text="section_list.name"></option>
                        </select>
                    </td>
                    <!-- <td class="col-2">
                    <select class="form-control" name="subject_id" v-model="formData.subject_id">
                        <option :value="subject_list.id" v-for="(subject_list, index) in formData.subject"
                                v-text="subject_list.subject_name"></option>
                    </select>
                </td> -->

                    <td>
                        <select class="form-control" v-model="formData.department_id">
                            <option value="" selected disabled>select Department</option>
                            <option :value="depertment_list.id"
                                v-for="(depertment_list, index) in formData.department_info" :key="index"
                                v-text="depertment_list.department_name"></option>
                        </select>
                    </td>

                    <!--                <td class="col-1">-->
                    <!--                    <input type="number" placeholder="1" v-model="formData.from_sl" class="form-control"/>-->
                    <!--                </td>-->
                    <!--                <td class="col-1">-->
                    <!--                    <input type="number" placeholder="100" v-model="formData.to_sl" class="form-control"/>-->
                    <!--                </td>-->
                    <td class="col-2">
                        <input type="text" placeholder="Applicant ID" v-model="formData.applicant_id"
                            class="form-control" />
                    </td>
                    <td>
                        <button class="btn btn-primary fw-bold" @click="admission_mark_entry();">Submit</button>
                    </td>
                </tr>

            </tbody>
        </table>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <!-- <th>SL</th> -->
                    <th>Applicant ID</th>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Session</th>
                    <th>Section</th>
                    <th>Department</th>
                    <th>Total Mark</th>
                    <th>Subjectwise Marks</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(mark_entry, index) in AdmissionForm" :key="index">
                    <!-- <td v-text="index+1"></td> -->
                    <td v-text="mark_entry.applicant_id"></td>
                    <td v-text="mark_entry.applicant_name_en"></td>
                    <td v-text="mark_entry.class_info_name"></td>
                    <td v-text="mark_entry.session_title"></td>
                    <td v-text="mark_entry.section_name"></td>
                    <td v-text="mark_entry.department_name"></td>
                    <td class="col-1">
                        <input v-text="mark_entry.mark" class="form-control" disabled readonly
                            :value="mark_entry.total" />
                    </td>
                    <td class="col-3">

                        <template v-for="(mark_entry2, index) in mark_entry.subjectWiseData">
                            <div class="row">
                                <div class="col-6">
                                    <label class="control-label">{{ mark_entry2.subject_name }} ({{ mark_entry2.marks
                                        }})</label>
                                </div>
                                <div class="col-4">
                                    <input type="text" v-model="mark_entry2.subject_marks" name="subject_marks"
                                        @change="updateTotal($event, mark_entry)" class="form-control">
                                </div>
                            </div>
                        </template>
                    </td>
                </tr>
                <tr>
                    <td colspan="9">
                        <button class="btn btn-danger float-end ml-4" @click="store_mark();">Submit Mark</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
import axios from 'axios';

export default {
    data() {
        return {
            formData: {
                institute_type: 1,
                mark_entry_data: [],
                classinfos: [],
                session_info: [],
                section_info: [],
                department_info: [],

                class_id: '',
                section_id: '',
                department_id: '',
                session_id: '',
                subject_id: '',
                admission_id: '',
                from_sl: '',
                to_sl: '',
                applicant_id: '',
                admission: [
                    {
                        mark: '',
                        applicant_id: '',
                        applicant_primary_key: '',
                    }
                ],
            },
            AdmissionForm: [],
        }
    },
    methods: {
        updateTotal(evt, obj) {

        },
        admission_mark_entry: function () {
            const _this = this;
            const admission_formData = new FormData();
            admission_formData.append('class_id', this.formData.class_id);
            admission_formData.append('section_id', this.formData.section_id);
            // admission_formData.append('group_id', this.formData.group_id);
            admission_formData.append('department_id', this.formData.department_id);
            admission_formData.append('session_id', this.formData.session_id);
            admission_formData.append('from_sl', this.formData.from_sl);
            admission_formData.append('to_sl', this.formData.to_sl);
            admission_formData.append('applicant_id', this.formData.applicant_id);

            axios.post("/api/admission_mark_entry", admission_formData)
                .then((data) => {
                    if (data.data.status == 5000) {
                        _this.$toastr('warning', data.data.message, 'Exam Status')
                    } else {
                        ;
                        this.$set(_this.AdmissionForm, 'mark_entry_data', data.data.result.data_info.data);
                        let group = Object.groupBy(data.data.result.data_info.data, item => item.applicant_id);
                        let groupData = {}
                        Object.keys(group).forEach(key => {
                            let item = group[key]
                            item.forEach(entry => {
                                if (!groupData[entry.applicant_id]) {
                                    groupData[entry.applicant_id] = {
                                        applicant_id: entry.applicant_id,
                                        applicant_name_en: entry.applicant_name_en,
                                        class_info_name: entry.class_info.name,
                                        session_title: entry.session.title,
                                        section_name: entry.section.name,
                                        department_name: entry.department.department_name,
                                        class_id: entry.class_id,
                                        section_id: entry.section_id,
                                        department_id: entry.department_id,
                                        session_id: entry.session_id,
                                        group_id: entry.group_id,
                                        total: 0,
                                        subjectWiseData: []
                                    }
                                }
                                let gdata = groupData[entry.applicant_id]
                                gdata.total += parseInt(entry.subject_marks)
                                gdata.subjectWiseData.push({
                                    applicant_id: entry.applicant_id,
                                    subject_name: entry.subject_name,
                                    subject_marks: entry.subject_marks,
                                    subject_id: entry.subject_id,
                                    marks_entry_id: entry.entry_id,
                                    marks: entry.marks
                                })
                            })

                        })
                        this.AdmissionForm = groupData
                    }
                })
                .catch((erorr) => {
                    console.log(erorr);
                })
        },
        store_mark: function () {
            const _this = this;
            _this.axios.post('/api/admission_mark_entry/store', _this.AdmissionForm).then((response) => {
                if (parseInt(response.data.status) === 2000) {
                    _this.$toastr('success', 'Data Update Successfully Done', 'Store/Update')
                    this.$set(imageObject, dataModel, response.data.result);
                    if (typeof callback === 'function') {
                        callback(true);
                    }
                } else if (parseInt(response.data.status) === 3000) {
                    _this.$toastr('error', response.data.message, 'Error');
                }
            }).catch(function () {
                // _this.$store.commit('httpRequest', false);
            });
        },

        // admission_data: function () {
        //     const _this = this;
        //     axios.get("/api/admission_data")
        //     .then((response) => {
        //         _this.formData.classinfos = response.data.result.classes;
        //         _this.formData.groups = response.data.result.group;
        //         _this.formData.session_info = response.data.result.sessions;
        //         _this.formData.subject = response.data.result.subjects;
        //         _this.formData.shift_info = response.data.result.shifts;
        //         _this.formData.present_divisions = response.data.result.divisions;
        //         _this.formData.depertments = response.data.result.depertments;
        //         _this.formData.permanent_divisions = response.data.result.divisions;
        //         _this.formData.blood_group_data = response.data.result.blood_group;
        //         _this.formData.gender_data = response.data.result.gender;
        //         _this.formData.religion_data = response.data.result.religion;
        //         _this.formData.education_board_data = response.data.result.education_board;
        //         _this.formData.subject_mapping = response.data.result.education_level;
        //     })
        //     .catch((error) => {
        //         console.log(error);
        //     })
        // },

        admission_data: function () {
            const _this = this;
            axios.get('/admission_data')
                .then((response) => {
                    _this.formData.classinfos = response.data.result.classes;
                    _this.formData.section_info = response.data.result.sections;
                    _this.formData.session_info = response.data.result.sessions;
                    _this.formData.department_info = response.data.result.departments;
                    _this.formData.subject = response.data.result.subjects;
                    _this.formData.shift_info = response.data.result.shifts;
                    _this.formData.present_divisions = response.data.result.divisions;
                    _this.formData.depertments = response.data.result.depertments;
                    _this.formData.permanent_divisions = response.data.result.divisions;
                    _this.formData.blood_group_data = response.data.result.blood_group;
                    _this.formData.gender_data = response.data.result.gender;
                    _this.formData.religion_data = response.data.result.religion;
                    _this.formData.education_board_data = response.data.result.education_board;
                    _this.formData.subject_mapping = response.data.result.education_level;
                })
                .catch((erorr) => {
                    console.log(erorr);
                })
        },
    },
    mounted() {
        this.admission_data();
    }
}

</script>

<style scoped></style>