<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" :default-object="{}" table-title="Student Demotion">
                    <template v-slot:page_top>
                        <page-top
                            :default-object="{ department_id: '', class_id: '', session_year_id: '', section_id: '' }"
                            topPageTitle="Student Demotion"
                            :default-add-button="can('student_demotion_add')"></page-top>
                    </template>
                    <template v-slot:filter> </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ showData(data.student, 'student_name_en') }}</td>
                            <td>{{ showData(data.old_class, 'name') }}</td>
                            <td>{{ showData(data.old_section, 'name') }}</td>
                            <td>{{ data.student_roll }}</td>
                            <td>{{ showData(data.new_class, 'name') }}</td>
                            <td>{{ showData(data.new_section, 'name') }}</td>
                            <td>{{ data.demotion_roll }}</td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <a class="link-primary" @click="demotionDetails(data)"><i class="fa fa-eye"></i></a>
                                    <a v-if="can('student_demotion_delete')" class="link-danger"
                                        @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>

        <formModal modalSize="modal-xl" id="formModal" @submit="submitData()">
            <div class="row mb-2">

                <div class="col-md-3">
                    <select v-model="formObject.session_year_id" name="session_year_id" class="form-control">
                        <option value="">Select Session</option>
                        <template v-for="(data, index) in requiredData.session">
                            <option :value="data.id">{{ data.title }}</option>
                        </template>
                    </select>
                </div>

                <div class="col-md-3">
                    <select v-model="formObject.class_id" v-validate="'required'" name="class_id" class="form-control"
                        @change="
                            getGeneralData({
                                section: { class_id: formObject.class_id },
                                departments: { class_id: formObject.class_id },
                                getStudentData,
                            })
                            ">
                        <option value="">Select Class</option>
                        <template v-for="(eachData, index) in requiredData.class">
                            <option :value="eachData.id">{{ eachData.name }}</option>
                        </template>
                    </select>
                </div>

                <div class="col-md-3">
                    <select v-model="formObject.department_id" name="department_id" class="form-control">
                        <option value="">Select Department</option>
                        <template v-for="(eachData, index) in requiredData.departments">
                            <option :value="eachData.id">{{ eachData.department_name }}</option>
                        </template>
                    </select>
                </div>


                <div class="col-md-3">
                    <select v-model="formObject.section_id" @change="getStudentData" name="section_id"
                        class="form-control">
                        <option value="">Select Section</option>
                        <template v-for="(eachData, index) in requiredData.section">
                            <option :value="eachData.id">{{ eachData.name }}</option>
                        </template>
                    </select>
                </div>
            </div>

            <hr>
            <div class="row table-responsive">
                <h5 class="mb-3 text-white px-2 py-1 rounded text-uppercase" style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
                    Student Demotion
                </h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2" style="vertical-align: middle !important;">SL</th>
                            <th colspan="4" style="text-align: center">Promotion Status</th>
                            <th colspan="5" style="text-align: center">Demotion Status</th>
                        </tr>
                        <tr>

                            <th style="text-align: center">Name</th>
                            <th style="text-align: center">Class</th>
                            <th style="text-align: center">Section</th>
                            <th style="text-align: center">Department</th>
                            <th style="text-align: center">
                                <select class="form-control" v-model="bulkDemotion.class_id"
                                    @change="applyBulkDemotion('class')">
                                    <option value="">Class</option>
                                    <template v-for="(eachData, index) in requiredData.new_class">
                                        <option :value="eachData.id">{{ eachData.name }}</option>
                                    </template>
                                </select>
                            </th>

                            <th style="text-align: center">
                                <select class="form-control" v-model="bulkDemotion.section_id"
                                    @change="applyBulkDemotion('section')">
                                    <option value="">Section</option>
                                    <template v-for="(eachData, index) in requiredData.new_section">
                                        <option :value="eachData.id">{{ eachData.name }}</option>
                                    </template>
                                </select>
                            </th>

                            <th style="text-align: center">
                                <select class="form-control" v-model="bulkDemotion.session_id"
                                    @change="applyBulkDemotion('session')">
                                    <option value="">Session</option>
                                    <template v-for="(data, index) in requiredData.session">
                                        <option :value="data.id">{{ data.title }}</option>
                                    </template>
                                </select>
                            </th>

                            <th style="text-align: center">
                                <select class="form-control" v-model="bulkDemotion.department_id"
                                    @change="applyBulkDemotion('department')">
                                    <option value="">Department</option>
                                    <template v-for="(eachData, index) in requiredData.new_department">
                                        <option :value="eachData.id">{{ eachData.department_name }}</option>
                                    </template>
                                </select>
                            </th>

                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="(data, index) in details">
                            <td class="sl-cell">
                                <div class="sl-wrapper">
                                    <input type="checkbox" v-model="data.skipDemotion" @change="toggleDemotion(data)" />
                                    <span>{{ index + 1 }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-nowrap">{{ data.student_name_en }}</span><br />
                                <small class="text-muted">ID: {{ data.student_roll }}</small><br />
                                <small class="text-muted">Class Roll: {{ data.merit_roll }}</small>
                            </td>
                            <td>{{ data.class_name }}</td>
                            <td>{{ data.section_name }}</td>
                            <td style="text-align: center">{{ data.department_name }}</td>
                            <td>
                                <select class="form-control" v-model="data.demotion_class_id" name="demotion_class_id"
                                    v-validate="'required'">
                                    <option value="">Class</option>
                                    <template v-for="(eachData, index) in requiredData.new_class">
                                        <option :value="eachData.id">{{ eachData.name }}</option>
                                    </template>
                                </select>
                            </td>

                            <td>
                                <select v-model="data.demotion_section_id" name="demotion_section_id"
                                    class="form-control" v-validate="'required'">
                                    <option value="">Section</option>
                                    <template v-for="(eachData, index) in requiredData.new_section">
                                        <option :value="eachData.id">{{ eachData.name }}</option>
                                    </template>
                                </select>
                            </td>

                            <template>
                                <td>
                                    <select v-model="data.demotion_session_id" name="demotion_session_id"
                                        class="form-control" v-validate="'required'">
                                        <option value="">Session</option>
                                        <template v-for="(data, index) in requiredData.session">
                                            <option :value="data.id">{{ data.title }}</option>
                                        </template>
                                    </select>
                                </td>
                            </template>

                            <template>
                                <td>
                                    <select v-model="data.demotion_department_id" name="demotion_department_id"
                                        class="form-control" v-validate="'required'">
                                        <option value="">Department</option>
                                        <template v-for="(eachData, index) in requiredData.new_department">
                                            <option :value="eachData.id">{{ eachData.department_name }}</option>
                                        </template>
                                    </select>
                                </td>
                            </template>

                        </tr>
                    </tbody>
                </table>
            </div>
        </formModal>

        <general-modal modal-id="detailsModal" modalSize="modal-l">
            <template v-slot:title>
                <span>Student Demotion Details</span>
            </template>
            <template v-slot:body>
                <table class="table table-bordered align-middle">
                    <colgroup>
                        <col style="width: 40%;">
                        <col style="width: 5%;">
                        <col style="width: 55%;">
                    </colgroup>
                    <tbody>
                        <tr>
                            <th class="text-left">Name</th>
                            <td class="text-center">:</td>
                            <td>{{ showData(details.student, "student_name_en") }}</td>
                        </tr>
                        <tr>
                            <th class="text-left">Old Class</th>
                            <td class="text-center">:</td>
                            <td>{{ showData(details.old_class, "name") }}</td>
                        </tr>
                        <tr>
                            <th class="text-left">Old Section</th>
                            <td class="text-center">:</td>
                            <td>{{ showData(details.old_section, "name") }}</td>
                        </tr>
                        <tr>
                            <th class="text-left">Old Department</th>
                            <td class="text-center">:</td>
                            <td>{{ showData(details.o_department, "department_name") }}</td>
                        </tr>
                        <tr>
                            <th class="text-left">New Class</th>
                            <td class="text-center">:</td>
                            <td>{{ showData(details.new_class, "name") }}</td>
                        </tr>
                        <tr>
                            <th class="text-left">New Section</th>
                            <td class="text-center">:</td>
                            <td>{{ showData(details.new_section, "name") }}</td>
                        </tr>
                        <tr>
                            <th class="text-left">New Department</th>
                            <td class="text-center">:</td>
                            <td>{{ showData(details.new_department, "department_name") }}</td>
                        </tr>
                        <tr>
                            <th class="text-left">Old Roll</th>
                            <td class="text-center">:</td>
                            <td>{{ details.roll }}</td>
                        </tr>
                        <tr>
                            <th class="text-left">Demotion Roll</th>
                            <td class="text-center">:</td>
                            <td>{{ details.demotion_roll }}</td>
                        </tr>
                    </tbody>
                </table>
            </template>
        </general-modal>
    </div>
</template>
<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";
import GeneralModal from "../../components/generalModal"

export default {
    name: "studentDemotion",
    components: { PageTop, Pagination, DataTable, formModal, GeneralModal },
    data() {
        return {
            tableHeading: ["Sl", "Student Name", "Old Class", "Old Section", "Old Roll", "New Class", "New Section", "New Roll", "Action"],
            formModalId: "formModal",
            details: [],
            bulkDemotion: {
                class_id: "",
                section_id: "",
                session_id: "",
                department_id: "",
            },
        };
    },
    watch: {
        "formObject.class_id": function (newClassId) {
            this.updateDemotionClass(newClassId);
            if (newClassId) {
                this.getStudentData(newClassId);
            }
        },
        "formObject.session_year_id": function (newVal) {
            if (newVal) {
                this.getStudentData(newVal);
            }
        },
        "formObject.section_id": function (newVal) {
            if (newVal) {
                this.getStudentData(newVal);
            }
        },
        "formObject.department_id": function (newVal) {
            if (newVal) {
                this.getStudentData(newVal);
            }
        },
    },
    methods: {

        getStudentData(data) {
            const _this = this;
            if (!_this.formObject.class_id && !_this.formObject.class_id) return;
            var URL = `${_this.urlGenerate('api/get_demotion_data')}`;
            const paramsData = {
                session_year_id: data.session_id ? data.session_id : _this.formObject.session_year_id,
                class_id: data.class_id ? data.class_id : _this.formObject.class_id,
                section_id: data.section_id ? data.section_id : _this.formObject.section_id,
                department_id: data.department_id ? data.department_id : _this.formObject.department_id,
            };
            _this.getData(URL, "get", paramsData, {}, function (retData) {
                _this.details = retData.map((student) => ({
                    ...student,
                    skipDemotion: false,
                }));
            });
        },
        applyBulkDemotion(type) {
            this.details.forEach((student) => {
                if (student.skipDemotion) {
                    if (type === "class")
                        student.demotion_class_id = this.bulkDemotion.class_id;
                    if (type === "section")
                        student.demotion_section_id = this.bulkDemotion.section_id;
                    if (type === "session")
                        student.demotion_session_id = this.bulkDemotion.session_id;
                    if (type === "department")
                        student.demotion_department_id = this.bulkDemotion.department_id;
                    if (type === "optional_subject")
                        student.optional_subject_id = this.bulkDemotion.optional_subject_id;
                }
            });
        },

        toggleDemotion(student) {
            const _this = this;
            if (!student.skipDemotion) {
                student.demotion_class_id = null;
                student.demotion_section_id = null;
                student.demotion_session_id = null;
                student.demotion_department_id = null;
            } else {
                if (this.bulkDemotion.class_id)
                    student.demotion_class_id = this.bulkDemotion.class_id;
                if (this.bulkDemotion.section_id)
                    student.demotion_section_id = this.bulkDemotion.section_id;
                if (this.bulkDemotion.session_id)
                    student.demotion_session_id = this.bulkDemotion.session_id;
                if (this.bulkDemotion.department_id)
                    student.demotion_department_id = this.bulkDemotion.department_id;
            }
        },
        updateDemotionClass(selectedClassId) {

            const currentClassIndex = this.requiredData.class.findIndex(
                (classItem) => classItem.id === selectedClassId
            );

            if (
                currentClassIndex !== -1 &&
                currentClassIndex < this.requiredData.class.length - 1
            ) {
                const nextClass = this.requiredData.class[currentClassIndex - 1];

                this.requiredData.new_class = [nextClass];

                this.details.forEach((student) => {
                    student.demotion_class_id = nextClass.id;
                });

                this.getNewClassWiseData(nextClass.id);
                this.getClassWiseDepartment(nextClass.id);

            } else {
                this.requiredData.new_class = [];
                this.details.forEach((student) => {
                    student.demotion_class_id = null;
                });
            }
        },

        getNewClassWiseData(classId) {
            const URL = `${this.urlGenerate("api/get_new_class_wise_data")}`;
            const paramsData = { class_id: classId };

            this.getData(URL, "get", paramsData, {}, (retData) => {
                this.requiredData.new_section = retData.sections || [];
            });
        },

        getClassWiseDepartment(classId) {
            const URL = `${this.urlGenerate("api/get_class_wise_department")}`;
            const paramsData = { class_id: classId };

            this.getData(URL, "get", paramsData, {}, (retData) => {
                this.requiredData.new_department = retData.department || [];
            });
        },

        demotionDetails: function (data) {
            const _this = this;
            _this.openModal('detailsModal', false);
            var URL = `${_this.urlGenerate()}/${data.id}`;
            _this.getData(URL, "get", {}, {}, function (retData) {
                _this.details = retData;
                _this.openModal('detailsModal', false);
            });
        },

        submitData() {
            const _this = this;
            const submittedValue = _this.details.filter(data =>
                (data.demotion_roll && parseInt(data.demotion_roll) !== 0) ||
                (data.demotion_class_id && parseInt(data.demotion_class_id) !== 0) ||
                (data.demotion_session_id && parseInt(data.demotion_session_id) !== 0) ||
                (data.demotion_section_id && parseInt(data.demotion_section_id) !== 0) ||
                (data.demotion_department_id && parseInt(data.demotion_department_id) !== 0)
            );
            const requestData = {
                class_id: _this.formObject.class_id,
                student_id: _this.formObject.student_id,
                section_id: _this.formObject.section_id,
                details: submittedValue,
            };
            const URL = baseUrl + "/api/student_demotion";
            _this.axios.post(URL, requestData).then(response => {
                if (response.data.status === 2000) {
                    _this.$toastr('success', response.data.message, "Success");
                    _this.closeModal('formModal');
                } else {
                    _this.$toastr('error', response.data.message, "Error");
                }
            }).catch(error => {
                _this.$toastr('error', 'Failed to submit data.', "Error");
            });
        },
    },
    mounted() {
        const _this = this;
        this.getDataList();
        _this.$set(_this.formObject, 'all-check', true);
        this.getGeneralData(["class", "section", "new_class", "new_section", "session", "departments"]);
    },
};
</script>

<style scoped>
.table-center .action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.modal-title {
    margin-bottom: 0;
    line-height: 0.5;
    text-transform: uppercase;
}

.table thead th {
    background-color: #cccbcb;
    text-align: center;
}

.common-data {
    font-weight: bold;
}

.sl-cell {
    text-align: center;
    vertical-align: middle !important;
}

.sl-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
</style>
