<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Building" >
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Building List" :default-add-button="can('building_add')" ></page-top>
                    </template>
                    <template v-slot:filter>

                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.name }}</td>
                            <td style="text-align:center">
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td style="display:flex; justify-content:center">
                                <div class="hstack gap-3 fs-15">
                                    <a v-if="can('building_update')" class="link-primary" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('building_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-md" @submit="submitForm(formObject, 'formModal')">

            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Building Name :</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" v-model="formObject.name" v-validate="'required'" placeholder="Building Name" name="name" class="form-control">
                    </div>
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
        name: "BuildingList",
        components: { PageTop, Pagination, DataTable, formModal },
        data() {
            return {
                tableHeading: ["Sl","Name","Status", "Action"],
                formModalId: "formModal",
            };
        },
        mounted() {
            const _this = this;
            _this.getDataList();
        },
    };
</script>

<style scoped>

</style>

