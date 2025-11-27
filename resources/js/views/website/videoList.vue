<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
          <div class="col">
            <data-table :table-heading="tableHeading" table-title="Video List" >
              <template v-slot:page_top>
                <page-top :default-object="{school_id:'',designation:'',show_on: ''}" topPageTitle="Video List" :default-add-button="can('video_add')" ></page-top>
              </template>
              <template v-slot:data>
                <tr v-for="(data, index) in dataList.data">
                  <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                  <td>{{ data.title }}</td>
                  <td>{{data.video_link}}</td>
                  <td class="center">
                    <span v-if="parseInt(data.show_on) == 0">All</span>
                    <span v-if="parseInt(data.show_on) == 1">Home Page</span>
                    <span v-if="parseInt(data.show_on) == 2">Gallery</span>
                    
                </td>
                  <td class="center">
                    <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                  </td>
                  <td class="action-buttons">
                    <div class="hstack gap-3 fs-15">
                      <!-- <a class="link-primary" @click="globalDetails(data, data.name)"><i class="fa fa-eye"></i></a> -->
                      <a v-if="can('video_update')" class="link-primary" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                      <a v-if="can('video_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
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
                <input type="text" v-model="formObject.title" v-validate="'required'" name="title" class="form-control" placeholder="Title">
            </div>
            <div class="mb-2">
                <label class="col-form-label">Link :</label>
                <input type="text" v-model="formObject.video_link" v-validate="'required'" name="link" class="form-control" placeholder="Link">
            </div>
            <div class="mb-2">
                <label class="form-label">Show On :</label>
                <select name="show_on" class="form-control" v-validate="'required'" v-model="formObject.show_on">
                    <option value="" selected disabled >Select</option>
                    <option value="0">All</option>
                    <option value="1">Home Page</option>
                    <option value="2">Gallery</option>
                </select>
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
    name: "videoList",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
      return {
        tableHeading: ["Sl","Title","Video Link","Show On","Status", "Action"],
        formModalId: "formModal",
      };
    },
    mounted() {
      const _this = this;
        _this.getDataList();
        _this.getGeneralData(['school','designation']);


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
