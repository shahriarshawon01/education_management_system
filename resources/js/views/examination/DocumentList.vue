<template :key="$route.meta.name">
  <div class="main_component">
    <div class="row">
      <div class="col">
        <data-table :table-heading="tableHeading" table-title="Question Paper">
          <template v-slot:page_top>
            <page-top :default-object="{ exam_id: '',class_id: '', session_year_id: '', }" topPageTitle="Question Paper" :default-add-button="can('documents_add')"></page-top>
          </template>
          <template v-slot:filter> </template>
          <template v-slot:data>
            <tr v-for="(data, index) in dataList.data">
              <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
              <td>{{ showData(data.class, "name") }}</td>
              <td>{{ showData(data.session, "title") }}</td>
              <td>{{ showData(data.exam_name, "name") }}</td>
              <td>
                <a v-if="data.file" @click="openFile(getImage(data.file))">
                  <i class="fa fa-download"> File</i>
                </a>
                <p v-else>-</p>
              </td>
              <td>
                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
              </td>
              <td>
                <div class="hstack gap-3 fs-15">
                  <a v-if="can('documents_update')" class="link-primary" @click="editDataInformation(data, data.id)"><i
                      class="fa fa-edit"></i></a>
                  <a v-if="can('documents_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i
                      class="fa fa-trash"></i></a>
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
            <label class="form-label">Class Name:</label>
          </div>
          <div class="col-md-8">
            <select v-model="formObject.class_id" v-validate="'required'" name="class_id" class="form-control"
             @change="getGeneralData({ examination: { class_id: formObject.class_id, },})">
              <option value="">Select Class</option>
              <template v-for="(eachData, index) in requiredData.class">
                <option :value="eachData.id">
                  {{ eachData.name }}
                </option>
              </template>
            </select>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-md-4">
            <label class="form-label">Session :</label>
          </div>
          <div class="col-md-8">
            <select class="form-control" v-validate="'required'" v-model="formObject.session_year_id" name="session_year_id">
              <option value="">Select Session</option>
              <template v-for="(data, index) in requiredData.session">
                <option :value="data.id">{{ data.title }}</option>
              </template>
            </select>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-md-4">
            <label class="form-label">Exam Name :</label>
          </div>
          <div class="col-md-8">
            <select class="form-control" v-validate="'required'" v-model="formObject.exam_id" name="exam_id">
              <option value="">Select Exam</option>
              <template v-for="(eachData, index) in requiredData.examination">
                <option :value="eachData.id"> {{ eachData.name }}
                </option>
              </template>
            </select>
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
  name: "DocumentList",
  components: { PageTop, Pagination, DataTable, formModal },
  data() {
    return {
      tableHeading: ["Sl", "Class Name","Session","Exam Name", "Entry By", "File","Status","Action",],
      formModalId: "formModal",
    };
  },
  methods: {
    editDataInformation: function (data, id) {
      const _this = this;

      _this.editData(data, id, "formModal", function () {
        var editUrl = `${_this.urlGenerate()}/${id}/edit`;

        _this.getData(editUrl, "get", {}, {}, function (retData) {
          if (retData) {
            _this.$set(_this.formObject, "session_year_id", retData.session_id);
            _this.$set(_this.formObject, "class_id", retData.class_id);

            _this.getGeneralData(
              { examination: { class_id: retData.class_id } },
              () => {
                _this.$set(_this.formObject, "exam_id", retData.exam_id);
              }
            );
          } else {
            console.error("Invalid response or data is missing:", retData);
          }
        });
      });
    },
  },
  mounted() {
    const _this = this;
    _this.getDataList();
    _this.getGeneralData(["class", "session"]);
  },
};
</script>

<style scoped></style>
