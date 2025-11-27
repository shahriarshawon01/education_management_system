<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Parent List">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Parent List" page-modal-title="Parent Add/Edit"
                            :default-add-button="false">
                            <router-link v-if="can('parents_add')" to="/admin/parents/add" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add Parent
                            </router-link>
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.status" name="status">
                                <option value="">Select Parent Status</option>
                                <option value="">All</option>
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                            </select>
                        </div>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>
                                <a v-if="data.image" @click="openFile(getImage(data.image))">
                                    <i class="fa fa-download"> File</i>
                                </a>
                                <p v-else>-</p>
                            </td>
                            <td>{{ data.name }}</td>
                            <td>{{ data.phone }}</td>
                            <td>{{ data.profession }}</td>
                            <td>
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td>
                                <div class="hstack gap-3 fs-15">
                                    <a class="link-info" @click="parentDetails(data)"><i class="fa fa-eye"></i></a>
                                    <!-- <router-link class="link-primary" :to="`/admin/parents_view/${data.id}`"><i class="fa fa-eye"></i></router-link> -->
                                    <router-link v-if="can('parents_update')" class="link-primary"
                                        :to="`/admin/parents/add/${data.id}`"><i class="fa fa-edit"></i></router-link>
                                    <a v-if="can('parents_delete')" class="link-danger"
                                        @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
                <general-modal modal-id="detailsModal" modalSize="modal-lg">
                    <template v-slot:title>
                        <!-- <span>Parent details</span> -->
                    </template>
                    <template v-slot:body>
                        <div class="row">
                            <label class="col-4">Name</label>
                            <div class="col-md-8">
                                <strong>: </strong> <span>{{ (details.name) }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-4">Phone</label>
                            <div class="col-md-8">
                                <strong>: </strong> <span>{{ details.phone }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-4">Gender </label>
                            <div class="col-md-8">
                                <!-- <strong>: </strong> <span>{{details.gender}}</span> -->
                                <label v-if="parseInt(details.gender) === 1">Male</label>
                                <label v-if="parseInt(details.gender) === 2">Female</label>
                                <label v-else-if="parseInt(details.gender) === 3">Other</label>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-4">Profession</label>
                            <div class="col-md-8">
                                <strong>: </strong> <span>{{ details.profession }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h4>Address</h4>
                            </div>
                            <div class="col-md-12">
                                <div class="row mb-2">
                                    <label class="col-md-2"></label>
                                    <div class="col-md-10">
                                        <div class="row">
                                            <div class="col-2">
                                                <strong>Division</strong>
                                            </div>
                                            <div class="col-2">
                                                <strong>District</strong>
                                            </div>
                                            <div class="col-2">
                                                <strong>Upazilla</strong>
                                            </div>
                                            <div class="col-2">
                                                <strong>Union</strong>
                                            </div>
                                            <div class="col-2">
                                                <strong>Post Office</strong>
                                            </div>
                                            <div class="col-2">
                                                <strong>Village</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                            <div class="col-md-12">
                                <div class="row mb-2" v-for="(address, index) in details.address">
                                    <label class="col-md-2" v-if="parseInt(address.type) === 1">Permanent</label>
                                    <label class="col-md-2" v-else>Present</label>
                                    <div class="col-md-10">
                                        <div class="row">
                                            <div class="col-2">
                                                <label>{{ address.division }}</label>
                                            </div>
                                            <div class="col-2">
                                                <label>{{ address.district }}</label>
                                            </div>
                                            <div class="col-2">
                                                <label>{{ address.upazilla }}</label>
                                            </div>
                                            <div class="col-2">
                                                <label>{{ address.union_name }}</label>
                                            </div>
                                            <div class="col-2">
                                                <label>{{ address.post_office }}</label>
                                            </div>
                                            <div class="col-2">
                                                <label>{{ address.village }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </general-modal>
            </div>
        </div>
    </div>
</template>

<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import GeneralModal from "../../components/generalModal";

export default {
    name: "parentList",
    components: { PageTop, Pagination, DataTable, GeneralModal },
    data() {
        return {
            tableHeading: ['Sl', 'Image', 'Parent Name', 'Mobile No', 'Profession', 'Status', 'Action'],
            details: {}

        }
    },
    methods: {
        parentDetails: function (data) {
            const _this = this;
            var URL = `${_this.urlGenerate()}/${data.id}`;
            var title = `${_this.showData(data, 'name')}`;
            _this.openModal('detailsModal', title, function () {
                _this.getData(URL, "get", {}, {}, function (retData) {
                    _this.details = retData;
                });
            });
        },
    },

    mounted() {
        const _this = this;
        this.getDataList();
    }
}
</script>

<style scoped></style>
