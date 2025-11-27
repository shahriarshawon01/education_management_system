<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Role List">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Role List" page-modal-title="Role Add/Edit"></page-top>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{parseInt(dataList.from)+index}}</td>
                            <td>{{data.display_name}}</td>
                            <td>{{data.name}}</td>
                            <td class="center">
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="action-buttons">
                                <div class="hstack gap-3 fs-15">
                                    <a class="link-primary" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a class="link-danger"  @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-xs" @submit="submitForm(formObject,'formModal')">
            <div class="mb-2">
                <label class="col-form-label">Display Name:</label>
                <input type="text" v-model="formObject.display_name" v-validate="'required'" name="display_name" class="form-control" placeholder="Display Name">
            </div>
            <div class="mb-2">
                <label class="col-form-label">Name:</label>
                <input type="text" :disabled="formType === 2" v-model="formObject.name" v-validate="'required'" name="name" class="form-control" placeholder="Name">
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
        name: "roleList",
        components: {PageTop, Pagination, DataTable, formModal},
        data() {
            return {
                tableHeading: ['Sl','Display name','Name','Status', 'Action'],
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
