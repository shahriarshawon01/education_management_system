<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
          <div class="col">
            <data-table :table-heading="tableHeading" table-title="Fee Structure" >
              <template v-slot:page_top>
                <page-top :default-object="{department_id:''}" topPageTitle="Fee Structure" :default-add-button="can('course_category_add')" ></page-top>
              </template>
              <template v-slot:data>
                <tr v-for="(data, index) in dataList.data">
                  <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                  <td>{{showData(data.departments,'department_name')}}</td>
                  <td class="table-center">
                    <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                  </td>
                  <td class="action-buttons">
                    <div class="hstack gap-3 fs-15 action-buttons">
                      <!-- <a class="link-primary" @click="globalDetails(data, data.name)"><i class="fa fa-eye"></i></a> -->
                      <a v-if="can('course_category_update')" class="link-primary" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                      <a v-if="can('course_category_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                    </div>
                  </td>
                </tr>
              </template>
            </data-table>
          </div>
        </div>
        <formModal modalSize="modal-md" @submit="submitForm(formObject, 'formModal')">
            <div class="mb-2">
                <label class="form-label">Department :</label>
                <select class="form-control" v-model="formObject.department_id" name="department_id"  @click="getGeneralData({students: { class_id: formObject.class_id, school_id: Config.school_id }})">
                    <option value="">Select Department</option>
                    <template v-for="(data,index) in requiredData.department">
                        <option :value="data.id">{{data.department_name}}</option>
                    </template>
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label">Description :</label>
                <editor api-key="no-api-key" :init="tinyMCESetup" v-model="formObject.fees_structure" v-validate="'required'" />
            </div>
        </formModal>
    </div>
  </template>
  <script>
  import DataTable from "../../components/dataTable";
  import Pagination from "../../plugins/pagination/pagination";
  import PageTop from "../../components/pageTop";
  import formModal from "../../components/formModal";
  import Editor from '@tinymce/tinymce-vue'

  export default {
    name: "feeStructure",
    components: { PageTop, Pagination, DataTable, formModal, Editor},
    data() {
      return {
        tableHeading: ["Sl","Title","Status", "Action"],
        formModalId: "formModal",
      };
    },
    mounted() {
      const _this = this;
        _this.getDataList();
        _this.getGeneralData(['school','department']);

    },
  };
  </script>

<style scoped>
.modal-title {
    margin-bottom: 0;
    line-height: 0.5;
    text-transform: uppercase;
}
 .table-center {
        text-align: center;
    }

   .action-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
    }
</style>
