<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Module Permission">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Module Permissions" :default-add-button="false" page-modal-title="Module Add/Edit"></page-top>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{parseInt(dataList.from)+index}}</td>
                            <td>{{data.display_name}}</td>
                            <td>{{data.name}}</td>
                            <td class="center">
                                <a v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="action-buttons">
                                <template v-for="(role, index) in data.role_modules">
                                    <a class="btn btn-outline-danger">{{role.display_name}}</a>
                                </template>
                            </td>
                            <td></td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-xs" @submit="submitForm(formObject,'formModal')">
            <div class="mb-2">
                <label class="col-form-label">Display Name:</label>
                <input type="text" v-model="formObject.display_name" v-validate="'required'" name="display_name" class="form-control">
            </div>
            <div class="mb-2">
                <label class="col-form-label">Name:</label>
                <input type="text" :disabled="formType === 2" v-model="formObject.name" v-validate="'required'" name="name" class="form-control">
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
        name: "roleModules",
        components: {PageTop, Pagination, DataTable, formModal},
        data() {
            return {
                tableHeading: ['Sl','Display name','Name','Status','Permitted Roles', 'Action'],
                formModalId : 'formModal',
            }
        },
        mounted(){
            this.getDataList();
        }
    }
</script>

<style scoped>
.center {
        text-align: center;
    }

   .action-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

</style>
