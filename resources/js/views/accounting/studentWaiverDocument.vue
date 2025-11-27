<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Student Waiver Document">
                    <template v-slot:page_top>
                        <page-top :default-object="{ reason: '', class_id: '', section_id: '' }"
                            topPageTitle="Student Waiver Document"
                            :default-add-button="can('student_waiver_document_add')"></page-top>
                    </template>

                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.class_id" name="class_id" class="form-control" @change="
                                getGeneralData({
                                    students: { class_id: formFilter.class_id },
                                    section: { class_id: formFilter.class_id },
                                })
                                ">
                                <option value="">Select Class</option>
                                <template v-for="(eachData, index) in requiredData.class">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
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
                    </template>

                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>
                                <strong>Name : </strong>{{ showData(data.student, "student_name_en") }}<br />
                                <strong>Student ID : </strong>{{ showData(data.student, "student_roll") }}<br />
                                <strong>Class : </strong>{{ data.student ? data.student.classes.name : "" }}<br />
                                <strong>Class Roll : </strong>{{ showData(data.student, "merit_roll") }}<br />
                                <strong>Session : </strong>{{ data.student ? data.student.sessions.title : "" }}
                            </td>
                            <td>{{ getReason(data.reason) }}</td>

                            <td>
                                <!-- Debtor Member & HEM staff -->
                                <template v-if="[1, 3].includes(Number(data.reason))">
                                    <strong>Name :</strong> {{ data.reference_name }}<br />
                                    <template v-if="Number(data.reason) === 1">
                                        <strong>Member Code :</strong> {{ data.reference_code }}<br />
                                    </template>
                                    <template v-else>
                                        <strong>Employee ID :</strong> {{ data.reference_code }}<br />
                                    </template>
                                    <strong>Phone :</strong> {{ data.reference_phone }}<br />
                                    <strong>NID :</strong> {{ data.reference_nid }}
                                </template>

                                <!-- TPSC Staff -->
                                <template v-else-if="Number(data.reason) === 2">
                                    <strong>Name :</strong> {{ data.staffs?.name || data.teachers?.name }}<br />
                                    <strong>Employee ID :</strong> {{ data.staffs?.employee_id ||
                                        data.teachers?.employee_id }}<br />
                                    <strong>Phone :</strong> {{ data.staffs?.phone || data.teachers?.phone }}<br />
                                    <strong>NID :</strong> {{ data.staffs?.nid || data.teachers?.nid || 'N/A' }}
                                </template>

                                <!-- Merit Position, Child Merit, General -->
                                <template v-else-if="[4, 5, 6].includes(Number(data.reason))">
                                    <strong>Name :</strong> {{ data.student?.student_name_en }}<br />
                                    <strong>Student ID :</strong> {{ data.student?.student_roll }}<br />
                                    <strong>Class :</strong> {{ data.student?.classes?.name }}<br />
                                    <strong>Class Roll :</strong> {{ data.student?.merit_roll }}<br />
                                    <strong>Session :</strong> {{ data.student?.sessions?.title }}
                                </template>
                            </td>
                            <td style="text-align: left">
                                {{ showData(data.created_user, "username") }}
                            </td>
                            <td style="text-align: left">
                                {{ showData(data.updated_user, "username") }}
                            </td>
                            <td class="table-center">
                                <span>
                                    <img class="mouse-action" :src="getImage(data.file)"
                                        @click="openFile(getImage(data.file))">
                                </span>
                            </td>
                            <td>{{ data.remarks }}</td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <a v-if="can('student_waiver_document_update')" class="link-primary"
                                        @click="editDataInformation(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('student_waiver_document_delete')" class="link-danger"
                                        @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>

        <formModal modalSize="modal-lg" @submit="submitForm(formObject, 'formModal')">
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <label class="col-form-label">Student ID</label>
                                    <autocomplete v-model="formObject.student_roll" api-url="/api/get-student"
                                        placeholder="Enter Student ID" name="student_roll" v-validate="'required'"
                                        validation_name="Student ID" @select="fillStudentData" :disabled="isEditMode" />
                                </div>
                                <div class="col-md-12">
                                    <label class="col-form-label">Name</label>
                                    <input type="text" v-model="formObject.student_name" name="student_name"
                                        class="form-control" placeholder="Auto-filled" readonly >
                                </div>
                                <div class="col-md-12">
                                    <label class="col-form-label">Class</label>
                                    <input type="text" v-model="formObject.student_class" name="student_class"
                                        class="form-control" placeholder="Auto-filled" readonly>
                                </div>
                                <div class="col-md-12">
                                    <label class="col-form-label">Department</label>
                                    <input type="text" v-model="formObject.student_department" name="student_department"
                                        class="form-control" placeholder="Auto-filled" readonly>
                                </div>
                                <div class="col-md-12">
                                    <label class="col-form-label">Section</label>
                                    <input type="text" v-model="formObject.section_name" name="section_name"
                                        class="form-control" placeholder="Auto-filled" readonly>
                                </div>
                                <div class="col-md-12">
                                    <label class="col-form-label">Session</label>
                                    <input type="text" v-model="formObject.session_name" name="session_name"
                                        class="form-control" placeholder="Auto-filled" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="col-md-12 form-group">
                                    <label class="col-form-label">Reason</label>
                                    <select v-model="formObject.reason" name="reason" class="form-control"
                                        v-validate="'required'">
                                        <option value="">Select Reason</option>
                                        <option :value="1">Debtor Member</option>
                                        <option :value="2">TPSC Staff</option>
                                        <option :value="3">HEM Staff</option>
                                        <option :value="4">Merit Position</option>
                                        <option :value="5">Child Merit</option>
                                        <option :value="6">General</option>
                                    </select>
                                </div>

                                <div class="col-md-12 form-group">
                                    <label class="col-form-label">Waiver Document</label>
                                    <div class="col-md-12">
                                        <div @click="clickImageInput('image')">
                                            <div class="form-group image_upload"
                                                :style="{ backgroundImage: 'url(' + getImage(null, 'images/upload.png') + ')' }"
                                                style="background-size:360px !important">
                                                <img v-if="formObject.file" :src="getImage(formObject.file)">
                                                <input name="thumbnail"
                                                    :v-validate="formType === 1 ? 'required' : 'sometimes'"
                                                    style="display: none;" id="image" type="file"
                                                    @change="uploadFile($event, formObject, 'file')">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Debtor member section -->
                                <div v-if="formObject.reason === 1">
                                    <div class="col-md-12">
                                        <label class="col-form-label">Debtor Member Code</label>
                                        <input type="text" v-model="formObject.reference_code" v-validate="'required'"
                                            name="reference_code" class="form-control" placeholder="Member Code">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="col-form-label">Member Name</label>
                                        <input type="text" v-model="formObject.reference_name" name="reference_name"
                                            class="form-control" placeholder="Member Name">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="col-form-label">Member NID</label>
                                        <input type="text" v-model="formObject.reference_nid" name="reference_nid"
                                            class="form-control" placeholder="Member NID">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="col-form-label">Member Phone</label>
                                        <input type="text" v-model="formObject.reference_phone" name="reference_phone"
                                            class="form-control" placeholder="Member Phone">
                                    </div>
                                </div>

                                <!-- TPSC Staff Section -->
                                <div v-if="formObject.reason === 2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- <label class="col-form-label">TPSC Staff ID</label>
                                            <input type="text" v-model="formObject.tpsc_staff_id"
                                                v-validate="'required'" name="tpsc_staff_id" class="form-control"
                                                placeholder="TPSC Staff ID" @change="getTpscStaff"> -->
                                            <label class="col-form-label">TPSC Staff ID</label>
                                            <autocomplete v-model="formObject.employee_id" api-url="/api/get-employee"
                                                placeholder="Enter Employee ID" name="employee_id"
                                                validation_name="Employee ID" @select="fillEmployeeData"
                                                :disabled="isEditMode" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label">Name</label>
                                            <input type="text" v-model="formObject.employee_name"
                                                name="employee_name" class="form-control"
                                                placeholder="Auto-filled" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label">Department</label>
                                            <input type="text" v-model="formObject.employee_department"
                                                name="employee_department" class="form-control"
                                                placeholder="Auto-filled" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label">Email</label>
                                            <input type="text" v-model="formObject.employee_email"
                                                name="employee_email" class="form-control"
                                                placeholder="Auto-filled" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label">Phone</label>
                                            <input type="text" v-model="formObject.employee_phone"
                                                name="employee_phone" class="form-control"
                                                placeholder="Auto-filled" readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- HEM Staff section -->
                                <div v-if="formObject.reason === 3">
                                    <div class="col-md-12">
                                        <label class="col-form-label">HEM Staff ID</label>
                                        <input type="text" v-model="formObject.reference_code" v-validate="'required'"
                                            name="reference_code" class="form-control" placeholder="HEM Staff ID">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="col-form-label">Member Name</label>
                                        <input type="text" v-model="formObject.reference_name" name="reference_name"
                                            class="form-control" placeholder="HEM Staff Name">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="col-form-label">Member NID</label>
                                        <input type="text" v-model="formObject.reference_nid" name="reference_nid"
                                            class="form-control" placeholder="HEM Staff NID">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="col-form-label">Member Phone</label>
                                        <input type="text" v-model="formObject.reference_phone" name="reference_phone"
                                            class="form-control" placeholder="HEM Staff Phone">
                                    </div>
                                </div>

                                <!-- Child Merit Student Section -->
                                <div v-if="formObject.reason === 5">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="col-form-label">Child Merit Student ID</label>
                                            <input type="text" v-model="formObject.merit_child_roll"
                                                v-validate="'required'" name="merit_child_roll" class="form-control"
                                                placeholder="Enter Student ID" @change="getChildMeritStudent">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="col-form-label">Name</label>
                                            <input type="text" v-model="formObject.merit_child_student_name"
                                                name="merit_child_student_name" class="form-control"
                                                placeholder="Auto-filled" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label">Class</label>
                                            <input type="text" v-model="formObject.merit_child_student_class"
                                                name="merit_child_student_class" class="form-control"
                                                placeholder="Auto-filled" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label">Department</label>
                                            <input type="text" v-model="formObject.merit_child_student_department"
                                                name="merit_child_student_department" class="form-control"
                                                placeholder="Auto-filled" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label">Section</label>
                                            <input type="text" v-model="formObject.merit_child_section_name"
                                                name="merit_child_section_name" class="form-control"
                                                placeholder="Auto-filled" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label">Session</label>
                                            <input type="text" v-model="formObject.merit_child_session_name"
                                                name="merit_child_session_name" class="form-control"
                                                placeholder="Auto-filled" readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- remarks Section -->
                                <div class="col-md-12">
                                    <label class="col-form-label"><strong>Remarks</strong></label>
                                    <textarea name="remarks" class="form-control" v-model="formObject.remarks"
                                         rows="3" placeholder="Remarks"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </formModal>
    </div>
