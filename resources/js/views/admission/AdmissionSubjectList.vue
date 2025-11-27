<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Admission Subject"  :defaultFilter="false" :default-object="defaultFilter">
                    <template v-slot:page_top>
                        <page-top

                        :default-object="{session_year_id:'',class_id:'',section_id: '',department_id: '',building_id: '',floor_id: '',room_id: '', selection_subject_mapping:[{
                        subject_name: '',
                        marks: ''
                        }]}"

                        topPageTitle="Admission Subject"  page-modal-title="Admission Subject Add/Edit"></page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select type="text" class="form-control" v-model="formFilter.class_id" name="class_id">
                                <option value="">Select Class</option>
                                <template v-for="(eachData, index) in requiredData.class">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select type="text" class="form-control" v-model="formFilter.session_year_id" name="session_year_id">
                                <option value="">Select Session</option>
                                <template v-for="(eachData, index) in requiredData.session">
                                    <option :value="eachData.id">{{ eachData.title }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select type="text" class="form-control" v-model="formFilter.section_id" name="section_id">
                                <option value="">Select Section</option>
                                <template v-for="(eachData, index) in requiredData.section">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{parseInt(dataList.from)+index}}</td>
                            <td style="text-align:center">{{showData(data.class_info, 'name')}}</td>
                            <td style="text-align:center">{{showData(data.current_sessions, 'title')}}</td>
                            <td style="text-align:center">{{showData(data.section, 'name')}}</td>
                            <td style="text-align:center">{{showData(data.department, 'department_name')}}</td>
                            <td style="text-align:center">{{ data.max_mark}}</td>
                            <td style="text-align:center">{{ data.pass_mark}}</td>
                            <!-- <td><div class="badge bg-dark">Total</div> - {{data.total_seat}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div class="badge bg-primary">Vecant</div> - {{data.seat_allowance}}</td> -->
                            <!-- <td>{{ data.exam_date}}</td> -->
                            <td>
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td style="display:flex; justify-content:center">
                                <div class="hstack gap-3 fs-15">
                                    <!-- <a v-if="can('admission_subject_update')" class="link-primary" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('admission_subject_delete')" class="link-danger"  @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a> -->
                                    <a class="link-primary" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a class="link-danger"  @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-lg" @submit="submitForm(formObject,'formModal')">
            <div class="row">
                <div class="col-md-4">
                    <label class="col-form-label">Class:</label>
                    <select type="text" class="form-control" v-model="formObject.class_id" v-validate="'required'" name="class_id">
                        <option value="">Select Class</option>
                        <template v-for="(eachData, index) in requiredData.class">
                            <option :value="eachData.id">{{ eachData.name }}</option>
                        </template>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="col-form-label">Session:</label>
                    <select type="text" class="form-control" v-model="formObject.session_year_id" v-validate="'required'" name="session_year_id">
                        <option value="">Select Session</option>
                        <template v-for="(eachData, index) in requiredData.session">
                            <option :value="eachData.id">{{ eachData.title}}</option>
                        </template>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="col-form-label">Section:</label>
                    <select type="text" class="form-control" v-model="formObject.section_id" name="section_id">
                        <option value="">Select Section</option>
                        <template v-for="(eachData, index) in requiredData.section">
                            <option :value="eachData.id">{{ eachData.name }}</option>
                        </template>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="col-form-label">Department:</label>
                    <select type="text" class="form-control" v-model="formObject.department_id" name="department_id">
                        <option value="">Select Department</option>
                        <template v-for="(eachData, index) in requiredData.departments">
                            <option :value="eachData.id">{{ eachData.department_name }}</option>
                        </template>
                    </select>
                 </div>
                    <div class="col-md-4">
                        <label class="col-form-label">Maximum Mark:</label>
                        <input type="text" v-model="formObject.max_mark" v-validate="'required'" placeholder="Max. Marks" name="max_mark" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">Pass Mark:</label>
                        <input type="text" v-model="formObject.pass_mark" v-validate="'required'" placeholder="Pass Marks" name="pass_mark" class="form-control">
                    </div>

                   <div v-for="(itemm, index) in formObject.selection_subject_mapping" :key="index" class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Subject Name:</label>
                        <input type="text" v-model="itemm.subject_name" v-validate="'required'" placeholder="Subject Name" :name="'subject_name_'+ index" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">Marks:</label>
                        <input type="text" v-model="itemm.marks" v-validate="'required'" placeholder="Marks" :name="'marks_'+ index" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">Action:</label>
                        <div class="form-group">
                            <button v-if="formObject.selection_subject_mapping.length > 1"  @click="removeField(index)" class="btn btn-danger btn-sm">-</button>
                            <button @click="addField" class="btn btn-primary btn-sm">+</button>
                        </div>

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
        name: "AdmissionSubjectList",
        components: {PageTop, Pagination, DataTable, formModal},
        data() {
            return {
                tableHeading: ['Sl','Class Name ', 'Session', 'Section','Department','Max_Marks','Pass_marks','Status','Action'],
                formModalId : 'formModal',
                defaultFilter: {class_id: '', session_year_id: '', section_id: ''},
            }
        },

        mounted(){
            this.getDataList();
            const _this = this;
            _this.getGeneralData(['class','section','session','departments','building','floor','room']);
            _this.$set(_this.formObject, 'selection_subject_mapping', [{
                subject_name: "",
                marks: "",
            }]);
        },
        methods : {
            addField() {
                const _this = this
                _this.formObject.selection_subject_mapping.push(
                    {
                        subject_name: "",
                        marks: "",
                    }
                )
            },
            removeField(index) {
                this.formObject.selection_subject_mapping.splice(index, 1);
            },


            editDataInformation: function (data, id) {
                const _this = this;
                _this.editData(data, id, 'formModal', function (retData) {
                    var editUrl = `${_this.urlGenerate()}/${id}/edit`;
                    _this.getData(editUrl, 'get', {}, {}, function (retData) {
                        _this.$store.commit('formObject', retData);
                    });
                })
            },
        },
    }
</script>

<style scoped>

</style>