<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Section">
                    <template v-slot:page_top>
                        <page-top :default-object="{ school_id: '', class_id: '' }" topPageTitle="Section"
                            :default-add-button="can('section_add')"></page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.class_id" name="class_id">
                                <option value="">Select Class</option>
                                <template v-for="(data, index) in requiredData.class">
                                    <option :value="data.id">{{ data.name }}</option>
                                </template>
                            </select>
                        </div>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.name }}</td>
                            <td>{{ showData(data.classes, 'name') }}</td>
                            <td style="text-align: center !important;">
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <a v-if="can('section_update')" class="link-primary"
                                        @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('section_delete')" class="link-danger"
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
                <label class="form-label">Class</label>
                <select class="form-control" v-model="formObject.class_id" name="class_id" v-validate="'required'"
                    @click="getGeneralData({ students: { class_id: formObject.class_id, school_id: Config.school_id } })">
                    <option value="">Select Class</option>
                    <template v-for="(data, index) in requiredData.class">
                        <option :value="data.id">{{ data.name }}</option>
                    </template>
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label">Section Name</label>
                <input type="text" v-model="formObject.name" v-validate="'required'" name="section" class="form-control"
                    placeholder="Enter section name">
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
    name: "sectionList",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ["Sl", "Name", "Class", "Status", "Action"],
            formModalId: "formModal",
        };
    },
    mounted() {
        const _this = this;
        _this.getDataList();
        _this.$set(_this.formFilter, 'class_id', '');
        _this.getGeneralData(['school', 'classes'], function (retData) {
            _this.getGeneralData({
                class: { school_id: _this.Config.school_id }
            });
        });
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
