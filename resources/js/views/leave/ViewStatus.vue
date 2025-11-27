<template :key="$route.meta.name">
    <div class="main_component">
      <data-table :table-heading="{}" :defaultPagination="false" :default-filter="false" :defaultSearchButton="false" table-title="Leave Status Details">
        <template v-slot:page_top>
                <page-top :default-object="{}" topPageTitle="Leave Status Details" :default-add-button="false" >
                    <button @click="printData('printDiv')" class="btn btn-success"><i class="fa fa-print"></i> Print</button>
                </page-top>
        </template>
        <template v-slot:filter>

        </template>

        <template v-slot:bottom_data>
            <div id="printDiv">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <u><h2 class="text-center">Leave Application Details</h2></u>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6 mt-4">
                                <h4>
                                    <template v-if="details.student">
                                        {{ details.student.student_name_en }}
                                    </template>
                                    <template v-if="details.teacher">
                                        {{ details.teacher.name }}
                                    </template>
                                </h4>
                                <div class="row mb-2">
                                    <div class="col-md-5">
                                        <label>Date </label>
                                    </div>
                                    <div class="col-md-7">
                                        <p>: {{ details.apply_date }}</p>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-5">
                                        <label>Apply To </label>
                                    </div>
                                    <div class="col-md-7">
                                        <p>: {{ details.apply_to }}</p>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-5">
                                        <label>Form Date </label>
                                    </div>
                                    <div class="col-md-7">
                                        <p>: {{ details.from_date }}</p>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-5">
                                        <label>To Date </label>
                                    </div>
                                    <div class="col-md-7">
                                        <p>: {{ details.to_date }}</p>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-5">
                                        <label>Total Days </label>
                                    </div>
                                    <div class="col-md-7">
                                        <p>: {{ details.no_of_days }}</p>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-5">
                                        <label>Leave Category </label>
                                    </div>
                                    <div class="col-md-7">
                                        <p v-if="details.category">: {{ details.category.title }}</p>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-5">
                                        <label>Reason </label><br>
                                    </div>
                                    <div class="col-md-7">
                                        <p>: {{ details.note }}</p>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-5">
                                        <label>File </label><br>
                                    </div>
                                    <div class="col-md-7">
                                        <!-- <p>: {{ details.file }}</p> -->
                                        <p>: <a v-if="details.file" @click="openFile(getImage(details.file))">
                                                <i class="fa fa-download"> File</i>
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mt-4">
                                <h4>Approved Information</h4>
                                <div class="row mb-4">
                                    <table class="table table-bordered table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Status</th>
                                                <th>From Date</th>
                                                <th>To Date</th>
                                                <th>Total Days</th>
                                                <th>Leave Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(row, index) in details.approveData" :key="index">
                                                <td>
                                                    <a class="badge badge-soft-danger" v-if="parseInt(row.status) === 1">Pending</a>
                                                    <a v-if="parseInt(row.status) === 3">
                                                        <span class="badge badge-soft-danger">Declined</span>
                                                    </a>
                                                    <a v-if="parseInt(row.status) === 2">
                                                        <span class="badge badge-soft-success">Approved</span>
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    {{ row.leave_from }}
                                                </td>
                                                <td class="text-center">
                                                    {{ row.leave_to }}
                                                </td>
                                                <td>
                                                    {{ row.approve_days }}
                                                </td>
                                                <td>
                                                    <a v-if="parseInt(row.leave_type) === 1">
                                                        <span class="badge badge-soft-success">With Pay</span>
                                                    </a>
                                                    <a v-if="parseInt(row.leave_type) === 2">
                                                        <span class="badge badge-soft-warning">Without Pay</span>
                                                    </a>
                                                    <a v-if="parseInt(row.leave_type) === 3">
                                                        <span class="badge badge-soft-danger">Cancel</span>
                                                    </a>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                                <h4>Leave Balance</h4>
                                <div class="row">
                                    <table class="table table-bordered table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Total Leave</th>
                                                <th>Apply Leave</th>
                                                <th>Approved Leave</th>
                                                <th>Balance</th>
                                                <th>Leave Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(row, index) in details.balanceLeave" :key="index">
                                                <td class="text-center">
                                                  {{ row.total_leave }}
                                                </td>
                                                <td class="text-center">
                                                    {{ row.applied_leave }}
                                                </td>
                                                <td class="text-center">
                                                    {{ row.approved }}
                                                </td>
                                                <td class="text-center">
                                                    {{ row.balance }}
                                                </td>
                                                <td>
                                                   {{ row.title }}
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </template>
        </data-table>
    </div>
  </template>
  <script>
  import DataTable from "../../components/dataTable";
    import Pagination from "../../plugins/pagination/pagination";
    import PageTop from "../../components/pageTop";
  import axios from 'axios';
  export default {
    name: "ViewStatus",
    components: { DataTable,PageTop,Pagination },
    data() {
      return {
        details: {},
      };
    },
    methods: {
        showData() {
            const _this = this;
            axios.get(`/api/apply_leave/${_this.$route.params.id}`)
            .then((response) => {
                _this.$set(_this, 'details', response.data.result);
            })
            .catch((error) => {
                console.error("Failed to fetch leave details", error);
            });
        },

    },
    mounted() {
        this.showData();
    },
  };
  </script>

  <style scoped>
.headingData{
    background: #f1f1f1;
    padding: 8px 10px 10px 10px;
}
h4{
    font-weight: bold !important;
}

</style>
