<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Component"
                    :default-object="{ group: '', school: '', component_type: '' }">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Component" page-modal-title="Component Add/Edit"
                            :default-object="{ group_id: '' }"></page-top>
                    </template>
                    <!-- <template v-slot:filter>

                        <div class="col-md-2">
                            <label class="col-form-label">Type:</label>
                            <select v-model="formFilter.component_type" v-validate="'required'" name="component_type"
                                class="form-control">
                                <option value="">Select Type</option>
                                <option value="1">Academic</option>
                                <option value="2">Canteen</option>
                                <option value="3">Dormitory</option>
                                <option value="4">Transport</option>
                            </select>
                        </div>
                    </template> -->
                     <template v-slot:filter>
                        <div class="col-md-2">
                           <select v-model="formFilter.component_type"  name="component_type"
                                class="form-control">
                                <option value="">Select Type</option>
                                <option value="1">Academic</option>
                                <option value="2">Canteen</option>
                                <option value="3">Dormitory</option>
                                <option value="4">Transport</option>
                            </select>
                        </div>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.name }}</td>
                            <td>{{ showComponentType(data.component_type) }}</td>
                            <td>{{ showData(data, 'value') }}</td>
                            <td>
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <a class="link-primary" @click="editData(data, data.id)"><i
                                            class="fa fa-edit"></i></a>
                                    <a class="link-danger" @click="deleteInformation(index, data.id)"><i
                                            class="fa fa-trash"></i></a>
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
                    placeholder="Component Name">
            </div>
            <div class="mb-2">
                <label class="col-form-label">Type:</label>
                <select v-model="formObject.component_type" v-validate="'required'" name="component_type"
                    class="form-control">
                    <option value="">Select Type</option>
                    <option value="1">Academic</option>
                    <option value="2">Canteen</option>
                    <option value="3">Dormitory</option>
                    <option value="4">Transport</option>
                </select>
            </div>
            <div class="mb-2">
                <label class="col-form-label">Amount</label>
                <input type="text" v-model="formObject.value" name="value" class="form-control"
                    placeholder="Component Amount">
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
    name: "componentList",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ['Sl', 'Component Name', 'Type', 'Amount', 'Status', 'Action'],
        }
    },
    methods: {
        showComponentType(component_type) {
            switch (component_type) {
                case 1: return 'Academic';
                case 2: return 'Canteen';
                case 3: return 'Dormitory';
                case 4: return 'Transport';
                default: return '-';
            }
        },
    },
    mounted() {
        const _this = this;
        _this.getDataList();
        _this.getGeneralData(['componentGroup']);
        _this.$set(_this.formObject, 'school_id', '');
        _this.$set(_this.formObject, 'component_type', '');

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
