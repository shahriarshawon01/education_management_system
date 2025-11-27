<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :default-object="{session_year_id:'',class_id:'',teacher_id:'',section_id:'',room_id:'',starting_hour:'',end_hour:''}" table-title="Class Routine" :defaultFilter="false">
                    <template v-slot:page_top>
                        <page-top :default-object="selectedObject" topPageTitle="Class Routine" :default-add-button="can('class_routines_add')" ></page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.session_year_id" name="session_year_id">
                                <option value="">Select Session</option>
                                <template v-for="(data, index) in requiredData.session">
                                    <option :value="data.id">{{ data.title }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="formFilter.class_id" name="class_id"
                                        class="form-control"
                                @change="getGeneralData({ section: { class_id: formFilter.class_id },examination: { class_id: formFilter.class_id } })">
                                <option value="">Select Class</option>
                                <template v-for="(eachData, index) in requiredData.class">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="formFilter.section_id" name="section_id"
                                        class="form-control">
                                <option value="">Select Section</option>
                                <template v-for="(eachData, index) in requiredData.section">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                    </template>
                    <template v-slot:thead>
                        <thead class="data_table">
                            <tr>
                                <th scope="col">Day/Period</th>
                                <th v-for="data in dataList.period">
                                <span>Period - {{data}}</span>
                                </th>
                            </tr>
                        </thead>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.routines">
                            <td>{{index}}</td>
                            <template v-for="(routine, rIndex) in data">
                                <td>
                                    <strong>
                                        Class :
                                    </strong>
                                    {{showData(routine.class, 'name')}}<br>
                                    <strong>
                                        Subject :
                                    </strong>
                                    {{showData(routine.subject, 'name')}} <br>
                                    <strong>
                                        Session :
                                    </strong>
                                    {{showData(routine.sessions, 'title')}}<br>
                                    <strong>
                                        Section :
                                    </strong>
                                    {{showData(routine.sections, 'name')}}<br>
                                    <strong>
                                        Building :
                                    </strong>
                                    {{showData(routine.building, 'name')}}<br>
                                    <strong>
                                        Class Room :
                                    </strong>
                                    {{showData(routine.class_room, 'name')}}<br>
                                    <strong>
                                        Class Teacher :
                                    </strong>
                                    {{showData(routine.teacher,'name')}}<br>
                                    <strong>Time : </strong>
                                    ({{formatTime(routine.starting_hour)}}-{{formatTime(routine.end_hour)}}) <br>
                                    <strong>Image:</strong>
                                        <a v-if="routine.image" @click="openFile(getImage(routine.image))">
                                        <!-- <img :src="getImage(routine.image)" class="img-thumbnail" style="max-width: 100px; height: auto;" /> -->
                                        <i class="fa fa-download"> File</i>
                                        </a>
                                        <br>
                                    <strong><a v-if="can('class_routines_update')" class="link-primary" @click="editData(routine, routine.id)"><i class="fa fa-edit"></i></a> </strong> --
                                    <strong><a v-if="can('class_routines_delete')" class="link-danger" @click="deleteInformation(index, routine.id)"><i class="fa fa-trash"></i></a></strong>

                                </td>
                            </template>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-lg" @submit="submitForm(formObject, 'formModal')">

            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Choose a Day</label>
                    </div>
                    <div class="col-md-8">
                        <select type="text" class="form-control" v-model="formObject.day" v-validate="'required'" name="day">
                            <option value="">Select day</option>
                            <option value="saturday">Saturday</option>
                            <option value="sunday">Sunday</option>
                            <option value="monday">Monday</option>
                            <option value="tuesday">Tuesday</option>
                            <option value="wednesday">Wednesday</option>
                            <option value="thursday">Thursday</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Session:</label>
                    </div>
                    <div class="col-md-8">
                        <select class="form-control" v-model="formObject.session_year_id" name="session_year_id"
                            v-validate="'required'">
                            <option value="">Select session</option>
                            <template v-for="(data, index) in requiredData.session">
                                <option :value="data.id">{{ data.title }}</option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Class:</label>
                    </div>
                    <div class="col-md-8">
                        <select class="form-control" v-model="formObject.class_id" name="class_id"  @change="getGeneralData({ subjects: { class_id: formObject.class_id },section: { class_id: formObject.class_id } })" v-validate="'required'">
                            <option value="">Select class</option>
                            <template v-for="(data,index) in requiredData.class">
                                <option :value="data.id">{{data.name}}</option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Section:</label>
                    </div>
                    <div class="col-md-8">
                        <select type="text" class="form-control" v-model="formObject.section_id" v-validate="'required'" name="section">
                            <option value="">Select section</option>
                            <template v-for="(eachData, index) in requiredData.section">
                                <option :value="eachData.id">{{ eachData.name }}</option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Subject:</label>
                    </div>
                    <div class="col-md-8">
                        <select type="text" class="form-control" v-model="formObject.subject_id" v-validate="'required'" name="subject_id">
                            <option value="">Select subject</option>
                            <template v-for="(eachData, index) in requiredData.subjects">
                                <option :value="eachData.id">{{ eachData.name }}</option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Building:</label>
                    </div>
                    <div class="col-md-8">
                        <select type="text" class="form-control" v-model="formObject.building_id"
                            name="building_id" @change="getGeneralData({ classRoom: { building_id: formObject.building_id } })">
                            <option value="">Select building</option>
                            <template v-for="(eachData, index) in requiredData.building">
                                <option :value="eachData.id">{{ eachData.name }}</option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Class Room:</label>
                    </div>
                    <div class="col-md-8">
                        <select type="text" class="form-control" v-model="formObject.room_id" v-validate="'required'" name="room_id">
                            <option value="">Select classRoom</option>
                            <template v-for="(eachData, index) in requiredData.classRoom">
                                <option :value="eachData.id">{{ eachData.name }}</option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Teachers:</label>
                    </div>
                    <div class="col-md-8">
                        <select v-select2 type="text" class="form-control" v-model="formObject.teacher_id" v-validate="'required'" name="teacher_id">
                            <option value="">Select teacher</option>
                            <template v-for="(eachData, index) in requiredData.teachers">
                                <option :value="eachData.id">{{ eachData.name }} - {{ eachData.employee_id }}</option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Start Time:</label>
                    </div>
                    <div class="col-md-8">
                        <input type="time" v-model="formObject.starting_hour"  name="starting_hour" class="form-control">
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">End Time:</label>
                    </div>
                    <div class="col-md-8">
                        <input type="time" v-model="formObject.end_hour"  name="end_hour" class="form-control">
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <label class="form-label">Image</label>
                <div @click="clickImageInput('image')" class="mb-2 mt-3">
                      <div class="form-group image_upload" :style="{ backgroundImage: 'url(' + getImage(null, 'images/upload.png') + ')' }" style="background-size:260px !important">
                          <img v-if="formObject.image" :src="getImage(formObject.image)">
                          <input name="thumbnail" v-validate="'required'" :v-validate="formType === 1 ? 'required' : 'sometimes'" style="display: none;" id="image" type="file" @change="uploadFile($event, formObject, 'image')">
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
        name: "ClassRoutine",
        components: { PageTop, Pagination, DataTable, formModal },
        data() {
            return {
                tableHeading: ["Sl","Name","Status", "Action"],
                formModalId: "formModal",
                selectedObject : {
                    day:'',
                    class_id:'',
                    session_year_id:'',
                    section_id:'',
                    room_id:'',
                    subject_id:'',
                    teacher_id: '',
                    building_id: '',
                    teacher_id: ''
                }
            };
        },
        methods:{
            formatTime(time) {
            if (!time) return '-';

            const [hours, minutes] = time.split(':');

            let formattedHours = hours % 12;
            formattedHours = formattedHours ? formattedHours : 12;

            const formattedTime = `${formattedHours}:${minutes}`;
            const period = hours < 12 ? 'am' : 'pm';

            return `${formattedTime} ${period}`;
            }
        },
        mounted() {
            const _this = this;
            _this.getDataList();
            _this.getGeneralData(['class','session','teachers','building','subjects','section','classRoom']);
            _this.$set(_this.formObject, 'teacher_id', '');
        },
    };
</script>

<style scoped>

</style>

