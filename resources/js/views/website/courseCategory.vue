<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
          <div class="col">
            <data-table :table-heading="tableHeading" table-title="Course Category" >
              <template v-slot:page_top>
                <page-top :default-object="{school_id:''}" topPageTitle="Course Category" :default-add-button="can('course_category_add')" ></page-top>
              </template>
              <template v-slot:data>
                <tr v-for="(data, index) in dataList.data">
                  <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                  <td>{{ data.name }}</td>
                  <td>{{ data.description }}</td>
                  <td class="table-center">
                    <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                  </td>
                  <td class="action-buttons">
                    <div class="hstack gap-3 fs-15">
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
                <label class="form-label">Title :</label>
                <input type="text" v-model="formObject.name" v-validate="'required'" name="name" class="form-control" placeholder="Title">
            </div>
            <div class="mb-2">
                <label class="form-label">Description :</label>
                <textarea type="text" v-model="formObject.description" v-validate="'required'" name="description" class="form-control" rows="3" placeholder="Description"></textarea>
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
    name: "courseCategory",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
      return {
        tableHeading: ["Sl","Title","Description","Status", "Action"],
        formModalId: "formModal",
      };
    },
    mounted() {
      const _this = this;
        _this.getDataList();
        _this.getGeneralData(['school']);


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
