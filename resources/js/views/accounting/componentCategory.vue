<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title=" Component Category"
                    :default-object="{ school: '' }">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Component Category"
                            page-modal-title="Component Category Add/Edit"></page-top>
                    </template>
                    <template v-slot:filter>

                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.name }}</td>
                            <td class="table-center">
                                <a class="action-buttons" @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <a class="link-primary" @click="editData(data, data.id)"><i
                                            class="fa fa-edit"></i></a>
                                    <!-- <a class="link-danger" @click="deleteInformation(index, data.id)"><i
                                            class="fa fa-trash"></i></a> -->
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-xs" @submit="submitForm(formObject, 'formModal')">
            <div class="mb-2">
                <label class="col-form-label">Name</label>
                <input type="text" v-model="formObject.name" v-validate="'required'" name="name" class="form-control"
                    placeholder="Category Name">
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
    name: " componentCategory",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ['Sl', 'Category Name', 'Status', 'Action'],
        }
    },
    mounted() {
        const _this = this;
        _this.$set(_this.formObject, 'school_id', '');
        _this.getDataList();
        _this.getGeneralData(['school']);
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