</template>
<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";

export default {
    name: "studentWaiverDocument",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ["Sl", "Student", "Reason", "Reference", "Created By", "Updated By", "File", "Remarks", "Action"],
            formModalId: "formModal",

            student_roll: '',
            merit_child_roll: '',
        };
    },

    methods: {
        getReason(value) {
            const reasons = {
                1: 'Debtor Member',
                2: 'TPSC Staff',
                3: 'HEM Staff',
                4: 'Merit Position',
                5: 'Child Merit',
                6: 'General',
            };
            return reasons[value] || 'Unknown';
        },

        fillStudentData(student) {
            if (student && student.id) {
                this.$set(this.formObject, 'student_id', student.id);
                this.$set(this.formObject, 'student_roll', student.student_roll);
                this.$set(this.formObject, 'student_name', student.student_name_en);
                this.$set(this.formObject, 'student_class', student.class_name);
                this.$set(this.formObject, 'student_department', student.department_name);
                this.$set(this.formObject, 'section_name', student.section_name);
                this.$set(this.formObject, 'session_name', student.session_name);
                this.$set(this.formObject, 'school_name', student.school_name);
                this.$set(this.formObject, 'school_id', student.school_id);

                this.$toastr('success', 'Student data filled successfully!', 'Success');
            } else {
                this.$toastr('error', 'Student data not found!', 'Error');
            }
        },

        fillEmployeeData(employee) {
            if (employee && employee.id) {
                this.$set(this.formObject, 'employee_primary_id', employee.id);
                this.$set(this.formObject, 'employee_id', employee.employee_id);
                this.$set(this.formObject, 'employee_name', employee.name);
                this.$set(this.formObject, 'employee_department', employee.department);
                this.$set(this.formObject, 'employee_designation', employee.designation);
                this.$set(this.formObject, 'employee_email', employee.email);
                this.$set(this.formObject, 'employee_phone', employee.phone);
                this.$set(this.formObject, 'school_name', employee.school_name);
                this.$set(this.formObject, 'school_id', employee.school_id);

                this.$toastr('success', 'Employee data filled successfully!', 'Success');
            } else {
                this.$toastr('error', 'Employee data not found!', 'Error');
            }
        },

        getChildMeritStudent() {
            const _this = this;
            const URL = `${baseUrl}/api/get_child_merit_student?student_roll=${_this.formObject.merit_child_roll}`;
            _this.axios.get(URL)
                .then(response => {
                    const studentData = response.data.result;
                    if (studentData) {
                        _this.$set(_this.formObject, 'reference_id', studentData.id);
                        _this.$set(_this.formObject, 'merit_child_student_name', studentData.student_name_en);
                        _this.$set(_this.formObject, 'merit_child_student_class', studentData.class_name);
                        _this.$set(_this.formObject, 'merit_child_student_department', studentData.department_name);
                        _this.$set(_this.formObject, 'merit_child_section_name', studentData.section_name);
                        _this.$set(_this.formObject, 'merit_child_session_name', studentData.session_name);
                    } else {
                        _this.$toastr('error', 'Student not found', "Error");
                    }
                })
                .catch(error => {
                    console.error("API Error:", error);
                });
        },

        getTpscStaff() {
            const _this = this;
            const URL = `${baseUrl}/api/get_tpsc_staff?employee_id=${_this.formObject.tpsc_staff_id}`;
            _this.axios.get(URL)
                .then(response => {
                    const staffData = response.data.result;
                    if (staffData) {
                        _this.$set(_this.formObject, 'reference_id', staffData.id);
                        _this.$set(_this.formObject, 'tpsc_staff_name', staffData.name);
                        _this.$set(_this.formObject, 'tpsc_staff_department', staffData.department);
                        _this.$set(_this.formObject, 'tpsc_staff_email', staffData.email);
                        _this.$set(_this.formObject, 'tpsc_staff_phone', staffData.phone);

                        _this.$toastr('success', response.data.message, "Success");
                    } else {
                        _this.$toastr('error', response.data.message, "Error");
                    }
                })
                .catch(error => {
                    console.error('Error fetching product data:', error);
                });
        },

        editDataInformation: function (data, id) {
            const _this = this;
            _this.editData(data, id, 'formModal', function () {
                const editUrl = `${_this.urlGenerate()}/${id}/edit`;
                _this.getData(editUrl, 'get', {}, {}, function (retData) {
                    console.log("Edit Data Response:", retData);

                    if (retData.status === 1) {
                        const doc = retData;

                        // Basic fields
                        _this.$set(_this.formObject, 'student_id', doc.student_id);
                        _this.$set(_this.formObject, 'reason', doc.reason);
                        _this.$set(_this.formObject, 'remarks', doc.remarks);
                        _this.$set(_this.formObject, 'file', doc.file);
                        _this.$set(_this.formObject, 'reference_id', doc.reference_id);

                        // Student basic info
                        _this.$set(_this.formObject, 'student_roll', doc.student?.student_roll || '');
                        _this.$set(_this.formObject, 'student_name', doc.student?.student_name_en || '');
                        _this.$set(_this.formObject, 'student_class', doc.student?.classes?.name || '');
                        _this.$set(_this.formObject, 'student_department', doc.student?.departments?.department_name || '');
                        _this.$set(_this.formObject, 'section_name', doc.student?.sections?.name || '');
                        _this.$set(_this.formObject, 'session_name', doc.student?.sessions?.title || '');

                        // Reset all optional reference fields
                        _this.$set(_this.formObject, 'reference_name', '');
                        _this.$set(_this.formObject, 'reference_code', '');
                        _this.$set(_this.formObject, 'reference_phone', '');
                        _this.$set(_this.formObject, 'reference_nid', '');

                        _this.$set(_this.formObject, 'tpsc_staff_id', '');
                        _this.$set(_this.formObject, 'tpsc_staff_name', '');
                        _this.$set(_this.formObject, 'tpsc_staff_department', '');
                        _this.$set(_this.formObject, 'tpsc_staff_email', '');
                        _this.$set(_this.formObject, 'tpsc_staff_phone', '');

                        _this.$set(_this.formObject, 'merit_child_roll', '');
                        _this.$set(_this.formObject, 'merit_child_student_name', '');
                        _this.$set(_this.formObject, 'merit_child_student_class', '');
                        _this.$set(_this.formObject, 'merit_child_student_department', '');
                        _this.$set(_this.formObject, 'merit_child_section_name', '');
                        _this.$set(_this.formObject, 'merit_child_session_name', '');

                        // Fill conditional fields based on reason
                        switch (Number(doc.reason)) {
                            case 1: // Debtor Member
                                _this.$set(_this.formObject, 'reference_code', doc.reference_info?.code || '');
                                _this.$set(_this.formObject, 'reference_name', doc.reference_info?.name || '');
                                _this.$set(_this.formObject, 'reference_nid', doc.reference_info?.nid || '');
                                _this.$set(_this.formObject, 'reference_phone', doc.reference_info?.phone || '');
                                break;

                            case 3: // HEM Staff
                                _this.$set(_this.formObject, 'reference_name', doc.reference_info?.name || '');
                                _this.$set(_this.formObject, 'reference_code', doc.reference_info?.code || '');
                                _this.$set(_this.formObject, 'reference_phone', doc.reference_info?.phone || '');
                                _this.$set(_this.formObject, 'reference_nid', doc.reference_info?.nid || '');
                                break;

                            case 2: // TPSC Staff
                                _this.$set(_this.formObject, 'tpsc_staff_id', doc.reference_id);
                                _this.$set(_this.formObject, 'tpsc_staff_name', doc.reference_info?.name || '');
                                _this.$set(_this.formObject, 'tpsc_staff_department', doc.reference_info?.department || '');
                                _this.$set(_this.formObject, 'tpsc_staff_email', doc.reference_info?.email || '');
                                _this.$set(_this.formObject, 'tpsc_staff_phone', doc.reference_info?.phone || '');
                                _this.$set(_this.formObject, 'reference_code', doc.reference_info?.employee_id || '');
                                _this.$set(_this.formObject, 'reference_phone', doc.reference_info?.phone || '');
                                _this.$set(_this.formObject, 'reference_nid', doc.reference_info?.nid || '');
                                break;

                            case 5: // Child Merit
                                _this.$set(_this.formObject, 'merit_child_roll', doc.reference_id);
                                _this.$set(_this.formObject, 'merit_child_student_name', doc.reference_info?.name || '');
                                _this.$set(_this.formObject, 'merit_child_student_class', doc.reference_info?.class || '');
                                _this.$set(_this.formObject, 'merit_child_student_department', doc.reference_info?.department || '');
                                _this.$set(_this.formObject, 'merit_child_section_name', doc.reference_info?.section || '');
                                _this.$set(_this.formObject, 'merit_child_session_name', doc.reference_info?.session || '');
                                break;

                            case 4: // Merit Position
                            case 6: // General
                                _this.$set(_this.formObject, 'merit_child_roll', doc.reference_id);
                                _this.$set(_this.formObject, 'merit_child_student_name', doc.reference_info?.name || '');
                                _this.$set(_this.formObject, 'merit_child_student_class', doc.reference_info?.class || '');
                                _this.$set(_this.formObject, 'merit_child_student_department', doc.reference_info?.department || '');
                                _this.$set(_this.formObject, 'merit_child_section_name', doc.reference_info?.section || '');
                                _this.$set(_this.formObject, 'merit_child_session_name', doc.reference_info?.session || '');
                                break;
                        }
                    } else {
                        _this.$toastr('error', 'Error fetching data', "Error");
                    }
                });
            });
        },
        assign() {
            const _this = this;
            _this.$set(_this.formFilter, 'class_id', '');
            _this.$set(_this.formFilter, 'section_id', '');
            _this.$set(_this.formObject, 'student_id', '');
        },
    },
    mounted() {
        const _this = this;
        _this.getDataList();
        _this.assign();
        _this.getGeneralData(['class']);
    },
};
</script>

<style scoped>
.table-center {
    text-align: center;
}
.table-center .action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}


.mouse-action {
    height: 40px;
    cursor: pointer;
}
input[readonly],
textarea[readonly],
select[disabled] {
    cursor: not-allowed !important;
    background-color: #f9f9f9 !important;
    color: #666 !important;
}
</style>
