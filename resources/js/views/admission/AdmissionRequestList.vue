<template :key="$route.meta.name">
    <div class="main_component">


        <div class="modal fade" id="certificate_modal" tabindex="-1" aria-labelledby="certificate_modalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="certificate_modalLabel">Academic Document</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <img width="100%" height="auto" class="img-center " :src="'/' + certificate_url" />
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
                <DataTable table-title="Applicant List" :defaultSearchMethod="false" :defaultFilter="false"
                    :default-object="defaultFilter" :tableHeading="[]" @search="getResults">
                    <template v-slot:page_top>

                        <page-top topPageTitle="Applicant List" :default-add-button="false"
                            page-modal-title="Admission Add/Edit">
                            <!-- <div class="btn" align="center"><b>Accommodation :-</b> Total Seat :  {{ totalSeatInfo.totalSeats }} Vacant : <b>Total Application :</b> <span class="badge text-bg-light">{{application_count ?? 0}}</span> </div> -->
                            <div class="btn" align="center"><b>Total Application :</b> <span class="badge text-bg-light"
                                    style="font-size:12px">{{ application_count ?? 0 }}</span> </div>

                            <div class="btn">
                                <select v-model="selectText" @change="multipleStatusChange($event.target.value)"
                                    class="form-control">
                                    <option value="" selected>Bulk Action</option>
                                    <option value="short_listed">Short List</option>
                                    <option value="preliminary_enroll">Preliminary Enroll</option>
                                    <option value="reject">Reject</option>
                                </select>
                            </div>

                            <button @click="printData('printDiv')" class="btn btn-primary btn-sm"><i
                                    class="fa fa-print"></i> Print </button>
                            <a class="btn btn-success btn-sm"
                                @click="openFile(`${urlGenerate('api/applicant_list_export_excel')}`)"><i
                                    class="fa fa-file-excel"></i><i class="fa fa-file-excel-o"></i> Export Excel</a>

                        </page-top>

                    </template>
                    <template v-slot:filter>
                        <!-- <div class="col-md-1" style="display:flex; align-items:center">
                            <h6 style="margin-bottom: 0rem !important">Filter By:</h6>
                        </div> -->
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
                            <select type="text" class="form-control" v-model="formFilter.department_id"
                                name="department_id">
                                <option value="">Select Department</option>
                                <template v-for="(eachData, index) in requiredData.departments">
                                    <option :value="eachData.id">{{ eachData.department_name }}</option>
                                </template>
                            </select>
                        </div>
                    </template>

                    <template v-slot:sorting>
                        <div class="col-md-2">
                            <a class="btn-group" @click="viewSorting">
                                <a class="btn waves-effect waves-light"
                                    :class="{ 'btn-outline-success': applyAdvanceFilter, 'btn-outline-primary': !applyAdvanceFilter }"><i
                                        class="fa fa-filter"></i><span> Advance Search </span></a>
                            </a>
                        </div>
                    </template>

                    <template v-slot:reportHeader>
                        <div class="row mb-3 mt-3 report_header">
                            <div class="col-12 text-center">
                                <h4>Applicant List Report</h4>
                            </div>
                            <div class="col-12 text-start">
                                <!-- <strong>Date : {{requiredData.date}}</strong> -->
                            </div>
                        </div>
                    </template>
                    <template v-slot:data>
                        <tr>
                            <th class="d-print-none">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" :value="checked" v-model="checked"
                                        id="selectAll" @click="selectAllCheck($event, dataList.data)">
                                </div>
                            </th>
                            <th>SL</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th class="d-print-none">Gender</th>
                            <th class="d-print-none">DOB</th>
                            <th class="d-print-none">Religion</th>
                            <th>Class</th>
                            <th>Session</th>
                            <th>Section</th>
                            <th>Department</th>
                            <th>Marks</th>
                            <th class="d-print-none">Quota</th>
                            <th class="d-print-none">Status</th>
                            <th class="d-print-none">Action</th>
                        </tr>

                        <tr v-for="(data, index) in dataList.data">
                            <td class="d-print-none">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="selectedMultipleData"
                                        :value="data.id">
                                </div>
                            </td>
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.applicant_id }}</td>
                            <td>{{ data.applicant_name_en }}</td>
                            <td class="d-print-none">
                                {{ data.gender }}
                            </td>
                            <td class="d-print-none">{{ data.date_of_birth }}</td>
                            <td class="d-print-none">
                                {{ data.religion }}
                            </td>
                            <td>{{ showData(data.class_info, 'name') }}</td>
                            <td>{{ showData(data.session, 'title') }}</td>
                            <td>{{ showData(data.section, 'name') }}</td>
                            <td>{{ showData(data.department, 'department_name') }}</td>
                            <td>
                                <span v-for="(result, result_index ) in data.previous_result">
                                    <div class="badge bg-dark">{{ result.exam_name }}</div> - <span>GPA:
                                        {{ result.gpa }}</span>
                                    <br>
                                </span>
                            </td>
                            <td class="d-print-none">
                                <span v-if="data.quota">
                                    <span class="badge bg-success">{{ data.quota }}</span>
                                </span>
                                <span v-else>
                                    <span class="badge bg-danger">No Quota</span>
                                </span>
                            </td>

                            <!-- <td class="d-print-none">
                                <div style="cursor:pointer; text-align: center;" title="Download Document" @click="downloadPreviousCertificate(data.id)">
                                    <i class="fas fa-file-download" style="font-size: 20px"></i>
                                </div>
                            </td> -->
                            <!-- <td>
                                <span v-for="(result,result_index ) in data.previous_result">
                                    <div style="cursor:pointer; text-align: center;" title="Download Document" @click="downloadPreviousCertificate(data.id)">
                                        <i class="fas fa-file-download" style="font-size: 15px"></i>
                                    </div>
                                </span>
                            </td> -->

                            <td class="d-print-none">
                                <span v-if="data.short_list == 1">
                                    <span class="badge bg-success">Short Listed</span>
                                </span>
                                <span v-if="data.short_list == 2">
                                    <span class="badge bg-info">Preliminary Enrolled</span>
                                </span>
                                <span v-if="data.short_list == 3">
                                    <span class="badge bg-danger">Rejected</span>
                                </span>
                            </td>

                            <td style="width: 150px !important; display: flex" class="d-print-none">

                                <div class="hstack gap-1 fs-15 no-print">
                                    <a :href="'/api/view_application_details_backend' + '/' + data.id" target="_blank">
                                        <button class="btn btn-sm btn-light">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </a>
                                    <a v-if="can('admission_delete')" class="link-danger"
                                        @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i>
                                    </a>
                                </div>
                                <div class="dropdown ms-1 no-print">
                                    <a href="#" role="button" id="dropdownMenuLink1" data-bs-toggle="dropdown"
                                        aria-expanded="false" class="btn btn-sm btn-light">
                                        <i class="ri-more-2-fill"></i>
                                    </a>

                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink1" style="">
                                        <li class="dropdown-item"><a
                                                @click="custom_short_list_status_change(data.id)">Short List</a></li>
                                        <li class="dropdown-item"><a
                                                @click="custom_preliminary_status(data.id)">Preliminary Enroll</a></li>
                                        <li class="dropdown-item"><a @click="custom_reject_status(data.id)">Reject</a>
                                        </li>

                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </template>
                </DataTable>
            </div>
        </div>

        <!-- <admission_details :message="details_id"></admission_details> -->

        <sortingModal :modal-id="advanceFilterModal" :defaultSearchMethod="false" @search="doApplyAdvanceFilter"
            :disableCloseButtonAction="true" @clear="doApplyAdvanceFilter(false)" @close="() => { }">
            <template v-slot:filter>
                <div class="row border">
                    <div class="col-md-4">
                        <div class="fw-semibold">GPA</div>
                        <div class="form-group">
                            <input class="form-control" placeholder="Grade Point / Marks" v-model="filter.gpa"
                                type="gpa" @change="filterGenderChecked">
                        </div><br />

                        <div class="form-group">
                            <select type="text" class="form-control" v-model="filter.gpa_direction">
                                <option value="1">Equal or Above this Grade/Marks</option>
                                <option value="2">Equal or Below this Grade/Marks</option>
                            </select>
                        </div><br />
                        <!-- <div class="form-group">
                <select type="text" class="form-control" v-model="filter.gpa_orderby">
                    <option value="3">Highest Marks</option>
                    <option value="4">Lowest Marks</option>
                </select>
            </div> -->

                        <div class="col-md-6">
                            <!-- <div class="form-group" v-for="(eachData, index) in requiredData.marks" :key="index">
                <label>
                    <input class="form-check-input" type="checkbox" v-model="filter['m_' + eachData]" :value="eachData" @change="filterGenderChecked">
                     {{eachData}}
                </label>
            </div> -->
                        </div>

                    </div>
                    <div class="col-md-2">
                        <div class="fw-semibold">Gender</div>
                        <div class="form-group" v-for="(eachData, index) in requiredData.gender" :key="index">
                            <label>
                                <input class="form-check-input" type="checkbox" v-model="filter['g_' + eachData]"
                                    :value="eachData" @change="filterGenderChecked">
                                {{ eachData }}
                            </label>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="fw-semibold">Religion</div>
                        <div class="form-group" v-for="(eachData, index) in requiredData.religion" :key="index">
                            <label>
                                <input class="form-check-input" type="checkbox" v-model="filter['r_' + eachData]"
                                    :value="eachData" @change="filterGenderChecked">
                                {{ eachData }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="fw-semibold">Quota</div>
                        <div class="form-group" v-for="(eachData, index) in requiredData.quota" :key="index">
                            <label>
                                <input class="form-check-input" type="checkbox" v-model="filter['q_' + eachData]"
                                    :value="eachData">
                                {{ eachData }}
                            </label>
                        </div>
                    </div>
                </div>
            </template>

        </sortingModal>

    </div>
</template>




<script>
import DataTable from "../../components/dataTable";
import PageTop from "../../components/pageTop";
// import admission_details from './admission_details';
import formModal from "../../components/formModal";
import sortingModal from "../../components/sortingModal"
import axios from 'axios';


export default {
    name: "admissionRequestList",
    components: { PageTop, DataTable, formModal, sortingModal },
    data() {
        return {
            selectedMultipleData: [],
            checked: false,
            certificate_url: '',
            tableHeading: ['Check All', 'SI', 'Applicant ID', 'Applicant Name', 'Gender', 'DOB', 'Phone No.', 'Religion', 'Fathers Name', 'Mothers Name', 'Class', 'Session', 'Group', 'Shift', 'marks', 'quota', 'Selection Status', 'Action'],
            formModalId: 'formModal',
            defaultFilter: { class_id: '', session_id: '', section_id: '', department_id: '' },
            advanceFilterModal: 'detailsModal',
            advanceFilter: { g_Male: false, g_Female: false, g_Others: false, r_Islam: false, r_Hinduism: false, r_Christianity: false, r_Buddhism: false, r_Judaism: false, r_Sikhism: false, r_Others: false, q_Freedom_Fighter: false, q_Physically_Challenged: false, q_Tribe: false, q_None_Quota: false, gpa: '' },
            applyAdvanceFilter: false,
            totalSeatInfo: {
                totalSeats: '',
                vacantSeats: '',
                totalApplications: '',

            },
            searchDetailsData: {},
            application_count: '',
            selectText: ''
        }
    },

    methods: {
        // getTotalApplication(){
        //     axios.get('/api/get_total_application')
        //     .then((res)=> {
        //         this.application_count = res.data.result.application_count
        //     }).catch((error => {
        //         console.log(error);
        //     }))
        // },
        // async downloadPreviousCertificate(id) {

        //     try {
        //         const apiEndpoint = `/api/download_previous_certificate/${id}`;

        //         const response = await axios.get(apiEndpoint, { responseType: 'blob' });
        //         const blob = new Blob([response.data], { type: 'application/pdf' });

        //         const blobUrl = URL.createObjectURL(blob);
        //         const link = document.createElement('a');
        //         link.href = blobUrl;
        //         link.download = `applicant_${id}_file.pdf`;
        //         link.click();
        //         URL.revokeObjectURL(blobUrl);
        //     } catch (error) {
        //         console.error('Error downloading PDF:', error);
        //     }
        // },

        async downloadPreviousCertificate(id) {
            try {
                const apiEndpoint = `/api/download_previous_certificate/${id}`;

                const response = await axios.get(apiEndpoint, { responseType: 'blob' });
                const blob = new Blob([response.data], { type: 'application/zip' });

                const blobUrl = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = blobUrl;
                link.download = `applicant_${id}_files.zip`;
                link.click();
                URL.revokeObjectURL(blobUrl);
            } catch (error) {
                console.error('Error downloading ZIP archive:', error);
            }
        },

        doApplyAdvanceFilter(apply = true) {
            if (apply) {
                $('#' + this.advanceFilterModal).modal('hide');
                this.getResults()
            } else {
                this.filter = {}
            }
            this.applyAdvanceFilter = apply
        },
        filterGenderChecked(evt) {
            // console.log('checked: ', evt.target.checked, evt.target.value)
            let val = evt.target.value
            if (evt.target.checked) {
                // this.formObject.gender
                // 'Male', 'Female', 'Others'
            }
            // v-model="formObject.gender"
        },
        selectAllCheck(event, all_data) {
            if (this.checked == false) {
                const _this = this;
                $.each(all_data, function (index, item) {
                    _this.selectedMultipleData.push(item.id)
                });
            } else {
                this.checked = false
                this.selectedMultipleData = []
            }
        },
        viewSorting: function () {
            const _this = this;
            _this.openModal('detailsModal', false);
        },
        getResults: function (data = null) {
            const _this = this;
            let combinedFilter = Object.assign({}, this.formFilter, this.formObject)
            this.getDataList()
            // if(data){
            //     _this.searchDetailsData = data;
            // }
            // var studentUrl = `${_this.urlGenerate()}/${_this.searchDetailsData}`;
            // _this.getData(studentUrl, 'get',_this.formObject,_this.formFilter.date, (retData) => {
            //     this.attdenceDetails = retData;
            //     this.openModal('detailsModal', false);
            // });
        },
        multipleStatusChange(type) {
            const _this = this
            if (this.selectedMultipleData) {
                axios.get(`/api/multiple_selected_status_changes/${type}/${this.selectedMultipleData}`)
                    .then(res => {
                        _this.selectedMultipleData = []
                        _this.getDataList();
                        _this.checked = false
                        _this.$toastr('success', 'Successfully Updated', 'Success')
                    })
                    .catch(e => {
                        console.log(e);
                    })
            } else {
                alert('please,check first');
            }

        },
        custom_short_list_status_change(id) {
            axios.get('/api/custom_short_list_status_change/' + id)
                .then(res => {
                    this.getDataList();
                    this.$toastr('success', 'Successfully Short Listed', 'Success')
                })
                .catch(e => {
                    console.log(e);
                })
        },
        custom_preliminary_status(id) {
            axios.get('/api/custom_preliminary_status/' + id)
                .then(res => {
                    this.getDataList();
                    this.$toastr('success', 'Successfully Preliminary Listed', 'Success')
                })
                .catch(e => {
                    console.log(e);
                })
        },
        custom_reject_status(id) {
            axios.get('/api/custom_reject_status/' + id)
                .then(res => {
                    this.getDataList();
                    this.$toastr('success', 'Successfully Rejected', 'Success')
                })
                .catch(e => {
                    console.log(e);
                })
        },
        // admission_details:function(admission_details_id){
        //   this.admission_details_id= admission_details_id;

        // },
        preview_certificate: function (data) {
            this.certificate_url = data.file;
            $('#certificate_modal').modal('show');
        },
    },

    //     mounted() {
    //     sortByGPA() {
    //     // Make an AJAX request to get the sorted students
    //     axios.post('/api/students/sort', { sortOrder: this.formFilter.gpa })
    //         .then(response => {
    //         this.students = response.data.students;
    //         })
    //         .catch(error => {
    //         console.error('Error fetching sorted students:', error);
    //         });
    //     },
    // },

    mounted() {
        this.getDataList();
        // this.getTotalApplication();
        const _this = this;
        _this.getGeneralData([
            "class",
            "session",
            "section",
            "departments",
            "gender",
            "religion",
            "quota",
            "marks",
        ]);
    },
}

</script>

<style scoped>
.img-center {
    display: block;
    margin-left: auto;
    margin-right: auto;
}
</style>