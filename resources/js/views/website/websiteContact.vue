<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
          <div class="col">
            <data-table :table-heading="tableHeading" table-title="Contact" >
              <template v-slot:page_top>
                <!-- <page-top :default-object="{school_id:'',designation_id:''}" topPageTitle="Contact" :default-add-button="can('contact_add')" ></page-top> -->
                 
              </template>
              <template v-slot:data>
                <tr v-for="(data, index) in dataList.data">
                  <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                  <td>{{ data.first_name }}</td>
                   <td>{{ data.last_name }}</td>
                  <td>{{ data.phone }}</td>
                  <td>{{ data.email }}</td>
                  <td>{{ data.subject }}</td>
                  <td>{{ data.message }}</td>
                  <td>
                    <div class="hstack gap-3 fs-15">
                    <a class="link-primary" @click="contactDetails(data)"><i class="fa fa-eye"></i></a>
                      <a v-if="can('contact_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                    </div>
                  </td>
                </tr>
              </template>
            </data-table>
          </div>
        </div>

          <general-modal modal-id="detailsModal" modalSize="modal-lg">
            <template v-slot:body>
                <div class="row border_bottom">
                    <label class="col-4">First Name </label>
                    <div class="col-md-8">
                        <strong>: </strong> <span>{{ details.first_name }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-4">Last Name </label>
                    <div class="col-md-8">
                        <strong>: </strong> <span>{{ details.last_name }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-4">Email </label>
                    <div class="col-md-8">
                        <strong>: </strong> <span>{{ details.email }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-4">Phone </label>
                    <div class="col-md-8">
                        <strong>: </strong> <span>{{ details.phone }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-4">Subject </label>
                    <div class="col-md-8">
                        <strong>: </strong> <span>{{ details.subject }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-4">Message</label>
                    <div class="col-md-8">
                        <strong>: </strong> <span>{{ details.message }}</span>
                    </div>
                </div>
            </template>
        </general-modal>
    </div>
  </template>
  <script>
  import DataTable from "../../components/dataTable";
  import Pagination from "../../plugins/pagination/pagination";
  import PageTop from "../../components/pageTop";
  import formModal from "../../components/formModal";
  import GeneralModal from "../../components/generalModal"

  export default {
    name: "websiteContact",
    components: { PageTop, Pagination, DataTable, formModal, GeneralModal},
    data() {
      return {
        tableHeading: ["Sl","First Name","Last Name","Phone","Email","Subject","Message", "Action"],
        formModalId: "formModal",
        details:{},
       
      };
    },
    methods: {
      contactDetails: function (data) {
            const _this = this;
            var URL = `${_this.urlGenerate()}/${data.id}`;
            var title = `${_this.showData(data, 'first_name')}`;
            _this.openModal('detailsModal', title, function () {
                _this.getData(URL, "get", {}, {}, function (retData) {
                    _this.details = retData;
                });
            });
        },
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
.border_bottom{
        border-bottom: 1px solid #ebebeb !important;
        line-height: 32px !important;
    }
    .border_bottom label{
        margin-bottom: 0 !important;
    }
    .border_bottom strong{
        margin-right: 5px !important;
    }
</style>
