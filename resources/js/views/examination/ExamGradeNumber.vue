<template :key="$route.meta.name">
  <div class="main_component">
    <div class="row">
      <div class="col">
        <data-table :table-heading="tableHeading" table-title="Exam Grade Number">
          <template v-slot:page_top>
            <page-top
              :default-object="{ section_id: '', session_year_id: '', class_id: '', subject_id: '', exam_id: '', exam_type_id: '', department_id: '', exam_component_id: '', exam_mark_type: [{ mark_component: '', exam_mark: '', convert_num: '', total_mark: '', pass_mark: '' }] }"
              topPageTitle="Exam Grade Number" :default-add-button="can('grade_number_add')"></page-top>
          </template>

          <template v-slot:filter>
            <div class="col-md-2">
              <select class="form-control" v-model="formFilter.class_id" name="class_id" @change="
                getGeneralData({
                  departments: { class_id: formFilter.class_id },
                  section: { class_id: formFilter.class_id },
                })
                ">
                <option value="">Select Class</option>
                <template v-for="(data, index) in requiredData.class">
                  <option :value="data.id">{{ data.name }}</option>
                </template>
              </select>
            </div>

            <div class="col-md-2">
              <select v-model="formFilter.department_id" name="department_id" class="form-control">
                <option value="">Select Department</option>
                <template v-for="(data, index) in requiredData.departments">
                  <option :value="data.id">{{ data.department_name }}</option>
                </template>
              </select>
            </div>

            <div class="col-md-2">
              <select v-model="formFilter.section_id" name="section_id" class="form-control">
                <option value="">Select Section</option>
                <template v-for="(eachData, index) in requiredData.section">
                  <option :value="eachData.id">{{ eachData.name }}</option>
                </template>
              </select>
            </div>

            <!-- <div class="col-md-1">
              <select v-model="formFilter.exam_id" name="exam_id" class="form-control" @change="
                getGeneralData({
                  examinationType: { exam_id: formFilter.exam_id },
                  examClass: { class_id: formFilter.class_id },
                })
                ">
                <option value="">Exam Name</option>
                <template v-for="(eachData, index) in requiredData.examName">
                  <option :value="eachData.id">{{ eachData.name }}</option>
                </template>
              </select>
            </div>

            <div class="col-md-1">
              <select v-model="formFilter.exam_type_id" name="exam_type_id" class="form-control">
                <option value="">Exam Type</option>
                <template v-for="(eachData, index) in requiredData.examinationType">
                  <option :value="eachData.id">{{ eachData.type_name }}</option>
                </template>
              </select>
            </div> -->

          </template>

          <template v-slot:data>
            <tr v-for="(data, index) in dataList.data">
              <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>

              <td class="table-left">{{ showData(data.classes, "name") }}</td>
              <td class="table-left">{{ showData(data.department, "department_name") }}</td>
              <td class="table-left">{{ showData(data.section, "name") }}</td>

              <td class="table-left">{{ showData(data.subject, "name") }}</td>
              <td class="table-left">{{ showData(data.subject, "subject_mark") }}</td>

              <td class="table-left">{{ showData(data.exam, "name") }}</td>
              <td class="table-left">{{ showData(data.exam_type, "type_name") }}</td>
              <td class="table-center">
                {{ data.mark_component ? data.mark_component.join(", ") : "" }}
              </td>
              <td class="table-center">
                {{ data.exam_mark ? data.exam_mark.join(", ") : "" }}
              </td>
              <td class="table-center">
                {{ data.convert_num ? data.convert_num.join(", ") : "" }}
              </td>
              <!-- <td class="table-center">
                {{ data.total_mark ? data.total_mark.join(", ") : "" }}
              </td> -->
              <td class="table-center">
                {{ data.pass_mark ? data.pass_mark.join(", ") : "" }}
              </td>
              <td class="table-center">
                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
              </td>
              <td class="table-center">
                <div class="hstack gap-3 fs-15 action-buttons">
                  <a v-if="can('grade_number_update')" class="link-primary"
                    @click="editDataInformation(data, data.id)"><i class="fa fa-edit"></i></a>
                  <a v-if="can('grade_number_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i
                      class="fa fa-trash"></i></a>
                </div>
              </td>
            </tr>
          </template>
        </data-table>
      </div>
    </div>

    <formModal modalSize="modal-lg" @submit="submitForm(formObject, 'formModal')">
      <h5 class="mb-3 text-white px-2 py-1 rounded" style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
        Class & Session Details
      </h5>

      <div class="row g-3 mb-4">
        <div class="col-md-3">
          <label class="form-label">Class</label>
          <select v-model="formObject.class_id" name="class_id" v-validate="'required'" class="form-control" @change="getGeneralData({
            section: { class_id: formObject.class_id },
            departments: { class_id: formObject.class_id },
          })">
            <option value="">Select Class</option>
            <template v-for="(eachData, index) in requiredData.class">
              <option :value="eachData.id">{{ eachData.name }}</option>
            </template>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Department</label>
          <select v-model="formObject.department_id" name="department_id" v-validate="'required'" class="form-control">
            <option value="">Select Department</option>
            <template v-for="(eachData, index) in requiredData.departments">
              <option :value="eachData.id">{{ eachData.department_name }}</option>
            </template>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Section</label>
          <select v-model="formObject.section_id" name="section_id" v-validate="'required'" class="form-control"
            @change="getGeneralData({
              section_subjects: { section_id: formObject.section_id }
            })">
            <option value="">Select Section</option>
            <template v-for="(eachData, index) in requiredData.section">
              <option :value="eachData.id">{{ eachData.name }}</option>
            </template>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Session</label>
          <select v-model="formObject.session_year_id" name="session_year_id" v-validate="'required'"
            class="form-control">
            <option value="">Select Session</option>
            <template v-for="(eachData, index) in requiredData.session">
              <option :value="eachData.id">{{ eachData.title }}</option>
            </template>
          </select>
        </div>

        <div class="col-md-4" hidden>
          <input type="text" v-model="formObject.grade_number" v-validate="'required'" name="grade_number"
            class="form-control" placeholder="Exam Grade Number" readonly />
        </div>
      </div>

      <h5 class="mb-3 text-white px-2 py-1 rounded" style="background: linear-gradient(90deg, #3b6e67, #82b9ae);">
        Exam Information
      </h5>

      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <label class="form-label">Subject</label>
          <select v-model="formObject.subject_id" name="subject_id" v-validate="'required'" class="form-control">
            <option value="">Select Subject</option>
            <template v-for="(eachData, index) in requiredData.section_subjects">
              <option :value="eachData.id">{{ eachData.name }}</option>
            </template>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Exam Name</label>
          <select v-model="formObject.exam_id" name="exam_id" v-validate="'required'" class="form-control" @change="getGeneralData({
            examinationType: { exam_id: formObject.exam_id },
            examClass: { class_id: formObject.class_id },
          })">
            <option value="">Select Exam Name</option>
            <template v-for="(eachData, index) in requiredData.examName">
              <option :value="eachData.id">{{ eachData.name }}</option>
            </template>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Exam Type</label>
          <select v-model="formObject.exam_type_id" name="exam_type_id" v-validate="'required'" class="form-control">
            <option value="">Select Exam Type</option>
            <template v-for="(eachData, index) in requiredData.examinationType">
              <option :value="eachData.id">{{ eachData.type_name }}</option>
            </template>
          </select>
        </div>
      </div>

      <h5 class="mb-3 text-white px-2 py-1 rounded" style="background: linear-gradient(90deg, #3b6e67, #9dd5c9);">
        Configure Subject Mark
      </h5>

      <table class="table table-bordered table-striped">
        <thead class="table-success">
          <tr>
            <th style="width: 25%">Component</th>
            <th style="width: 15%">Exam Mark</th>
            <th style="width: 15%">Convert(%)</th>
            <th style="width: 15%">Pass Mark</th>
            <th style="width: 15%; text-align: center">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(row, index) in formObject.exam_mark_type" :key="index">
            <td>
              <select v-model="row.mark_component" name="mark_component" v-validate="'required'" class="form-control">
                <option value="" disabled>Exam Component</option>
                <template v-for="(eachData, index) in requiredData.exam_component">
                  <option :value="eachData.name">{{ eachData.name }}</option>
                </template>
              </select>
            </td>
            <td>
              <input type="number" @input="examMarkHandler(row, index)" v-model="row.exam_mark" name="exam_mark"
                class="form-control" placeholder="Exam Mark" v-validate="'required'" />
            </td>
            <td>
              <input type="number" @input="convertExamMark(row, index)" v-model="row.convert_num" name="convert_num"
                class="form-control" placeholder="Convert" v-validate="'required'" />
            </td>
            <td>
              <!-- <input type="text" v-model="row.pass_mark" name="pass_mark" class="form-control" placeholder="Pass Mark"
                v-validate="'required'" /> -->

              <input type="text" v-model="row.pass_mark" name="pass_mark" class="form-control" placeholder="Pass Mark"
                v-validate="'required'" />
            </td>
            <td class="text-center">
              <button type="button" class="btn btn-outline-success me-1" @click="addRow(formObject.exam_mark_type, {
                mark_component: '',
                exam_mark: '',
                convert_num: '',
                total_mark: '',
                pass_mark: '',
              })">
                <i class="fa fa-plus"></i>
              </button>
              <button type="button" class="btn btn-outline-danger" v-if="formObject.exam_mark_type.length > 1"
                @click="deleteRow(formObject.exam_mark_type, index)">
                <i class="fa fa-close"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </formModal>
  </div>
