
<template :key="$route.meta.name">
    <div class="main_component">

        <div class="row">
            <div class="col">
                <ResultTable
                :defaultFilter="false"
                :default-object="defaultFilter"
                 >

                 <template v-slot:page_top>
                        <h4>Admission Test Result</h4>
                    </template>

                    <template v-slot:filter>
                            <div class="col-md-3">
                                <input type="number" name="applicant_id" v-model="formFilter.applicant_id" class="form-control" placeholder="Applicant ID No." />
                            </div>
                    </template>

                    <template v-slot:data>
                        <br/><br/>
                    <div class="invoice">

                    <div class="row" v-for="(data_info, index) in dataList.data_info" :key="index">
                        <div class="col-2"></div>
                        <div class="col-8">
                            
                                <div class="page-header text-center" align="center" style="font-family: Times New Roman, sans-serif; font-size: 30px; font-weight: bold;">{{data_info.title}}</div>
                                <div class="page-header text-center" align="center" style="font-family: Times New Roman, sans-serif; font-size: 21px;">{{data_info.address}}</div>
                                <div class="page-header text-center" align="center" style="font-family: Times New Roman, sans-serif; font-size: 17px;">Emis code : {{data_info.emis_code}}</div>
                          
                        </div>
                        <div class="col-2" v-if="data_info.profile_photo">
                            <div class="page-header text-center" align="center" style="font-family: Times New Roman, sans-serif; font-size: 17px;">
                                  <img :src="getProfilePhotoUrl(data_info.profile_photo)" alt="Profile Photo" style="width: 110px; object-fit: cover">
                            </div>
                        </div>
                       
                    <div class="page-header text-center" align="center" style="font-family: Georgia, sans-serif; font-size: 18px; font-weight: bold;">Admission Test Results</div><br/><br/><br/>
                    <div class="col-md-12">
                        <div id="result_display">
                        <div class="table-responsive">
                            <table class="table  table-bordered" width="100%">
                               <tbody>
                                    <tr>
                                        <td><b>Applicant ID</b></td><td>{{data_info.applicant_id}}</td>
                                        <td><b>Applicant Name</b></td><td>{{data_info.applicant_name_en}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Date of Birth</b></td><td>{{data_info.date_of_birth}}</td>
                                        <td><b>Gender</b></td><td>{{data_info.gender}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Class</b></td><td>{{showData(data_info.class_info, 'name')}}</td>
                                        <!-- <td><b>Session</b></td><td>{{showData(data_info.session_info, 'title')}}</td> -->
                                        <td><b>Session</b></td><td>{{data_info.session_info}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Section</b></td><td>{{showData(data_info.section, 'name')}}</td>
                                        <td><b>Department</b></td><td>{{showData(data_info.department, 'department_name')}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Marks</b></td><td>{{ data_info.mark }}</td>
                                        <td><b>Remarks</b></td>
                                        <td>
                                        <span v-if="data_info.mark > 33"><span style="color: green">Pass</span></span>
                                        <span v-else-if="data_info.mark === 33"><span style="color: green">Pass</span></span>
                                        <span v-else><span style="color: red">Fail</span></span>
                                        </td>
                                    </tr>
                             </tbody>
                        </table>
                    </div>

                    <br/><br/>
                    <div class="page-header text-center" align="center" style="font-family: Georgia, sans-serif; font-size: 22px;"><u>Subjectwise Marks</u></div><br/>
                    <table class="table table-striped table-bordered" width="100%">
                    <thead>
                    <tr>
                    <th class="text-center">SL</th>
                    <th class="text-center">Subject Name</th>
                    <th class="text-center">Subject Marks</th>
                    </tr>
                    </thead>

                    <tbody>

                    <tr v-for="(customName, index) in data_info.subjectwise" :key="index">
                        <td align="center">{{ dataList.from ? parseInt(dataList.from) + index + 1 : index + 1 }}</td>
                        <td align="center">{{ showData(customName.subjectname, 'subject_name') }}</td>
                        <td align="center">{{ customName.subject_marks }}</td>
                    </tr>

                    </tbody>
                    </table>


                        <br>

                    <!-- <div class="row">
                    <div class="col-md-12">

                    <table align="right">
                    <tr>
                    <td width="50%"><strong>Total Marks</strong></td>
                    <td width="50%" align="right">{{ data_info.mark }}</td>
                    </tr>
                    <tr>
                    <td width="50%"><strong>Remrks</strong></td>
                    <td width="50%" align="right">
                        <span v-if="data_info.mark > 33">Pass</span>
                        <span v-else-if="data_info.mark === 33">Pass</span>
                        <span v-else>Fail</span>
                    </td>
                    </tr>
                    </table>
                        </div>
                    </div> -->
                    <br/><br/>
                    <div align="right">
                    <button id="printNone" @click="printData('printDiv')" class="btn btn-success"><i class="fa fa-print"></i> Print</button>
                    </div>
                    <br/>
                    </div>
                    </div>
                    </div>
                    </div>
                    </template>
                </ResultTable>
            </div>
        </div>
    </div>
</template>

<script>
    import ResultTable from "../../components/resultTable";
    import PageTop from "../../components/pageTop";
    export default {
        name: "TestResult",
        components: {PageTop, ResultTable},

        methods : {
             getProfilePhotoUrl(photoPath) {
               return `/storage/admission/${photoPath}`;
            },
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

    #customers {
      font-family: Arial, Helvetica, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }

    #customers td, #customers th {
      border: 1px solid #ddd;
      padding: 8px;
    }

    #customers tr:nth-child(even){background-color: #ffffff;}

    #customers tr:hover {background-color: #ddd;}

    #customers th {
      padding-top: 12px;
      padding-bottom: 12px;
      text-align: left;

      color: rgb(0, 0, 0);
    }

      #main_header2 {
       margin-top: 0!important;
      }

    #main_header2 {
    height: auto;
    background-color: #00909b;
    text-align: center;
    }

    .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
    border: 1px solid #ddd;
     }

   .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 8px;
    line-height: 1.42857143;
   }
   .btn-success {
    color: #fff;
    background-color: #00909b;
    border-color: #00909b;
     }
   table {
    border-collapse: collapse;
    }

    @media print {
    #printNone {
        display: none;
        }
    }

