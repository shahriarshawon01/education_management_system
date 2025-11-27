<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Canteen Configure">
                    <template v-slot:page_top>
                        <page-top :default-object="{ component: '' }" topPageTitle="Canteen Configure"
                            :default-add-button="can('canteen_configure_add')"></page-top>
                    </template>
                    <template v-slot:filter>

                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ showData(data.component, 'name') }}</td>
                            <td class="table-center">
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons" style="text-align: center;">
                                    <a v-if="can('canteen_configure_update')" class="link-primary"
                                        @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('canteen_configure_delete')" class="link-danger"
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
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label">Configure Name</label>
                        <select v-model="formObject.component" class="form-control" v-validate="'required'">
                            <option value="">Select</option>
                            <template v-for="(data, index) in requiredData.canteenComponent">
                                <option :value="data.id">{{ data.name }}</option>
                            </template>
                        </select>
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
    name: "CanteenConfigure",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ["Sl", "Component Name", "Status", "Action"],
            formModalId: "formModal",
        };
    },
    methods: {
    },
    mounted() {
        const _this = this;
        _this.getDataList();
        _this.getGeneralData(['canteenComponent']);
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
</style>
