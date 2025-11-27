<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" :default-object="{school:'',class_id:''}" table-title="Course List">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Course List" page-modal-title="Course Add/Edit" :default-add-button="false">
                            <router-link v-if="can('website_course_add')" to="/admin/website_course/add" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add Course
                            </router-link>
                        </page-top>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{parseInt(dataList.from)+index}}</td>
                            <td>{{ showData(data.website_course, 'name') }}</td>
                            <td>{{ data.student_name }}</td>
                            <td>{{ data.roll_no }}</td>
                            <td>{{ data.reg_no }}</td>
                            <td>{{ data.email }}</td>
                            <td>{{ data.phone }}</td>
                            <td>{{ data.guardian_mobile }}</td>
                            <td>
                                <div v-if="data.gender == 1">Male</div>
                                <div v-else-if="data.gender == 2">FeMale</div>
                                <div v-else-if="data.gender == 3">Other</div>

                            </td>
                            <td>{{ data.start_date }}</td>
                            <td>{{ data.end_date }}</td>
                            <td>
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td>
                                <div class="hstack gap-3 fs-15">
                                    <a class="link-primary" @click="courseDetails(data)"><i class="fa fa-eye"></i></a>
                                    <router-link v-if="can('website_course_update')" class="link-primary" :to="`/admin/website_course/add/${data.id}`"><i class="fa fa-edit"></i></router-link>
                                    <a v-if="can('website_course_delete')" class="link-danger"  @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
                <general-modal modal-id="detailsModal" modalSize="modal-m" :isEnableSubmit="false">
                    <template v-slot:body>
                        <div class="row border_bottom">
                            <label class="col-4">Category Name</label>
                            <div class="col-md-8">
                                <strong>: </strong> <span>{{ showData(details.website_course, 'name') }}</span>
                            </div>
                        </div>
                        <div class="row border_bottom">
                            <label class="col-4">Student Name</label>
                            <div class="col-md-8">
                                <strong>: </strong> <span>{{ (details.student_name) }}</span>
                            </div>
                        </div>
                        <div class="row border_bottom">
                            <label class="col-4">Roll</label>
                            <div class="col-md-8">
                                <strong>: </strong> <span>{{ details.roll_no }}</span>
                            </div>
                        </div>
                        <div class="row border_bottom">
                            <label class="col-4">Registration</label>
                            <div class="col-md-8">
                                <strong>: </strong> <span>{{ details.reg_no }}</span>
                            </div>
                        </div>
                        <div class="row border_bottom">
                            <label class="col-4">Student Email</label>
                            <div class="col-md-8">
                                <strong>: </strong> <span>{{ details.email }}</span>
                            </div>
                        </div>
                        <div class="row border_bottom">
                            <label class="col-4">Phone</label>
                            <div class="col-md-8">
                                <strong>: </strong> <span>{{ details.phone }}</span>
                            </div>
                        </div>
                        <div class="row border_bottom">
                            <label class="col-4">Guardian Mobile</label>
                            <div class="col-md-8">
                                <strong>: </strong> <span>{{ details.guardian_mobile }}</span>
                            </div>
                        </div>
                        <div class="row border_bottom">
                            <label class="col-4">Gender </label>
                            <div class="col-md-8">
                                <label v-if="parseInt(details.gender) === 1"> <strong>: </strong> Male</label>
                                 <label v-if="parseInt(details.gender) === 2"> <strong>: </strong>Female</label>
                                <label v-if="parseInt(details.gender) === 3"> <strong>: </strong>Other</label>
                            </div>
                        </div>
                        <div class="row border_bottom">
                            <label class="col-4">Religion</label>
                            <div class="col-md-8">
                                <label v-if="parseInt(details.religion) === 1"> <strong>: </strong>Muslim</label>
                                <label v-else-if="parseInt(details.religion) === 2"> <strong>: </strong>Hindus</label>
                                <label v-else-if="parseInt(details.religion) === 3"> <strong>: </strong>Christian</label>
                                <label v-else-if="parseInt(details.religion) === 4"> <strong>: </strong>Buddhist</label>
                                <label v-else-if="parseInt(details.religion) === 5"> <strong>: </strong>Others</label>
                            </div>
                        </div>
                        <div class="row border_bottom">
                            <label class="col-4">Start Date</label>
                            <div class="col-md-8">
                                <strong>: </strong> <span>{{ details.start_date }}</span>
                            </div>
                        </div>
                        <div class="row border_bottom">
                            <label class="col-4">End Date</label>
                            <div class="col-md-8">
                                <strong>: </strong> <span>{{ details.end_date }}</span>
                            </div>
                        </div>
                    </template>
                </general-modal>
            </div>
        </div>
    </div>
</template>

<script>
    import DataTable from "../../../components/dataTable";
    import Pagination from "../../../plugins/pagination/pagination";
    import PageTop from "../../../components/pageTop";
    import GeneralModal from "../../../components/generalModal";

    export default {
        name: "courseList",
        components: {PageTop, Pagination, DataTable, GeneralModal},
        data() {
            return {
                tableHeading: ['Sl','Category Name','Student Name','Roll','Registration No','Gmail','Phone','Guardian Mobile Number','Gender','Start Date','End Date','Status', 'Action'],
                details:{}
            }
            
        },
        methods: {
        courseDetails: function (data) {
            const _this = this;
            var URL = `${_this.urlGenerate()}/${data.id}`;
            var title = `${_this.showData(data, 'student_name')}`;
            _this.openModal('detailsModal', title, function () {
                _this.getData(URL, "get", {}, {}, function (retData) {
                    _this.details = retData;
                });
            });
        },
    },
        mounted(){
            this.getDataList();
            this.getGeneralData(['class','departments','sections']);
        }
    }
</script>

<style scoped>
.border_bottom{
        border-bottom: 1px solid #ebebeb !important;
        line-height: 32px !important;
    }
    .border_bottom label{
        margin-bottom: 0 !important;
    }
    .border_bottom strong{
        margin-right: 5px !important;
    }
    .border_bottom label{
        font-weight: 400 !important;
    }

</style>
