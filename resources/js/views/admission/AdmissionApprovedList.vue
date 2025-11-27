
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
                        <page-top topPageTitle="Sort Listed Candidates" :default-add-button="false"
                                  page-modal-title="Admission Add/Edit">
                            <div class="btn">
                                <select @change="multipleDeleteItem" class="form-control">
                                    <option value="" disabled selected>Bulk Action</option>
                                    <option value="">Delete All</option>
                                </select>
                           </div>
                            <button @click="printData('printDiv')" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Print</button>
                            <a class="btn btn-success btn-sm"
                            @click="openFile(`${urlGenerate('api/applicant_sort_list_export_excel')}`)"><i class="fa fa-file-excel"></i><i class="fa fa-file-excel-o"></i> Export Excel</a>
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

                            <th class="d-print-none">DOB</th>
                           
                            <!-- <th class="d-print-none">Father Name</th>
                            <th class="d-print-none">Mother Name</th> -->
                            <th>Class</th>
                            <th>Session</th>
                            <th>Section</th>
                            <th>Department</th>
                            <th class="d-print-none">Marks</th>
                            <th>Quota</th>
                            <th class="d-print-none">Status</th>
                            <th class="d-print-none">Action</th>
                        </tr>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="d-print-none">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="deletedMultipleData" :value="data.id">
                                </div>
                            </td>
                            <td class="fw-medium">{{parseInt(dataList.from)+index}}</td>
                            <td>{{data.applicant_id}}</td>
                            <td>{{data.applicant_name_en}}</td>
                           
                            <td class="d-print-none">{{data.date_of_birth}}</td>
                            <!-- <td class="d-print-none">
                                <span v-if="data.admission_requests_communications">
                                    {{data.admission_requests_communications.father_name}}
                                </span>
                                <span v-else>
                                    No Father
                                </span>
                            </td>
                            <td class="d-print-none">
                                <span v-if="data.admission_requests_communications">
                                    {{data.admission_requests_communications.mother_name}}
                                </span>
                                <span v-else>
                                    No Father
                                </span>
                            </td> -->

                            <td>{{showData(data.class_info, 'name')}}</td>
                            <td>{{showData(data.session, 'title')}}</td>
                            <td>{{showData(data.section, 'name')}}</td>
                            <td>{{showData(data.department, 'department_name')}}</td>

                            <td class="d-print-none">
                                <span v-for="(result,result_index ) in data.previous_result" >
                                    <div class="badge bg-dark">{{result.exam_name}}</div> - <span>GPA: {{result.gpa}}</span>
                                    <br>
                                </span>

                            </td>
                            <td>
                                <span v-if="data.quota">
                                    <span class="badge bg-success">{{data.quota}}</span>
                                </span>
                                <span v-else>
                                    <span class="badge bg-danger">No Quota</span>
                                </span>
                            </td>
                            <td class="d-print-none">
                                <span v-if="data.short_list">
                                    <span class="badge bg-success">Selected Apllicant</span>
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

                            <td style="width: 150px !important; display: flex" class="d-print-none">
                                <div class="hstack gap-1 fs-15 no-print">
                                    <a :href="'/view_admit_card_backend'+'/'+data.id" target="_blank"  v-if="data.short_list_status==1">
                                        <button class="btn btn-sm btn-light"
                                        >
                                            <i class="fa fa-print"></i>
                                        </button>
                                    </a>
                                    <a :href="'/api/view_application_details_backend'+'/'+data.id" target="_blank">
                                        <button class="btn btn-sm btn-light"
                                        >
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </a>
                                    <a class="link-danger" @click="custom_remove_status(data.id)"><button class="btn btn-sm btn-light"><i class="fa fa-trash"></i></button></a>
                                    <!-- <a  class="link-danger" @click="editData(data, data.id)"><i class="fa fa-list"></i></a> -->
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
    </div>
</template>

<script>
    import DataTable from "../../components/dataTable";
    import PageTop from "../../components/pageTop";
    import axios from 'axios';
    export default {
        name: "admissionApprovedList",
        components: {PageTop, DataTable},
        data() {
            return {
                deletedMultipleData: [],
                checked: false,
                certificate_url:'',
                tableHeading: ['Sl', 'Applicant ID', 'Applicant Name', 'Gender', 'Date Of Birth','Phone No.', 'Religion', 'Father Name','Mother Name', 'Class', 'Session', 'Group', 'Shift', 'Mark', 'Quota', 'Status','Action'],
                formModalId : 'formModal',
                defaultFilter: {class_id: '', session_id: '', section_id: '', department_id: ''},
            }
        },
        methods : {
            selectAllCheck(event, all_data){
                if(this.checked == false){
                    const _this = this;
                    $.each(all_data, function(index, item){
                        _this.deletedMultipleData.push(item.id)
                    });
                }else{
                    this.checked = false
                    this.deletedMultipleData = []
                }
            },
             multipleDeleteItem(){
                const _this = this
                    if(this.deletedMultipleData){
                        axios.get(`/api/multiple_selected_delete_status/${this.deletedMultipleData}`)
                    .then(res => {
                        _this.deletedMultipleData = []
                        _this.getDataList();
                        _this.checked = false
                        _this.$toastr('success', 'Removed Successfully', 'Success')
                    })
                    .catch(e => {
                        console.log(e);
                    })
                }else{
                    alert('please,check first');
                }
            },
            custom_remove_status(id){
                axios.get('/api/remove_from_shortlist/'+id)
                .then( res=> {
                    this.getDataList();
                    this.$toastr('success', 'Successfully Removed', 'Success')
                })
                .catch( e=> {
                    console.log(e);
                })
            },
            preview_certificate:function(data){
                this.certificate_url=data.file;
                $('#certificate_modal').modal('show');
            },
        },
        mounted(){
            this.getDataList();
            const _this = this;
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
</style>