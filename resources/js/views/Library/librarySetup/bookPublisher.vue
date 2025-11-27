<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Book Publisher" page-modal-title="Add/Edit" v-if="can('publisher_add')"></page-top>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{parseInt(dataList.from)+index}}</td>
                            <td>{{data.name}}</td>
                            <td class="center">
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="action-buttons">
                                <div class="hstack gap-3 fs-15">
                                    <a class="link-primary" v-if="can('publisher_update')" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a class="link-danger" v-if="can('publisher_delete')" @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-xs" @submit="submitForm(formObject,'formModal')">
            <div class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <label class="col-form-label">Name</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" v-model="formObject.name" v-validate="'required'" name="name" class="form-control" placeholder="Name">
                    </div>
                </div>
            </div>
        </formModal>
    </div>
</template>

<script>
import DataTable from "../../../components/dataTable";
import Pagination from "../../../plugins/pagination/pagination";
import PageTop from "../../../components/pageTop";
import formModal from "../../../components/formModal";
export default {
    name: "bookPublisher",
    components: {PageTop, Pagination, DataTable, formModal},
    data() {
        return {
            tableHeading: ['Sl','Name','Status', 'Action'],
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
