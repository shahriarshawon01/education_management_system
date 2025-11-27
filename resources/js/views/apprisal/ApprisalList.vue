<template :key="$route.meta.name">
  <div class="main_component">
    <div class="row">
      <div class="col">
        <data-table :table-heading="tableHeading" table-title="Appraisal List">
          <template v-slot:page_top>
            <page-top
              :default-object="{
                apprisal_type: '1',
                student_id: '',
                class_id: '',
                teacher_id: '',
                staff_id: '',
                template_id: '',
              }"
              page-modal-title="Appraisal Add/Edit"
              topPageTitle="Appraisal List"
              :default-add-button="can('appraisal_add')"
            ></page-top>
          </template>
          <template v-slot:filter> </template>
          <template v-slot:data>
            <tr v-for="(data, index) in dataList.data">
              <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
              <td>
                <span v-if="data.apprisal_type == 1">Students</span>
                <span v-if="data.apprisal_type == 2">Teachers</span>
                <span v-if="data.apprisal_type == 3">Staffs</span>
              </td>
              <td v-if="data.student">
                {{ showData(data.student, "student_name_en") }}
              </td>
              <td v-else-if="data.teacher">
                {{ showData(data.teacher, "name") }}
              </td>
              <td v-else>
                {{ showData(data.staff, "name") }}
              </td>
              <td>{{ showData(data.template, "apprisal_for") }}</td>
              <td>{{ data.start_date }}</td>
              <td>{{ data.end_date }}</td>
              <td>{{ data.converted }}</td>
              <td>{{ data.total_score }}</td>
              <td>{{ Math.round(data.cgpa) }}</td>
              <td>{{ data.remark }}</td>
              <td>
                <a
                  @click="changeStatus(data)"
                  v-html="showStatus(data.status)"
                ></a>
              </td>
              <td>
                <div class="hstack gap-3 fs-15">
                  <a class="link-primary" @click="viewDetails(data)"
                    ><i class="fa fa-eye"></i
                  ></a>
                  <a
                    v-if="can('appraisal_update')"
                    class="link-primary"
                    @click="editDataInformation(data, data.id)"
                    ><i class="fa fa-edit"></i
                  ></a>
                  <a
                    v-if="can('appraisal_delete')"
                    class="link-danger"
                    @click="deleteInformation(index, data.id)"
                    ><i class="fa fa-trash"></i
                  ></a>
                </div>
              </td>
            </tr>
          </template>
        </data-table>
      </div>
    </div>
    <formModal modalSize="modal-lg" @submit="submitData">
      <div class="mb-2">
        <div class="row mb-2">
          <div class="col-md-4">
            <input type="hidden" v-model="formObject.id" />
            <label class="form-label">Apprisal Type :</label>
          </div>
          <div class="col-md-8">
            <select
              class="form-control"
              v-model="formObject.apprisal_type"
              name="apprisal_type"
              v-validate="'required'"
            >
              <option value="">Select</option>
              <option value="1">Students</option>
              <option value="2">Teachers</option>
              <option value="3">Staffs</option>
            </select>
          </div>
        </div>
      </div>
      <div class="mb-2" v-if="formObject.apprisal_type == 1">
        <div class="row mb-2">
          <div class="col-md-4">
            <label class="form-label">Class :</label>
          </div>
          <div class="col-md-8">
            <select
              v-model="formObject.class_id"
              name="class_id"
              class="form-control"
              v-validate="'required'"
              data-vv-as="class_id"
              @change="
                getGeneralData({
                  students: { class_id: formObject.class_id },
                })
              "
            >
              <option value="">Select Class</option>
              <template v-for="(eachData, index) in requiredData.class">
                <option :value="eachData.id">{{ eachData.name }}</option>
              </template>
            </select>
          </div>
        </div>
      </div>

      <div class="mb-2">
        <div class="row mb-2">
          <div class="col-md-4">
            <label class="form-label">Apprisal For :</label>
          </div>
          <div class="col-md-8" v-if="formObject.apprisal_type == '1'">
            <select
              v-select2
              class="form-control"
              v-model="formObject.student_id"
              name="student_id"
              v-validate="'required'"
              required
            >
              <option value="">Select Student</option>
              <template v-for="(data, index) in requiredData.students">
                <option :value="data.id">
                  {{ data.student_name_en }} (<b
                    >ROLL : {{ data.student_roll }}</b
                  >)
                </option>
              </template>
            </select>
          </div>
          <div class="col-md-8" v-if="formObject.apprisal_type == '2'">
            <select
              v-select2
              class="form-control"
              v-model="formObject.teacher_id"
              name="teacher_id"
              v-validate="'required'"
              required
            >
              <option value="">Select Teachers</option>
              <template v-for="(data, index) in requiredData.teachers">
                <option :value="data.id">
                  {{ data.name }} (<b>EMP ID: {{ data.employee_id }}</b> )
                </option>
              </template>
            </select>
          </div>
          <div class="col-md-8" v-if="formObject.apprisal_type == '3'">
            <select
              v-select2
              class="form-control"
              v-model="formObject.staff_id"
              name="staff_id"
              v-validate="'required'"
              required
            >
              <option value="">Select Staff</option>
              <template v-for="(data, index) in requiredData.staff">
                <option :value="data.id">
                  {{ data.name }} (<b>EMP ID: {{ data.employee_id }}</b> )
                </option>
              </template>
            </select>
          </div>
        </div>
      </div>
      <div class="mb-2">
        <div class="row mb-2">
          <div class="col-md-4">
            <label class="form-label">Apprisal Template :</label>
          </div>
          <div class="col-md-8">
            <select
              class="form-control"
              v-model="formObject.template_id"
              @change="getTemplateData"
              name="template_id"
              v-validate="'required'"
              required
            >
              <option value="">Select Template</option>
              <template v-for="(data, index) in requiredData.apprisalTemplte">
                <option :value="data.id">{{ data.apprisal_for }}</option>
              </template>
            </select>
          </div>
        </div>
      </div>
      <div class="mb-2">
        <div class="row mb-2">
          <div class="col-md-4">
            <label class="form-label">Start Date :</label>
          </div>
          <div class="col-md-8">
            <datepicker
              v-model="formObject.start_date"
              name="start_date"
              input_class="form-control"
              v-validate="'required'"
              required
            >
              <input
                slot="input"
                class="form-control"
                placeholder="Select a date"
                required
              />
            </datepicker>
          </div>
        </div>
      </div>
      <div class="mb-4">
        <div class="row mb-2">
          <div class="col-md-4">
            <label class="form-label">End Date :</label>
          </div>
          <div class="col-md-8">
            <datepicker
              v-model="formObject.end_date"
              name="end_date"
              input_class="form-control"
              v-validate="'required'"
              required
            >
              <input
                slot="input"
                class="form-control"
                placeholder="Select a date"
                required
              />
            </datepicker>
          </div>
        </div>
      </div>
      <h5>Goals</h5>
      <hr />

      <table class="table table-bordered">
        <thead>
          <tr>
            <th style="width: 40% !important;">Goal</th>
            <th style="width: 40%">Weightage(%)</th>
            <th style="width: 20%">Score</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(data, index) in details.mergedData" :key="index">
            <td>
              <input
                type="text"
                class="form-control"
                v-model="data.kra"
                name="kra"
                readonly
              />
            </td>
            <td>
              <input
                type="text"
                class="form-control"
                v-model="data.weightage"
                name="weightage"
                readonly
              />
            </td>
            <td>
              <input
                type="text"
                v-model="data.get_mark"
                @input="totalCalculate(data, index)"
                class="form-control"
                name="get_mark"
                required
              />
            </td>
          </tr>
        </tbody>
      </table>

      <div class="mb-2 mt-2">
        <div class="row mb-2">
          <div class="col-md-4">
            <label class="form-label">Converted :</label>
          </div>
          <div class="col-md-8">
            <input
              type="text"
              class="form-control"
              v-model="formObject.converted"
              placeholder="Convert"
              name="converted"
              required
            />
          </div>
        </div>
      </div>
      <div class="mb-2">
        <div class="row mb-2">
          <div class="col-md-4">
            <label class="form-label">Total Score :</label>
          </div>
          <div class="col-md-8">
            <input
              type="text"
              class="form-control"
              v-model="formObject.total_score"
              name="total_score"
              readonly
            />
          </div>
        </div>
      </div>
      <div class="mb-2">
        <div class="row mb-2">
          <div class="col-md-4">
            <label class="form-label">CGPA :</label>
          </div>
          <div class="col-md-8">
            <label
              >{{ Math.round(cgpaGrade) }} Out of
              {{ formObject.converted }}</label
            >
          </div>
        </div>
      </div>
      <div class="mb-2">
        <div class="row mb-2">
          <div class="col-md-4">
            <label class="form-label">Remark :</label>
          </div>
          <div class="col-md-8">
            <input
              type="text"
              class="form-control"
              placeholder="Remark"
              v-model="formObject.remark"
              name="remark"
              required
            />
          </div>
        </div>
      </div>
    </formModal>

    <general-modal modal-id="detailsModal" modalSize="modal-lg">
      <template v-slot:title>
        <span>Apprisal Details</span>
      </template>
      <template v-slot:body>
        <table class="table table-striped apprisan_details">
          <tr>
            <th class="thedcss">Apprisal For</th>
            <th>:</th>
            <td>
              <span
                v-if="viewData.apprisal_type == 1"
                style="padding: 0 !important"
                >Students</span
              >
              <span
                v-if="viewData.apprisal_type == 2"
                style="padding: 0 !important"
                >Teachers</span
              >
              <span
                v-if="viewData.apprisal_type == 3"
                style="padding: 0 !important"
                >Staffs</span
              >
            </td>
          </tr>
          <tr>
            <th class="thedcss">Apprisal Name</th>
            <th>:</th>

            <td v-if="viewData.student">
              {{ showData(viewData.student, "student_name_en") }}
            </td>
            <td v-else-if="viewData.teacher">
              {{ showData(viewData.teacher, "name") }}
            </td>
            <td v-else>
              {{ showData(viewData.staff, "name") }}
            </td>
          </tr>
          <tr>
            <th class="thedcss">Apprisal Template</th>
            <th>:</th>
            <td>{{ showData(viewData.template, "apprisal_for") }}</td>
          </tr>
          <tr>
            <th class="thedcss">Converted</th>
            <th>:</th>
            <td>{{ viewData.converted }}</td>
          </tr>
          <tr>
            <th class="thedcss">Total Score</th>
            <th>:</th>
            <td>{{ viewData.total_score }}</td>
          </tr>
          <tr>
            <th class="thedcss">CGPA</th>
            <th>:</th>
            <td>{{ viewData.cgpa }}</td>
          </tr>
          <tr>
            <th class="thedcss">Start Date</th>
            <th>:</th>
            <td>{{ viewData.start_date }}</td>
          </tr>
          <tr>
            <th class="thedcss">End Date</th>
            <th>:</th>
            <td>{{ viewData.end_date }}</td>
          </tr>
          <tr>
            <th class="thedcss">Remark</th>
            <th>:</th>
            <td>
              <strong
                style="
                  background-color: rgb(241 241 241);
                  border-radius: 4px;
                  padding: 0 4px;
                "
                >{{ viewData.remark }}</strong
              >
            </td>
          </tr>
        </table>
        <table
          class="table table-striped table-bordered apprisal_individual_score"
        >
          <thead class="table-light">
            <tr>
              <th style="width: 33%">Goal</th>
              <th style="width: 33%">Weightage(%)</th>
              <th style="width: 33%">Score</th>
            </tr>
          </thead>
          <tbody
            v-if="
              viewData.template &&
              viewData.template.kra &&
              viewData.template.wheightage
            "
          >
            <tr v-for="(kmark, index) in viewData.template.kra" :key="index">
              <td>
                <label>{{ kmark }}</label>
              </td>
              <td>
                <label>{{ viewData.template.wheightage[index] }}</label>
              </td>
              <td>
                <label
                  v-if="viewData.get_mark && viewData.get_mark.length > index"
                >
                  {{ viewData.get_mark[index] }}
                </label>
                <label v-else>-</label>
                <!-- Fallback for missing get_mark -->
              </td>
            </tr>
          </tbody>
        </table>
      </template>
    </general-modal>
  </div>
