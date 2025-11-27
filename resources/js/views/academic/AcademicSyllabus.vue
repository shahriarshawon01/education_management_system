<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Academic Syllabus" :default-object="{}">
                    <template v-slot:page_top>
                        <page-top :default-object="{class_id:'',subject_id:'',department_id:''}" topPageTitle="Academic Syllabus" :default-add-button="can('academic_syllabus_add')" ></page-top>
                    </template>
                    <template v-slot:filter>

                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.title }}</td>
                            <td>{{ data.description }}</td>
                            <td class="center">{{ showData(data.class,'name') }}</td>
                            <td class="center">{{ showData(data.subject,'name') }}</td>
                            <td class="center">{{ showData(data.department,'department_name') }}</td>
                            <td class="center">
                                <a v-if="data.file" @click="openFile(getImage(data.file))">
                                    <i class="fa fa-download"> File</i>
                                </a>
                                <p v-else>-</p>
                            </td>
                            <td class="center">
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="action-buttons">
                                <div class="hstack gap-3 fs-15">
                                    <a v-if="can('academic_syllabus_update')" class="link-primary" @click="editDataInformation(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('academic_syllabus_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-lg" @submit="submitForm(formObject, 'formModal')">

            <div class="mb-2">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Title :</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" v-model="formObject.title" v-validate="'required'" name="title" class="form-control" placeholder="Title">
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Class Name :</label>
                    </div>
                    <div class="col-md-8">
                        <select v-model="formObject.class_id" name="class_id" class="form-control"
                            @change="getGeneralData({
                                subjects: { class_id: formObject.class_id },
                                departments: { class_id: formObject.class_id },
                                    })">
                            <option value="">Select Class</option>
                            <template v-for="(eachData, index) in requiredData.class">
                                <option :value="eachData.id">{{ eachData.name }}</option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Department :</label>
                    </div>
                    <div class="col-md-8">
                        <select v-model="formObject.department_id" name="department_id" class="form-control">
                            <option value="">Select Department</option>
                            <template v-for="(eachData, index) in requiredData.departments">
                                <option :value="eachData.id">{{ eachData.department_name }}</option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Subject :</label>
                    </div>
                    <div class="col-md-8">
                        <select v-model="formObject.subject_id" name="subject_id" class="form-control">
                            <option value="">Select Subject</option>
                            <template v-for="(eachData, index) in requiredData.subjects">
                                <option :value="eachData.id">{{ eachData.name }}</option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Description:</label>
                    </div>
                    <div class="col-md-8">
                        <textarea v-model="formObject.description" v-validate="'required'" name="description" class="form-control" rows="2" placeholder="Description"></textarea>
                    </div>
                </div>
                <div class="row mb-2">
                    <label class="form-label">Upload File :</label>
                    <div class="col-md-12">
                        <div @click="clickImageInput('image')" class="mb-2 mt-3">
                            <div class="form-group image_upload" :style="{ backgroundImage: 'url(' + getImage(null, 'images/upload.png') + ')' }" style="background-size:360px !important">
                                <img v-if="formObject.file" :src="getImage(formObject.file)">
                                <input name="thumbnail" :v-validate="formType === 1 ? 'required' : 'sometimes'" style="display: none;" id="image" type="file" @change="uploadFile($event, formObject, 'file')">
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
        name: "AcademicSyllabus",
        components: { PageTop, Pagination, DataTable, formModal },
        data() {
            return {
                tableHeading: ["Sl","Title","Description","Class","Subject","Department","File", "Status","Action"],
                formModalId: "formModal",
            };
        },
        methods:{
            editDataInformation: function (data, id) {
                const _this = this;
                _this.editData(data, id, 'formModal', function (retData) {
                    var editUrl = `${_this.urlGenerate()}/${id}/edit`;
                    _this.getData(editUrl, 'get', {}, {}, function (retData) {
                        if (retData) {
                            const syllabus = retData;
                             _this.getGeneralData({ 
                                  departments: { class_id: syllabus.class_id },
                                  subjects: { class_id: syllabus.class_id } 
                                });
                                // Set department_id when fetched
                                _this.formObject.department_id = syllabus.department_id;
                                _this.formObject.subject_id = syllabus.subject_id;
                            
                        } else {
                            _this.$toastr('error', response.message, "Error");
                        }
                    });
                });
            },
        },
        mounted() {
            const _this = this;
            _this.getDataList();
            _this.getGeneralData(['class']);
            
        },
    };
</script>

<style scoped>
.center {
      text-align: center;
  }

.action-buttons {
      display: flex;
      justify-content: center;
      gap: 10px;
  }
</style>

