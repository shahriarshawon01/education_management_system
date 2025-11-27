<template :key="$route.meta.name">
  <div class="main_component">
    <div class="row">
      <div class="col">
        <data-table :table-heading="tableHeading" :default-object="{}" table-title="Student Promotion">
          <template v-slot:page_top>
            <page-top :default-object="{
              department_id: '',
              class_id: '',
              session_year_id: '',
              section_id: '',
              promotion_session_id: '',
              optional_subject_id: '',
            }" topPageTitle="Student Promotion" :default-add-button="can('promotion_add')"></page-top>
          </template>
          <template v-slot:filter> </template>

          <template v-slot:data>
            <tr v-for="(data, index) in dataList.data">
              <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
              <td>
                <strong>{{ showData(data.student, "student_name_en") }}</strong>
                <span class="text-mute"> ({{ data.student_roll }}) </span>
              </td>
              <td style="text-align: center">
                {{ showData(data.old_class, "name") }}
              </td>
              <td style="text-align: center">
                {{ showData(data.old_section, "name") }}
              </td>
              <td>{{ showData(data.new_class, "name") }}</td>
              <td>{{ showData(data.new_section, "name") }}</td>
              <td>{{ data.promotion_roll }}</td>
              <td class="table-center">
                <div class="hstack gap-3 fs-15 action-buttons">
                  <a class="link-primary" @click="promotionDetails(data)"><i class="fa fa-eye"></i></a>
                  <!-- <a v-if="can('promotion_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i
                      class="fa fa-trash"></i></a> -->
                </div>
              </td>
            </tr>
          </template>
        </data-table>
      </div>
    </div>

    <formModal modalSize="modal-xl" id="formModal" @submit="submitData()">
      <div class="row mb-2">
        <div class="col-md-3">
          <select v-model="formObject.session_year_id" name="session_year_id" class="form-control">
            <option value="">Select Session</option>
            <template v-for="(data, index) in requiredData.session">
              <option :value="data.id">{{ data.title }}</option>
            </template>
          </select>
        </div>

        <div class="col-md-3">
          <select v-model="formObject.class_id" v-validate="'required'" name="class_id" class="form-control" @change="getGeneralData({
            section: { class_id: formObject.class_id },
            departments: { class_id: formObject.class_id },
            getStudentData,
          })">
            <option value="">Select Class</option>
            <template v-for="(eachData, index) in requiredData.class">
              <option :value="eachData.id">{{ eachData.name }}</option>
            </template>
          </select>
        </div>

        <div class="col-md-3">
          <select v-model="formObject.department_id" name="department_id" class="form-control">
            <option value="">Select Department</option>
            <template v-for="(eachData, index) in requiredData.departments">
              <option :value="eachData.id">{{ eachData.department_name }}</option>
            </template>
          </select>
        </div>

        <div class="col-md-3">
          <select v-model="formObject.section_id" @change="getStudentData" name="section_id" class="form-control">
            <option value="">Select Section</option>
            <template v-for="(eachData, index) in requiredData.section">
              <option :value="eachData.id">{{ eachData.name }}</option>
            </template>
          </select>
        </div>
      </div>

      <hr />

      <div class="row table-responsive">
        <h5 class="mb-3 text-white px-2 py-1 rounded" style="background: linear-gradient(90deg, #3b6e67, #5e9c92);">
          Student Promotion & Department Change
        </h5>

        <table class="table table-bordered">
          <thead>
            <tr>
              <th rowspan="2" style="vertical-align: middle !important;">SL</th>
              <th colspan="3">Present Status</th>
              <th colspan="6">Promotion Status</th>
            </tr>
            <tr>
              <th style="text-align: center">Name</th>
              <th style="text-align: center">Class</th>
              <th style="text-align: center">Department</th>
              <th style="text-align: center">New Roll</th>

              <th style="text-align: center">
                <select class="form-control" v-model="bulkPromotion.optional_subject_id"
                  @change="applyBulkPromotion('optional_subject')">
                  <option value="">Optional Subject</option>
                  <template v-for="subject in requiredData.optional_subjects">
                    <option :value="subject.id">{{ subject.name }}</option>
                  </template>
                </select>
              </th>

              <th style="text-align: center">
                <select class="form-control" v-model="bulkPromotion.class_id" @change="applyBulkPromotion('class')">
                  <option value="">Class</option>
                  <template v-for="(eachData, index) in requiredData.new_class">
                    <option :value="eachData.id">{{ eachData.name }}</option>
                  </template>
                </select>
              </th>

              <th style="text-align: center">
                <select class="form-control" v-model="bulkPromotion.section_id" @change="applyBulkPromotion('section')">
                  <option value="">Section</option>
                  <template v-for="(eachData, index) in requiredData.new_section">
                    <option :value="eachData.id">{{ eachData.name }}</option>
                  </template>
                </select>
              </th>

              <th style="text-align: center">
                <select class="form-control" v-model="bulkPromotion.session_id" @change="applyBulkPromotion('session')">
                  <option value="">Session</option>
                  <template v-for="(data, index) in requiredData.session">
                    <option :value="data.id">{{ data.title }}</option>
                  </template>
                </select>
              </th>

              <th style="text-align: center">
                <select class="form-control" v-model="bulkPromotion.department_id"
                  @change="applyBulkPromotion('department')">
                  <option value="">Department</option>
                  <template v-for="(eachData, index) in requiredData.new_department">
                    <option :value="eachData.id">{{ eachData.department_name }}</option>
                  </template>
                </select>
              </th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(data, index) in details" :key="data.id">

              <td class="sl-cell">
                <div class="sl-wrapper">
                  <input type="checkbox" v-model="data.skipPromotion" @change="togglePromotion(data)" />
                  <span>{{ index + 1 }}</span>
                </div>
              </td>

              <td>
                <span class="fw-bold">{{ data.student_name_en }}</span><br />
                <small class="text-muted">ID: {{ data.student_roll }}</small><br />
                <small class="text-muted">Class Roll: {{ data.merit_roll }}</small>
              </td>

              <td>{{ data.class_name }}</td>
              <td>{{ data.department_name }}</td>

              <td style="width: 8% !important;">
                <input type="number" class="form-control" v-model="data.promotion_roll" name="promotion_roll" />
              </td>

              <!-- optional subject -->
              <td style="text-align: center;">
                <select class="form-control" v-model="data.optional_subject_id">
                  <option value=""> Optional Subject</option>
                  <template v-for="subject in requiredData.optional_subjects">
                    <option :value="subject.id">{{ subject.name }}</option>
                  </template>
                </select>
              </td>

              <td>
                <select class="form-control" v-model="data.promotion_class_id">
                  <option value="">Class</option>
                  <template v-for="(eachData, index) in requiredData.new_class">
                    <option :value="eachData.id">{{ eachData.name }}</option>
                  </template>
                </select>
              </td>

              <td>
                <select class="form-control" v-model="data.promotion_section_id">
                  <option value="">Section</option>
                  <template v-for="(eachData, index) in requiredData.new_section">
                    <option :value="eachData.id">{{ eachData.name }}</option>
                  </template>
                </select>
              </td>

              <td>
                <select class="form-control" v-model="data.promotion_session_id">
                  <option value="">Session</option>
                  <template v-for="(data, index) in requiredData.session">
                    <option :value="data.id">{{ data.title }}</option>
                  </template>
                </select>
              </td>

              <td>
                <select class="form-control" v-model="data.promotion_department_id">
                  <option value="">Department</option>
                  <template v-for="(eachData, index) in requiredData.new_department">
                    <option :value="eachData.id">{{ eachData.department_name }}</option>
                  </template>
                </select>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </formModal>

    <general-modal modal-id="detailsModal" modalSize="modal-l">
      <template v-slot:title>
        <span>Student Promotion Details</span>
      </template>
      <template v-slot:body>
        <table class="table table-bordered align-middle">
          <colgroup>
            <col style="width: 40%;">
            <col style="width: 5%;">
            <col style="width: 55%;">
          </colgroup>
          <tbody>
            <tr>
              <th class="text-left">Name</th>
              <td class="text-center">:</td>
              <td>{{ showData(details.student, "student_name_en") }}</td>
            </tr>
            <tr>
              <th class="text-left">Old Class</th>
              <td class="text-center">:</td>
              <td>{{ showData(details.old_class, "name") }}</td>
            </tr>
            <tr>
              <th class="text-left">Old Section</th>
              <td class="text-center">:</td>
              <td>{{ showData(details.old_section, "name") }}</td>
            </tr>
            <tr>
              <th class="text-left">Old Department</th>
              <td class="text-center">:</td>
              <td>{{ showData(details.o_department, "department_name") }}</td>
            </tr>
            <tr>
              <th class="text-left">New Class</th>
              <td class="text-center">:</td>
              <td>{{ showData(details.new_class, "name") }}</td>
            </tr>
            <tr>
              <th class="text-left">New Section</th>
              <td class="text-center">:</td>
              <td>{{ showData(details.new_section, "name") }}</td>
            </tr>
            <tr>
              <th class="text-left">New Department</th>
              <td class="text-center">:</td>
              <td>{{ showData(details.new_department, "department_name") }}</td>
            </tr>
            <tr>
              <th class="text-left">Old Roll</th>
              <td class="text-center">:</td>
              <td>{{ details.roll }}</td>
            </tr>
            <tr>
              <th class="text-left">Promotion Roll</th>
              <td class="text-center">:</td>
              <td>{{ details.promotion_roll }}</td>
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
  name: "studentPromotion",
  components: { PageTop, Pagination, DataTable, formModal, GeneralModal },
  data() {
    return {
      tableHeading: [
        "Sl",
        "Student Name",
        "Old Class",
        "Old Section",
        "New Class",
        "New Section",
        "New Class Roll",
        "Action",
      ],
      formModalId: "formModal",
      details: [],
      bulkPromotion: {
        class_id: "",
        section_id: "",
        session_id: "",
        department_id: "",
        optional_subject_id: "",
      },
    };
  },

  watch: {
    "formObject.class_id": function (newClassId) {
      this.updatePromotionClass(newClassId);
      if (newClassId) {
        this.getStudentData(newClassId);
      }
    },
    "formObject.session_year_id": function (newVal) {
      if (newVal) {
        this.getStudentData(newVal);
      }
    },
    "formObject.section_id": function (newVal) {
      if (newVal) {
        this.getStudentData(newVal);
      }
    },
    "formObject.department_id": function (newVal) {
      if (newVal) {
        this.getStudentData(newVal);
      }
    },
  },

  methods: {

    getStudentData(data) {
      const _this = this;
      if (!_this.formObject.class_id && !_this.formObject.class_id) return;
      const URL = `${_this.urlGenerate("api/get_promotion_data")}`;
      const paramsData = {
        session_year_id: data.session_id ? data.session_id : _this.formObject.session_year_id,
        class_id: data.class_id ? data.class_id : _this.formObject.class_id,
        section_id: data.section_id ? data.section_id : _this.formObject.section_id,
        department_id: data.department_id ? data.department_id : _this.formObject.department_id,
      };

      _this.getData(URL, "get", paramsData, {}, (retData) => {
        _this.details = retData.map((student) => ({
          ...student,
          skipPromotion: true,
        }));
      });
    },

    togglePromotion(student) {
      if (!student.skipPromotion) {
        student.promotion_class_id = null;
        student.promotion_section_id = null;
        student.promotion_session_id = null;
        student.promotion_department_id = null;
      } else {
        if (this.bulkPromotion.class_id)
          student.promotion_class_id = this.bulkPromotion.class_id;
        if (this.bulkPromotion.section_id)
          student.promotion_section_id = this.bulkPromotion.section_id;
        if (this.bulkPromotion.session_id)
          student.promotion_session_id = this.bulkPromotion.session_id;
        if (this.bulkPromotion.department_id)
          student.promotion_department_id = this.bulkPromotion.department_id;
      }
    },

    applyBulkPromotion(type) {
      this.details.forEach((student) => {
        if (student.skipPromotion) {
          if (type === "class")
            student.promotion_class_id = this.bulkPromotion.class_id;
          if (type === "section")
            student.promotion_section_id = this.bulkPromotion.section_id;
          if (type === "session")
            student.promotion_session_id = this.bulkPromotion.session_id;
          if (type === "department")
            student.promotion_department_id = this.bulkPromotion.department_id;
          if (type === "optional_subject")
            student.optional_subject_id = this.bulkPromotion.optional_subject_id;
        }
      });
    },

    updatePromotionClass(selectedClassId) {
      const allClasses = this.requiredData.class;
      const selectedClass = allClasses.find((c) => c.id === selectedClassId);
      if (!selectedClass) return;

      let nextClasses = [];
      switch (selectedClass.name) {
        case "Ten":
          nextClasses = allClasses.filter((c) => c.name === "SSC");
          break;
        case "SSC":
          nextClasses = allClasses.filter(
            (c) => c.name === "Exe Class" || c.name === "Eleven"
          );
          break;
        case "Twelve":
          nextClasses = allClasses.filter((c) => c.name === "HSC");
          break;
        case "HSC":
          nextClasses = allClasses.filter((c) => c.name === "Exe Class");
          break;
        default:
          const currentIndex = allClasses.findIndex((c) => c.id === selectedClassId);
          if (currentIndex !== -1 && currentIndex < allClasses.length - 1) {
            nextClasses = [allClasses[currentIndex + 1]];
          }
          break;
      }

      this.requiredData.new_class = nextClasses;
      this.details.forEach((student) => {
        student.promotion_class_id =
          nextClasses.length > 0 ? nextClasses[0].id : null;
      });

      if (nextClasses.length > 0) {
        this.getNewClassWiseData(nextClasses[0].id);
        this.getClassWiseDepartment(nextClasses[0].id);
      } else {
        this.requiredData.new_section = [];
        this.requiredData.new_department = [];
      }
    },

    getNewClassWiseData(classId) {
      const URL = `${this.urlGenerate("api/get_new_class_wise_data")}`;
      const paramsData = { class_id: classId };

      this.getData(URL, "get", paramsData, {}, (retData) => {
        this.requiredData.new_section = retData.sections || [];
      });
      this.getOptionalSubjects(classId);
    },

    getOptionalSubjects(classId) {
      const URL = `${this.urlGenerate("api/get_optional_subjects")}`;
      const paramsData = { class_id: classId };

      this.getData(URL, "get", paramsData, {}, (retData) => {
        this.requiredData.optional_subjects = retData.optional_subjects || [];
      });
    },

    getClassWiseDepartment(classId) {
      const URL = `${this.urlGenerate("api/get_class_wise_department")}`;
      const paramsData = { class_id: classId };

      this.getData(URL, "get", paramsData, {}, (retData) => {
        this.requiredData.new_department = retData.department || [];
      });
    },

    promotionDetails: function (data) {
      const _this = this;
      _this.openModal("detailsModal", false);
      var URL = `${_this.urlGenerate()}/${data.id}`;
      _this.getData(URL, "get", {}, {}, function (retData) {
        _this.details = retData;
        _this.openModal("detailsModal", false);
      });
    },

    submitData() {
      const _this = this;
      const submittedValue = _this.details.filter(
        (data) =>
          (data.promotion_roll && parseInt(data.promotion_roll) !== 0) ||
          (data.promotion_class_id && parseInt(data.promotion_class_id) !== 0) ||
          (data.promotion_section_id && parseInt(data.promotion_section_id) !== 0) ||
          (data.promotion_session_id && parseInt(data.promotion_session_id) !== 0) ||
          (data.promotion_department_id && parseInt(data.promotion_department_id) !== 0)
      );
      const requestData = {
        class_id: _this.formObject.class_id,
        student_id: _this.formObject.student_id,
        section_id: _this.formObject.section_id,
        details: submittedValue,
      };
      const URL = baseUrl + "/api/promotion";
      _this.axios
        .post(URL, requestData)
        .then((response) => {
          if (response.data.status === 2000) {
            _this.$toastr("success", response.data.message, "Success");
            _this.closeModal("formModal");
          } else {
            _this.$toastr("error", response.data.message, "Error");
          }
        })
        .catch((error) => {
          _this.$toastr("error", "Failed to submit data.", "Error");
        });
    },

  },
  mounted() {
    const _this = this;
    _this.getDataList();
    _this.getGeneralData(["class", "section", "new_class", "new_section", "session", "departments"]);
  }
};
</script>

<style scoped>
.table-center .action-buttons {
  display: flex;
  justify-content: center;
  gap: 10px;
}

.modal-title {
  margin-bottom: 0;
  line-height: 0.5;
  text-transform: uppercase;
}

.table thead th {
  background-color: #cccbcb;
  text-align: center;
}

.common-data {
  font-weight: bold;
}

table.table tr th:first-child,
table.table tr td:first-child {
  width: initial !important;
  line-height: 28px !important;
}

.sl-cell {
  text-align: center;
  vertical-align: middle !important;
}

.sl-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}
</style>
