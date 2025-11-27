<template :key="$route.meta.name">
  <div class="main_component">
    <div class="row">
      <div class="col">
        <data-table :defaultSearchButton="false" :table-heading="[]" :default-object="{
          class_id: '',
          session_year_id: '',
          section_id: '',
          department_id: '',
          from_date: '',
          to_date: '',
        }" :defaultFilter="false" :default-pagination="false" table-title="Student's Aging Report"
          :table-responsive="false">
          <template v-slot:page_top>
            <page-top :default-object="{}" topPageTitle="Student Aging Report" :default-add-button="false">
              <div @click="printData('printDiv')">
                <button class="btn btn-outline-primary">
                  <i class="fa-sharp fa-solid fa-print"></i> Print
                </button>
              </div>
            </page-top>
          </template>
          <template v-slot:filter>
            <div class="col-md-1">
              <select v-model="formFilter.class_id" name="class_id" class="form-control" @change="getGeneralData({
                students: { class_id: formFilter.class_id }, section: { class_id: formFilter.class_id },
                departments: { class_id: formFilter.class_id },
              })">
                <option value="">Class</option>
                <template v-for="(eachData, index) in requiredData.class">
                  <option :value="eachData.id">{{ eachData.name }}</option>
                </template>
              </select>
            </div>
            <div class="col-md-1">
              <select class="form-control" v-model="formFilter.session_year_id" name="session_year_id">
                <option value="">Session</option>
                <template v-for="(data, index) in requiredData.session">
                  <option :value="data.id">{{ data.title }}</option>
                </template>
              </select>
            </div>
            <div class="col-md-1">
              <div>
                <select v-model="formFilter.section_id" name="section_id" class="form-control">
                  <option value="">Section</option>
                  <template v-for="(eachData, index) in requiredData.section">
                    <option :value="eachData.id">{{ eachData.name }}</option>
                  </template>
                </select>
              </div>
            </div>
            <div class="col-md-1">
              <div>
                <select v-model="formFilter.department_id" name="department_id" class="form-control">
                  <option value="">Department</option>
                  <template v-for="(eachData, index) in requiredData.departments">
                    <option :value="eachData.id">{{ eachData.department_name }}</option>
                  </template>
                </select>
              </div>
            </div>

            <div class="col-md-1">
              <datepicker class="form-control" v-model="formFilter.from_date" placeholder="From Date" name="from_date">
              </datepicker>
            </div>
            <div class="col-md-1">
              <datepicker class="form-control" v-model="formFilter.to_date" placeholder="To Date" name="to_date">
              </datepicker>
            </div>

            <div class="col-md-2">
              <button class="btn btn-primary" @click.prevent.stop="getAgingData" :disabled="loading">Get Aging</button>
            </div>
            <Loader :visible="loading" />
          </template>

          <template v-slot:bottom_data>
            <template v-if="Object.keys(agingData).length > 0">
              <!-- Reusable School Info Component -->
              <schoolInfo title="Students Aging Report" />
              <div class="row mb-4">
                <div class="col-md-6">
                  <div class="student-details-card">
                    <div v-if="formFilter.from_date != '' && formFilter.to_date != ''" class="student-detail">
                      <strong>Reporting Period:</strong>
                      <span>{{ formFilter.from_date }} &nbsp; - <span>{{
                        formFilter.to_date
                          }}</span></span>
                    </div>
                    <div class="student-detail">
                      <strong>Print Date:</strong>
                      <span>{{ formatDate(new Date()) }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <div style="overflow-x: auto;">
                <table class="table table-bordered grand-total-style">
                  <thead>
                    <tr>
                      <th style="vertical-align: middle;" rowspan="2">Sl</th>
                      <th style="vertical-align: middle;" rowspan="2">Class</th>
                      <th style="vertical-align: middle;" rowspan="2">Total Student</th>
                      <th style="vertical-align: middle;" rowspan="2">Total Due Student</th>
                      <th style="vertical-align: middle;" rowspan="2">Total Receivable</th>
                      <th colspan="6">Aging</th>
                      <th style="vertical-align: middle;" rowspan="2">Remarks</th>
                    </tr>

                    <tr>
                      <th>(1 - 30) Days</th>
                      <th>(31 - 90) Days</th>
                      <th>(91 - 180) Days</th>
                      <th>(181 - 365) Days</th>
                      <th>Above One Year</th>
                      <th style="vertical-align: middle;">Total</th>
                    </tr>

                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in Object.keys(agingData)" :key="item">
                      <td>{{ index + 1 }}</td>

                      <td role="button" tabindex="0" @click="fetchDueStudents(agingData[item].class_id)"
                        @keyup.enter="fetchDueStudents(agingData[item].class_id)" class="class-name-cell"
                        style="position: relative;">
                        <div class="d-flex align-items-center justify-content-between">
                          <span class="class-name-text">
                            {{ agingData[item].class_name }}
                          </span>
                        </div>
                      </td>
                      <td style="width: 8%; text-align: center;">
                        {{ agingData[item].total_students }}
                      </td>
                      <td style="width: 8%; text-align: center;">
                        {{ agingData[item].due_students }}
                      </td>
                      <td style="text-align: right; width: 8%;">
                        {{ formatCurrency(agingData[item].total_receivable) }}
                      </td>
                      <td style="text-align: right; width: 15%;">
                        {{ formatCurrency(agingData[item]['1_30_days']) }}</td>
                      <td style="text-align: right; width: 15%;">
                        {{ formatCurrency(agingData[item]['31_90_days']) }}
                      </td>
                      <td style="text-align: right; width: 15%;">
                        {{ formatCurrency(agingData[item]['91_180_days']) }}
                      </td>
                      <td style="text-align: right; width: 15%;">
                        {{ formatCurrency(agingData[item]['181_365_days']) }}
                      </td>
                      <td style="text-align: right; width: 15%;">
                        {{ formatCurrency(agingData[item].above_one_year) }}
                      </td>
                      <td style="text-align: right; width: 8%;">
                        {{ formatCurrency(agingData[item].total) }}
                      </td>
                      <td></td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th colspan="2">Grand Totals</th>
                      <th>{{ totalStudentsSum }}</th>
                      <th>{{ grandTotal.grand_total_due_student }}</th>
                      <th>{{ formatCurrency(grandTotal.total_receivable) }}</th>
                      <th>{{ formatCurrency(grandTotal['1_30_days']) }}</th>
                      <th>{{ formatCurrency(grandTotal['31_90_days']) }}</th>
                      <th>{{ formatCurrency(grandTotal['91_180_days']) }}</th>
                      <th>{{ formatCurrency(grandTotal['181_365_days']) }}</th>
                      <th>{{ formatCurrency(grandTotal.above_one_year) }}</th>
                      <th style="text-align: center;">{{ formatCurrency(grandTotal.total_grand_total) }}</th>
                      <th>-</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </template>
          </template>
        </data-table>
      </div>
    </div>

    <general-modal modal-id="detailsModal" modalSize="modal-lg">
      <template v-slot:title>
        <span>Due Students</span>
      </template>
      <template v-slot:body>
        <div id="printReceipt">
          <div class="d-flex justify-content-between align-items-center mb-3" style="margin-top: -8px;">
            <h5 class="mb-0 due-student-bg">Total Due Students: {{ dueStudents.length }}</h5>
            <button class="btn btn-outline-primary no-print" @click="printData('printReceipt')">
              <i class="fas fa-print"></i> Print
            </button>
          </div>

          <table class="table table-bordered table-hover grand-total-style" style="margin-top: -10px;">
            <thead class="thead-light">
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Class</th>
                <th>Section</th>
                <th>Session</th>
                <th>Department</th>
                <th class="text-right">Due</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(student, index) in dueStudents" :key="student.student_id">
                <td>{{ index + 1 }}</td>
                <td>{{ student.student_name }} <strong>[{{ student.student_roll }}]</strong></td>
                <td>{{ student.class_name }}</td>
                <td>{{ student.section_name }}</td>
                <td style="text-align: center;">{{ student.session_name }}</td>
                <td>{{ student.department_name }}</td>
                <td class="text-right" style="text-align: right;">{{ formatCurrency(student.total_due) }}</td>
              </tr>
              <tr v-if="dueStudents.length === 0">
                <td colspan="7" class="text-center text-muted py-4">
                  No due students found for this class
                </td>
              </tr>
            </tbody>
            <tfoot v-if="dueStudents.length > 0">
              <tr>
                <th colspan="6" class="text-right">Total Due </th>
                <th class="text-right" style="text-align: right;">{{ formatCurrency(totalDueAmount) }}</th>
              </tr>
            </tfoot>
          </table>

        </div>
      </template>
    </general-modal>

  </div>
</template>
<script>
import DataTable from "../../../components/dataTable";
import PageTop from "../../../components/pageTop";
import GeneralModal from "../../../components/generalModal";
import schoolInfo from "../../../components/schoolInfo.vue";
import Loader from "../../../components/loader.vue"
import axios from 'axios';
export default {
  name: "agingReport",
  components: { PageTop, DataTable, GeneralModal, schoolInfo, Loader },

  data() {
    return {
      agingData: [],
      grandTotal: [],
      dueStudents: [],
      selectedClassName: '',
      selectedClassId: null,
      hoverClass: null,
      loading: false,
    };
  },

  methods: {
    formatDate(date) {
      let day = String(date.getDate()).padStart(2, "0");
      let month = String(date.getMonth() + 1).padStart(2, "0");
      let year = date.getFullYear();

      return `${day}-${month}-${year}`;
    },

    formatCurrency(value) {
      return value ? value.toLocaleString() : "0";
    },

    getAgingData() {
      const { class_id, session_year_id, section_id, department_id, from_date, to_date } = this.formFilter;

      this.loading = true;
      this.axios.get('/api/reports/aging_report', {
        params: { class_id, session_year_id, section_id, department_id, from_date, to_date }
      }).then(response => {
        const aging = response.data.result || {};
        this.agingData = aging.data || [];

        this.grandTotal = aging.grand_totals || [];
      })
        .catch(error => {
          console.error("Error fetching due students:", error);
          this.$toastr('error', 'Failed to load student data', 'Error');
        })
        .finally(() => {
          this.loading = false;
        });
    },

    fetchDueStudents(class_id) {
      if (!class_id) {
        console.warn("class_id is undefined. Cannot continue.");
        return;
      }

      this.selectedClassId = class_id;
      this.loading = true;
      this.axios.get('/api/reports/due_students', {
        params: {
          class_id: class_id,
          session_year_id: this.formFilter.session_year_id,
          section_id: this.formFilter.section_id,
          department_id: this.formFilter.department_id
        }
      })
        .then(response => {
          if (response.data.result?.due_students?.length) {
            this.dueStudents = response.data.result.due_students;
            $('#detailsModal').modal('show');
          } else {
            this.$toastr('info', 'No due students found for this class', 'Information');
          }
        })
        .catch(error => {
          console.error("Error fetching due students:", error);
          this.$toastr('error', 'Failed to load student data', 'Error');
        })
        .finally(() => {
          this.loading = false;
        });
    }
  },

  computed: {
    totalStudentsSum() {
      return Object.values(this.agingData).reduce((sum, item) => sum + (item.total_students || 0), 0);
    },
    totalDueStudents() {
      return this.dueStudents.length;
    },
    totalDueAmount() {
      return this.dueStudents.reduce((sum, student) => sum + (student.total_due || 0), 0);
    }
  },

  mounted() {
    const _this = this;
    _this.getGeneralData(['class', 'session']);
  },
};
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

.report-header {
  text-align: center;
  margin-bottom: 6px;
}

.report-header .school-name {
  font-size: 20px;
  font-weight: bold;
  color: #2c3e50;
  margin: 0;
}

.report-header .school-address {
  font-size: 16px;
  color: #7f8c8d;
  margin: 2px 0;
}

.report-header .report-title {
  font-size: 18px;
  font-weight: 600;
  color: #34495e;
  text-transform: uppercase;
  border-top: 1px solid #bdc3c7;
  border-bottom: 1px solid #bdc3c7;
  padding: 6px 0;
  display: inline-block;
  margin-top: 0px;
}

.class-name-cell {
  cursor: pointer;
  transition: all 0.3s ease;
  padding: 12px 15px;
  color: var(--primary-color);
  background-color: rgba(0, 123, 255, 0.05);
  border-radius: 4px;
}

.class-name-cell:hover {
  color: #fff;
  background-color: #3b6e67;
  transform: translateY(-1px);
}

.class-name-cell:focus {
  outline: 2px solid rgba(0, 123, 255, 0.3);
  outline-offset: 2px;
}

.class-name-text {
  position: relative;
  font-weight: 500;
}

.class-name-text::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 0;
  height: 1px;
  background-color: currentColor;
  transition: width 0.3s ease;
}

.class-name-cell:hover .class-name-text::after {
  width: 100%;
}

.link-icon {
  font-size: 0.8rem;
  opacity: 0.7;
  transition: transform 0.3s ease;
}

.class-name-cell:hover .link-icon {
  transform: translateX(3px);
  opacity: 1;
}

.due-student-bg {
  background-color: #3b6e67;
  padding: 6px 10px;
  border-radius: 7px;
  display: inline-block;
  color: #fff;
}

.student-details-card {
  margin-bottom: -20px;
}

.student-detail {
  margin-bottom: 2px;
  font-size: 16px;
  color: #34495e;
}

.student-detail strong {
  color: #2c3e50;
}

.student-detail span {
  margin-left: 10px;
  font-weight: 600;
  color: #7f8c8d;
}

@media print {
  .no-print {
    display: none !important;
  }

  .table,
  .table th,
  .table td {
    border: 1px solid #000;
    border-collapse: collapse;
  }
}
</style>
