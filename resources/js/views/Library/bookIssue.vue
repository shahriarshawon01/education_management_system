<template :key="$route.meta.name">
    <div class="main_component">
        <data-table :table-heading="tableHeading">
            <template v-slot:page_top>
                <page-top top-page-title="Book Issue" v-if="can('book_issue_view')"></page-top>
            </template>
            <template v-slot:data>
                <tr v-for="(data, index) in dataList.data" :key="data.id">
                    <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>

                    <!-- Member Name -->
                    <td>
                        <span>{{ data.member_name || 'N/A' }}</span>
                    </td>

                    <!-- Member Type -->
                    <td>
                        <span v-if="data.type == 1">Student</span>
                        <span v-else-if="data.type == 2">Teacher</span>
                        <span v-else-if="data.type == 3">Staff</span>
                        <span v-else-if="data.type == 4">Library Member</span>
                        <span v-else>Unknown</span>
                    </td>

                    <!-- Member ID / Roll -->
                    <td>
                        <span>{{ data.member_code || '-' }}</span>
                    </td>

                    <!-- Book Issue Info -->
                    <td>{{ data.issue_date }}</td>
                    <td>{{ data.total_books }}</td>
                    <td>{{ data.returned_books }}</td>

                    <!-- Actions -->
                    <td>
                        <div class="hstack gap-3 fs-15">
                            <a class="link-info" v-if="can('book_issue_view')" title="View Issued Books"
                                @click="viewIssuedBook(data)">
                                <i class="fa fa-eye"></i>
                            </a>

                            <a class="link-primary" v-if="can('book_issue_update')" @click="editData(data, data.id)">
                                <i class="fa fa-edit"></i>
                            </a>

                            <template v-if="data.returned_books < data.total_books">
                                <a class="link-success" title="Receive All Books" @click="receiveAllBooks(data.id)">
                                    <i class="fa fa-check"></i>
                                </a>
                            </template>

                            <a class="link-danger" v-if="can('book_issue_delete')"
                                @click="deleteInformation(index, data.id)">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            </template>

        </data-table>
        <formModal modalSize="modal-xs" @submit="submitForm(formObject, 'formModal')">
            <div class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Type</label>
                    </div>
                    <div class="col-md-8">
                        <select class="form-control" v-model="formObject.type" v-validate="'required'" name="type"
                            @change="handleTypeChange">
                            <option value="">--Select--</option>
                            <option value="1">Student</option>
                            <option value="2">Teacher</option>
                            <option value="3">Staff</option>
                            <option value="4">Library Member</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-2" v-if="formObject.type == 1">
                    <div class="col-md-4">
                        <label class="col-form-label">Student</label>
                    </div>
                    <div class="col-md-8">
                        <select v-select2 class="form-control" v-model="formObject.student_id" v-validate="'required'"
                            name="student" id="student">
                            <option value="">Search Student</option>
                            <template v-for="(data, bIndex) in requiredData.students">
                                <option :value="data.id">{{ data.student_name_en }} - [Roll : {{ data.student_roll }}]
                                </option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="row mt-2" v-if="formObject.type == 2">
                    <div class="col-md-4">
                        <label class="col-form-label">Teacher</label>
                    </div>
                    <div class="col-md-8">
                        <select v-select2 class="form-control" v-model="formObject.teacher_id" v-validate="'required'"
                            name="student" id="student">
                            <option value="">Teachers</option>
                            <template v-for="(data, bIndex) in requiredData.teachers">
                                <option :value="data.id">{{ data.name }} - [Employee ID : {{ data.emp_id }}]</option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="row mt-2" v-if="formObject.type == 3">
                    <div class="col-md-4">
                        <label class="col-form-label">Staff</label>
                    </div>
                    <div class="col-md-8">
                        <select v-select2 class="form-control" v-model="formObject.teacher_id" v-validate="'required'"
                            name="student" id="student">
                            <option value="">Staff</option>
                            <template v-for="(data, bIndex) in requiredData.staff">
                                <option :value="data.id">{{ data.name }} - [Employee ID : {{ data.emp_id }}]</option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="row mt-2" v-if="formObject.type == 4">
                    <div class="col-md-4">
                        <label class="col-form-label">Member :</label>
                    </div>
                    <div class="col-md-8">
                        <select v-select2 class="form-control" v-model="formObject.member_id" v-validate="'required'"
                            name="member_id">
                            <option value="">Select</option>
                            <template v-for="(data, bIndex) in requiredData.library_member">
                                <option v-if="data.student_roll != null" :value="data.id">
                                    {{ data.student_name }} - [Roll : {{ data.student_roll }}]
                                </option>
                                <option v-else :value="data.id">
                                    {{ data.teacher_name }} - [Employee ID :{{ data.emp_id }}]
                                </option>
                                <option v-else :value="data.id">
                                    {{ data.teacher_name }} - [Employee ID :{{ data.emp_id }}]
                                </option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-4">
                        <label class="col-form-label">Issue Date :</label>
                    </div>
                    <div class="col-md-8">
                        <datepicker v-model="formObject.issue_date" name="issue_date" id="issue_date"
                            input_class="form-control" v-validate="'required'" placeholder="Issue Date"></datepicker>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-4">
                        <label class="col-form-label">Expected Return :</label>
                    </div>
                    <div class="col-md-8">
                        <datepicker v-model="formObject.expected_return" name="expected_return" id="expected_return"
                            input_class="form-control" placeholder="Expected Return Date"></datepicker>
                    </div>
                </div>
                <hr>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 70% !important;">Book</th>
                            <th style="width: 30% !important;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, index) in formObject.book" :key="index">
                            <td>
                                <select v-select2 class="form-control" v-model="row.book_accession_id"
                                    v-validate="'required'" name="books" id="books">
                                    <option value="">Search Book</option>
                                    <template v-for="(data, bIndex) in requiredData.stocked_books">
                                        <option :value="data.b_id">{{ data.book_title }}</option>
                                    </template>
                                </select>
                            </td>
                            <td class="text-center">
                                <a class="btn btn-outline-success"
                                    @click="addRow(formObject.book, { book_accession_id: '' })">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </a>
                                <a class="btn btn-outline-danger" v-if="formObject.book.length > 1"
                                    @click="deleteRow(formObject.book, index)">
                                    <i class="fa fa-close" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>
        </formModal>

        <general-modal modal-id="detailsModal" modal-size="modal-lg">
            <template v-slot:title>
            </template>
            <template v-slot:body>
                <table class="table table-bordered table-striped-columns">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Book Name</th>
                            <th>Issue Date</th>
                            <th>Expected Return</th>
                            <th>Return Date</th>
                            <th>Status</th>
                            <th>Return</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(data, index) in details">
                            <td class="fw-medium">{{ index + 1 }}</td>
                            <td>{{ data.book_title }}</td>
                            <td>{{ data.issue_date }}</td>
                            <td>{{ data.expected_return }}</td>
                            <td>{{ data.return_date }}</td>
                            <td>
                                <template v-if="data.status === 1">
                                    <span class="badge badge-soft-success">Issued</span>
                                </template>
                                <template v-if="data.status === 2">
                                    <span class="badge badge-soft-info">Returned</span>
                                </template>
                            </td>
                            <td>
                                <template v-if="data.status === 1">
                                    <a class="link-info" @click="returnSingleBook(data.id)"
                                        title="Return Book"><strong><i class="fa fa-undo"></i></strong></a>
                                </template>
                                <template v-else>
                                    <a class="link-success"><i class="fa fa-check"></i></a>
                                </template>
                            </td>
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
import GeneralModal from "../../components/generalModal.vue";
import axios from "axios";
export default {
    name: "bookIssue",
    components: { GeneralModal, PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            tableHeading: ['Sl', 'Member Name', 'Member Type', 'Member ID', 'Issue Date', 'Total Book', 'Returned', 'Action'],
            book: [{ book_accession_id: '' }],
            details: []
        }
    },
    methods: {
        handleTypeChange() {
            const _this = this;
            if (_this.formObject.type == '1') {
                _this.getGeneralData(['students']);
            } else if (_this.formObject.type == '2') {
                _this.getGeneralData(['teachers']);
            } else if (_this.formObject.type == '3') {
                _this.getGeneralData(['staff']);
            }
            else if (_this.formObject.type == '4') {
                _this.getGeneralData(['library_member']);
            }
        },
        viewIssuedBook: function (data) {
            const _this = this;
            var url = `${_this.urlGenerate()}/${data.id}`;
            _this.getData(url, "get", {}, {}, function (retData) {
                _this.details = retData;
                _this.openModal('detailsModal', "Details of Issued Book");
            });
        },
        returnSingleBook: function (id) {
            const _this = this;
            axios.post('/api/return_single_book', { id: id })
                .then(response => {
                    if (response.data.message) {
                        _this.$toastr('success', response.data.message, 'Success');
                        const book = _this.details.find(b => b.id === id);
                        if (book) {
                            book.status = 2;
                            book.return_date = new Date().toISOString().split('T')[0];
                        }
                        _this.getDataList()
                    }
                });
        },
        receiveAllBooks: function (id) {
            const _this = this;
            _this.$swal({
                title: "Are you sure?",
                text: "Do you really want to return all books?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: '<i class="fa fa-check"></i>',
                cancelButtonText: '<i class="fa fa-close"></i>',
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/api/return_all_book', { id: id }).then(response => {
                        if (response.data.message) {
                            _this.$toastr('success', response.data.message, 'Success');
                            _this.getDataList();
                        }
                    }).catch(error => {
                        _this.$toastr('error', 'An error occurred while returning books.', 'Error');
                    });
                } else {
                    _this.$toastr('warning', 'Return operation cancelled.', 'Cancelled');
                }
            });
        }


    },
    mounted() {
        const _this = this;
        _this.$set(_this.formObject, 'member_id', '');
        _this.$set(_this.formObject, 'issue_date', '');
        _this.$set(_this.formObject, 'expected_return', '');
        _this.$set(_this.formObject, 'type', '');
        _this.getDataList();
        _this.getGeneralData(['stocked_books']);
        _this.$set(_this.formObject, 'book', [{ book_accession_id: '' }]);
    }
}
</script>

<style scoped></style>
