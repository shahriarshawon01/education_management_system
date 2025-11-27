<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
          <div class="col">
            <data-table :table-heading="tableHeading" table-title="Facultie List" >
              <template v-slot:page_top>
                <page-top :default-object="{school_id:'',teacher_id:'',department_id:''}" topPageTitle="Facultie List" :default-add-button="can('faculties_add')" ></page-top>
              </template>
              <template v-slot:data>
                <tr v-for="(data, index) in dataList.data">
                  <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                  <td>{{showData(data.departments,'department_name')}}</td>
                  <td class="center">{{showData(data.teachers,'name')}}</td>
                  <td class="center">
                    <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                  </td>
                  <td class="action-buttons">
                    <div class="hstack gap-3 fs-15">
                      <!-- <a class="link-primary" @click="globalDetails(data, data.name)"><i class="fa fa-eye"></i></a> -->
                      <a v-if="can('faculties_update')" class="link-primary" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                      <a v-if="can('faculties_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
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
                <select class="form-control" v-model="formObject.department_id" name="department_id">
                    <option value="">Select Department</option>
                    <template v-for="(data,index) in requiredData.department">
                        <option :value="data.id">{{data.department_name}}</option>
                    </template>
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label">Teacher :</label>
                <select class="form-control" v-model="formObject.teacher_id" name="teacher_id">
                    <option value="">Select Teacher</option>
                    <template v-for="(data,index) in requiredData.teachers">
                        <option :value="data.id">{{data.name}}</option>
                    </template>
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label">Message From Head :</label>
                <textarea type="text" v-model="formObject.msg_from_head" v-validate="'required'" name="msg_from_head" class="form-control" rows="3" placeholder="Message From Head"></textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">Lab Info :</label>
                <textarea type="text" v-model="formObject.lab_info" v-validate="'required'" name="lab_info" class="form-control" rows="3" placeholder="Lab Info"></textarea>
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
    name: "facilitieList",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
      return {
        tableHeading: ["Sl","Department","Teacher","Status", "Action"],
        formModalId: "formModal",
      };
    },
    mounted() {
      const _this = this;
        _this.getDataList();
        _this.getGeneralData(['school','department','teachers']);


    },
  };
  </script>

<style scoped>
.modal-title {
    margin-bottom: 0;
    line-height: 0.5;
    text-transform: uppercase;
}
.center {
        text-align: center;
    }

   .action-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
    }
</style>
