<template :key="$route.meta.name">
  <div class="main_component">
    <div class="row">
      <div class="col">
        <data-table :table-heading="tableHeading" table-title="Class List">
          <template v-slot:page_top>
            <page-top :default-object="{ teacher_id: '' }" topPageTitle="Class List" :default-add-button="can('class_add')"></page-top>
          </template>
          <template v-slot:data>
            <tr v-for="(data, index) in dataList.data">
              <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
              <!-- <td>{{ showData(data.teacher, 'name') }}</td> -->
              <td>{{ data.name }}</td>
              <td>{{ data.numeric_name }}</td>
             
              <td style="text-align: center !important;">
                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
              </td>
              <td class="table-center">
                <div class="hstack gap-3 fs-15 action-buttons">
                  <!-- <a class="link-primary" @click="globalDetails(data, data.name)"><i class="fa fa-eye"></i></a> -->
                  <a v-if="can('class_update')" class="link-primary" @click="editData(data, data.id)"><i
                      class="fa fa-edit"></i></a>
                  <a v-if="can('class_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i
                      class="fa fa-trash"></i></a>
                </div>
              </td>
            </tr>
          </template>
        </data-table>
      </div>
    </div>
    <formModal modalSize="modal-md" @submit="submitForm(formObject, 'formModal')">
      <div class="mb-2">
        <label class="form-label">Teacher Name</label>
        <select v-select2 type="text" class="form-control" v-model="formObject.teacher_id" v-validate="'required'" name="teacher_id">
          <option value="">Select Teacher</option>
          <template v-for="(eachData, index) in requiredData.teachers">
            <option :value="eachData.id">{{ eachData.name }} - {{ eachData.employee_id }}</option>
          </template>
        </select>
      </div>
      <div class="mb-2">
        <label class="form-label">Class Name</label>
        <input type="text" v-model="formObject.name" v-validate="'required'" name="name" class="form-control" placeholder="Enter Class Name">
      </div>
      <div class="mb-2">
        <label class="form-label">Numeric Name</label>
        <input type="text" v-model="formObject.numeric_name" v-validate="'required'" name="numeric_name"
          class="form-control" placeholder="Enter Numeric Name">
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
  name: "studentClassList",
  components: { PageTop, Pagination, DataTable, formModal },
  data() {
    return {
      tableHeading: ["Sl","Class Name","Numeric Name", "Status", "Action"],
      formModalId: "formModal",
    };
  },
  mounted() {
    const _this = this;
    _this.getDataList();
    _this.getGeneralData(['teachers']);
    _this.$set(_this.formObject, 'teacher_id', '');


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

.table-center .action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}
</style>
