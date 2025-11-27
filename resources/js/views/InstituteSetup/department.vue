<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Department List">
                    <template v-slot:page_top>
                        <page-top :default-object="{ class_id: '' }" topPageTitle="Department List"
                            :default-add-button="can('department_add')"></page-top>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.department_name }}</td>
                            <td>{{ data.department_code }}</td>
                            <!-- <td>{{ showData(data.class, 'name') }}</td> -->
                            <td style="text-align: center !important;">
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <a class="link-primary" @click="departmentDetails(data)"><i
                                            class="fa fa-eye"></i></a>
                                    <a v-if="can('department_update')" class="link-primary"
                                        @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('department_delete')" class="link-danger"
                                        @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>

        <formModal modalSize="modal-md" @submit="submitForm(formObject, 'formModal')">
            <div class="mb-2">
                <label class="form-label">Class Name</label>
                <select class="form-control" v-model="formObject.class_id" name="class_id" v-validate="'required'">
                    <option value="">Select Class</option>
                    <template v-for="(data, index) in requiredData.class">
                        <option :value="data.id">{{ data.name }}</option>
                    </template>
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label">Name</label>
                <input type="text" v-model="formObject.department_name" v-validate="'required'" name="department_name"
                    class="form-control" placeholder="Enter Name">
            </div>
            <div class="mb-2">
                <label class="form-label">Code</label>
                <input type="text" v-model="formObject.department_code" v-validate="'required'" name="department_code"
                    class="form-control" placeholder="Enter Code">
            </div>
        </formModal>

        <general-modal modal-id="detailsModal" modalSize="modal-l">
            <template v-slot:title>
                <span>Department details</span>
            </template>
            <template v-slot:body>
                <table class="table table-striped institute_details">
                    <tr class="pb-2">
                        <th style="width: 25%">Department Name</th>
                        <th style="width: 5%">:</th>
                        <td>{{ details.department_name }}</td>
                    </tr>
                    <tr>
                        <th style="width: 25%">Department Code</th>
                        <th style="width: 5%">:</th>
                        <td>{{ details.department_code }}</td>
                    </tr>
                    <!-- <tr>
                        <th style="width: 25%">Class</th>
                        <th style="width: 5%">:</th>
                        <td>{{ details.class ? details.class.name : '' }}</td>
                    </tr> -->
                    <!-- <tr>
                        <th style="width: 25%">Created By</th>
                        <th style="width: 5%">:</th>
                        <td>{{ details.users ? details.users.username : '' }}</td>
                    </tr> -->

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
import GeneralModal from "../../components/generalModal"
export default {
    name: "department",
    components: { PageTop, Pagination, DataTable, formModal, GeneralModal },
    data() {
        return {
            tableHeading: ["Sl", "Department Name", "Department Code", "Status", "Action"],
            formModalId: "formModal",
            details: {}
        };
    },
    methods: {
        departmentDetails: function (data) {
            const _this = this;
            _this.openModal('detailsModal', false);
            var URL = `${_this.urlGenerate()}/${data.id}`;
            _this.getData(URL, "get", {}, {}, function (retData) {
                _this.details = retData;
                _this.openModal('detailsModal', false);
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

table.table tr th:first-child,
table.table tr td:first-child {
    width: initial !important;
    line-height: 28px !important;
}

table.table tr th:first-child,
table.table tr td:first-child {
    width: 20px;
}
</style>