</template>
<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";
import GeneralModal from "../../components/generalModal";
export default {
  name: "ApprisalList",
  components: { PageTop, Pagination, DataTable, formModal, GeneralModal },
  data() {
    return {
      tableHeading: [
        "Sl",
        "Apprisal For",
        "Apprisal Name",
        "Apprisal Template",
        "Start Date",
        "End Date",
        "Converted",
        "Total Score",
        "CGPA",
        "Remark",
        "Status",
        "Action",
      ],
      formModalId: "formModal",
      details: {},
      viewData: {},
      cgpaGrade: 0,
    };
  },
  watch: {
    "formObject.apprisal_type": function (newType) {
      this.$validator.reset();

      if (newType == "1") {
        this.formObject.teacher_id = null;
        this.formObject.staff_id = null;
        this.$nextTick(() => {
          // this.$validator.attach({ name: 'student_id', rules: 'required' }); // Attach validation for student_id
          this.$validator.detach("teacher_id");
          this.$validator.detach("staff_id");
        });
      } else if (newType == "2") {
        this.formObject.student_id = null;
        this.formObject.staff_id = null;
        this.$nextTick(() => {
          // this.$validator.attach({ name: 'teacher_id', rules: 'required' }); // Attach validation for teacher_id
          this.$validator.detach("student_id");
          this.$validator.detach("staff_id");
        });
      } else if (newType == "3") {
        this.formObject.student_id = null;
        this.formObject.teacher_id = null;
        this.$nextTick(() => {
          // this.$validator.attach({ name: 'staff_id', rules: 'required' }); // Attach validation for staff_id
          this.$validator.detach("student_id");
          this.$validator.detach("teacher_id");
        });
      }
    },

    "formObject.converted"(val) {
      if (!val) return;

      const totalScore = parseFloat(this.formObject.total_score) || 0;
      const converted = parseFloat(val) || 0;

      if (isNaN(totalScore) || isNaN(converted) || converted === 0) {
        this.cgpaGrade = 0;
      } else {
        this.cgpaGrade = (totalScore * converted) / 100;
      }
    },
  },

  methods: {
    submitData() {
      const _this = this;
      const getMarks =
        _this.details && Array.isArray(_this.details.mergedData)
          ? _this.details.mergedData.map((item) => item.get_mark)
          : [];

      const requestData = {
        id: _this.formObject.id ? _this.formObject.id : null,
        apprisal_type: _this.formObject.apprisal_type,
        class_id: _this.formObject.class_id,
        student_id: _this.formObject.student_id,
        teacher_id: _this.formObject.teacher_id,
        staff_id: _this.formObject.staff_id,
        template_id: _this.formObject.template_id,
        start_date: _this.formObject.start_date,
        end_date: _this.formObject.end_date,
        converted: _this.formObject.converted,
        total_score: _this.formObject.total_score,
        remark: _this.formObject.remark,
        cgpa: _this.cgpaGrade,
        get_mark: getMarks,
      };

      if (_this.formObject.apprisal_type == "1") {
        delete requestData.teacher_id;
        delete requestData.staff_id;
      } else if (_this.formObject.apprisal_type == "2") {
        delete requestData.student_id;
        delete requestData.staff_id;
      } else if (_this.formObject.apprisal_type == "3") {
        delete requestData.student_id;
        delete requestData.teacher_id;
      }

      this.submitForm(requestData, "formModal");
    },

    editDataInformation: function (data, id) {
      const _this = this;

      _this.$set(_this.details, "mergedData", []);

      _this.editData(data, id, "formModal", function (retData) {
        var editUrl = `${_this.urlGenerate()}/${id}/edit`;

        _this.getData(editUrl, "get", {}, {}, function (retData) {
          
          if (Array.isArray(retData.mergedData)) {
            const newMergedData = retData.mergedData.map((item) => ({
              kra: item.kra || "",
              weightage: item.weightage || "",
              get_mark: item.get_mark || "",
            }));

            _this.$set(_this.details, "mergedData", newMergedData);
          } else {
            _this.$set(_this.details, "mergedData", []);
          }

          _this.$set(
            _this.formObject,
            "apprisal_type",
            Number(retData.apprisal_type)
          );

          _this.$set(_this.formObject, "class_id", retData.class_id || null);

          if (retData.apprisal_type == 1) {
            _this.getGeneralData(
              {
                students: { class_id: retData.apprisalData.class_id },
              },
              function () {
                _this.$set(_this.formObject, "student_id", retData.student_id);
              }
            );
          } else if (retData.apprisal_type == 2) {
            _this.getGeneralData(
              {
                teachers: {},
              },
              function () {
                _this.$set(_this.formObject, "teacher_id", retData.teacher_id);
              }
            );
          } else if (retData.apprisal_type == 3) {
            _this.getGeneralData(
              {
                staff: {},
              },
              function () {
                _this.$set(_this.formObject, "staff_id", retData.staff_id);
              }
            );
          }
        });
      });
    },

    getTemplateData() {
      const _this = this;
      var URL = `${_this.urlGenerate("api/getDataByTemplateId")}`;
      const paramsData = {
        template_id: _this.formObject.template_id,
      };
      _this.getData(URL, "get", paramsData, {}, function (retData) {
        _this.details = retData;
      });
    },
    totalCalculate() {
      const _this = this;
      let totalMark = 0;

      _this.details.mergedData.forEach((component) => {
        const markValue = parseFloat(component.get_mark || 0);
        totalMark += markValue;
      });

      _this.$set(_this.formObject, "total_score", totalMark);
    },

    // viewDetails: function (data) {
    //   const _this = this;
    //   _this.openModal("detailsModal", false);
    //   var URL = `${_this.urlGenerate()}/${data.id}`;
    //   _this.getData(URL, "get", {}, {}, function (retData) {
    //     _this.viewData = retData;
    //     _this.openModal("detailsModal", false);
    //   });
    // },
    viewDetails: function (data) {
      const _this = this;
      _this.viewData = {
        template: {
          kra: [],
          wheightage: [],
        },
        get_mark: [],
      };

      const URL = `${_this.urlGenerate()}/${data.id}`;
      _this.openModal("detailsModal", false);

      _this.getData(URL, "get", {}, {}, function (retData) {

        if (retData && retData.template && retData.template.kra) {
          _this.viewData = retData;
          _this.openModal("detailsModal", true);
        } else {
          console.error("Error: No valid data returned");
        }
      });
    },
  },
  computed: {
    _cgpaGrade() {
      try {
        const totalScore = parseFloat(this.formObject.total_score) || 0;
        const converted = parseFloat(this.formObject.converted) || 0;
        if (isNaN(totalScore) || isNaN(converted) || converted === 0) {
          return 0;
        }
        let convertedTotal = (totalScore * converted) / 100;
        return convertedTotal;
      } catch (error) {
        console.error(error);
      }
    },
  },
  mounted() {
    const _this = this;
    _this.getDataList();
    _this.getGeneralData(["class", "teachers", "staff", "apprisalTemplte"]);
    _this.$set(this.formObject, "start_date", "");
    _this.$set(this.formObject, "end_date", "");
    if (_this.formObject.class_id) {
      _this.getGeneralData({
        students: { class_id: _this.formObject.class_id },
      });
    }
  },
};
</script>

<style scoped>
.thedcss {
  padding-right: 150px !important;
}
.apprisan_details tr th:first-child {
  width: 36% !important;
  line-height: 27px !important;
}
table.table tr td:first-child {
  width: initial !important;
  line-height: 27px !important;
}

.apprisal_individual_score tr th,
.apprisal_individual_score tr td {
  padding: 0 12px !important;
  vertical-align: middle;
}
</style>

