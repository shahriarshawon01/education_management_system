<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :defaultSearchButton="false" :table-heading="[]"
                    :default-object="{ class_id: 'all', session_id: 'all', gender_id: 'all', date: '', religion_id: 'all' }"
                    :defaultFilter="false" :default-pagination="false" table-title="SMS All Students"
                    :table-responsive="false">

                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="SMS to All Students"
                            :default-add-button="false"></page-top>
                    </template>

                    <template v-slot:filter>
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <datepicker v-model="formFilter.date" name="date" input_class="form-control"
                                    placeholder="Enter SMS Date">
                                </datepicker>
                            </div>
                        </div>

                        <div class="row col-md-12 mb-3">
                            <div class="col-md-2">
                                <select v-model="formFilter.class_id" name="class_id" class="form-control">
                                    <option value="all">All Class</option>
                                    <template v-for="(eachData) in requiredData.class">
                                        <option :value="eachData.id">{{ eachData.name }}</option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select v-model="formFilter.session_id" name="session_id" class="form-control">
                                    <option value="all">All Session</option>
                                    <template v-for="(data) in requiredData.session">
                                        <option :value="data.id">{{ data.title }}</option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" v-model="formFilter.gender_id" name="gender_id">
                                    <option value="all">All Gender</option>
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" v-model="formFilter.religion_id" name="religion_id">
                                    <option value="all">All Religion</option>
                                    <option value="1">Islam</option>
                                    <option value="2">Hindu</option>
                                    <option value="3">Buddhist</option>
                                    <option value="4">Christian</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-primary w-100" @click.prevent.stop="getStudent">Get Student</button>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-primary w-100"
                                    @click.prevent.stop="statusModal(formFilter, formFilter.id, 'createModal')">Send
                                    Custom SMS
                                </button>
                            </div>
                        </div>
                    </template>

                    <template v-slot:bottom_data>
                        <form @submit.prevent="submitData()">
                            <template v-if="students.length > 0">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Student Name</th>
                                            <th>Student ID</th>
                                            <th>Merit Roll</th>
                                            <th>Class</th>
                                            <th>Section</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Message</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(student, index) in students" :key="student.student_id">
                                            <td>{{ index + 1 }}</td>
                                            <td style="text-align: left;">{{ student.student_name_en }}</td>
                                            <td style="text-align: center;">{{ student.student_roll }}</td>
                                            <td style="text-align: center;">{{ student.merit_roll }}</td>
                                            <td style="text-align: center;">{{ student.class_name }}</td>
                                            <td style="text-align: center;">{{ student.section_name }}</td>
                                            <td style="text-align: center; color: darkblue;">{{ student.father_phone }}
                                            </td>
                                            <td style="text-align: center;">
                                                <strong style="text-align: center; color: green;">Active</strong>
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="checkbox" class="select-student-id"
                                                    :value="student.student_id" v-model="selectedStudentIds" />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </template>
                        </form>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-l" modal-id="createModal" @submit="submitStatus(formObject)">
            <div class="mb-2">
                <label class="form-label">Write Custom SMS Here....</label>
                <textarea name="description" class="form-control" v-model="formObject.description"
                    v-validate="'required'" rows="3" placeholder="Type sms "></textarea>
            </div>
        </formModal>
    </div>
</template>

<script>
import DataTable from "../../components/dataTable";
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";

export default {
    name: "smsAllStudent",
    components: { PageTop, DataTable, formModal },

    data() {
        return {
            students: [],
            selectedStudentIds: [],
        };
    },
    methods: {

        statusModal(data) {
            this.$set(this.formFilter, 'comments', data.comments);
            this.$store.state.activeFormType = 2;
            this.edit = false;
            this.add = true;
            this.openModal('createModal', 'Custom SMS');
        },

        submitStatus() {
            if (this.selectedStudentIds.length === 0) {
                this.$toastr('error', 'Please select at least one student.', "Error");
                return;
            }

            this.axios.post('/api/custom_sms_to_student', {
                student_id: this.selectedStudentIds,
                message: this.formObject.description
            })
                .then(response => {
                    this.$toastr('success', response.data.message, "Success");
                    this.formObject.description = '';
                    this.selectedStudentIds = [];
                })
                .catch(error => {
                    console.error("Error sending SMS:", error);
                    this.$toastr('error', 'Failed to send SMS. Please try again.', "Error");
                });
        },

        getStudent() {
            const { class_id, session_id, gender_id, religion_id, date } = this.formFilter;

            this.axios.get('/api/sms_to_all_student', {
                params: {
                    class_id,
                    session_id,
                    gender_id,
                    religion_id,
                    date
                }
            })
                .then(response => {
                    if (response.data.result && Array.isArray(response.data.result)) {
                        if (response.data.result.length === 0) {
                            this.$toastr('warning', 'No records found for the selected criteria.', "Warning");
                        } else {
                            this.students = response.data.result;
                            this.selectedStudentIds = this.students.map(student => student.student_id);
                        }
                    } else {
                        console.error("Invalid response structure:", response.data);
                        this.$toastr('error', 'Invalid response structure', "Error");
                    }
                })
                .catch(error => {
                    console.error("There was an error fetching student data:", error);
                });
        },

        sendSMS(studentId) {
            alert(`Sending SMS to student with id ${studentId}`);
        },
    },
    mounted() {
        this.getGeneralData(['class', 'session', 'section', 'departments']);
    },
}
</script>

<style scoped>
.main_component {
    padding: 20px;
}

.table thead th {
    background-color: #cccbcb;
}

.table th,
.table td {
    text-align: center;
    color: black;
    font-size: 14px;
}

.button-group {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
}

.btn {
    transition: background-color 0.3s, color 0.3s;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary:hover {
    background-color: #6c757d;
}
</style>
