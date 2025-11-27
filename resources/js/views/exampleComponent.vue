<template :key="$route.meta.name">
    <div class="main_component">
        <data-table :table-heading="tableHeading">
            <template v-slot:page_top>
                <page-top top-page-title="Example Component"></page-top>
            </template>
            <template v-slot:data>
                <tr>
                    <td>1</td>
                    <td>Active</td>
                    <td><i class="fa fa-pencil"></i></td>
                </tr>
            </template>
        </data-table>
        <formModal modalSize="modal-xs" @submit="submitForm(formObject,'formModal')">
            <div class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <label class="col-form-label">Name</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" v-model="formObject.component_name" v-validate="'required'" name="name" class="form-control">
                    </div>
                </div>
            </div>
        </formModal>
    </div>
</template>

<script>
import DataTable from "../components/dataTable";
import Pagination from "../plugins/pagination/pagination";
import PageTop from "../components/pageTop";
import formModal from "../components/formModal";
export default {
    name: "exampleComponent",
    components: {PageTop, Pagination, DataTable, formModal},
    data() {
        return {
            tableHeading: ['Sl','Status', 'Action'],
        }
    },
    methods:{

    },
    mounted(){
    }
}
</script>

<style scoped>

</style>