</style>










<!-- <template :key="$route.meta.name">
    <div class="main_component">

        <div class="row">
            <div class="col">
                <DataTable
                :defaultFilter="false"
                :default-object="defaultFilter">

                 <template v-slot:page_top>
                        <h4>Admission Test Result</h4>
                    </template>

                    <template v-slot:filter>
                        <div class="col-md-2">
                            <input type="number" v-model="formFilter.keyword" class="form-control"  placeholder="Applicant ID No."/>
                        </div>
                    </template>

                    <template v-slot:data >
                        <template v-if="dataList.data_info.length > 0">
                            <tr>
                                <th>Applicant ID</th>
                                <th>Applicant Name</th>
                                <th class="d-print-none">DOB</th>
                                <th class="d-print-none">Marks</th>
                            </tr>

                            <tr v-for="(data_info, index) in dataList.data_info" :key="index">
                                <td>{{ data_info.applicant_id }}</td>
                                <td>{{ data_info.name }}</td>
                                <td class="d-print-none">{{ data_info.birthday }}</td>
                                <td class="d-print-none">
                                    <span v-if="data_info.mark > 33"><b>{{ data_info.mark }}</b> (<span class="text-success">Pass</span>)</span>
                                    <span v-else-if="data_info.mark === 33">{<b>{{ data_info.mark }}</b> ((<span class="text-success">Pass</span>))</span>
                                    <span v-else>{{ data_info.mark }} (<span class="text-danger">Fail</span>)</span>
                                </td>
                            </tr>

                        </template>
                        <template v-else>
                            <tr>
                                <td colspan="4" class="text-center">No data found</td>
                            </tr>
                        </template>
                    </template>
                </DataTable>
            </div>
        </div>
    </div>
</template>

<script>
    import DataTable from "../../components/dataTable";
    import PageTop from "../../components/pageTop";
    import axios from 'axios';
    export default {
        name: "TestResult",
        components: {PageTop, DataTable},
        data() {
            return {
                selectedMultipleData: [],
                checked: false,
                certificate_url:'',
                formModalId : 'formModal',
                defaultFilter: {class_id: '', session_id: '', group_id: '', shift_id: ''},
                preliminary_id: '',
                showTable: false
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

        },
        computed:{

        },
        mounted(){
            const _this = this;
            _this.getDataList();
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
</style> -->