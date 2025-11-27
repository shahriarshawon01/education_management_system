<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Bank">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Bank" page-modal-title="Bank Add/Edit"></page-top>
                    </template>
                    <template v-slot:filter>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.name }}</td>
                            <td>{{ data.account_name }}</td>
                            <td>{{ data.account_number }}</td>
                            <td>{{ data.branch }}</td>
                            <td>{{ data.routing_number }}</td>
                            <td class="table-center">
                                <a class="action-buttons" @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <a v-if="can('bank_update')" class="link-primary" @click="editData(data, data.id)"><i
                                       class="fa fa-edit"></i></a>
                                    <a v-if="can('bank_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i
                                            class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-lg" @submit="submitForm(formObject, 'formModal')">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="col-form-label">Bank Name</label>
                        <input type="text" v-model="formObject.name" v-validate="'required'" name="name"
                            class="form-control" placeholder="Bank Name">
                    </div>

                    <div class="mb-2">
                        <label class="col-form-label">Account Name</label>
                        <input type="text" v-model="formObject.account_name" v-validate="'required'" name="account_name"
                            class="form-control" placeholder="Account Name">
                    </div>

                    <div class="mb-2">
                        <label class="col-form-label">Account Number</label>
                        <input type="text" v-model="formObject.account_number" v-validate="'required'"
                            name="account_number" class="form-control" placeholder="Account Number">
                    </div>

                    <div class="mb-2">
                        <label class="col-form-label">Branch</label>
                        <input type="text" v-model="formObject.branch" v-validate="'required'" name="branch"
                            class="form-control" placeholder="Branch">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="col-form-label">Routing Number</label>
                        <input type="number" v-model="formObject.routing_number" v-validate="'required|integer'"
                            name="routing_number" class="form-control" placeholder="Routing Number">
                    </div>

                    <div class="mb-2">
                        <label class="col-form-label">Swift Code</label>
                        <input type="text" v-model="formObject.swift_code" name="swift_code" class="form-control"
                            placeholder="SWIFT Code">
                    </div>

                    <div class="mb-2">
                        <label class="col-form-label">Address</label>
                        <input type="text" v-model="formObject.address" name="address" class="form-control"
                            placeholder="Bank Address">
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
    name: "bankList",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ['Sl', 'Bank Name', 'Account Name', 'Account Number', 'Branch', 'Routing Number', 'Status', 'Action'],
        }
    },
    mounted() {
        const _this = this;
        _this.getDataList();
    }
}
</script>

<style scoped>
.table-center .action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}
</style>
