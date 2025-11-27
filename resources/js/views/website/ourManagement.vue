<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
          <div class="col">
            <data-table :table-heading="tableHeading" table-title="Our Management" >
              <template v-slot:page_top>
                <page-top :default-object="{school_id:'',designation_id:''}" topPageTitle="Our Management" :default-add-button="can('our_management_add')" ></page-top>
              </template>
              <template v-slot:data>
                <tr v-for="(data, index) in dataList.data">
                  <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                  <td>{{ data.name }}</td>
                  <td>{{ showData(data.designation,'name') }}</td>
                  <!-- <td>
                    <span>
                        <img style="height: 40px" :src="getImage(data.image)" @click="openFile(getImage(data.image))">
                    </span>
                </td> -->
                  <td>
                      <a v-if="data.image" @click="openFile(getImage(data.image))">
                          <i class="fa fa-download"> File</i>
                      </a>
                      <p v-else>-</p>
                  </td>
                  <td>
                    <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                  </td>
                  <td>
                    <div class="hstack gap-3 fs-15">
                      <!-- <a class="link-primary" @click="globalDetails(data, data.name)"><i class="fa fa-eye"></i></a> -->
                      <a v-if="can('our_management_update')" class="link-primary" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                      <a v-if="can('our_management_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                    </div>
                  </td>
                </tr>
              </template>
            </data-table>
          </div>
        </div>
        <formModal modalSize="modal-md" @submit="submitForm(formObject, 'formModal')">
            <div class="mb-2">
                <label class="form-label">Name :</label>
                <input type="text" v-model="formObject.name" v-validate="'required'" name="name" class="form-control" placeholder="Name">
            </div>
            <div class="mb-2">
                <label class="form-label">Designation :</label>
                <select v-model="formObject.designation_id" v-validate="'required'" name="designation_id" class="form-control">
                    <option value="">Select Designation</option>
                    <template v-for="(eachData, index) in requiredData.designation">
                        <option :value="eachData.id">{{eachData.name}}</option>
                    </template>
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label">Image :</label>
                <div @click="clickImageInput('image')" class="mb-2 mt-3">
                    <div class="form-group image_upload" :style="{ backgroundImage: 'url(' + getImage(null, 'images/upload.png') + ')' }" style="background-size:260px !important">
                        <img v-if="formObject.image" :src="getImage(formObject.image)">
                        <input name="thumbnail" :v-validate="formType === 1 ? 'required' : 'sometimes'" style="display: none;" id="image" type="file" @change="uploadFile($event, formObject, 'image')">
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
    name: "ourManagement",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
      return {
        tableHeading: ["Sl","Name","Designation","Image","Status", "Action"],
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
</style>
