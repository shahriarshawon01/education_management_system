<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table
                    :table-heading="tableHeading"
                    table-title="Live Class"
                >
                    <template v-slot:page_top>
                        <page-top
                            :default-object="{
                                teacher_id: '',
                                class_id: '',
                                session_year_id: '',
                                department_id: '',
                            }"
                            topPageTitle="Live Class"
                            :default-add-button="can('live_class_add')"
                        ></page-top>
                    </template>
                    <template v-slot:filter> </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">
                                {{ parseInt(dataList.from) + index }}
                            </td>
                            <td>{{ data.topic }}</td>
                            <td>{{ showData(data.teacher, "name") }}</td>
                            <td style="text-align: center">
                                {{ showData(data.class, "name") }}
                            </td>
                            <td style="text-align: center">
                                {{
                                    showData(
                                        data.departments,
                                        "department_name"
                                    )
                                }}
                            </td>
                            <td style="text-align: center">
                                {{ showData(data.sessions, "title") }}
                            </td>
                            <td style="text-align: center">{{ data.date }}</td>
                            <!-- <td>{{ data.start_time }}</td>
                            <td>{{ data.end_time }}</td> -->
                            <td style="text-align: center">
                                {{ formatTime(data.start_time) }}
                            </td>
                            <td style="text-align: center">
                                {{ formatTime(data.end_time) }}
                            </td>
                            <td>
                                <a
                                    v-if="data.file"
                                    @click="openFile(getImage(data.file))"
                                >
                                    <i class="fa fa-download"> File</i>
                                </a>
                                <p v-else>-</p>
                            </td>
                            <td style="text-align: center">
                                <a
                                    @click="changeStatus(data)"
                                    v-html="showStatus(data.status)"
                                ></a>
                            </td>
                            <td style="text-align: center">
                                <div class="hstack gap-3 fs-15">
                                    <a
                                        v-if="can('live_class_update')"
                                        class="link-primary"
                                        @click="editData(data, data.id)"
                                        ><i class="fa fa-edit"></i
                                    ></a>
                                    <a
                                        v-if="can('live_class_delete')"
                                        class="link-danger"
                                        @click="
                                            deleteInformation(index, data.id)
                                        "
                                        ><i class="fa fa-trash"></i
                                    ></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal
            modalSize="modal-lg"
            @submit="submitForm(formObject, 'formModal')"
        >
            <div class="mb-2">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Topic :</label>
                    </div>
                    <div class="col-md-8">
                        <input
                            type="text"
                            v-model="formObject.topic"
                            class="form-control"
                            name="topic"
                            v-validate="'required'"
                            placeholder="Topic"
                        />
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">date :</label>
                    </div>
                    <div class="col-md-8">
                        <datepicker
                            v-model="formObject.date"
                            name="date"
                            input_class="form-control"
                            v-validate="'required'"
                            placeholder="Date"
                        ></datepicker>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Start Time :</label>
                    </div>
                    <div class="col-md-8">
                        <input
                            type="time"
                            v-model="formObject.start_time"
                            class="form-control"
                            name="start_time"
                        />
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">End Time :</label>
                    </div>
                    <div class="col-md-8">
                        <input
                            type="time"
                            v-model="formObject.end_time"
                            class="form-control"
                            name="end_time"
                        />
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Duration :</label>
                    </div>
                    <div class="col-md-8">
                        <input
                            type="text"
                            readonly
                            v-model="formObject.duration"
                            class="form-control"
                            name="duration"
                            placeholder="Duration"
                        />
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Teacher Name:</label>
                    </div>
                    <div class="col-md-8">
                        <select
                            v-select2
                            type="text"
                            class="form-control"
                            v-model="formObject.teacher_id"
                            v-validate="'required'"
                            name="teacher_id"
                        >
                            <option value="">Select Teacher</option>
                            <template
                                v-for="(
                                    eachData, index
                                ) in requiredData.teachers"
                            >
                                <option :value="eachData.id">
                                    {{ eachData.name }} - {{ eachData.emp_id }}
                                </option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Class :</label>
                    </div>
                    <div class="col-md-8">
                        <select
                            v-model="formObject.class_id"
                            v-validate="'required'"
                            name="class_id"
                            class="form-control"
                            @change="
                                getGeneralData({
                                    section: { class_id: formObject.class_id },
                                    departments: {
                                        class_id: formObject.class_id,
                                    },
                                })
                            "
                        >
                            <option value="">Select Class</option>
                            <template
                                v-for="(eachData, index) in requiredData.class"
                            >
                                <option :value="eachData.id">
                                    {{ eachData.name }}
                                </option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Department :</label>
                    </div>
                    <div class="col-md-8">
                        <select
                            v-model="formObject.department_id"
                            v-validate="'required'"
                            name="department_id"
                            class="form-control"
                        >
                            <option value="">Select Department</option>
                            <template
                                v-for="(
                                    eachData, index
                                ) in requiredData.departments"
                            >
                                <option :value="eachData.id">
                                    {{ eachData.department_name }}
                                </option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Sessions :</label>
                    </div>
                    <div class="col-md-8">
                        <select
                            class="form-control"
                            v-validate="'required'"
                            v-model="formObject.session_year_id"
                            name="session_year_id"
                        >
                            <option value="">Select Session</option>
                            <template
                                v-for="(data, index) in requiredData.session"
                            >
                                <option :value="data.id">
                                    {{ data.title }}
                                </option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="row mb-2">
                    <label class="form-label">Upload File :</label>
                    <div class="col-md-12">
                        <div
                            @click="clickImageInput('image')"
                            class="mb-2 mt-3"
                        >
                            <div
                                class="form-group image_upload"
                                :style="{
                                    backgroundImage:
                                        'url(' +
                                        getImage(null, 'images/upload.png') +
                                        ')',
                                }"
                                style="background-size: 360px !important"
                            >
                                <img
                                    v-if="formObject.file"
                                    :src="getImage(formObject.file)"
                                />
                                <input
                                    name="thumbnail"
                                    :v-validate="
                                        formType === 1
                                            ? 'required'
                                            : 'sometimes'
                                    "
                                    style="display: none"
                                    id="image"
                                    type="file"
                                    @change="
                                        uploadFile($event, formObject, 'file')
                                    "
                                />
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
    name: "LiveClassList",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: [
                "Sl",
                "Topic",
                "Teacher",
                "Class Name",
                "Department",
                "Session",
                "Date",
                "Start Time",
                "End Time",
                "File",
                "Status",
                "Action",
            ],
            formModalId: "formModal",
            start_time: "",
            end_time: "",
            duration: "",
        };
    },
    watch: {
        "formObject.start_time": "calculateDuration",
        "formObject.end_time": "calculateDuration",
    },

    methods: {
        calculateDuration() {
            const _this = this;

            if (_this.formObject.start_time && _this.formObject.end_time) {
                // Ensure the time is in the correct format before calculation
                const startTime = new Date(
                    `1970-01-01T${_this.formObject.start_time}:00`
                );
                const endTime = new Date(
                    `1970-01-01T${_this.formObject.end_time}:00`
                );

                if (!isNaN(startTime) && !isNaN(endTime)) {
                    let duration = (endTime - startTime) / 60000;

                    if (duration < 0) {
                        duration += 24 * 60;
                    }

                    const hours = Math.floor(duration / 60);
                    const minutes = duration % 60;

                    _this.formObject.duration = `${hours} Hours, ${minutes} Minutes`;
                }
            }
        },

        // Format the time to a 12-hour format with AM/PM
        formatTime(time) {
            if (!time) return "-";

            const [hours, minutes] = time.split(":");
            let formattedHours = hours % 12;
            formattedHours = formattedHours ? formattedHours : 12;

            const period = hours < 12 ? "AM" : "PM";
            return `${formattedHours}:${minutes} ${period}`;
        },

        // Load data for editing and ensure the correct time format is used
        loadDataForEdit(data) {
            this.formObject.start_time = data.start_time;
            this.formObject.end_time = data.end_time;
            this.calculateDuration();
        },
    },

    mounted() {
        const _this = this;
        _this.getDataList();
        _this.getGeneralData(["teachers", "session", "class"]);
        _this.$set(this.formObject,'date', '')
        _this.$set(this.formObject,'teacher_id', '')
    },
};
</script>

<style scoped>
img.upload_image {
    border: 1px solid #cdcdcd;
    cursor: pointer;
    width: 100%;
}
.eachImage.edit-file-box {
    position: relative;
}

a.file-close.pointer {
    position: absolute;
    right: 15px;
    padding: 5px;
}
</style>
