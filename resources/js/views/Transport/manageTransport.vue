<template :key="$route.meta.name">
    <div class="main_component">
        <data-table :table-heading="tableHeading">
            <template v-slot:page_top>
                <page-top top-page-title="Manage Transport" v-if="can('manage_transport_add')"></page-top>
            </template>
            <template v-slot:data>
                <tr v-for="(data, index) in dataList.data">
                    <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                    <td>{{ showData(data.routes, 'route_name') }}</td>
                    <td>{{ data.transport_name }}</td>
                    <td>{{ data.transport_no }}</td>
                    <td>{{ data.licence_no }}</td>
                    <td>{{ data.register_date }}</td>
                    <td>{{ data.running_date }}</td>
                    <td>{{ data.total_seat }}</td>
                    <td>{{ data.color }}</td>
                    <td>{{ data.description ?? '-' }}</td>
                    <td>
                        <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                    </td>
                    <td class="table-center">
                        <div class="hstack gap-3 fs-15 action-buttons">
                            <a class="link-primary" v-if="can('manage_transport_update')"
                                @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                            <a class="link-danger" v-if="can('manage_transport_delete')"
                                @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
            </template>
        </data-table>

        <formModal modalSize="modal-lg" @submit="submitForm(formObject, 'formModal')">
            <div class="row">
                <!-- Left Column -->
                <h5 class="mb-3 text-white px-2 py-1 rounded"
                    style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
                    Manage Transport
                </h5>
                <div class="col-md-6">

                    <div class="mb-3">
                        <label class="form-label">Route</label>
                        <select class="form-control" v-model="formObject.route_id" v-validate="'required'"
                            name="route_id">
                            <option value="">Search Route : </option>
                            <template v-for="(data, bIndex) in requiredData.routes">
                                <option :value="data.id">{{ data.route_name }}</option>
                            </template>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transport Name</label>
                        <input type="text" v-model="formObject.transport_name" v-validate="'required'"
                            name="transport_name" class="form-control" placeholder="Transport Name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transport No </label>
                        <input type="text" v-model="formObject.transport_no" v-validate="'required'" name="transport_no"
                            class="form-control" placeholder="Transport No">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Seat </label>
                        <input type="number" v-model="formObject.total_seat" v-validate="'required'" name="total_seat"
                            class="form-control" placeholder="Total Seat">
                    </div>
                    <div class="mb-3">
                        <textarea type="text" v-model="formObject.description" name="description" class="form-control"
                            placeholder="Description"></textarea>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">

                    <div class="mb-3">
                        <label class="form-label">License No</label>
                        <input type="text" v-model="formObject.licence_no" v-validate="'required'" name="licence_no"
                            class="form-control" placeholder="License No">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Register Date</label>
                        <datepicker v-model="formObject.register_date" name="register_date" input_class="form-control"
                            v-validate="'required'"></datepicker>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Running Date</label>
                        <datepicker v-model="formObject.running_date" name="running_date" input_class="form-control"
                            v-validate="'required'"></datepicker>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color</label>
                        <input type="text" v-model="formObject.color" name="color" class="form-control"
                            placeholder="Color">
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
    name: "manageTransport",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ['Sl', 'Route Name', 'Transport Name', 'Vehicle No', 'Licence No', 'Register Date', 'Running Date', 'No of Seat', 'Color', 'Description', 'Status', 'Action'],
        }
    },
    methods: {

    },
    mounted() {
        const _this = this;
        _this.$set(_this.formObject, 'route_id', '')
        _this.$set(_this.formObject, 'register_date', '')
        _this.$set(_this.formObject, 'running_date', '')
        _this.getDataList();
        _this.getGeneralData(['routes'])
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
