<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Fees Type List">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Fees Type List" :default-add-button="can('fees_type_add')" page-modal-title="Fees Type Add/Edit"></page-top>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{parseInt(dataList.from)+index}}</td>
                            <td style="text-align: center">{{data.name}}</td>
                            <td style="text-align: center">{{data.code}}</td>
                            <td style="text-align: center">
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td>
                                <div class="hstack gap-3 fs-15" style="text-align: center">
                                    <a v-if="can('department_update')" class="link-primary" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('department_delete')" class="link-danger"  @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal @submit="submitForm(formObject,'formModal')" :modalSize = "modal-xl">
            <div class="row">
                <div class="col-md-12">
                    <label class="col-form-label"> Name:</label>
                    <input type="text" v-model="formObject.name" v-validate="'required'" name="name" class="form-control" placeholder="Name">
                </div>
                <div class="col-md-12">
                    <label class="col-form-label"> Code:</label>
                    <input type="text" v-model="formObject.code" v-validate="'required'" name="code" class="form-control" placeholder="Code">
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
import Editor from '@tinymce/tinymce-vue';
export default {
    name: "FeesTypeList",
    components: {PageTop, Pagination, DataTable, formModal, Editor},
    data() {
        return {
            tableHeading: ['Sl','Name','Code','Status','Action'],
            formModalId : 'formModal',
        }
    },
    mounted(){
        this.getDataList();
    }
}
</script>

<style scoped>

</style>