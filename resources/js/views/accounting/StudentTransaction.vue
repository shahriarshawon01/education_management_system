<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" :default-object="{ class_id: '' }"
                    table-title="Student Transaction">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Student Transaction" :default-add-button="false">
                            <div @click="printData('printDiv')">
                                <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i>
                                    Print</button>
                            </div>
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.class_id" name="class_id" class="form-control">
                                <option value="">Select Class</option>
                                <template v-for="(eachData, index) in requiredData.class">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ index + 1 }}</td>
                            <td>{{ data.student_name }}</td>
                            <td>{{ data.class_name }}</td>
                            <td style="text-align: center;">{{ data.student_roll }}</td>
                            <td style="text-align: right;">{{ data.total_payable }}</td>
                            <td style="text-align: right;">{{ data.total_transaction }}</td>
                            <td style="text-align: right;">{{ data.due_amount }}</td>
                            <td style="text-align: right;">{{ data.total_transaction }}</td>
                            <td style="text-align: center;">
                                <div class="hstack gap-3 fs-15" style="text-align: right;">
                                    <a class="btn btn-primary btn-sm"
                                        @click="detailsModalTransaction(data, data.id, 'detailsModal')">View
                                        Transaction</a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <general-modal modal-id="detailsModal" modalSize="modal-lg">
            <template v-slot:title>
                <span>Student Transaction Details</span>
            </template>
            <template v-slot:body>
                <div id="printDetails">
                    <table class="table table-striped">
                        <thead class="baground">
                            <tr>
                                <th style="width: 33%;">Date</th>
                                <th style="width: 33%;">Component</th>
                                <th style="width: 33%;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(data, index) in details.data">
                                <td>{{ data.date }}</td>
                                <td>{{ data.component_name }}</td>
                                <td>{{ data.payable_amount }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </template>
            <template v-slot:buttons>
                <a class="btn btn-outline-primary" @click="printData('printDetails')"><i class="fa fa-print"></i>
                    Print</a>
            </template>
        </general-modal>
    </div>
</template>
<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import GeneralModal from "../../components/generalModal"
export default {
    name: "StudentTransaction",
    components: { PageTop, Pagination, DataTable, GeneralModal },

    data() {
        return {
            tableHeading: ['Sl', ' Student Name ', ' Class', 'Roll ', 'Total Amount ', 'Get Transaction ', 'Due ', 'Paid ', 'Action'],
            details: {},
        };

    },
    methods: {
        detailsModalTransaction: function (data) {
            const _this = this;
            _this.openModal('detailsModal', false);
            var URL = `${_this.urlGenerate('api/transactionView')}/${data.id}`;

            _this.getData(URL, "get", {}, {}, function (retData) {
                _this.details = retData;
                _this.openModal('detailsModal', false);
            });

        },
    },
    mounted() {
        const _this = this;
        _this.getDataList();
        _this.getGeneralData(['class']);
    },
}
</script>
<style scoped>
.baground {
    background: #eaebeb;
}
</style>
