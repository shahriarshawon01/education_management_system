
<template :key="$route.meta.name">
    <div class="main_component">
        <div class="modal fade" id="certificate_modal" tabindex="-1" aria-labelledby="certificate_modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="certificate_modal">Academic Document</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <data-table
                table-title="Sort Listed Candidates"
                :defaultFilter="false"
                :default-object="defaultFilter"
                 >

                <template v-slot:page_top>
                    <page-top topPageTitle="Waiting List" :default-add-button="false"
                            page-modal-title="Admission Add/Edit">
                                <div class="btn">
                                    <select @change="multipleWaitingChange($event.target.value)" class="form-control">
                                        <option value="" disabled selected>Bulk Action</option>
                                        <option value="merit_list">Merit List</option>
                                        <option value="delete_list">Delete</option>
                                    </select>
                                </div>
                            <button @click="printData('printDiv')" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Print</button>
                            <a class="btn btn-success btn-sm"
                       @click="openFile(`${urlGenerate('api/applicant_waiting_export_excel')}`)"><i class="fa fa-file-excel"></i><i class="fa fa-file-excel-o"></i> Export Excel</a>
                        </page-top>
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
                            <select type="text" class="form-control" v-model="formFilter.session_id" name="session_id">
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
                        <div class="col-md-2">
                            <select type="text" class="form-control" v-model="formFilter.department_id" name="department_id">
                                <option value="">Select Department</option>
                                <template v-for="(eachData, index) in requiredData.departments">
                                    <option :value="eachData.id">{{ eachData.department_name }}</option>
                                </template>
                            </select>
                        </div>
                    </template>

                    <template v-slot:data>
                        <tr>
                            <th class="d-print-none">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" :value="checked" v-model="checked" id="selectAll" @click="selectAllCheck($event,dataList.data)">
                                </div>
                            </th>
                            <th>SL</th>
                            <th>Applicant ID</th>
                            <th>Applicant Name</th>
                            <th class="d-print-none">Gender</th>
                            <th class="d-print-none">DOB</th>
                           
                            <th>Class</th>
                            <th>Session</th>
                            <th>Section</th>
                            <th>Department</th>
                            <!-- <th>Shift</th> -->
                            <th>Quota</th>
                            <!-- <th class="d-print-none">Source</th> -->
                            <th>Marks</th>
                            <!-- <th>Merit List</th> -->
                            <th>Status</th>
                            <!-- <th class="d-print-none">Action</th> -->
                        </tr>

                        <tr v-for="(data, index) in dataList.data">
                            <td class="d-print-none">
                                <div @click.prevent="handleClick" class="form-check" v-if="data.final_enroll_status == '1'">
                                    <input class="form-check-input" type="checkbox" :disabled="true">
                                </div>
                                <div class="form-check" v-else>
                                    <input class="form-check-input" type="checkbox" v-model="selectedMultipleData" :value="data.id">
                                </div>
                            </td>
                            <td class="fw-medium">{{parseInt(dataList.from)+index}}</td>
                            <td>{{data.applicant_id}}</td>
                            <td>{{data.applicant_name_en}}</td>
                            <td class="d-print-none">{{data.gender}}</td>
                            <td class="d-print-none">{{data.date_of_birth}}</td>
                          
                            <td>{{showData(data.class_info, 'name')}}</td>
                            <td>{{showData(data.session, 'title')}}</td>
                            <td>{{ showData(data.section, "name") }}</td>
                            <td>{{ showData(data.department, "department_name") }}</td>
                            <td>
                                <span v-if="data.quota">
                                    <span class="badge bg-dark">{{data.quota}}</span>
                                </span>
                                <span v-else>
                                    <span class="badge bg-danger">No Quota</span>
                                </span>
                            </td>
                            <!-- <td class="d-print-none">
                                <span v-if="data.short_list_status == 1" class="d-print-none">
                                    <span class="badge bg-success">Short Listed</span>
                                </span>
                                <span v-else-if="data.short_list_status == 2" class="d-print-none">
                                    <span class="badge bg-info">Preliminary Enrolled</span>
                                </span>
                                <span v-else>
                                    <span class="badge bg-primary d-print-none">Others</span>
                                </span>

                            </td> -->
                            <td>
                                <span v-if="data.short_list == 2" v-for="(result,result_index ) in data.previous_result" >
                                    <div  class="badge bg-dark">{{result.exam_type}}</div> - <span>GPA: {{result.gpa}}</span>
                                    <br>
                                </span>
                                <span v-if="data.short_list == 1" >
                                    <span>{{data.mark}}</span>
                                </span>
                            </td>

                             <!--<td class="d-print-none">

                            </td>

                            <td>
                                <p class="d-block font-weight-bold">
                                    <i class="fa-solid fa-user icon text-dark font-weight-bold"></i>
                                    <span class="text-danger"> {{data.name}}  </span>&nbsp;&nbsp;
                                    <i class="fa-solid fa-envelope icon text-dark font-weight-bold"></i>
                                    <span class="text-primary font-weight-bold  ">{{data.email}} </span> &nbsp;&nbsp;
                                    <i class="fa-solid fa-smile icon text-dark font-weight-bold"></i>
                                    <span class="text-primary font-weight-bold  "> {{data.gender}} </span>

                                </p>
                                <p class=" w-75 text-dark">
                                    <span><i class="fa-solid fa-x text-dark"></i> {{data.religion}}</span> &nbsp;&nbsp;
                                    <span><i class="fa-solid fa-phone text-dark"></i> {{data.phone_no}}</span>
                                </p>
                            </td>
                            <td>
                                <p class="d-block font-weight-bold">
                                    <span v-if="showData(Config.school, 'institution_type_id') ==1" class="text-danger"> Class : {{showData(data.class_info, 'name')}} </span> &nbsp;&nbsp; , &nbsp;&nbsp;

                                    <span v-if="showData(Config.school, 'institution_type_id') !=1"
                                          class="text-primary font-weight-bold  ">Depertment : {{showData(data.depertment, 'name')}}</span>
                                    <span>Group : {{data.group.name}}</span><br/>
                                    <span class="text-primary"> Shift :  </span>{{showData(data.shift, 'name')}} &nbsp;&nbsp; , &nbsp;&nbsp;
                                   Session : {{showData(data.session, 'session_title')}} <br/><br/>
                                    <span v-for="(result,result_index ) in data.previous_result" >

                                        <div class="btn btn-sm btn-primary font-weight-bold">{{result.gpa}}</div>&nbsp;

                                    </span>
                                </p>

                            </td>

                            <td>
                                <span class="text-danger fw-bold">Payment :</span>
                                <a @click="custom_status_change('payment_status',data.id)"
                                   v-if="data.payment_status==0"><i class="fa-regular fa-circle-xmark "></i></a>
                                <a v-else=""><i class="fa-solid fa-check"></i></a><br/>
                                <span class="text-danger fw-bold">Admit Print :</span> <a @click="custom_status_change('admit_print_status',data.id)"
                                   v-if="data.admit_print_status==0"><i class="fa-regular fa-circle-xmark "></i></a>
                                <a v-else=""><i class="fa-solid fa-check"></i></a>
                            </td> -->
                            <!-- <td>
                              <span class="badge bg-info waves-effect waves-light" v-if="data.mark >= 80 && data.mark <= 100">
                                        1st Merit List
                              </span>
                             <span class="badge bg-secondary" v-else-if="data.mark >= 70 && data.mark <= 79">
                                       2nd Merit List
                             </span>
                            <span class="badge bg-warning waves-effect waves-light" v-else-if="data.mark >= 60 && data.mark <= 69">
                                      3rd Merit List
                            </span>
                            <span v-else>

                            </span>
                            </td> -->
                            <td>
                                <span v-if="data.merit_waiting_status == '2'">
                                    <span class="badge bg-info">Waiting List</span>
                                </span>
                                <span v-else>
                                    <span class="badge bg-primary d-print-none"></span>
                                </span>

                            </td>
                            <!-- <td style="width: 150px !important; display: flex" class="d-print-none">
                                <div class="hstack gap-1 fs-15 no-print">
                                    <a @click.prevent="handleClick" v-if="data.final_enroll_status == '1'">
                                    <button class="btn btn-sm btn-light disabled-link" :disabled="true">
                                        <i class="fa fa-save text-success"></i>
                                        <span class="text-success">Enrolled</span>
                                    </button>
                                    </a>
                                    <a title="Enroll" @click="viewEnroll(data)"  v-else>
                                        <button class="btn btn-sm btn-light">
                                            <i class="fa fa-save"></i>
                                            <span>Enroll</span>
                                        </button>
                                    </a>
                                    <a class="link-danger" @click="custom_remove_status(data.id)"><button class="btn btn-sm btn-light"><i class="fa fa-trash"></i></button></a>

                                </div>
                            </td> -->

                        </tr>
                    </template>
                </data-table>
            </div>
        </div>

        <generalModal modalSize="modal-md" modal-id="detailsModal" :isEnablePrint="false" @submit="submitStudentForAdmission()">
            <template v-slot:body>
                <template v-for="(fees_type, index) in formObject.fees">
                    <div class="row form-group mt-3">
                        <div class="col-4">
                            <label class="control-label">{{fees_type.name}} ({{ fees_type.amount }})</label>
                        </div>
                        <div class="col-2">
                            <label>
                                <input @change="checkAmount($event, fees_type)" type="checkbox" :checked="fees_type.checked">
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="text" v-model="fees_type.paid" class="form-control">
                        </div>
                    </div>
                </template>
            </template>
        </generalModal>
    </div>
