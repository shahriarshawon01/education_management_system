<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Schedule Exam"  :defaultFilter="false" :default-object="defaultFilter">
                    <template v-slot:page_top>
                        <page-top

                        :default-object="{user_id:'', session_year_id:'',class_id:'',section_id: '',department_id: '', selection_subject_mapping:[{
                            subject_name: '',
                            marks: ''
                        }]}"

                        topPageTitle="Schedule Exam" :default-add-button="can('admission_exam_add')" page-modal-title="Schedule Exam Add/Edit"></page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.class_id" name="class_id">
                                <option value="">Select Class</option>
                                <template v-for="(eachData, index) in requiredData.class">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.session_year_id" name="session_year_id">
                                <option value="">Select Session</option>
                                <template v-for="(eachData, index) in requiredData.session">
                                    <option :value="eachData.id">{{ eachData.title }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.section_id" name="section_id">
                                <option value="">Select Section</option>
                                <template v-for="(eachData, index) in requiredData.section">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.department_id" name="department_id">
                                <option value="">Select Department</option>
                                <template v-for="(eachData, index) in requiredData.department">
                                    <option :value="eachData.id">{{ eachData.department_name }}</option>
                                </template>
                            </select>
                        </div>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data" :key="data.id">
                            <td class="fw-medium">{{parseInt(dataList.from)+index}}</td>
                            <td style="text-align:center">{{showData(data.class_info, 'name')}}</td>
                            <td style="text-align:center">{{showData(data.current_sessions, 'title')}}</td>
                            <td style="text-align:center">{{showData(data.section, 'name')}}</td>
                            <td style="text-align:center">{{showData(data.department, 'department_name')}}</td>
                            <td style="text-align:center">{{ data.exam_date}}</td>
                            <td style="text-align:center">
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td style="display:flex; justify-content:center">
                                <div class="hstack gap-3 fs-15">
                                    <a v-if="can('admission_exam_update')" class="link-primary" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('admission_exam_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>

                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-lg" @submit="submitForm(formObject,'formModal')">
            <div class="row">
                <div class="col-md-3">
                    <label class="col-form-label">Class:</label>
                    <select type="text" class="form-control" v-model="formObject.class_id" v-validate="'required'" name="class_id">
                        <option value="">Select Class</option>
                        <template v-for="(eachData, index) in requiredData.class">
                            <option :value="eachData.id">{{ eachData.name }}</option>
                        </template>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="col-form-label">Session:</label>
                    <select type="text" class="form-control" v-model="formObject.session_year_id" v-validate="'required'" name="session_year_id">
                        <option value="">Select Session</option>
                        <template v-for="(eachData, index) in requiredData.session">
                            <option :value="eachData.id">{{ eachData.title}}</option>
                        </template>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="col-form-label">Section:</label>
                    <select type="text" class="form-control" v-model="formObject.section_id" v-validate="'required'" name="section_id">
                        <option value="">Select Section</option>
                        <template v-for="(eachData, index) in requiredData.section">
                            <option :value="eachData.id">{{ eachData.name }}</option>
                        </template>
                    </select>
                </div>
                 <div class="col-md-3">
                    <label class="col-form-label">Department:</label>
                    <select type="text" class="form-control" v-model="formObject.department_id" v-validate="'required'" name="department_id">
                        <option value="">Select Department</option>
                        <template v-for="(eachData, index) in requiredData.department">
                            <option :value="eachData.id">{{ eachData.department_name }}</option>
                        </template>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="col-form-label">Exam Date:</label>
                    <datepicker input_class="form-control" v-validate="'required'" v-model="formObject.exam_date" name="birthday"></datepicker>
                </div>
                <div class="col-md-3">
                    <label class="col-form-label">Exam Venue:</label>
                    <input type="text" v-model="formObject.venue" v-validate="'required'" placeholder="Venue Name" name="venue" class="form-control">
                </div>
                    <!-- <div class="col-md-3">
                            <label class="col-form-label">Building:</label>
                            <select v-model="formObject.building_id" class="form-control" v-validate="'required'" @change="updateBuilding" :name="'building_id' + index">
                                <option value="">Select Building</option>
                                <template v-for="(eachData, index) in requiredData.building">
                                    <option :value="eachData.id">{{ eachData.building_name }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="col-form-label">Floor:</label>
                            <select v-model="formObject.floor_id" class="form-control" v-validate="'required'" @change="updateFloor" :name="'floor_id' + index">
                                <option value="">Select Floor</option>
                                <template v-for="(eachData, index) in filteredFloors">
                                    <option :value="eachData.id">{{ eachData.floor_name }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="col-form-label">Room:</label>
                            <select v-model="formObject.room_id" class="form-control" v-validate="'required'" :name="'room_id' + index">
                                <option value="">Select Room</option>
                                <template v-for="(eachData, index) in filteredRooms">
                                    <option :value="eachData.id">{{ eachData.room_name }}</option>
                                </template>
                            </select>
                        </div> -->
                    <!-- <div class="col-md-3">
                        <label class="col-form-label">Total Seat:</label>
                        <input type="text" v-model="formObject.total_seat" v-validate="'required'" placeholder="Total Seat Number" name="total_seat" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="col-form-label">Set Available:</label>
                        <input type="text" v-model="formObject.seat_allowance" v-validate="'required'" placeholder="Enter Seat Number" name="seat_allowance" class="form-control">
                    </div> -->
                    <div class="col-md-3">
                        <label class="col-form-label">Exam Start_Time:</label>
                        <input type="time" v-model="formObject.starting_time" name="starting_time" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="col-form-label">Exam End_Time:</label>
                        <input type="time" v-model="formObject.ending_time" name="ending_time" class="form-control">
                    </div>
                    <!-- <div class="col-md-3">
                        <label class="col-form-label">Maximum Mark:</label>
                        <input type="text" v-model="formObject.max_mark" v-validate="'required'" placeholder="Max. Marks" name="max_mark" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="col-form-label">Pass Mark:</label>
                        <input type="text" v-model="formObject.pass_mark" v-validate="'required'" placeholder="Pass Marks" name="pass_mark" class="form-control">
                    </div> -->
                    <div class="col-md-12">
                        <label class="col-form-label">Requirements:</label>
                        <textarea rows="5" class="form-control" v-model="formObject.requirements" name="requirements"></textarea>
                    </div>
                    <!-- <div class="col-md-3">
                        <label class="col-form-label"> <input v-model="formObject.is_seat_fixed" class="form-check-input" type="checkbox" id="SwitchCheck1"> Seat Fixed:</label>
                        <input v-if="formObject.is_seat_fixed" type="text" placeholder="Enter Seat Number" v-model="formObject.seat_allowance" v-validate="'required'" name="seat_allowance" class="form-control">

                                           <label class="col-form-label">Seat Available:</label>
                                         <input type="text" v-model="formObject.seat_allowance" v-validate="'required'" name="seat_allowance" class="form-control">
                    </div> -->

                   <!-- <div v-for="(itemm, index) in formObject.selection_subject_mapping" :key="index" class="row">
                    <div class="col-md-3">
                        <label class="col-form-label">Subject Name:</label>
                        <input type="text" v-model="itemm.subject_name" v-validate="'required'" placeholder="Subject Name" :name="'subject_name_'+ index" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="col-form-label">Marks:</label>
                        <input type="text" v-model="itemm.marks" v-validate="'required'" placeholder="Marks" :name="'marks_'+ index" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="col-form-label">Action:</label>
                        <div class="form-group">
                            <button v-if="formObject.selection_subject_mapping.length > 1"  @click="removeField(index)" class="btn btn-danger btn-sm">-</button>
                            <button @click="addField" class="btn btn-primary btn-sm">+</button>
                        </div>

                    </div>
                </div> -->

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
        name: "AdmissionExamList",
        components: {PageTop, Pagination, DataTable, formModal},
        data() {
            return {
                tableHeading: ['Sl', 'Class', 'Session', 'Section','Department', 'Exam Date', 'Status', 'Action'],
                formModalId: 'formModal',
                defaultFilter: { class_id: '', session_year_id: '', section_id: '', department_id: ''},
                modalFilters: {
                    building_id: '',
                    floor_id: '',
                    room_id: '',
                },
                filteredFloors: [],
                filteredRooms: [],
                // dataList: {
                //     data: [],
                //     from: 1 // Default value for the starting index
                // },
            }
        },

        mounted(){
            this.getDataList();
            const _this = this;
            _this.getGeneralData(['class','section','session','department','building','floor','room']);
            //  _this.$set(_this.formObject, 'selection_subject_mapping', [{
            //  subject_name: "",
            //     marks: "",
            // }]);
        },
        methods : {
           // addField() {
             //   const _this = this
             //   _this.formObject.selection_subject_mapping.push(
                //    {
                   //     subject_name: "",
                     //   marks: "",
                  //  }
               // )
          //  },
          //  removeField(index) {
             //   this.formObject.selection_subject_mapping.splice(index, 1);
          //  },

          updateBuilding(evt) {
                    this.modalFilters.building_id = evt.target.value
                    this.getGeneralData(['floor'])
                },
            updateFloor(evt) {
                this.modalFilters.floor_id = evt.target.value
                this.getGeneralData(['room'])
            },

            // editData: function (data, id) {
            //     const _this = this;
            //     _this.editData(data, id, 'formModal', function (retData) {
            //         var editUrl = `${_this.urlGenerate()}/${id}/edit`;
            //         _this.getData(editUrl, 'get', {}, {}, function (retData) {
            //             _this.$store.commit('formObject', retData);
            //         });
            //     })
            // },
        },
        watch: {
            'modalFilters.building_id':  {
                immediate: true,
                handler(val) {
                    if(!this.requiredData.floor) {
                        this.filteredFloors.splice(0)
                    } else {
                        this.filteredFloors.splice(0)
                        this.requiredData.floor.forEach(item => {
                            if(item.building_id == this.modalFilters.building_id) {
                                this.filteredFloors.push(item)
                            }
                        })
                    }

                   },

                },
         'modalFilters.floor_id': {
            immediate: true,
            handler(val) {
            if(!this.requiredData.room) {
                this.filteredRooms.splice(0)
            } else {
                this.filteredRooms.splice(0)
                this.requiredData.room.forEach(item => {
                    if(item.floor_id == this.modalFilters.floor_id) {
                        this.filteredRooms.push(item)
                    }
                })

            }


            },
        }
        }
    }
</script>

<style scoped>

</style>