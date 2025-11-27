<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Module Permission" :default-pagination="false">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Role Permissions" :default-add-button="false"></page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select class="form-control pointer" @change="getDataList()" v-model="formFilter.role_id">
                                <option value="">Select Role</option>
                                <template v-for="(role, index) in requiredData.role">
                                    <option :value="role.id">{{role.display_name}}</option>
                                </template>
                            </select>
                        </div>
                    </template>
                    <template v-slot:data >
                        <template v-for="(data, index) in allModules.data">
                            <tr class="parent">
                                <td class="fw-medium"><strong>{{parseInt(allModules.from)+index}}</strong></td>
                                <td>
                                    <input class="form-check-input" type="checkbox" :id="`box${index}`" :checked="modules.includes(data.id)" @click="selectModule($event, data)">
                                    <label class="form-check-label" :for="`box${index}`">
                                        <strong>{{data.display_name}}</strong>
                                    </label>
                                </td>
                                <td>
                                    <div class="row">
                                        <div class="col-md-3" v-for="(permission, pIndex) in data.permissions">
                                            <div class="form-check form-check-outline form-check-secondary">
                                                <input class="form-check-input" type="checkbox" :id="`box${index}${pIndex}`" :checked="permissions.includes(permission.id)" @click="selectPermissions($event, permission.id)">
                                                <label class="form-check-label" :for="`box${index}${pIndex}`">{{permission.display_name}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                            <template v-for="(eachData, sIndex) in data.submenus">
                                <tr class="child">
                                    <td class="fw-medium">{{parseInt(allModules.from)+index}}.{{parseInt(allModules.from)+sIndex}}</td>
                                    <td>
                                        <input class="form-check-input" type="checkbox" :id="`box${sIndex}${index}`" :checked="modules.includes(eachData.id)" @click="selectModule($event, eachData)">
                                        <label class="form-check-label" :for="`box${sIndex}${index}`">
                                            <strong>{{eachData.display_name}}</strong>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-3" v-for="(permission, psIndex) in eachData.permissions">
                                                <div class="form-check form-check-outline form-check-secondary">
                                                    <input class="form-check-input" type="checkbox" :id="`box${index}${sIndex}${psIndex}`" :checked="permissions.includes(permission.id)" @click="selectPermissions($event, permission.id)">
                                                    <label class="form-check-label" :for="`box${index}${sIndex}${psIndex}`">{{permission.display_name}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                                <template v-for="(eachDataLast, sLIndex) in eachData.submenus">
                                    <tr class="last_child">
                                        <td class="fw-medium">{{parseInt(allModules.from)+index}}.{{parseInt(allModules.from)+sIndex}}</td>
                                        <td>
                                            <input class="form-check-input" type="checkbox" :id="`box${sIndex}${index}{sLIndex}${sLIndex}`" :checked="modules.includes(eachDataLast.id)" @click="selectModule($event, eachDataLast)">
                                            <label class="form-check-label" :for="`box${sIndex}${index}{sLIndex}${sLIndex}`">
                                                <strong>{{eachDataLast.display_name}}</strong>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col-md-3" v-for="(permission, psIndex) in eachDataLast.permissions">
                                                    <div class="form-check form-check-outline form-check-secondary">
                                                        <input class="form-check-input" type="checkbox" :id="`box${index}${sIndex}${psIndex}${sLIndex}{psIndex}`" :checked="permissions.includes(permission.id)" @click="selectPermissions($event, permission.id)">
                                                        <label class="form-check-label" :for="`box${index}${sIndex}${psIndex}${sLIndex}{psIndex}`">{{permission.display_name}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                </template>
                            </template>
                        </template>
                    </template>
                    <template v-slot:pagination>
                        <div class="mt-3 text-right">
                            <pagination previousText="PREV" nextText="NEXT" :data="allModules" @paginateTo="getDataList"></pagination>
                        </div>
                    </template>
                </data-table>
            </div>
        </div>
    </div>
</template>

<script>
    import DataTable from "../../components/dataTable";
    import Pagination from "../../plugins/pagination/pagination";
    import PageTop from "../../components/pageTop";
    import formModal from "../../components/formModal";
    export default {
        name: "rolePermissions",
        components: {PageTop, Pagination, DataTable, formModal},
        data() {
            return {
                tableHeading: ['Sl','Name','Permissions', 'Action'],
                formModalId : 'formModal',
                reqData : {
                    module_id : '',
                    permissions : [],
                    type : '',
                    role_id : ''
                }
            }
        },
        computed:{
            allModules(){
                const _this = this;
                if (_this.dataList.all_modules !== undefined){
                    return _this.dataList.all_modules;
                }
                return {}
            },
            modules(){
                const _this = this;
                if (_this.dataList.modules !== undefined){
                    return _this.dataList.modules;
                }
                return [];
            },
            permissions(){
                const _this = this;
                if (_this.dataList.permissions !== undefined){
                    return _this.dataList.permissions;
                }
                return [];
            },
        },
        methods : {
            selectModule : function ($event, module) {
                const _this = this;

                if (!_this.formFilter.role_id){
                    _this.$toastr('warning', 'You must select a role first', 'Warning');
                    return;
                }

                _this.reqData.role_id = _this.formFilter.role_id;

                if ($event.target.checked){
                    _this.modules.push(module.id);

                    _this.reqData.type = 'insert';
                    _this.reqData.module_id = module.id;

                    $.each(module.permissions, function (index, value) {
                        _this.permissions.push(value.id);
                        _this.reqData.permissions.push(value.id);
                    });

                }else{
                    _this.reqData.type = 'remove';
                    _this.reqData.module_id = module.id;

                    var index = _this.modules.indexOf(module.id);
                    _this.modules.splice(index, 1);

                    $.each(module.permissions, function (i, value) {
                        var index = _this.permissions.indexOf(value.id);
                        _this.permissions.splice(index, 1);

                        _this.reqData.permissions.push(value.id);
                    });
                }

                _this.getData(_this.urlGenerate(), 'post', {}, _this.reqData, function (retData) {
                    _this.reqData.module_id = '';
                    _this.reqData.permissions = [];
                    _this.reqData.type = '';

                    _this.getConfigurations();
                });

            },

            selectPermissions : function (event, id) {
                const _this = this;

                if (!_this.formFilter.role_id){
                    _this.$toastr('warning', 'You must select a role first', 'Warning');
                    return;
                }

                _this.reqData.role_id = _this.formFilter.role_id;

                if (event.target.checked){
                    _this.permissions.push(id);
                    _this.reqData.permissions.push(id);
                    _this.reqData.type = 'insert';
                }else{

                    var index = _this.permissions.indexOf(id);
                    _this.permissions.splice(index, 1);

                    _this.reqData.permissions.push(id);
                    _this.reqData.type = 'remove';
                }

                _this.getData(_this.urlGenerate(), 'post', {}, _this.reqData, function (retData) {
                    _this.reqData.module_id = '';
                    _this.reqData.permissions = [];
                    _this.reqData.type = '';

                    _this.getConfigurations();
                });

            }
        },
        mounted(){
            const _this = this;
            _this.getGeneralData(['role'], function (retData) {
                _this.$set(_this.formFilter, 'role_id', '');
                _this.getDataList();
            });
        }
    }
</script>

<style scoped>

</style>
