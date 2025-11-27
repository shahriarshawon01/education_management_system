<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]"
                    :default-object="{ session_id: '', class_id: '',exam_id: '', exam_type_id: '', gender_id: '', generate_type: '', template_type: '1', section_id: '' }"
                    :defaultFilter="false" :default-pagination="false" :defaultSearchButton="false"
                    table-title="Admit Card & Seat Plan" :table-responsive="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Admit Card & Seat Plan"
                            :default-add-button="false">
                            <div @click="printData('printDiv')">
                                <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i>
                                    Print</button>
                            </div>
                        </page-top>
                    </template>

                    <template v-slot:filter>
                        <div class="col-md-1">
                            <select v-model="formFilter.session_id" name="session_id" class="form-control">
                                <option value="">Session</option>
                                <template v-for="(data, index) in requiredData.session">
                                    <option :value="data.id">{{ data.title }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <select v-model="formFilter.class_id" name="class_id" class="form-control"
                                @change="getGeneralData({ section: { class_id: formFilter.class_id }, departments: { class_id: formFilter.class_id } })">
                                <option value="">Class</option>
                                <template v-for="(eachData, index) in requiredData.class">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <select v-model="formFilter.section_id" name="section_id" class="form-control">
                                <option value="">Section</option>
                                <template v-for="(eachData, index) in requiredData.section">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>

                        <!-- <div class="col-md-1">
                            <select v-model="formFilter.department_id" name="department_id" class="form-control">
                                <option value="">Department</option>
                                <template v-for="(eachData, index) in requiredData.departments">
                                    <option :value="eachData.id">{{ eachData.department_name }}</option>
                                </template>
                            </select>
                        </div> -->

                        <div class="col-md-1">
                            <select v-model="formFilter.gender_id" name="gender_id" class="form-control">
                                <option value="">Gender</option>
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <select v-model="formFilter.generate_type" name="generate_type" class="form-control">
                                <option value="">Admit or Seat</option>
                                <option value="1">Admit Card</option>
                                <option value="2">Seat Plan</option>
                            </select>
                        </div>

                        <div class="col-md-1" v-if="formFilter.generate_type == 2">
                            <select v-model="formFilter.template_type" name="template_type" class="form-control">
                                <option value="">Seat Plan Template</option>
                                <option value="1">Reguler</option>
                                <option value="2">Small</option>
                            </select>
                        </div>

                        <div class="col-md-1">
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
                        </div>



                        <!-- <div class="col-md-2">
                            <select v-model="formFilter.exam_type_id" name="exam_type_id" class="form-control">
                                <template v-for="(eachData, index) in requiredData.examinationType">
                                    <option :value="eachData.id">{{ eachData.type_name }}</option>
                                </template>
                            </select>
                        </div> -->

                        <div class="col-md-1">
                            <button class="btn btn-primary" @click.prevent.stop="generateAdmitAndSeat">Submit</button>
                        </div>
                    </template>

                    <template v-slot:bottom_data>
                        <template v-if="formFilter.template_type == 1">
                            <div class="admit_report_gen">
                                <div class="admit-card-wrapper" v-for="(student, index) in admitCardSeatPlan"
                                    :key="index">
                                    <div class="admit-card">
                                        <div class="school_info">
                                            <div class="logo">
                                                <img :src="getImage('uploads/logotpsc.png')" class="school-logo" />
                                            </div>
                                            <div class="info-container">
                                                <div class="address">
                                                    <h6 class="school_name">{{ school_name }}</h6>
                                                    <h6 style="margin-bottom: 1px;">{{ school_address }}</h6>
                                                    <h6>{{ exam_type }} - {{ student.session_name }}</h6>
                                                    <h6 class="admit" v-if="formFilter.generate_type == 1">Admit Card
                                                    </h6>
                                                    <h6 class="admit" v-else-if="formFilter.generate_type == 2">Seat
                                                        Plan
                                                    </h6>
                                                </div>
                                                <div class="student_photo">
                                                    <div class="photo-container">
                                                        <img :src="getImage(student.photo)" class="student-photo" />
                                                        <p v-if="formFilter.generate_type == 1" class="student-roll">
                                                            <strong>ID:</strong> {{
                                                                student.student_roll }}
                                                        </p>
                                                        <p><strong>Roll No:</strong> {{ student.merit_roll }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="student_info" style="margin-top: -60px !important;">
                                            <div class="student_details">
                                                <h5><strong>{{ student.student_name_en }}</strong></h5>
                                                <!-- <p style="margin-bottom: 2px;"><strong>Class:</strong> {{
                                                    student.class_name
                                                    }} | <strong>Section:
                                                    </strong> {{ student.section_name }}</p> -->
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p style="margin-bottom: 2px;"><strong>Class:</strong>
                                                            {{ student.class_name }}
                                                        </p>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <p style="margin-bottom: 2px;"><strong>Section: </strong> {{
                                                            student.section_name }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <p style="margin-bottom: 2px;"><strong>Group:</strong> {{
                                                    student.department_name }}</p>
                                                <p v-if="formFilter.generate_type == 2" style="margin-bottom: 2px;">
                                                    <strong>Relision:</strong> {{
                                                        getReligionName(student.religion) }}
                                                </p>

                                                <p v-if="formFilter.generate_type == 1" style="margin-bottom: 2px;">
                                                    <strong>Room No:</strong>
                                                    ..............
                                                </p>
                                            </div>
                                        </div>

                                        <div class="signature-container">
                                            <div v-if="formFilter.generate_type == 1" class="teacher-signature">
                                                <hr class="teacher-signature-line">
                                                <p class="teacher-signature-text">Class Teacher</p>
                                            </div>

                                            <div class="signature_section">
                                                <img :src="getImage('uploads/principalSignature.png')"
                                                    class="principal-signature" />
                                                <hr class="signature-line">
                                                <p class="signature-text">Principal</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template v-else-if="formFilter.template_type == 2">
                            <div class="admit_report_gen template-2">
                                <div class="admit-card-wrapper wrapper-template-2"
                                    v-for="(student, index) in admitCardSeatPlan" :key="index">
                                    <div class="admit-card admin-card-template-2">
                                        <div class="school_info">
                                            <div class="info-container">
                                                <div class="address">
                                                    <!-- <h6 class="school_name">{{ school_name }}</h6> -->
                                                    <h6>{{ exam_type }} - {{ student.session_name }}</h6>
                                                    <!-- <h6 class="admit">Seat Plan</h6> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="student_info" style="margin-top: -70px !important;">
                                            <div class="row student_details" style="margin-bottom: -12px;">
                                                <div class="col-md-6">
                                                    <p><strong>Class:</strong>
                                                        {{ student.class_name }}
                                                    </p>
                                                </div>

                                                <div class="col-md-6">
                                                    <p><strong>Roll No:</strong> {{ student.merit_roll }}</p>
                                                </div>
                                            </div>
                                            <p><strong>Section: </strong>
                                                {{ student.section_name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
    name: "admitCardAndSeatPlan",
    components: { PageTop, Pagination, DataTable },

    data() {
        return {
            admitCardSeatPlan: [],
            session: '',
            exam_type: ''
        };

    },
    methods: {

        getReligionName(religion) {
            switch (parseInt(religion)) {
                case 1:
                    return 'Islam';
                case 2:
                    return 'Hindu';
                case 3:
                    return 'Buddhist';
                case 4:
                    return 'Christian';
                default:
                    return 'Others';
            }
        },

        formatDate(date) {
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = String(d.getFullYear()).slice(-2);
            return `${day}-${month}-${year}`;
        },

        generateAdmitAndSeat() {
            const { class_id, session_id, section_id, exam_type_id, gender_id, generate_type, template_type } = this.formFilter;

            const validationMessages = {
                class_id: 'Please select the class.',
                session_id: 'Please select the session.',
                section_id: 'Please select the section.',
                exam_type_id: 'Please select exam.',
                generate_type: 'Please select generate type.',
                template_type: 'Please select seat plan template.'
            };

            // Validate fields
            if (!class_id) {
                this.$toastr('error', validationMessages.class_id, "Validation Error");
                return;
            }
            if (!session_id) {
                this.$toastr('error', validationMessages.session_id, "Validation Error");
                return;
            }
            if (!section_id) {
                this.$toastr('error', validationMessages.section_id, "Validation Error");
                return;
            }

            if (!exam_type_id) {
                this.$toastr('error', validationMessages.exam_type_id, "Validation Error");
                return;
            }
            if (!generate_type) {
                this.$toastr('error', validationMessages.generate_type, "Validation Error");
                return;
            }
            // if (!template_type) {
            //     this.$toastr('error', validationMessages.template_type, "Validation Error");
            //     return;
            // }

            this.axios.get('/api/admit_card_seat_plan', {
                params: { class_id, session_id, section_id, exam_type_id, gender_id, generate_type, template_type }
            }).then(response => {
                const result = response.data.result;


                if (!result.data || result.data.length === 0) {
                    this.$toastr('error', 'No Student Found', "Validation Error");
                    return;
                }

                this.admitCardSeatPlan = result.data;
                this.exam_type = result.examName.type_name;
                this.school_name = result.school_name;
                this.school_address = result.school_address;

            }).catch(error => {
                console.error("Error fetching admit card and seat plan:", error.response ? error.response.data : error.message);
                this.$toastr('error', 'Failed to fetch data.', "Error");
            });
        },
    },

    mounted() {
        const _this = this;
        _this.getGeneralData(['class', 'session', 'examName']);

    },
}
</script>
<style scoped>
.admit_report_gen {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    page-break-inside: avoid;
}

.admit-card-wrapper {
    width: 30%;
    margin: 1%;
    box-sizing: border-box;
    page-break-inside: avoid;
}

.admit-card {
    position: relative;
    border: 1px solid #000;
    padding: 10px;
    width: 100%;
    height: 300px;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.admit-card.admin-card-template-2 {
    position: relative;
    border: 1px solid #000;
    padding: 10px;
    width: 80%;
    height: 100px;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.admit-card-wrapper+.admit-card-wrapper {
    margin-left: 10px;
}

.student-photo {
    width: 70px;
    height: 85px;
    border: 2px solid #000;
}

.school_info {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
}

.logo {
    margin-right: 5px;
    flex-shrink: 0;
}

.school-logo {
    width: 50px;
    height: 50px;
    border: 2px solid #fff;
    border-radius: 50%;
    margin-bottom: 101px;
}

.school_name {
    margin: 0;
    align-items: center;
    font-weight: bold !important;
}

.admit {
    border: 2px solid black;
    border-radius: 23px;
    text-align: center;
    width: 100px;
    font-size: 14px;
    padding: 2px;
    margin: 0 auto;
    margin-bottom: 13px;
    margin-top: -6px;
}

.info-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.address {
    text-align: center;
    flex: 1;
    margin-bottom: 60px;
}

.student_photo {
    display: flex;
    justify-content: center;
    align-items: center;
}

.photo-container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.student-photo {
    width: 77px;
    height: 90px;
    border: 2px solid #000;
    margin-bottom: 5px;
}

.student-roll {
    margin: 0;
    font-size: 14px;
    text-align: center !important;
    margin-left: -2px;
}

.signature_section {
    position: absolute;
    bottom: 10px;
    right: 10px;
    text-align: center;
    width: 70px;
}

.teacher-signature {
    text-align: left;
    margin-left: 0;
    padding-left: 0px;
    margin-top: 15px;
}

.principal-signature {
    width: 70px;
    height: auto;
}

.signature-line {
    margin: 2px 0;
}

.teacher-signature-line {
    margin: 2px 0;
    width: 100px;
}

.signature-text {
    font-size: 14px;
    font-weight: bold;
    margin-top: 5px;
}

.teacher-signature-text {
    margin-top: 5px;
    font-size: 14px;
    font-weight: bold;
}

@media print {

    .admit_report_gen {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        page-break-inside: avoid;
        gap: 2px;
        width: 100%;
    }

    .admit-card-wrapper {
        width: calc(48% - 2px);
        margin: 0;
        height: auto;
        box-sizing: border-box;
        page-break-inside: avoid;
    }

    .admit-card {
        width: 100%;
        height: 260px;
        border: 1px solid #000;
        padding: 3px;
        background-color: #fff;
        box-shadow: none;
        page-break-inside: avoid;
    }

    .school-logo {
        width: 50px;
        height: 50px;
    }

    .student-photo {
        width: 60px;
        height: 80px;
    }

    /* Apply styles only when template_type == 2 */
    .admit_report_gen.template-2 {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        page-break-inside: avoid;
        gap: 2px;
        width: 100%;
    }

    .admit-card-wrapper.wrapper-template-2 {
        width: calc(33% - 2px);
        /* Adjust to 4 cards per row */
        margin: 0;
        height: auto;
        box-sizing: border-box;
        page-break-inside: avoid;
    }

    .admit-card.template-2 {
        width: 100%;
        height: 130px;
        /* Adjust height for template 2 */
        border: 1px solid #000;
        padding: 2px;
        background-color: #fff;
        box-shadow: none;
        page-break-inside: avoid;
    }

    /* Ensure that each page has no more than 16 cards */
    .admit_report_gen.template-2::after {
        content: "";
        page-break-after: always;
        display: block;
    }

    body {
        margin: 0;
        padding: 0;
    }
}
</style>
