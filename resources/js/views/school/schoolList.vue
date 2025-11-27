<template :key="$route.meta.name">
  <div class="main_component">
    <div class="row">
      <div class="col">
        <data-table :table-heading="tableHeading" table-title="Institute List">
          <template v-slot:page_top>
            <page-top :default-object="{ name: '' }" topPageTitle="Institute List"
              :default-add-button="can('school_add')"></page-top>
          </template>
          <template v-slot:data>
            <tr v-for="(data, index) in dataList.data">
              <td class="fw-medium">
                {{ parseInt(dataList.from) + index }}
              </td>
              <td class="table-center">{{ data.title }}</td>
              <td class="table-center">{{ data.address }}</td>
              <td class="table-center">
                {{ data.institute_code }}
              </td>
              <td class="table-center">{{ data.phone }}</td>
              <td class="table-center">{{ data.email }}</td>
              <td class="table-center">
                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
              </td>
              <td class="action-buttons">
                <div class="hstack gap-3 fs-15 action-buttons">
                  <a class="link-primary" @click="schoolDetails(data)"><i class="fa fa-eye"></i></a>
                  <a v-if="can('school_update')" class="link-primary" @click="editData(data, data.id)"><i
                      class="fa fa-edit"></i></a>
                  <a v-if="can('school_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i
                      class="fa fa-trash"></i></a>
                </div>
              </td>
            </tr>
          </template>
        </data-table>
      </div>
    </div>
    <formModal modalSize="modal-lg" @submit="submitForm(formObject, 'formModal')">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div @click="clickImageInput('image')" class="mb-2 mt-3">
            <div class="form-group image_upload" :style="{
              backgroundImage:
                'url(' + getImage(null, 'images/upload.png') + ')',
            }" style="background-size: 360px !important">
              <img v-if="formObject.logo" :src="getImage(formObject.logo.path)" />
              <input name="thumbnail" :v-validate="formType === 1 ? 'required' : 'sometimes'" style="display: none"
                id="image" type="file" @change="uploadFile($event, formObject, 'logo')" />
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <label class="col-form-label">Institute Name:</label>
          <input type="text" v-model="formObject.title" v-validate="'required'" name="title" class="form-control"
            placeholder="Institute Name" />
        </div>
        <div class="col-md-6">
          <label class="col-form-label">STEB Year:</label>
          <input type="text" v-model="formObject.steb_number" name="steb_number" class="form-control"
            placeholder="STEB Year" />
        </div>
        <div class="col-md-6">
          <label class="col-form-label">Registration:</label>
          <input type="text" v-model="formObject.reg_number" name="reg_number" class="form-control"
            placeholder="Registration" />
        </div>
        <div class="col-md-6">
          <label class="col-form-label">EMIS Code:</label>
          <input type="text" v-model="formObject.emis_code" name="emis_code" class="form-control"
            placeholder="EMIS Code" />
        </div>
        <div class="col-md-6">
          <label class="col-form-label">Institute Code:</label>
          <input type="text" v-model="formObject.institute_code" v-validate="'required'" name="institute_code"
            class="form-control" placeholder="Institute Code" />
        </div>
        <div class="col-md-6">
          <label class="col-form-label">Phone:</label>
          <input type="text" v-model="formObject.phone" v-validate="'required|digits:11'" name="phone"
            class="form-control" placeholder="Phone" />
        </div>
        <div class="col-md-6">
          <label class="col-form-label">Email:</label>
          <input type="email" v-model="formObject.email" v-validate="'required'" name="email" class="form-control"
            placeholder="Email" />
        </div>
        <div class="col-md-6">
          <label class="col-form-label">Address:</label>
          <textarea name="address" class="form-control" v-model="formObject.address" v-validate="'required'"
            placeholder="Address" rows="3"></textarea>
        </div>
        <div class="col-md-6">
          <label class="col-form-label">Founder Info:</label>
          <textarea name="founder_info" class="form-control" v-model="formObject.founder_info" rows="3"
            placeholder="Founder Info"></textarea>
        </div>
        <div class="col-md-6">
          <label class="col-form-label">Map Link:</label>
          <textarea name="text" class="form-control" v-model="formObject.map_link" placeholder="Map Link"
            rows="3"></textarea>
        </div>
      </div>
    </formModal>
    <general-modal modal-id="detailsModal" modalSize="modal-l">
      <template v-slot:title>
        <span>Institute details</span>
      </template>
      <template v-slot:body>
        <table class="table table-striped institute_details">
          <tr class="pb-2">
            <th style="width: 25%">Institute Name</th>
            <th style="width: 5%">:</th>
            <td>{{ details.title }}</td>
          </tr>
          <tr>
            <th style="width: 25%">Institute Code</th>
            <th style="width: 5%">:</th>
            <td>{{ details.institute_code }}</td>
          </tr>
          <tr>
            <th style="width: 25%">STEB Number</th>
            <th style="width: 5%">:</th>
            <td>{{ details.steb_number }}</td>
          </tr>
          <tr>
            <th style="width: 25%">Registration</th>
            <th style="width: 5%">:</th>
            <td>{{ details.reg_number }}</td>
          </tr>
          <tr>
            <th style="width: 25%">EMIS Code</th>
            <th style="width: 5%">:</th>
            <td>{{ details.emis_code }}</td>
          </tr>
          <tr>
            <th style="width: 25%">Phone</th>
            <th style="width: 5%">:</th>
            <td>{{ details.phone }}</td>
          </tr>
          <tr>
            <th style="width: 25%">Email</th>
            <th style="width: 5%">:</th>
            <td>{{ details.email }}</td>
          </tr>
          <tr>
            <th style="width: 25%">Address</th>
            <th style="width: 5%">:</th>
            <td>{{ details.address }}</td>
          </tr>
        </table>
      </template>
    </general-modal>
  </div>
</template>
<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";
import GeneralModal from "../../components/generalModal";

export default {
  name: "schoolList",
  components: { PageTop, Pagination, DataTable, formModal, GeneralModal },
  data() {
    return {
      tableHeading: [
        "Sl",
        "Name",
        "Address",
        "Institute Code",
        "Phone",
        "Email",
        "Status",
        "Action",
      ],
      formModalId: "formModal",
      details: {},
      myvar: 11,
    };
  },
  methods: {
    schoolDetails: function (data) {
      const _this = this;
      _this.openModal("detailsModal", false);
      var URL = `${_this.urlGenerate()}/${data.id}`;
      _this.getData(URL, "get", {}, {}, function (retData) {
        _this.details = retData;
        _this.openModal("detailsModal", false);
      });
    },
  },
  mounted() {
    this.getDataList();
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

table.table tr th:first-child,
table.table tr td:first-child {
  width: initial !important;
  line-height: 26px !important;
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
