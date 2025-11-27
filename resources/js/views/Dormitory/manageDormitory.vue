<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Dormitory List">
                    <template v-slot:page_top>
                        <page-top :default-object="{ employee_id: '' }" topPageTitle="Dormitory List"
                            :default-add-button="can('dormitory_add')"></page-top>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.dormitory_name }}</td>
                            <td>{{ data.dormitory_no }}</td>
                            <td>{{ showData(data.employee, 'name') }}</td>
                            <td>{{ data.total_floor }}</td>
                            <td>{{ data.total_room }}</td>
                            <td>{{ data.total_seat }}</td>
                            <td>{{ data.location }}</td>
                            <td class="table-center">
                                <a class="action-buttons" @click="changeStatus(data)"
                                    v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="table-center">
                                <div class="hstack gap-3 fs-15 action-buttons">
                                    <a v-if="can('dormitory_update')" class="link-primary"
                                        @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('dormitory_delete')" class="link-danger"
                                        @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-lg" @submit="submitForm(formObject, 'formModal')">
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-12">
                        <div v-if="selectedEmployee" class="mt-2 p-2 bg-light border rounded">
                            <span class="text-primary font-weight-bold"><strong>Teacher: </strong> </span>
                            <span>{{ selectedEmployee.name }}</span>
                            <br>
                            <span class="text-secondary"><strong>Designation:</strong> {{
                                selectedEmployee.designation
                            }}</span>
                        </div>
                        <label class="col-form-label">Dormitory Author</label>
                        <autocomplete v-model="formObject.employee_id" api-url="/api/get-employee"
                            placeholder="Enter Employee ID" name="employee_id" validation_name="Employee ID"
                            @select="fillEmployeeData" />
                    </div>

                    <div class="col-md-12">
                        <label class="col-form-label">Dormitory Number</label>
                        <input type="number" v-model="formObject.dormitory_no" name="dormitory_no" class="form-control"
                            placeholder="Enter Dormitory Number">
                    </div>
                    <div class="col-md-12">
                        <label class="col-form-label">Dormitory Name</label>
                        <input type="text" v-model="formObject.dormitory_name" name="dormitory_name"
                            class="form-control" v-validate="'required'" placeholder="Enter Dormitory Name">
                    </div>
                    <div class="col-md-12">
                        <label class="col-form-label">Location</label>
                        <input type="text" v-model="formObject.location" v-validate="'required'" name="location"
                            class="form-control" placeholder="Enter Location">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="col-md-12">
                        <label class="col-form-label">Total Seat</label>
                        <input type="number" v-model="formObject.total_seat" v-validate="'required'" name="total_seat"
                            class="form-control" placeholder="Enter Total Seat">
                    </div>

                    <div class="col-md-12">
                        <label class="col-form-label">Total Floor</label>
                        <input type="number" v-model="formObject.total_floor" v-validate="'required'" name="total_floor"
                            class="form-control" placeholder="Enter Total Floor">
                    </div>
                    <div class="col-md-12">
                        <label class="col-form-label">Total Room</label>
                        <input type="number" v-model="formObject.total_room" v-validate="'required'" name="total_room"
                            class="form-control" placeholder="Enter Total Room">
                    </div>

                    <div class="col-md-12">
                        <label class="col-form-label">Dormitory Description</label>
                        <textarea name="description" class="form-control" v-model="formObject.description" rows="3"
                            placeholder="Dormitory Description"></textarea>
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
import GeneralModal from "../../components/generalModal"

export default {
    name: "manageDormitory",
    components: { PageTop, Pagination, DataTable, formModal, GeneralModal },
    data() {
        return {
            tableHeading: ["Sl", "Dormitory Name", "Dormitory No", "Dormitory Author", "Dormitory Floor", "Total Room", "Total Seat", "Address", "Status", "Action"],
            formModalId: "formModal",
            details: {},
            selectedEmployee: null
        };
    },
    methods: {

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

                this.selectedEmployee = {
                    name: employee.name,
                    designation: employee.designation
                };

                this.$toastr('success', 'Employee data filled successfully!', 'Success');
            } else {
                this.$toastr('error', 'Employee data not found!', 'Error');
            }
        },

        editDataInformation: function (data, id) {
            const _this = this;
            _this.editData(data, id, 'formModal', function (retData) {
                var editUrl = `${_this.urlGenerate()}/${id}/edit`;
                console.log('editUrl', editUrl);

                _this.getData(editUrl, 'get', {}, {}, function (retData) {
                    if (retData.status === 1) {
                        const canteenData = retData;
                        console.log('canteenData', retData);

                        _this.$set(_this.formObject, 'comments', canteenData.comments);

                    } else {
                        _this.$toastr('error', 'Error fetching data', "Error");
                    }
                });
            });
        },
    },
    mounted() {
        this.getDataList();
    },
};
</script>

<style scoped>
.table-center .action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}
</style>
