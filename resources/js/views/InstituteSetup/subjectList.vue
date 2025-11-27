<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Subject">
                    <template v-slot:page_top>
                        <page-top
                            :default-object="{ class_id: '', is_optional: '', department_id: '', section_id: '', teacher_id: '', subject_group_id: '', session_year_id: '', subject_type: '' }"
                            topPageTitle="Subject" :default-add-button="can('subjects_add')"></page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.class_id" name="class_id" @change="
                                getGeneralData({
                                    departments: { class_id: formFilter.class_id },
                                    section: { class_id: formFilter.class_id },
                                })
                                ">
                                <option value="">Select Class</option>
                                <template v-for="(data, index) in requiredData.class">
                                    <option :value="data.id">{{ data.name }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select v-model="formFilter.department_id" name="department_id" class="form-control">
                                <option value="">Select Department</option>
                                <template v-for="(data, index) in requiredData.departments">
                                    <option :value="data.id">{{ data.department_name }}</option>
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

                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>
                                <span>{{ data.name }}</span><br />
                                <small v-if="data.is_optional == 1" class="text-muted">
                                    <strong class="fw-bold">Type:</strong> <span class="text-muted">Compulsory</span>
                                </small>
                                <small v-else-if="data.is_optional == 2" class="text-muted">
                                    <strong class="fw-bold">Type:</strong> <span class="text-muted">Optional</span>
                                </small>
                            </td>
                            <td>{{ showData(data.group_subject, 'name') }}</td>
                            <td>{{ data.subject_mark }}</td>
                            <!-- <td>{{ data.subject_code }}</td> -->
                            <td>{{ showData(data.sessions, 'title') }}</td>
                            <td>{{ showData(data.classes, 'name') }}</td>
                            <td>{{ showData(data.departments, 'department_name') }}</td>
                            <td>{{ showData(data.sections, 'name') }}</td>
                            <td>{{ showData(data.teachers, 'name') }}</td>
                            <td>
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <a class="link-primary" @click="subjectDetails(data)"><i class="fa fa-eye"></i></a>
                                    <a v-if="can('subjects_update')" class="link-primary"
                                        @click="editDataInformation(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('subjects_delete')" class="link-danger"
                                        @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>

        <formModal modalSize="modal-lg" @submit="submitForm(formObject, 'formModal')">
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <h5 class="mb-3 text-white px-2 py-1 rounded"
                        style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
                        Subject Information
                    </h5>

                    <div class="mb-3">
                        <label class="form-label">Subject Name</label>
                        <input type="text" v-model="formObject.name" v-validate="'required'" name="name"
                            class="form-control" placeholder="Subject Name">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Group</label>
                            <select class="form-control" v-model="formObject.subject_group_id" name="subject_group_id">
                                <option value="">Select Group</option>
                                <template v-for="(data, index) in requiredData.groupSubject">
                                    <option :value="data.id">{{ data.name }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Subject Mark</label>
                            <input type="text" v-model="formObject.subject_mark" v-validate="'required'"
                                name="subject_mark" class="form-control" placeholder="Mark">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Subject Code</label>
                            <input type="text" v-model="formObject.subject_code" name="subject_code"
                                class="form-control" placeholder="Code">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Teacher</label>
                        <select v-select2 class="form-control" v-model="formObject.teacher_id" v-validate="'required'"
                            name="teacher_id">
                            <option value="">Select Teacher</option>
                            <template v-for="(eachData, index) in requiredData.teachers">
                                <option :value="eachData.id">{{ eachData.name }} - {{ eachData.employee_id }}</option>
                            </template>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Subject Type</label>
                            <select v-model="formObject.subject_type" v-validate="'required'" name="subject_type"
                                class="form-control">
                                <option value="">Select Subject Type</option>
                                <option value="1">Tech</option>
                                <option value="2">Non-Tech</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Is Optional</label>
                            <select v-model="formObject.is_optional" v-validate="'required'" name="is_optional"
                                class="form-control">
                                <option value="">Select</option>
                                <option value="1">Compulsory</option>
                                <option value="2">Optional</option>
                            </select>
                        </div>

                        <!-- <div class="col-md-4">
                            <label class="form-label">Subject Code</label>
                            <input type="text" v-model="formObject.subject_code" name="subject_code"
                                class="form-control" placeholder="Code">
                        </div> -->
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <h5 class="mb-3 text-white px-2 py-1 rounded"
                        style="background: linear-gradient(90deg, #3b6e67, #82b9ae);">
                        Class Details
                    </h5>

                    <div class="mb-3">
                        <label class="form-label">Class</label>
                        <select v-model="formObject.class_id" name="class_id" class="form-control"
                            v-validate="'required'" @change="
                                getGeneralData({
                                    section: { class_id: formObject.class_id },
                                    departments: { class_id: formObject.class_id }
                                })
                                ">
                            <option value="">Select Class</option>
                            <template v-for="(eachData, index) in requiredData.class">
                                <option :value="eachData.id">{{ eachData.name }}</option>
                            </template>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Session</label>
                        <select class="form-control" v-model="formObject.session_year_id" name="session_year_id"
                            v-validate="'required'">
                            <option value="">Select Session</option>
                            <template v-for="(data, index) in requiredData.session">
                                <option :value="data.id">{{ data.title }}</option>
                            </template>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select class="form-control" v-model="formObject.department_id" name="department_id">
                            <option value="">Select Department</option>
                            <template v-for="(data, index) in requiredData.departments">
                                <option :value="data.id">{{ data.department_name }}</option>
                            </template>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Section</label>
                        <select v-model="formObject.section_id" v-validate="'required'" name="section_id"
                            class="form-control">
                            <option value="">Select Section</option>
                            <template v-for="(eachData, index) in requiredData.section">
                                <option :value="eachData.id">{{ eachData.name }}</option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
        </formModal>

        <general-modal modal-id="detailsModal" modalSize="modal-l">
            <template v-slot:body>
                <div class="details-container">
                    <div class="details-row" v-if="details.teachers">
                        <label>Subject Teacher</label>
                        <div><strong>:</strong> {{ showData(details.teachers, 'name') }}</div>
                    </div>
                    <div class="details-row" v-if="details.name">
                        <label>Subject Name</label>
                        <div><strong>:</strong> {{ details.name }}</div>
                    </div>

                    <div class="details-row" v-if="details.classes">
                        <label>Class</label>
                        <div><strong>:</strong> {{ showData(details.classes, 'name') }}</div>
                    </div>

                    <div class="details-row" v-if="details.departments">
                        <label>Department</label>
                        <div><strong>:</strong> {{ showData(details.departments, 'department_name') }}</div>
                    </div>

                    <div class="details-row" v-if="details.subject_code">
                        <label>Subject Group</label>
                        <div><strong>:</strong> {{ showData(details.group_subject, 'name') }}</div>
                    </div>

                    <div class="details-row" v-if="details.subject_code">
                        <label>Subject Code</label>
                        <div><strong>:</strong> {{ details.subject_code }}</div>
                    </div>

                    <div class="details-row" v-if="details.sessions">
                        <label>Sessions</label>
                        <div><strong>:</strong> {{ showData(details.sessions, 'title') }}</div>
                    </div>
                </div>
            </template>
        </general-modal>
    </div>
</template>
<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";
import GeneralModal from "../../components/generalModal";
export default {
    name: "subjectList",
    components: { PageTop, Pagination, DataTable, formModal, GeneralModal },
    data() {
        return {
            tableHeading:
                [
                    "Sl",
                    "Subject Name",
                    "Group Subject",
                    "Subject Mark",
                    "Session",
                    "Class",
                    "Department",
                    "Section",
                    "Teacher Name",
                    "Status",
                    "Action"
                ],
            formModalId: "formModal",
            details: {}
        };
    },
    methods: {

        editDataInformation: function (data, id) {
            const _this = this;
            _this.editData(data, id, 'formModal', function () {
                const editUrl = `${_this.urlGenerate()}/${id}/edit`;
                _this.getData(editUrl, 'get', {}, {}, function (retData) {
                    if (retData.status === 1) {
                        const subject = retData;

                        _this.$set(_this.formObject, 'name', subject.name);
                        _this.$set(_this.formObject, 'subject_code', subject.subject_code);
                        // _this.$set(_this.formObject, 'is_optional', subject.is_optional);
                        _this.$set(_this.formObject, 'subject_mark', subject.subject_mark);
                        _this.$set(_this.formObject, 'subject_type', subject.subject_type);
                        _this.$set(_this.formObject, 'teacher_id', subject.teacher_id);
                        _this.$set(_this.formObject, 'class_id', subject.class_id);
                        _this.$set(_this.formObject, 'session_year_id', subject.session_year_id);
                        _this.$set(_this.formObject, 'subject_group_id', subject.subject_group_id);
                        _this.$set(_this.formObject, 'department_id', subject.department_id);

                        _this.getGeneralData({
                            section: { class_id: subject.class_id },
                            departments: { class_id: subject.class_id },
                            section: { class_id: subject.class_id },
                        });

                    } else {
                        _this.$toastr('error', retData.message, "Error");
                    }
                });
            });
        },

        subjectDetails: function (data) {
            const _this = this;
            var URL = `${_this.urlGenerate()}/${data.id}`;
            var title = `${_this.showData(data, 'name')}`;
            _this.openModal('detailsModal', title, function () {
                _this.getData(URL, "get", {}, {}, function (retData) {
                    _this.details = retData;
                });
            });
        },

        assign() {
            const _this = this;
            _this.$set(_this.formFilter, 'class_id', '');
            _this.$set(_this.formFilter, 'department_id', '');
            _this.$set(_this.formFilter, 'section_id', '');
        },
    },
    mounted() {
        const _this = this;
        _this.getDataList();
        _this.assign();
        _this.$set(_this.formObject, 'teacher_id', '');
        _this.getGeneralData(['session', 'teachers', 'school', 'groupSubject'], function (retData) {
            _this.getGeneralData({
                class: { school_id: _this.Config.school_id },
            });
        });
    },
};
</script>

<style scoped>
.table-center .action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.details-container {
    padding: 10px 5px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.details-row {
    display: flex;
    align-items: flex-start;
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 8px;
    font-size: 15px;
    color: #333;
}

.details-row label {
    width: 30%;
    font-weight: 600;
    color: #444;
    margin-bottom: 0;
}

.details-row div {
    width: 70%;
    display: flex;
    gap: 5px;
    color: #222;
}
</style>
