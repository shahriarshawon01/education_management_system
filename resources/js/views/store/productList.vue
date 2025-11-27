<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Product List">
                    <template v-slot:page_top>
                        <page-top :default-object="selectedObject" topPageTitle="Product List"
                            :default-add-button="can('product_add')" page-modal-title="Product Add/Edit"></page-top>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.name }}</td>
                            <td>{{ showData(data.store_category, 'name') }}</td>
                            <td>{{ data.description }}</td>
                            <td>
                                <div class="hstack gap-3 fs-15">
                                    <a v-if="can('product_update')" class="link-primary"
                                        @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('product_delete')" class="link-danger"
                                        @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-l" @submit="submitForm(formObject, 'formModal')">
            <div class="row">
                <div class="md-2">
                    <label class="col-form-label">Product Category</label>
                    <select type="text" class="form-control" v-model="formObject.category_id" v-validate="'required'"
                        name="category_id">
                        <option value="">Select Category</option>
                        <template v-for="(eachData, index) in requiredData.store_category">
                            <option :value="eachData.id">{{ eachData.name }}</option>
                        </template>
                    </select>
                </div>
                <div class="md-2">
                    <label class="col-form-label">Product Name</label>
                    <input type="text" v-model="formObject.name" v-validate="'required'" name="name"
                        class="form-control" placeholder="Enter Product Name">
                </div>
                <div class="md-2">
                    <label class="col-form-label">Product Description</label>
                    <textarea name="description" class="form-control" v-model="formObject.description"
                    rows="3" placeholder="Product Description"></textarea>
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
    name: "ProductList",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ['Sl', 'Product Name', 'Product Category', 'Description', 'Action'],
            formModalId: 'formModal',
            selectedObject: {
                category_id: ''
            }
        }
    },
    mounted() {
        this.getDataList();
        const _this = this;
        _this.getGeneralData(['store_category']);
    }
}
</script>

<style scoped></style>
