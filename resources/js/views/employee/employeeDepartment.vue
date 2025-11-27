<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Employee Department">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Employee Department"
                            :default-add-button="can('employee_department_add')"></page-top>
                    </template>
                    <template v-slot:filter>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.name }}</td>
                            <td style="text-align: center !important;">
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <a v-if="can('employee_department_update')" class="link-primary"
                                        @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('employee_department_delete')" class="link-danger"
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
                        <label class="form-label">Employee Department</label>
                        <input type="text" v-model="formObject.name" v-validate="'required'" name="name"
                            class="form-control" placeholder="Enter employee department">
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
    name: "employeeDepartment",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ["Sl", "Department Name", "Status", "Action"],
            formModalId: "formModal",
        };
    },
    mounted() {
        const _this = this;
        _this.getDataList();
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