</template>
<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";
export default {
  name: "ExamGradeNumber",
  components: { PageTop, Pagination, DataTable, formModal },
  data() {
    return {
      tableHeading: [
        "Sl",
        "Class",
        "Department",
        "Section",
        "Subject",
        "Full Marks",
        "Exam Name",
        "Exam Type",
        "Component Name",
        "Component Mark",
        "Converted Mark",
        "Pass mark",
        "Status",
        "Action",
      ],
      formModalId: "formModal",
      exam_mark_type: [
        {
          mark_component: "",
          exam_mark: "",
          convert_num: "",
          total_mark: "",
          pass_mark: "",
        },
      ],
      details: {},
    };
  },
  methods: {
    // convertExamMark(row, index) {
    //   const examMark = parseFloat(row.exam_mark) || 0;
    //   const convertMark = parseFloat(row.convert_num) || 0;
    //   row.total_mark = ((examMark * convertMark) / 100).toFixed(2);

    //   const totalSum = this.formObject.exam_mark_type.reduce(
    //     (sum, currentRow) => {
    //       const totalMarkValue = parseFloat(currentRow.total_mark) || 0;
    //       return sum + totalMarkValue;
    //     },
    //     0
    //   );

    //   this.$set(this.formObject, "grade_number", totalSum.toFixed(2));
    // },

    // 40% of convert_num
    convertExamMark(row, index) {
      const examMark = parseFloat(row.exam_mark) || 0;
      const convertMark = parseFloat(row.convert_num) || 0;
      row.total_mark = ((examMark * convertMark) / 100).toFixed(2);
      row.pass_mark = Math.round(convertMark * 0.4);
      const totalSum = this.formObject.exam_mark_type.reduce((sum, currentRow) => {
        const totalMarkValue = parseFloat(currentRow.total_mark) || 0;
        return sum + totalMarkValue;
      }, 0);

      this.$set(this.formObject, "grade_number", totalSum.toFixed(2));
    },

    examMarkHandler(row, index) {
      this.convertExamMark(row, index);
    },

    deleteRow: function (array, index) {
      array.splice(index, 1);
      this.formObject.exam_mark_type.forEach((row, idx) => {
        this.convertExamMark(row, idx);
      });
    },

    editDataInformation: function (data, id) {
      const _this = this;
      const URL = `${_this.urlGenerate()}/${id}/edit`;

      _this.$store.commit("updateId", id);
      _this.$store.commit("formType", 2);

      _this.getData(URL, "get", {}, {}, function (retData) {
        _this.openModal("formModal", "Edit Template");

        const MarkArray = Array.isArray(retData.mark_component) ? retData.mark_component
          : [retData.mark_component];
        const PassMarkArray = Array.isArray(retData.pass_mark) ? retData.pass_mark
          : [retData.pass_mark];
        const ExamMarkArray = Array.isArray(retData.exam_mark) ? retData.exam_mark
          : [retData.exam_mark];
        const ConvertedMarkArray = Array.isArray(retData.convert_num) ? retData.convert_num
          : [retData.convert_num];
        const TotalMarkArray = Array.isArray(retData.total_mark) ? retData.total_mark
          : [retData.total_mark];

        const examMarkType = MarkArray.map((mark_component, index) => ({
          mark_component: mark_component || "",
          pass_mark: PassMarkArray[index] || "",
          exam_mark: ExamMarkArray[index] || "",
          convert_num: ConvertedMarkArray[index] || "",
          total_mark: TotalMarkArray[index] || "",
        }));

        _this.$set(_this.formObject, "grade_number", retData.grade_number);
        _this.$set(_this.formObject, "exam_mark_type", examMarkType);
        _this.$set(_this.formObject, "subject_id", retData.subject_id);
        _this.$set(_this.formObject, "department_id", retData.department_id);
        _this.$set(_this.formObject, "section_id", retData.section_id);
        _this.$set(_this.formObject, "subject_id", retData.subject_id);
        _this.$set(_this.formObject, "session_year_id", retData.session_year_id);

        _this.$set(_this.formObject, "class_id", retData.class_id);
        _this.$set(_this.formObject, "exam_id", retData.exam_id);
        _this.$set(_this.formObject, "exam_type_id", retData.exam_type_id);

        _this.getGeneralData({
          section: { class_id: retData.class_id },
          section_subjects: { section_id: retData.section_id },
          departments: { class_id: retData.class_id },
          examinationType: { exam_id: retData.exam_id },
        });
      });
    },

    assign() {
      const _this = this;
      _this.$set(_this.formFilter, 'class_id', '');
      _this.$set(_this.formFilter, 'department_id', '');
      _this.$set(_this.formFilter, 'section_id', '');
      _this.$set(_this.formFilter, 'exam_id', '');
      _this.$set(_this.formFilter, 'exam_type_id', '');
    },

  },
  mounted() {
    const _this = this;
    _this.getDataList();
    _this.assign();
    _this.getGeneralData(['session', "class", "examName", "exam_component"], function (retData) {
      _this.getGeneralData({
        class: { school_id: _this.Config.school_id },
        class: { class_id: _this.Config.class_id },
      });
    });

    _this.$set(_this.formObject, "exam_mark_type", [
      {
        mark_component: "",
        exam_mark: "",
        convert_num: "",
        total_mark: "",
        pass_mark: "",
      },
    ]);
  },
};
</script>

<style scoped>
.table-center {
  text-align: center;
}

table-left {
  text-align: left;
}

.table-center .action-buttons {
  display: flex;
  justify-content: center;
  gap: 10px;
}
</style>
