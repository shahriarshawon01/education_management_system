<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Examination">
                    <template v-slot:page_top>
                        <page-top :default-object="{ exam_id: '', class_id: '', session_year_id: '' }"
                            topPageTitle="Examination" :default-add-button="can('examination_add')"></page-top>
                    </template>
                    <template v-slot:filter>

                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.type_name }}</td>
                            <td>{{ showData(data.exam_name, 'name') }}</td>
                            <td style="text-align:center">{{ showData(data.class, 'name') }}</td>
                            <td style="text-align:center">{{ showData(data.session, 'title') }}</td>
                            <td style="text-align:center">{{ data.start_date }}</td>
                            <td style="text-align:center">{{ data.end_date }}</td>
                            <td style="text-align:center">
                                <a v-if="data.file" @click="openFile(getImage(data.file))">
                                    <i class="fa fa-download"> File</i>
                                </a>
                                <p v-else>-</p>
                            </td>
                            <td style="text-align:center">
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <a v-if="can('examination_update')" class="link-primary"
                                        @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('examination_delete')" class="link-danger"
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
                <div class="col-md-6">
                    <div class="col-md-12 mb-2">
                        <label class="form-label">Exam Name</label>
                        <select class="form-control" v-model="formObject.exam_id" name="exam_id"
                            v-validate="'required'">
                            <option value="">Select exam name</option>
                            <template v-for="(data, index) in requiredData.examName">
                                <option :value="data.id">{{ data.name }}</option>
                            </template>
                        </select>
                    </div>

                    <div class="col-md-12 mb-2">
                        <label class="form-label">Exam Type</label>
                        <input type="text" v-model="formObject.type_name" v-validate="'required'" name="type_name"
                            class="form-control" placeholder="Enter exam type">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="form-label">Start Date</label>
                        <datepicker v-model="formObject.start_date" name="start_date" input_class="form-control"
                            v-validate="'required'">
                            <input slot="input" class="form-control" placeholder="Select a date">
                        </datepicker>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="form-label">End Date</label>
                        <datepicker v-model="formObject.end_date" name="end_date" input_class="form-control"
                            v-validate="'required'">
                            <input slot="input" class="form-control" placeholder="Select a date">
                        </datepicker>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="col-md-12 mb-2">
                        <label class="form-label">Class</label>
                        <select class="form-control" v-model="formObject.class_id" name="class_id"
                            v-validate="'required'">
                            <option value="">Select class</option>
                            <template v-for="(data, index) in requiredData.class">
                                <option :value="data.id">{{ data.name }}</option>
                            </template>
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="form-label">Session</label>
                        <select class="form-control" v-model="formObject.session_year_id" name="session_year_id"
                            v-validate="'required'">
                            <option value="">Select session</option>
                            <template v-for="(data, index) in requiredData.session">
                                <option :value="data.id">{{ data.title }}</option>
                            </template>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Upload Exam Routine</label>
                        <div @click="clickImageInput('image')">
                            <div class="form-group image_upload"
                                :style="{ backgroundImage: 'url(' + getImage(null, 'images/upload.png') + ')' }"
                                style="background-size:360px !important">
                                <img v-if="formObject.file" :src="getImage(formObject.file)">
                                <input name="thumbnail" :v-validate="formType === 1 ? 'required' : 'sometimes'"
                                    style="display: none;" id="image" type="file"
                                    @change="uploadFile($event, formObject, 'file')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </formModal>
    </div>
</template>
<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";
export default {
    name: "ExaminationList",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ["Sl", "Exam Type", "Exam Name", "Class", "Session", "Start Date", "End Date", "File", "Status", "Action"],
            formModalId: "formModal",
        };
    },
    mounted() {
        const _this = this;
        _this.getDataList();
        _this.getGeneralData(['examName', 'class', 'session']);
        this.$set(this.formObject, 'start_date', '')
        this.$set(this.formObject, 'end_date', '')
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
</style>
