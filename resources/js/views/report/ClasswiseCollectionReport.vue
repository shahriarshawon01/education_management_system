<template :key="$route.meta.name">
    <div class="main_component">
      <div class="row">
        <div class="col">
          <data-table :table-heading="[]" :default-object="{school_id:'',from_date:'',to_date:'',}" :defaultFilter="false" :default-pagination="false" table-title="ClassWise Collection Component" :table-responsive="false">
            <template v-slot:page_top>
              <page-top :default-object="{}" topPageTitle="ClassWise Collection Component" :default-add-button="false" >

                    <button @click="printData('printDiv')" class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i> Print</button>

                    <!-- <a target="_blank" @click="openFile(`${urlGenerate('classwie_collection_excel_report')}?school_id=${formFilter.school_id}&component_id=${formFilter.component_id}&from_date=${formFilter.from_date}&to_date=${formFilter.to_date}`)" class="btn btn-info"><i class="fa fa-file-excel"></i> Download Excel</a> -->
              </page-top>
            </template>
            <template v-slot:filter>
                <div class="col-md-2">
                    <select class="form-control" @change="getComponent" v-model="formFilter.school_id" name="school_id">
                        <option value="">Select Institute</option>
                        <template v-for="(data,index) in requiredData.school">
                            <option :value="data.id">{{data.title}}</option>
                        </template>
                    </select>
                </div>
                <!-- <div class="col-md-2">
                    <select v-model="formFilter.class_id"  @change="getComponent" name="class_id" class="form-control" @click="getGeneralData({students: { class_id: formFilter.class_id}})">
                        <option value="">Select Class</option>
                        <template v-for="(eachData, index) in requiredData.class">
                            <option :value="eachData.id">{{eachData.name}}</option>
                        </template>
                    </select>
                </div> -->
                <div class="col-md-2">
                    <datepicker class="form-control" v-model="formFilter.from_date" placeholder="From Date" name="from_date"></datepicker>
                </div>
                <div class="col-md-2">
                    <datepicker class="form-control" v-model="formFilter.to_date" placeholder="To Date" name="to_date"></datepicker>
                </div>
                <div class="col-md-11">
                    <div class="row mt-4">
                        <div class="col-md-3" v-for="(component,cIndex) in componentData.data" :key="cIndex">
                            <input type="checkbox" :value="component.id" name="component_id" v-model="formFilter.component_id"> <span>{{ component.name }}</span>
                        </div>
                    </div>
                </div>
            </template>
            <template v-slot:reportHeader>
                <div class="row mb-3 mt-3 report_header">
                    <!-- <div class="col-12 mb-3 text-center"><h3> TPSC </h3></div> -->
                    <div class="col-12 mb-3 text-center">
                        <h3> {{ dataList.school_name }} </h3>
                        <span>Code:{{ dataList.code }} STEB Year: {{ dataList.steb_number }}</span><br>
                        <label>{{ dataList.address }}</label>
                    </div>
                    <div class="col-12 mb-3 text-center"><h4> Students Component Collection Report </h4></div>
                    <div class="row">
                        <div class="col-6 text-start">
                        </div>
                        <div class="col-6 text-end">
                            <strong>Print Date: {{ formatDate(new Date()) }}</strong>
                        </div>
                    </div>
                </div>
            </template>
            <template v-slot:bottom_data>
               <template  v-if="dataList.data">
                 <div class="row mb-4">
                        <div class="col-md-6">
                            <label>School Name : {{ dataList.school_name }}</label><br>
                            <label v-if="dataList.formDate">Form Date : {{ dataList.formDate }}</label><br>
                            <label v-if="dataList.toDate">To Date : {{ dataList.toDate }}</label>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th rowspan="2">Sl</th>
                                <th rowspan="2">Class Name</th>
                                <th rowspan="2">Students Count</th>
                                <th class="text-center" v-for="(components, cIndex) in dataList.headerData" :key="cIndex" colspan="2">{{ components.name }}</th>
                                <th colspan="2" class="text-center">Total</th>
                            </tr>
                            <tr>
                                <template v-for="component in dataList.headerData">
                                    <th :key="'count-' + component.id" class="text-center">Count</th>
                                    <th :key="'amount-' + component.id" class="text-center">Amount</th>
                                </template>
                                <th class="text-center">Total Count</th>
                                <th class="text-center">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(data, index) in dataList.data">
                                <td class="fw-medium">{{ index+1 }}</td>
                                <td>{{ data.class_name }}</td>
                                <td>{{ data.student_count }}</td>
                                <template v-for="(tHead, idx) in tableHeaders">
                                    <td :key="idx + '__1'">
                                        {{ getCellValue(tHead, 'count', data.components) }}
                                    </td>
                                    <td :key="idx + '_2'">
                                        {{ getCellValue(tHead, 'amount', data.components) }}
                                    </td>
                                </template>
                                <td>{{ data.component_count }}</td>
                                <td>{{ data.total_amount }}</td>
                            </tr>
                           

                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Grand Total</th>
                                <th>{{ calculateGrandTotals().studentCount }}</th>
                                <template v-for="(header, idx) in tableHeaders">
                                    <th :key="'totalCount-' + idx">{{ calculateGrandTotals().componentCounts[header] }}</th>
                                    <th :key="'totalAmount-' + idx">{{ calculateGrandTotals().componentAmounts[header] }}</th>
                                </template>
                                <th>{{ calculateGrandTotals().totalCount }}</th>
                                <th>{{ calculateGrandTotals().totalAmount }}</th>
                            </tr>
                        </tfoot>
                    </table>
               </template>
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
export default {
    name:"ClasswiseCompnentCollect",
    components: { PageTop, Pagination, DataTable },

    data() {
      return {
        componentData:[],
        component_id: [],
        tableHeaders: [],
      };

    },

    watch: {
        'formFilter.component_id'(newValue) {
            if (newValue.length > 0 && newValue.length !== this.componentData.data.length) {
                this.formFilter.component_id = this.componentData.data.map(component => component.id);
            }
        },
        'dataList.headerData'(val) {
            let headNames = []
            val.forEach(item => headNames.push(item.name))
            this.$set(this, 'tableHeaders', headNames)
        }
    },
    methods: {
        getCellValue(tHead, keyName, compData) {
            let keys = Object.keys(compData);

            for(let i=0; i<keys.length; i++) {
                if(compData[keys[i]].name == tHead) return compData[keys[i]][keyName]
            }
            return 'N/A'
        },
        calculateGrandTotals() {
            if (!this.dataList || !this.dataList.data || !Array.isArray(this.dataList.data)) {
            return {
                studentCount: 0,
                componentCounts: {},
                componentAmounts: {},
                totalCount: 0,
                totalAmount: 0
            };
            }

            let grandTotals = {
            studentCount: 0,
            componentCounts: {},
            componentAmounts: {},
            totalCount: 0,
            totalAmount: 0
            };

            this.dataList.data.forEach((data) => {
            grandTotals.studentCount += data.student_count || 0;
            grandTotals.totalCount += data.component_count || 0;
            grandTotals.totalAmount += data.total_amount || 0;

            this.tableHeaders.forEach((header) => {
                if (!grandTotals.componentCounts[header]) grandTotals.componentCounts[header] = 0;
                if (!grandTotals.componentAmounts[header]) grandTotals.componentAmounts[header] = 0;

                grandTotals.componentCounts[header] += parseFloat(this.getCellValue(header, 'count', data.components)) || 0;
                grandTotals.componentAmounts[header] += parseFloat(this.getCellValue(header, 'amount', data.components)) || 0;
            });
            });

            return grandTotals;
        },
        getComponent(){
            const _this = this;
            var URL = `${_this.urlGenerate('api/reports/get_component_data')}`;
            const paramsData = {
                school_id: _this.formFilter.school_id
            };
            _this.getData(URL, "get", paramsData, {}, function (retData) {
                _this.componentData = retData;
                _this.formFilter.component_id = [];

            });
        },
    },
    mounted() {
        const _this = this;
        _this.getGeneralData(['school']);

    },
}
</script>
<style scoped>
.table thead th {
    background-color: #cccbcb;
    text-align: center;
}

.table tfoot th {
    background-color: #cccbcb;
    text-align: center;
}
</style>
