<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Store Category">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Store Category" :default-add-button="can('category_add')"
                            page-modal-title="Store Category  Add/Edit"></page-top>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.name }}</td>
                            <td>
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td>
                                <div class="hstack gap-3 fs-15">
                                    <a v-if="can('category_update')" class="link-primary"
                                        @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('category_delete')" class="link-danger"
                                        @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-xs" @submit="submitForm(formObject, 'formModal')">
            <div class="mb-2">
                <label class="col-form-label">Product Category</label>
                <input type="text" v-model="formObject.name" v-validate="'required'" name="name" class="form-control"
                    placeholder="Enter Product Category Name">
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
    name: "productCategory",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ['Sl', 'Product Category', 'Status', 'Action'],
            formModalId: 'formModal',
        }
    },
    mounted() {
        this.getDataList();
    }
}
</script>

<style scoped></style>