</template>

<script>
    import DataTable from "../../components/dataTable";
    import PageTop from "../../components/pageTop";
    import generalModal from "../../components/generalModal";
    import axios from 'axios';
    export default {
        name: "admissionApprovedList",
        components: {PageTop, DataTable,generalModal},
        data() {
            return {
                selectedMultipleData: [],
                checked: false,
                certificate_url:'',
                tableHeading: ['Sl', 'Applicant ID', 'Applicant Name', 'Gender', 'Date Of Birth','Phone No.', 'Religion', 'Father Name','Mother Name', 'Class', 'Session', 'Group', 'Shift', 'Mark', 'Quota', 'Status','Action'],
                formModalId : 'formModal',
                defaultFilter: {class_id: '', session_id: '', section_id: '', department_id: ''},
                preliminary_id: ''
            }
        },
        methods : {
            admissionAmount(amount){
                return amount;
            },
            handleClick(){
                this.$toastr('error', 'Student Already Enrolled', 'Error')
            },
            selectAllCheck(event, all_data){
                if(this.checked == false){
                    const _this = this;
                    $.each(all_data, function(index, item){
                        _this.selectedMultipleData.push(item.id)
                    });
                }else{
                    this.checked = false
                    this.selectedMultipleData = []
                }
            },
            finalMultipleStudentEnroll(type,amount){
            // finalMultipleStudentEnroll(type){
            const _this = this;
            _this.$swal({
                title: 'Payment Required',
                text: `admission Fees: ${amount}`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: '<i class="fa fa-check"></i>',
                cancelButtonText: '<i class="fa fa-close"></i>',
                showCloseButton: true,
            }).then((result) => {
            if (result.value) {
                axios.post(`/api/multiple_student_enroll/${type}/${_this.selectedMultipleData}`)
                    .then(function (response) {
                    var retData = response.data;
                    _this.$store.commit('httpRequest', false);
                    if (parseInt(retData.status) === 2000) {
                        _this.checked = false;
                        _this.$toastr('success', 'Student Admission Successfully Completed', 'Success');
                        _this.getDataList();
                        if (callDataList) {
                            _this.getDataList();
                        }
                        if (typeof callback == 'function') {
                            callback(true);
                        }
                    } else {
                        _this.$toastr('warning', retData.message, 'Warning');
                        _this.getDataList();

                    }
                }).catch(function (error) {
                    console.log(error);

                });
            }
        });
            // const _this = this
            //     if(_this.selectedMultipleData){
            //         axios.get(`/api/multiple_student_enroll/${type}/${_this.selectedMultipleData}`)
            //     .then(res => {
            //        if(res.data.result == 'enroll_all'){
            //         _this.selectedMultipleData = [];
            //         _this.getDataList();
            //         _this.checked = false;
            //         _this.$toastr('success', 'Student Enrolled Successfully', 'Success')
            //        }else if(res.data.result == 'delete_all'){
            //         _this.selectedMultipleData = [];
            //         _this.getDataList();
            //         _this.checked = false;
            //         _this.$toastr('success', 'Student Deleted Successfully', 'Success')
            //        }

            //     })
            //     .catch(e => {
            //         console.log(e);
            //     })
            // }else{
            //     alert('please,check first');
            // }

        },

            submitStudentForAdmission(){
                const _this = this;
                axios.post('/api/students_store_for_admission', _this.formObject)
               .then(res => {
                _this.getDataList();
                _this.$toastr('success', 'Student Successfully Enrolled', 'Store/Update')
                _this.closeModal('detailsModal', true);
               })
               .catch(e=>{
                  console.log(e);
               })
            },

            custom_remove_status(id){
                axios.get('/api/custom_remove_status/'+id)
                .then(res=>{
                    this.getDataList();
                    this.$toastr('success', 'uccessfully Removed', 'Success')
                })
                .catch(e=>{
                    console.log(e);
                })
            },
            preview_certificate:function(data){
                this.certificate_url=data.file;
                $('#certificate_modal').modal('show');
            },

            checkAmount : function(event, data){
                data.checked = event.target.checked ? 1 : 0
                data.paid = event.target.checked ? data.amount : 0;
            },
            viewEnroll: function (data) {
                // console.log(data)
                const _this = this;

                _this.$set(_this.formObject, 'class_id', data.class_id);
                _this.$set(_this.formObject, 'session_id', data.session_id);
                _this.$set(_this.formObject, 'group_id', data.group_id);
                _this.$set(_this.formObject, 'admission_id', data.id);

                _this.getGeneralData({fees_types:{
                    class_id : data.class_id,
                    session_id : data.session_id,
                    group_id : data.group_id,
                }}, function(retData){
                    _this.openModal('detailsModal', false);
                    _this.$set(_this.formObject, 'fees', retData.fees_types);
                });
            },

            multipleWaitingChange(type){
                const _this = this
                    if(this.selectedMultipleData){
                        axios.get(`/api/multiple_waiting_changes/${type}/${this.selectedMultipleData}`)
                    .then(res => {
                        _this.selectedMultipleData = []
                        _this.getDataList();
                        _this.checked = false
                        _this.$toastr('success', 'Successfully Updated', 'Success')
                    })
                    .catch(e => {
                        console.log(e);
                    })
                }else{
                    alert('please,check first');
                }

            },

        },
        computed:{

        },
        mounted(){
            const _this = this;
            _this.admissionAmount();
            _this.getDataList();
           _this.getGeneralData([
                  "class",
                  "session",
                  "section",
                  "departments",
            ]);

        }
    }
</script>

<style scoped>
    .img-center {
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    .disabled-link {
        cursor: not-allowed! important;
    }
    /* ssss */
</style>