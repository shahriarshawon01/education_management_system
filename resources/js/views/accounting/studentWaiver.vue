<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Student Waiver">
                    <template v-slot:page_top>
                        <page-top
                            :default-object="{ componentWaiver: [{ class_id: '', session_year_id: '', student_id: '', component_id: '', type: '', value: '' }] }"
                            topPageTitle="Student Waiver" :default-add-button="can('student_waiver_add')"></page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.class_id" name="class_id" class="form-control" @change="
                                getGeneralData({
                                    section: { class_id: formFilter.class_id },
                                })
                                ">
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
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.student.student_name_en }} - <strong>[{{ data.student.student_roll }}]</strong>
                            </td>
                            <td>{{ data.student.classes.name }}</td>
                            <td>{{ data.student.sessions.title }}</td>
                            <td>{{ data.component.name }}</td>
                            <td>{{ data.type === 1 ? 'Percentage (%)' : 'Fixed' }}</td>
                            <td style="text-align: right;">{{ data.value }}</td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <a v-if="can('student_waiver_update')" class="link-primary"
                                        @click="editDataInformation(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('student_waiver_delete')" class="link-danger"
                                        @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>

        <formModal modalSize="modal-xl" @submit="submitData">
            <h4>Student Waiver Manage</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 200px !important; text-align: center;">Session</th>
                        <th style="width: 200px !important; text-align: center;">Class</th>
                        <th style="width: 200px !important; text-align: center;">Student</th>
                        <th style="width: 180px !important; text-align: center;">Component</th>
                        <th style="width: 120px !important; text-align: center;">Type</th>
                        <th style="width: 120px !important; text-align: center;">Value</th>
                        <th style="width: 100px !important; text-align: center;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="(row, index) in formObject.componentWaiver" :key="index">
                        <td>
                            <select class="form-control" v-model="row.session_year_id" name="session_year_id"
                                v-validate="'required'">
                                <option value="">Select Session</option>
                                <template v-for="(data, index) in requiredData.session">
                                    <option :value="data.id">{{ data.title }}</option>
                                </template>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" v-model="row.class_id" name="class_id" v-validate="'required'"
                                @change="
                                    getGeneralData({
                                        students: {
                                            class_id: row.class_id,
                                            school_id: Config.school_id,
                                            objectName: `student_${row.class_id}`
                                        },
                                    })
                                    ">
                                <option value="">Select Class</option>
                                <template v-for="(data, index) in requiredData.account_student_class">
                                    <option :value="data.id">{{ data.name }}</option>
                                </template>
                            </select>
                        </td>

                        <td>
                            <select v-select2 class="form-control" v-model="row.student_id" v-validate="'required'"
                                name="student_id">
                                <option value="">Select student</option>
                                <template v-for="(data, index) in requiredData[`student_${row.class_id}`]">
                                    <option :value="data.id">
                                        {{ data.student_name_en }} - <strong>{{ data.student_roll }}</strong>
                                    </option>
                                </template>
                            </select>
                        </td>

                        <td>
                            <select v-select2 class="form-control" v-model="row.component_id" v-validate="'required'"
                                name="component_id">
                                <option value="">Select component</option>
                                <template v-for="(data, index) in requiredData.componentName">
                                    <option :value="data.id">
                                        {{ data.name }}
                                    </option>
                                </template>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" v-model="row.type" v-validate="'required'" name="type">
                                <option value="">Select type</option>
                                <option value="1">Percentage</option>
                                <option value="2">Fixed</option>
                            </select>
                        </td>
                        <td>
                            <template v-if="row.type == 1">
                                <input type="text" v-model="row.value" name="value" class="form-control"
                                    placeholder="Enter value in %">
                            </template>
                            <template v-else-if="row.type == 2">
                                <input type="text" v-model="row.value" name="value" class="form-control"
                                    placeholder="Enter value in amount">
                            </template>
                        </td>

                        <td class="text-center">
                            <a class="btn btn-outline-success btn-sm"
                                @click="addRow(formObject.componentWaiver, { session_year_id: '', class_id: '', student_id: '', component_id: '', type: '', value: '' })">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </a>
                            <a class="btn btn-outline-danger btn-sm" v-if="formObject.componentWaiver.length > 1"
                                @click="deleteRow(formObject.componentWaiver, index)">
                                <i class="fa fa-close" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </formModal>
    </div>
</template>

<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";

export default {
    name: "studentWaiver",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ["Sl", "Student Name", "Class", "Session", "Component Name", "Type", "Value", "Action"],
            formModalId: "formModal",
            componentWaiver: [{
                class_id: '',
                session_year_id: '',
                student_id: '',
                component_id: '',
                type: '',
                value: '',
            }],
            details: {},
        };
    },
    methods: {

        editDataInformation(data, id) {
            const _this = this;
            _this.editData(data, id, "formModal", function (retData) {
                const editUrl = `${_this.urlGenerate()}/${id}/edit`;
                _this.getData(editUrl, "get", {}, {}, function (retData) {
                    const studentWaiver = retData;

                    if (!studentWaiver) {
                        _this.$toastr('error', 'Student waiver data is missing', "Error");
                        return;
                    }
                    const studentKey = `student_${studentWaiver.class_id}`;
                    if (!_this.requiredData[studentKey]) {
                        _this.$set(_this.requiredData, studentKey, []);
                    }

                    const existingStudent = _this.requiredData[studentKey].find(
                        (student) => student.id === studentWaiver.student_id
                    );
                    if (!existingStudent) {
                        _this.requiredData[studentKey].push({
                            id: studentWaiver.student_id,
                            student_name_en: studentWaiver.student_name,
                            student_roll: studentWaiver.student_roll
                        });
                    }

                    _this.$set(_this.formObject, "componentWaiver", [{
                        id: studentWaiver.id,
                        class_id: studentWaiver.class_id,
                        session_year_id: studentWaiver.session_year_id,
                        student_id: studentWaiver.student_id,
                        student_name: studentWaiver.student_name || '',
                        component_id: studentWaiver.component_id,
                        component_name: studentWaiver.component_name || '',
                        type: studentWaiver.type,
                        value: studentWaiver.value || 0,
                    }]);

                    _this.openModal('formModal', 'Edit Template');
                });
            });
        },

        submitData() {
            const isEditMode = this.formObject.id !== null && this.formObject.id !== undefined;

            const requestData = {
                componentWaiver: this.formObject.componentWaiver,
            };
            if (isEditMode) {
                requestData.id = this.formObject.id;
            }
            this.submitForm(requestData, 'formModal');
        },

        assign() {
            const _this = this;
            _this.$set(_this.formObject, 'class_id', '');
            _this.$set(_this.formFilter, 'class_id', '');
            _this.$set(_this.formFilter, 'section_id', '');
            _this.$set(_this.formObject, 'session_year_id', '');
            _this.$set(_this.formObject, 'student_id', '');
            _this.$set(_this.formObject, 'component_id', '');
            _this.$set(this.formObject, 'type', '');
        },
    },

    mounted() {
        const _this = this;
        _this.assign();
        _this.getDataList();
        _this.getGeneralData(['class','session', 'componentName'], function (retData) {
            _this.getGeneralData({
                session: { school_id: _this.Config.school_id },
                account_student_class: { school_id: _this.Config.school_id },
            });
        });

        _this.$set(_this.formObject, 'componentWaiver', [{ class_id: '', session_year_id: '', student_id: '', component_id: '', type: '', value: '' }]);
    },
};
</script>

<style scoped>
.table-center {
    text-align: center;
}

.table-center .action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.table thead th {
    background-color: #cccbcb;
    text-align: center;
}
</style>
