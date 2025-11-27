<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Student List">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Module List" page-modal-title="Module Add/Edit"
                            :default-object="{ parent_id: '' }"></page-top>
                    </template>
                    <template v-slot:data>
                        <template v-for="(data, index) in dataList.data">
                            <tr class="parent">
                                <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                                <td>{{ data.display_name }}</td>
                                <td>{{ data.name }}</td>
                                <td>
                                    <template v-for="(chunk, chunkIndex) in chunkedPermissions(data.permissions, 4)"
                                        >
                                        <div>
                                            <template v-for="(permission, pIndex) in chunk" >
                                                <span class="badge badge-soft-info">{{ permission.name }}</span>
                                                <span v-if="pIndex < chunk.length - 1"></span>
                                            </template>
                                        </div>
                                    </template>
                                </td>
                                <td>
                                    <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                                </td>
                                <td>
                                    <div class="hstack gap-3 fs-15">
                                        <a class="link-primary" @click="editData(data, data.id)"><i
                                                class="fa fa-edit"></i></a>
                                        <a class="link-danger" @click="deleteInformation(index, data.id)"><i
                                                class="fa fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <template v-for="(eachData, sIndex) in data.submenus">
                                <tr class="child">
                                    <td class="fw-medium">{{ parseInt(dataList.from) + index }}.{{ sIndex + 1 }}</td>
                                    <td>{{ eachData.display_name }}</td>
                                    <td>{{ eachData.name }}</td>
                                    <td>
                                        <template v-for="(permission, pIndex) in eachData.permissions">
                                            <span class="badge badge-soft-info ml-2">{{ permission.name }}</span>
                                        </template>
                                    </td>
                                    <td>
                                        <a @click="changeStatus(eachData)" v-html="showStatus(eachData.status)"></a>
                                    </td>
                                    <td>
                                        <div class="hstack gap-3 fs-15">
                                            <a class="link-primary" @click="editData(eachData, eachData.id)"><i
                                                    class="fa fa-edit"></i></a>
                                            <a class="link-danger" @click="deleteInformation(sIndex, eachData.id)"><i
                                                    class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <template v-for="(eachDataEnd, sIndexEnd) in eachData.submenus">
                                    <tr class="last_child">
                                        <td class="fw-medium">
                                            {{ parseInt(dataList.from) + index }}.{{ sIndex + 1 }}.{{ sIndexEnd + 1 }}
                                        </td>
                                        <td>{{ eachDataEnd.display_name }}</td>
                                        <td>{{ eachDataEnd.name }} </td>
                                        <td>
                                            <template v-for="(permission, pIndex) in eachDataEnd.permissions">
                                                <span class="badge badge-soft-info ml-2">{{ permission.name }}</span>
                                            </template>
                                        </td>
                                        <td>
                                            <a @click="changeStatus(eachDataEnd)"
                                                v-html="showStatus(eachDataEnd.status)"></a>
                                        </td>
                                        <td>
                                            <div class="hstack gap-3 fs-15">
                                                <a class="link-primary"
                                                    @click="editData(eachDataEnd, eachDataEnd.id)"><i
                                                        class="fa fa-edit"></i></a>
                                                <a class="link-danger"
                                                    @click="deleteInformation(sIndex, eachDataEnd.id)"><i
                                                        class="fa fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </template>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-xs" @submit="submitForm(formObject, 'formModal')">
            <div class="mb-2">
                <label>Name</label>
                <input :disabled="formType === 2" v-validate="'required'" name="name" type="text"
                    v-model="formObject.name" class="form-control" placeholder="Name">
            </div>
            <div class="mb-2">
                <label>Display name</label>
                <input v-validate="'required'" name="display_name" type="text" v-model="formObject.display_name"
                    class="form-control" placeholder="Display Name">
            </div>
            <div class="mb-2">
                <label>Link/URL</label>
                <input v-validate="'required'" name="link" type="text" v-model="formObject.link" class="form-control"
                    placeholder="Link/URL">
            </div>
            <div class="mb-2">
                <label>Parent</label>
                <select name="parent_id" class="form-control" v-model="formObject.parent_id">
                    <option value="" selected>--Select Parent--</option>
                    <template v-for="(parent, index) in requiredData.module">
                        <option :value="parent.id">{{ parent.display_name }}</option>
                    </template>
                </select>
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
    name: "moduleList",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ['Sl', 'Display name', 'Name', 'Permissions', 'Status', 'Action'],
            formModalId: 'formModal',
        }
    },

    methods: {
        chunkedPermissions(array, size) {
            const chunks = [];
            for (let i = 0; i < array.length; i += size) {
                chunks.push(array.slice(i, i + size));
            }
            return chunks;
        }
    },

    mounted() {
        const _this = this;
        _this.getDataList();
        _this.getGeneralData(['module'])
    }
}
</script>

<style scoped></style>
