<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Student List">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Configuration List" :default-add-button="can('configuration_add')" page-modal-title="Configuration Add/Edit" :default-object="{key:'',type:'text',value:''}"></page-top>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td>{{parseInt(dataList.from)+index}}</td>
                            <td>{{data.display_name}}</td>
                            <td>{{data.key}}</td>
                            <td>
                                <span v-if="data.type == 'file'">
                                    <img style="height: 20px" :src="getImage(data.value)">
                                </span>
                                <span v-else v-html="showData(data,'value')"></span>
                            </td>
                            <td class="action-buttons">
                                <div class="hstack gap-3 fs-15">
                                    <a v-if="can('configuration_update')" class="link-primary" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('configuration_delete')" class="link-danger"  @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-xs" @submit="submitForm(formObject,'formModal')">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <label>Key</label>
                    <select v-validate="'required'" name="key"  v-model="formObject.key" class="form-control">
                        <option value="" selected>Select Key</option>
                        <template v-for="(key, index) in configKeys">
                            <option :value="index">{{key}}</option>
                        </template>
                    </select>
                </div>
                <div class="col-md-12 mb-2">
                    <label>Display name</label>
                    <input v-validate="'required'" name="display_name" type="text" v-model="formObject.display_name" class="form-control" placeholder="Display name">
                </div>
                <div class="col-md-12 mb-2">
                    <label>Type</label>
                    <select v-validate="'required'" name="display_name" v-model="formObject.type" class="form-control">
                        <option value="" selected>Select</option>
                        <option value="text">Text</option>
                        <option value="tinmyce">tinmyce</option>
                        <option value="textarea">Textarea</option>
                        <option value="file">File</option>
                        <option value="select">Select</option>
                    </select>
                </div>
                <div class="col-md-12 mb-2">
                    <label>Value</label>
                    <template v-if="formObject.type == 'text'" >
                        <input class="form-control form-control" v-validate="'required'" data-vv-as="Value" v-model="formObject.value" name="value" type="text" placeholder="Value">
                    </template>
                    <template v-if="formObject.type == 'file'">
                        <div @click="clickImageInput('value')" class="mb-2">
                            <div class="form-group image_upload" :style="{ backgroundImage: 'url(' + getImage(null, 'images/upload.png') + ')' }">
                                <img v-if="formObject.value" :src="getImage(formObject.value)">
                                <input name="thumbnail" :v-validate="formType === 1 ? 'required' : 'sometimes'" style="display: none;" id="value" type="file" @change="uploadFile($event, formObject, 'value')">
                            </div>
                        </div>
                    </template>
                    <template v-if="formObject.type == 'textarea'" >
                        <textarea class="form-control form-control" v-validate="'required'" data-vv-as="Value" v-model="formObject.value" style="width: 100%;" placeholder="Value"></textarea>
                    </template>
                    <template v-if="formObject.type == 'tinmyce'" >
                        <textarea class="form-control form-control" v-validate="'required'" data-vv-as="Value" v-model="formObject.value" style="width: 100%;" placeholder="Value"></textarea>
                    </template>
                    <template v-if="formObject.type == 'select'">
                        <select v-validate="'required'" name="key" v-model="formObject.value" class="form-control">
                            <option value="">Select</option>
                            <template v-for="(data, index) in dataSource">
                                <option :value="data.name">{{data.display_name}}</option>
                            </template>
                        </select>
                    </template>
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
        name: "configurationList",
        components: {PageTop, Pagination, DataTable, formModal},
        data() {
            return {
                tableHeading: ['Sl','Display name', 'Key','Value', 'Action'],
                formModalId : 'formModal',
                configKeys : {
                    logo : 'Logo',
                    name : 'Application name',
                    address_configuration : 'Address for Contact',
                    phone_configuration : 'Phone Number for Contact',
                },
            }
        },
        computed : {
            dataSource(){
                var key = this.formObject.key;
                return [
                    {name : 1, display_name : 'YES'},
                    {name : 0, display_name : 'NO'},
                ]
            }
        },
        mounted(){
            this.getDataList();
        }
    }
</script>

<style scoped>
.center {
        text-align: center;
    }

   .action-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
    }
</style>
