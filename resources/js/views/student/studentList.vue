<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading"
                    :default-object="{ school: '', class_id: '', section_id: '', status: '' }"
                    table-title="Student List">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Student List" page-modal-title="Student Add/Edit"
                            :default-add-button="false">
                            <router-link v-if="can('student_add')" to="/admin/student/add" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add Student
                            </router-link>
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.class_id" name="class_id" class="form-control" @change="
                                getGeneralData({
                                    students: { class_id: formFilter.class_id },
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
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.status" name="status">
                                <option value="">Select Student Status</option>
                                <option value="">All</option>
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                                <option value="2">Dropout</option>
                            </select>
                        </div>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium sticky-sl">{{ parseInt(dataList.from) + index }}</td>
                            <td class="sticky-name">
                                <div class="student-info">
                                    <div class="student-avatar">
                                        <img class="mouse-action" :src="getImage(data.photo)"
                                            @click="openFile(getImage(data.photo))">
                                    </div>
                                    <span class="student-name-text">{{ data.student_name_en }}</span>
                                </div>
                            </td>
                            <td class="sticky-merit-roll">{{ data.merit_roll }}</td>

                            <td>{{ data.student_roll }}</td>
                            <td>{{ showData(data.classes, 'name') }}</td>
                            <td>{{ showData(data.sections, 'name') }}</td>
                            <td>{{ showData(data.departments, 'department_name') }}</td>
                            <td>{{ showData(data.sessions, 'title') }}</td>
                            <td>{{ data.father_phone }}</td>
                            <td>{{ data.father_name_en }}</td>
                            <td>{{ data.mother_name_en ? data.mother_name_en : 'N/A' }}</td>

                            <!-- <td>{{ data.optional_subject?.name || 'N/A' }}</td> -->
                            <!-- Optional Subject Column - Now Clickable -->
                            <td>
                                <template v-if="can('student_add')">
                                    <a @click="openOptionalSubjectModal(data)" class="point-cursor">
                                        <span class="btn badge"
                                            :class="data.optional_subject?.name ? 'badge-soft-info' : 'badge-soft-secondary'"
                                            style="padding: 4px 7px; border-radius: 40%; font-size: 12px;">
                                            {{ data.optional_subject?.name || 'N/A' }}
                                        </span>
                                    </a>
                                </template>
                                <!-- <template v-else>
                                    <span class="btn badge"
                                        :class="data.optional_subject?.name ? 'badge-soft-info' : 'badge-soft-secondary'"
                                        style="padding: 4px 7px; border-radius: 40%; font-size: 12px; opacity: 0.6; cursor: not-allowed;">
                                        {{ data.optional_subject?.name || 'N/A' }}
                                    </span>
                                </template> -->
                            </td>


                            <td>
                                <template v-if="can('student_add')">
                                    <a v-if="parseInt(data.status) === 1"
                                        @click="statusModal(data, data.id, 'createModal')">
                                        <span class="btn btn-outline-success"
                                            style="padding: 4px 7px; border-radius: 40%; font-size: 12px;">Active</span>
                                    </a>
                                    <a v-if="parseInt(data.status) === 0"
                                        @click="statusModal(data, data.id, 'createModal')">
                                        <span class="badge badge-soft-danger"
                                            style="padding: 4px 7px; border-radius: 40%; font-size: 12px;">InActive</span>
                                    </a>
                                    <a v-if="parseInt(data.status) === 2"
                                        @click="statusModal(data, data.id, 'createModal')">
                                        <span class="badge badge-soft-danger"
                                            style="padding: 4px 7px; border-radius: 40%; font-size: 12px;">Dropout</span>
                                    </a>
                                </template>

                                <template v-else>
                                    <span class="btn btn-outline-success" v-if="parseInt(data.status) === 1"
                                        style="padding: 4px 7px; border-radius: 40%; font-size: 12px; opacity: 0.6; cursor: not-allowed;">
                                        Active
                                    </span>
                                    <span class="badge badge-soft-danger" v-if="parseInt(data.status) === 0"
                                        style="padding: 4px 7px; border-radius: 40%; font-size: 12px; opacity: 0.6; cursor: not-allowed;">
                                        InActive
                                    </span>
                                    <span class="badge badge-soft-danger" v-if="parseInt(data.status) === 2"
                                        style="padding: 4px 7px; border-radius: 40%; font-size: 12px; opacity: 0.6; cursor: not-allowed;">
                                        Dropout
                                    </span>
                                </template>
                            </td>

                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons"
                                    :class="{ 'temp-active': activeButton === data.id }">

                                    <router-link v-if="can('student_id_print') && data.status == 1" class="link-primary"
                                        :to="`/admin/student_id_card/${data.id}`" target="_blank"
                                        @click.native="handleButtonClick(data.id)">
                                        <i :class="{
                                            'fa-solid fa-id-card': true,
                                            'text-font': data.print_status == 1 && activeButton !== data.id,
                                            'text-back': data.print_status == 2 && activeButton !== data.id,
                                        }">
                                        </i>
                                    </router-link>

                                    <router-link v-if="can('student_view')" class="link-primary"
                                        :to="`/admin/student_view/${data.id}`"><i class="fa fa-eye"></i></router-link>

                                    <router-link v-if="can('student_update')" class="link-primary"
                                        :to="`/admin/student/add/${data.id}`"><i class="fa fa-edit"></i></router-link>

                                    <a v-if="can('student_delete')" class="link-danger"
                                        @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>

        <formModal modalSize="modal-xs" modal-id="createModal" @submit="submitStatus(formObject)">
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label"><strong>Student Status</strong></label>
                        <div class="d-flex flex-column">
                            <label class="form-check-label point-cursor mb-2">
                                <input type="radio" value="1" v-model="formObject.status" name="status"
                                    class="form-check-input"> Active
                            </label>
                            <label class="form-check-label point-cursor mb-2">
                                <input type="radio" value="0" v-model="formObject.status" name="status"
                                    class="form-check-input"> InActive
                            </label>
                            <label class="form-check-label point-cursor mb-2">
                                <input type="radio" value="2" v-model="formObject.status" name="status"
                                    class="form-check-input"> Dropout
                            </label>
                        </div>
                    </div>

                    <div class="col-md-8" v-if="formObject.status == 0">
                        <label class="col-form-label"><strong>Description</strong></label>
                        <textarea name="comments" class="form-control" v-model="formObject.comments"
                            rows="3"></textarea>
                    </div>
                    <div class="col-md-8" v-if="formObject.status == 2">
                        <label class="col-form-label"><strong>Dropout Date</strong></label>
                        <datepicker v-model="formObject.dropout_date" name="dropout_date" input_class="form-control"
                            v-validate="'required'" placeholder="Enter Dropout Date"></datepicker>
                        <div class="mt-3">
                            <label class="col-form-label"><strong>Dropout Description</strong></label>
                            <textarea name="comments" class="form-control" v-model="formObject.comments"
                                rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </formModal>

        <optional-subject-modal ref="optionalSubjectModal" :subjects="optionalSubjectsList"
            :student-id="optionalSubjectForm.student_id" v-model="optionalSubjectForm.optional_subject_id"
            @submit="submitOptionalSubject" />



    </div>
</template>

<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";
import optionalSubjectModal from "../../components/optionalSubjectModal";
export default {
    name: "studentList",
    components: { PageTop, Pagination, DataTable, formModal, optionalSubjectModal },
    data() {
        return {
            tableHeading:
                [
                    'Sl',
                    'Name',
                    'Roll',
                    'Student ID',
                    'Class',
                    'Section',
                    'Department',
                    'Session',
                    'Phone',
                    'Father Name',
                    'Mother Name',
                    'Optional Subject',
                    'Status',
                    'Action'
                ],
            activeButton: null,
            optionalSubjectForm: {
                student_id: null,
                optional_subject_id: null,
                class_id: null
            },
            optionalSubjectsList: []
        }
    },

    watch: {
        'formFilter.class_id'(newClassId) {
            if (newClassId) this.getStudentOptionalSubjects(newClassId);
        }
    },

    methods: {
        statusModal: function (data) {
            const _this = this;
            _this.$set(_this.formObject, 'id', data.id);
            _this.$set(_this.formObject, 'status', data.status);
            _this.$set(_this.formObject, 'dropout_date', data.dropout_date);
            _this.$set(_this.formObject, 'comments', data.comments);
            _this.$store.state.activeFormType = 2;
            _this.edit = false;
            _this.add = true;
            _this.openModal('createModal', 'Student status');

        },

        // openOptionalSubjectModal(data) {
        //     this.optionalSubjectForm.student_id = data.id;
        //     this.optionalSubjectForm.optional_subject_id = data.optional_subject_id || null;
        //     console.log("optional sub id : ", this.optionalSubjectForm.optional_subject_id);

        //     this.optionalSubjectForm.class_id = data.class_id;

        //     // Load optional subjects for this student's class
        //     this.getStudentOptionalSubjects(data.class_id, () => {
        //         // Open dedicated modal after data is loaded
        //         this.$refs.optionalSubjectModal.open();
        //     });
        // },

        openOptionalSubjectModal(data) {
            this.optionalSubjectForm.student_id = data.id;
            this.optionalSubjectForm.optional_subject_id = data.optional_subject_id ?? null;
            this.optionalSubjectForm.class_id = data.class_id;

            this.getStudentOptionalSubjects(data.class_id, () => {
                this.$refs.optionalSubjectModal.open();
            });
        },




        getStudentOptionalSubjects(classId, callback) {
            const _this = this;
            console.log("classId : ", classId);

            const URL = _this.urlGenerate("api/get_student_optional_subjects");
            _this.getData(URL, "get", { class_id: classId }, {}, (retData) => {
                console.log("get opt sub response: ", retData);

                // Direct access to optional_subjects (not nested under result)
                const subjects = retData?.optional_subjects || [];
                console.log("subjects array: ", subjects);

                // Use Vue.set to ensure reactivity
                _this.$set(_this, 'optionalSubjectsList', subjects);

                console.log("optionalSubjectsList after set: ", _this.optionalSubjectsList);
                console.log("optionalSubjectsList length: ", _this.optionalSubjectsList.length);

                // Execute callback after Vue updates the DOM
                if (callback) {
                    _this.$nextTick(() => {
                        console.log("Opening modal with subjects: ", _this.optionalSubjectsList);
                        callback();
                    });
                }
            });
        },

        submitOptionalSubject() {
            const _this = this;
            const formData = {
                optional_subject_id: _this.optionalSubjectForm.optional_subject_id
            };

            _this.submitForm(formData, false, function () {
                _this.$toastr('success', 'Optional subject updated successfully');
                _this.getDataList(); // Refresh the list
                _this.closeModal('optionalSubjectModal');
            }, true, `api/update_student_optional_subject/${_this.optionalSubjectForm.student_id}`);
        },

        submitStatus() {
            const _this = this;
            if (this.formObject.status === 1 || this.formObject.status === 0) {
                this.formObject.dropout_date = '';
            }
            _this.submitForm(_this.formObject, false, function (retData) {
                _this.getDataList();
                _this.closeModal('createModal');
            }, true, `api/student_status/${_this.formObject.id}`);
        },

        handleButtonClick(id) {
            this.activeButton = id;
        },
    },

    mounted() {
        const _this = this;
        _this.$set(_this.formObject, 'dropout_date', '');
        _this.getGeneralData(['class', 'departments', 'sections']);
        _this.getDataList();
    }
}
</script>

<style scoped>
.text-font {
    color: #f29f9f !important;
}

.text-back {
    color: #FF0000 !important;
}

.temp-active {
    background-color: #f50691 !important;
}

.table-center {
    text-align: center;

}

.table-center .action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.mouse-action {
    height: 40px;
    cursor: pointer;
}

.sticky-sl {
    position: sticky;
    left: 0;
    background-color: white;
    z-index: 1;
}

.sticky-name {
    position: sticky;
    left: 20px;
    background-color: white;
    z-index: 1;
}

.sticky-merit-roll {
    position: sticky;
    left: 200px;
    background-color: white;
    z-index: 1;
}

.student-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.student-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: linear-gradient(135deg, #e0e7ff 0%, #f5f3ff 100%);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    border: 2px solid #fff;
}

.student-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.student-name-text {
    font-weight: 600;
    color: #2c3e50;
}

/* optional subject & status */
.point-cursor {
    cursor: pointer;
}

.badge-soft-info {
    background-color: #d1ecf1;
    color: #0c5460;
}

.badge-soft-secondary {
    background-color: #e2e3e5;
    color: #6c757d;
}
</style>
