<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Exam Grade" :default-object="{grade_number_id:''}">
                    <template v-slot:page_top>
                        <page-top :default-object="{grade_number_id:''}" topPageTitle="Exam Grade" :default-add-button="can('examination_grade_add')" ></page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select class="form-control" v-model="formFilter.grade_number_id" name="grade_number_id">
                                <option value="">Select</option>
                                <template v-for="(data,index) in requiredData.examGradeNumber">
                                    <option :value="data.id">{{data.grade_number}}</option>
                                </template>
                            </select>
                        </div>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td class="table-center">{{ showData(data.grade_number, 'grade_number') }}</td>
                            <td class="table-center">{{ data.grade_name }}</td>
                            <td class="table-center">{{ data.grade_point }}</td>
                            <td class="table-center">{{ data.mark_from }}</td>
                            <td class="table-center">{{ data.mark_to }}</td>
                            <td class="table-center">
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <a v-if="can('examination_grade_update')" class="link-primary" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('examination_grade_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>

        <formModal modalSize="modal-md" @submit="submitForm(formObject, 'formModal')">

            <div class="mb-2">
                <div class="row mb-2">
                        <label class="form-label">Exam Grade Number </label>
                    <div class="col-md-12">
                        <select class="form-control" v-model="formObject.grade_number_id" name="grade_number_id" v-validate="'required'">
                            <option value="">Select Grade Number</option>
                            <template v-for="(data,index) in requiredData.examGradeNumber">
                                <option :value="data.id">{{data.grade_number}}</option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="row mb-2">
                        <label class="form-label">Grade Name</label>
                    <div class="col-md-12">
                        <input type="text" v-model="formObject.grade_name" v-validate="'required'" name="grade_name" class="form-control" placeholder="Grade Name">
                    </div>
                </div>
                <div class="row mb-2">
                        <label class="form-label">Grade Point</label>
                    <div class="col-md-12">
                        <input type="text" v-model="formObject.grade_point" v-validate="'required'" name="grade_point" class="form-control" placeholder="Grade Point">
                    </div>
                </div>
                <div class="row mb-2">
                    <label class="form-label">Mark Form</label>
                    <div class="col-md-12">
                        <input type="number" v-model="formObject.mark_from" v-validate="'required'" name="mark_from" class="form-control" placeholder="Mark Form">
                    </div>
                </div>
                <div class="row mb-2">
                    <label class="form-label">Mark To</label>
                    <div class="col-md-12">
                        <input type="number" v-model="formObject.mark_to" v-validate="'required'" name="mark_to" class="form-control" placeholder="Mark To">
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
        name: "ExamGradeList",
        components: { PageTop, Pagination, DataTable, formModal },
        data() {
            return {
                tableHeading: ["Sl","Grade Number","Grade Name","Grade Point","Mark From","Mark To", "Status","Action"],
                formModalId: "formModal",
            };
        },
        mounted() {
            const _this = this;
            _this.getDataList();
            _this.getGeneralData(['examGradeNumber']);
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
</style>

