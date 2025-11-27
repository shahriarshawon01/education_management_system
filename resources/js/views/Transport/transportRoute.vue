<template :key="$route.meta.name">
    <div class="main_component">
        <data-table :table-heading="tableHeading">
            <template v-slot:page_top>
                <page-top top-page-title="Route" v-if="can('route_add')"></page-top>
            </template>
            <template v-slot:data>
                <tr v-for="(data, index) in dataList.data">
                    <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                    <td>{{ data.route_name }}</td>
                    <td>{{ data.route_fare }}</td>
                    <td>{{ data.completing_hour }}</td>
                    <td>{{ data.from }}</td>
                    <td>{{ data.to }}</td>
                    <td>
                        <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                    </td>
                    <td class="table-center">
                        <div class="hstack gap-3 fs-15 action-buttons">
                            <a class="link-primary" v-if="can('route_update')" @click="editData(data, data.id)"><i
                                    class="fa fa-edit"></i></a>
                            <a class="link-danger" v-if="can('route_delete')"
                                @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
            </template>
        </data-table>

        <formModal modalSize="modal-xs" @submit="submitForm(formObject, 'formModal')">
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-12">
                        <label class="col-form-label">Route Name</label>
                        <input type="text" v-model="formObject.route_name" v-validate="'required'" name="route_name"
                            class="form-control form-control-sm rounded-3 shadow-sm" placeholder="Route Name">
                    </div>

                    <div class="col-md-12">
                        <label class="col-form-label">Distance</label>
                        <input type="text" v-model="formObject.route_length" v-validate="'required'" name="distance"
                            class="form-control form-control-sm rounded-3 shadow-sm" placeholder="Distance">
                    </div>

                    <div class="col-md-12">
                        <label class="col-form-label">End Place (To)</label>
                        <input type="text" v-model="formObject.to" v-validate="'required'" name="to"
                            class="form-control form-control-sm rounded-3 shadow-sm" placeholder="End Place (To)">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="col-md-12">
                        <label class="col-form-label">Route Fare</label>
                        <input type="text" v-model="formObject.route_fare" v-validate="'required'" name="route_fare"
                            class="form-control form-control-sm rounded-3 shadow-sm" placeholder="Route Fare">
                    </div>

                    <div class="col-md-12">
                        <label class="col-form-label">Completing Hour</label>
                        <input type="text" v-model="formObject.completing_hour" v-validate="'required'"
                            name="completing_hour" class="form-control form-control-sm rounded-3 shadow-sm"
                            placeholder="Completing Hour">
                    </div>

                    <div class="col-md-12">
                        <label class="col-form-label">Start Place (From)</label>
                        <input type="text" v-model="formObject.from" v-validate="'required'" name="from"
                            class="form-control form-control-sm rounded-3 shadow-sm" placeholder="Start Place (From)">
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
    name: "transportRoute",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ['Sl', 'Route Name', 'Route Fare', 'Completing Hour', 'From', 'To', 'Status', 'Action'],
        }
    },
    methods: {

    },
    mounted() {
        const _this = this;
        _this.getDataList();
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
