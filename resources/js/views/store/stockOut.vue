<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Stock Out List">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Stock Out List" :default-object="{ customer_type: '' }"
                            :default-add-button="can('stock_out_add')" page-modal-title="Stock Out Add/Edit"></page-top>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data" :key="index">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ showData(data.product, 'name') }}</td>
                            <td>{{ showData(data.stock_in, 'product_code') }}</td>
                            <td>{{ data.sale_date }}</td>
                            <td>{{ customerTypeCapitalize(data.customer_type) }}</td>
                            <td>
                                <span v-if="data.customer_type === 'student' && data.customer">
                                    {{ data.customer.student_roll }}
                                </span>
                                <span v-if="data.customer_type === 'teacher' && data.customer">
                                    {{ data.customer.employee_id || 'N/A' }}
                                </span>
                                <span v-if="data.customer_type === 'staff' && data.customer">
                                    {{ data.customer.employee_id || 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span v-if="data.customer_type === 'student' && data.customer">
                                    {{ data.customer.student_name_en || 'N/A' }}
                                </span>
                                <span v-if="data.customer_type === 'teacher' && data.customer">
                                    {{ data.customer.name || 'N/A' }}
                                </span>
                                <span v-if="data.customer_type === 'staff' && data.customer">
                                    {{ data.customer.name || 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span v-if="data.customer_type === 'student' && data.customer">
                                    {{ 'N/A' }}
                                </span>
                                <span v-if="data.customer_type === 'teacher' && data.customer">
                                    {{ data.customer.phone || 'N/A' }}
                                </span>
                                <span v-if="data.customer_type === 'staff' && data.customer">
                                    {{ data.customer.phone || 'N/A' }}
                                </span>
                            </td>
                            <td>{{ data.price }}</td>
                            <td>{{ data.quantity }}</td>
                            <td>{{ data.total_price }}</td>
                            <td>
                                <div class="hstack gap-3 fs-15">
                                    <a class="link-primary" @click="stockDetails(data, data.name)"><i
                                            class="fa fa-eye"></i></a>
                                    <a v-if="can('stock_out_update')" class="link-primary"
                                        @click="editDataInformation(data, data.id)">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a v-if="can('stock_out_delete')" class="link-danger"
                                        @click="deleteInformation(index, data.id)">
                                        <i class="fa fa-trash"></i>
                                    </a>
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
                        <label class="col-form-label">Customer Category</label>
                        <select v-model="formObject.customer_type" class="form-control" v-validate="'required'"
                            name="customer_type" id="customer_type">
                            <option value="">Select Customer Category</option>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <template v-if="formObject.customer_type == 'student'">
                        <div class="col-md-12">
                            <label class="col-form-label">Student ID</label>
                            <input type="text" v-model="formObject.student_roll" v-validate="'required'"
                                name="student_roll" class="form-control" placeholder="Enter Student ID"
                                @change="getStudent">
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Student Name</label>
                            <input type="text" v-model="formObject.student_name" name="student_name"
                                class="form-control" placeholder="Student Name" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Class</label>
                            <input type="text" v-model="formObject.student_class" name="student_class"
                                class="form-control" placeholder="Student Class" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Department</label>
                            <input type="text" v-model="formObject.student_department" name="student_department"
                                class="form-control" placeholder="Student Department" readonly>
                        </div>

                        <div class="col-md-12">
                            <label class="col-form-label">Section</label>
                            <input type="text" v-model="formObject.section_name" name="section_name" class="form-control"
                                placeholder="Student Section" readonly>
                        </div>
                    </template>

                    <template v-if="formObject.customer_type == 'teacher'">
                        <div class="col-md-12">
                            <label class="col-form-label">Teacher ID</label>
                            <input type="text" v-model="formObject.teacher_id" v-validate="'required'" name="teacher_id"
                                class="form-control" placeholder="Enter Teacher ID" @change="getTeacher">
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Teacher Name</label>
                            <input type="text" v-model="formObject.teacher_name" name="teacher_name"
                                class="form-control" placeholder="Teacher Name" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Teacher Department</label>
                            <input type="text" v-model="formObject.teacher_department" name="teacher_department"
                                class="form-control" placeholder="Teacher Department" readonly>
                        </div>

                        <div class="col-md-12">
                            <label class="col-form-label">Teacher E-mail</label>
                            <input type="text" v-model="formObject.teacher_email" name="teacher_email"
                                class="form-control" placeholder="Teacher E-mail" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Teacher Phone</label>
                            <input type="text" v-model="formObject.teacher_phone" name="teacher_phone"
                                class="form-control" placeholder="Teacher Phone" readonly>
                        </div>
                    </template>
                    <template v-if="formObject.customer_type == 'staff'">
                        <div class="col-md-12">
                            <label class="col-form-label">Staff ID</label>
                            <input type="text" v-model="formObject.staff_id" v-validate="'required'" name="staff_id"
                                class="form-control" placeholder="Enter Staff ID" @change="getStaff">
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Staff Name</label>
                            <input type="text" v-model="formObject.staff_name" name="staff_name" class="form-control"
                                placeholder="Staff Name" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Staff Department</label>
                            <input type="text" v-model="formObject.staff_department" name="staff_department"
                                class="form-control" placeholder="Staff Department" readonly>
                        </div>

                        <div class="col-md-12">
                            <label class="col-form-label">Staff E-mail</label>
                            <input type="text" v-model="formObject.staff_email" name="staff_email" class="form-control"
                                placeholder="Staff E-mail" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Staff Phone</label>
                            <input type="text" v-model="formObject.staff_phone" name="staff_phone" class="form-control"
                                placeholder="Staff Phone" readonly>
                        </div>
                    </template>
                </div>
                <input type="hide" v-model="formObject.customer_id" name="customer_id" class="form-control" hidden>

                <div class="col-md-6">
                    <div class="col-md-12">
                        <label class="col-form-label">Sale Date</label>
                        <datepicker input_class="form-control" v-model="formObject.sale_date" name="sale_date"><input
                                slot="input" class="form-control" placeholder="Select a date">
                        </datepicker>
                    </div>

                    <template
                        v-if="formObject.customer_type == 'student' || formObject.customer_type == 'teacher' || formObject.customer_type == 'staff'">
                        <div class="col-md-12">
                            <label class="col-form-label">Product Code</label>
                            <input type="number" v-model="formObject.product_code" v-validate="'required'"
                                name="product_code" class="form-control" placeholder="Enter Product Code"
                                @change="getProduct">
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Product Name</label>
                            <input type="text" v-model="formObject.product_name" name="product_name"
                                class="form-control" placeholder="Product Name" readonly>
                        </div>

                        <div class="col-md-12">
                            <label class="col-form-label">Price</label>
                            <input type="text" v-model="formObject.price" name="price" class="form-control"
                                placeholder="Product Price" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label">Quantity</label>
                            <input type="text" v-model="formObject.quantity" name="quantity" class="form-control"
                                placeholder="Enter Product Quantity" v-validate="'required'"
                                @input="stockOutValidation(formObject.quantity, formObject.stock_quantity)">
                        </div>

                        <div class="col-md-12">
                            <label class="col-form-label">Total Price</label>
                            <input name="total_price" class="form-control" v-model="formObject.total_price" rows="3"
                                placeholder="Total Price" readonly>
                        </div>
                    </template>
                </div>
            </div>
        </formModal>

        <general-modal modal-id="detailsModal" modalSize="modal-lg">
            <template v-slot:body>
                <div class="row border_bottom">
                    <label class="col-3">Product Name</label>
                    <div class="col-md-9">
                        <strong>: </strong> <span>{{ details.product.name }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Product Code</label>
                    <div class="col-md-9">
                        <strong>: </strong> <span>{{ details.stock_in.product_code }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Sale Date</label>
                    <div class="col-md-9">
                        <strong>: </strong> <span>{{ details.sale_date }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Customer Type</label>
                    <div class="col-md-9">
                        <strong>: </strong> <span>{{ details.customer_type }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Customer Code</label>
                    <div class="col-md-9">
                        <strong>: </strong>
                        <span v-if="details.customer_type === 'student'">{{ details.customer.student_roll }}</span>
                        <span v-if="details.customer_type === 'teacher'">{{ details.customer.employee_id }}</span>
                        <span v-if="details.customer_type === 'staff'">{{ details.customer.employee_id }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Customer Name</label>
                    <div class="col-md-9">
                        <strong>: </strong>
                        <span v-if="details.customer_type === 'student'">{{
                            details.customer.student_name_en }}</span>
                        <span v-if="details.customer_type === 'teacher'">{{ details.customer.name
                            }}</span>
                        <span v-if="details.customer_type === 'staff'">{{ details.customer.name
                            }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Customer Phone</label>
                    <div class="col-md-9">
                        <strong>: </strong>
                        <span v-if="details.customer_type === 'student'">{{
                            'N/A'
                        }}</span>
                        <span v-if="details.customer_type === 'teacher'">{{
                            details.customer.phone
                        }}</span>
                        <span v-if="details.customer_type === 'staff'">{{ details.customer.phone
                            }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Sale Price</label>
                    <div class="col-md-9">
                        <strong>: </strong> <span>{{ details.price }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Sale Quantity</label>
                    <div class="col-md-9">
                        <strong>: </strong> <span>{{ details.quantity }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Total Price</label>
                    <div class="col-md-9">
                        <strong>: </strong> <span>{{ details.total_price }}</span>
                    </div>
                </div>
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
    name: "StockOut",
    components: { PageTop, Pagination, DataTable, formModal, GeneralModal },
    data() {
        return {
            sale_date: null,
            tableHeading: ['Sl', 'Product Name', 'Product Code', 'Sale Date', 'Customer Type', 'Customer Code', 'Customer Name', 'Customer Phone', 'Price', 'Quantity', 'Total Price', 'Action'],
            details: {
                product: { name: '' },
                stock_in: { product_code: '' },
                sale_date: '',
                customer_type: '',
                customer: { student_roll: '', employee_id: '', student_name_en: '', name: '', mobile_no: '' },
                price: '',
                quantity: '',
                total_price: ''
            },
            formModalId: 'formModal',
            studentData: {},
            teacherData: {},
            staffData: {},
        }
    },
    created() {
        this.formObject.sale_date = this.getCurrentDate();
    },
    methods: {
        getStudent() {
            const _this = this;
            const URL = `${baseUrl}/api/get_student?student_roll=${_this.formObject.student_roll}`;
            _this.axios.get(URL)
                .then(response => {
                    const studentData = response.data.result;
                    if (studentData) {
                        _this.$set(_this.formObject, 'customer_id', studentData.id);
                        _this.$set(_this.formObject, 'student_name', studentData.student_name_en);
                        _this.$set(_this.formObject, 'student_class', studentData.class_name);
                        _this.$set(_this.formObject, 'student_department', studentData.department_name);
                        _this.$set(_this.formObject, 'section_name', studentData.section_name);

                        _this.$toastr('success', response.data.message, "Success");
                    } else {
                        _this.$toastr('error', response.data.message, "Error");
                    }
                })
        },
        getTeacher() {
            const _this = this;
            const URL = `${baseUrl}/api/get_teacher?teacher_id=${_this.formObject.teacher_id}`;
            _this.axios.get(URL)
                .then(response => {
                    const teacherData = response.data.result;
                    if (teacherData) {
                        _this.$set(_this.formObject, 'customer_id', teacherData.id);
                        _this.$set(_this.formObject, 'teacher_name', teacherData.name);
                        _this.$set(_this.formObject, 'teacher_department', teacherData.department);
                        _this.$set(_this.formObject, 'teacher_email', teacherData.email);
                        _this.$set(_this.formObject, 'teacher_phone', teacherData.phone);

                        _this.$toastr('success', response.data.message, "Success");
                    } else {
                        _this.$toastr('error', response.data.message, "Error");
                    }
                })
        },
        getStaff() {
            const _this = this;
            const URL = `${baseUrl}/api/get_staff?staff_id=${_this.formObject.staff_id}`;
            _this.axios.get(URL)
                .then(response => {
                    const staffData = response.data.result;
                    if (staffData) {
                        _this.$set(_this.formObject, 'customer_id', staffData.id);
                        _this.$set(_this.formObject, 'staff_name', staffData.name);
                        _this.$set(_this.formObject, 'staff_department', staffData.department);
                        _this.$set(_this.formObject, 'staff_email', staffData.email);
                        _this.$set(_this.formObject, 'staff_phone', staffData.phone);

                        _this.$toastr('success', response.data.message, "Success");
                    } else {
                        _this.$toastr('error', response.data.message, "Error");
                    }
                })
                .catch(error => {
                    console.error('Error fetching product data:', error);
                });
        },

        getProduct() {
            const _this = this;
            const URL = `${baseUrl}/api/get_product?product_code=${_this.formObject.product_code}`;

            _this.axios.get(URL)
                .then(response => {
                    const productData = response.data.result;
                    if (productData) {
                        _this.$set(_this.formObject, 'stock_id', productData.stock_ins_id);
                        _this.$set(_this.formObject, 'product_id', productData.store_products_id);
                        _this.$set(_this.formObject, 'product_name', productData.product_name);
                        _this.$set(_this.formObject, 'price', productData.sale_price);

                        _this.$set(_this.formObject, 'stock_quantity', productData.stock_quantity);

                        if (typeof _this.formObject.quantity === 'undefined' || _this.formObject.quantity === null) {
                            _this.$set(_this.formObject, 'quantity', 0);
                        }

                        if (_this.formObject.quantity > _this.formObject.stock_quantity) {
                            _this.stockOutValidation(_this.formObject.quantity, productData.stock_quantity);
                            return;
                        }

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
            _this.editData(data, id, 'formModal', function (retData) {
                var editUrl = `${_this.urlGenerate()}/${id}/edit`;
                _this.getData(editUrl, 'get', {}, {}, function (retData) {
                    if (retData.status === 1) {
                        const stockOutData = retData;

                        _this.$set(_this.formObject, 'customer_type', stockOutData.customer_type);
                        _this.$set(_this.formObject, 'customer_id', stockOutData.customer_id);
                        _this.$set(_this.formObject, 'product_code', stockOutData.product_code);
                        _this.$set(_this.formObject, 'product_name', stockOutData.product.name);
                        _this.$set(_this.formObject, 'price', stockOutData.price);
                        _this.$set(_this.formObject, 'quantity', stockOutData.quantity);
                        _this.$set(_this.formObject, 'total_price', stockOutData.total_price);
                        _this.$set(_this.formObject, 'sale_date', stockOutData.sale_date);

                        switch (stockOutData.customer_type) {
                            case 'student':
                                _this.$set(_this.formObject, 'student_roll', stockOutData.customer.student_roll);
                                _this.$set(_this.formObject, 'student_name', stockOutData.customer.student_name_en);
                                _this.$set(_this.formObject, 'student_class', stockOutData.customer.classes.name);
                                _this.$set(_this.formObject, 'student_department', stockOutData.customer.departments.department_name);
                                _this.$set(_this.formObject, 'section_name', stockOutData.customer.sections.name);
                                break;
                            case 'teacher':
                                _this.$set(_this.formObject, 'teacher_id', stockOutData.customer.employee_id);
                                _this.$set(_this.formObject, 'teacher_name', stockOutData.customer.name);
                                _this.$set(_this.formObject, 'teacher_department', stockOutData.customer.department.name);
                                _this.$set(_this.formObject, 'teacher_email', stockOutData.customer.email);
                                _this.$set(_this.formObject, 'teacher_phone', stockOutData.customer.phone);
                                break;
                            case 'staff':
                                _this.$set(_this.formObject, 'staff_id', stockOutData.customer.employee_id);
                                _this.$set(_this.formObject, 'staff_name', stockOutData.customer.name);
                                _this.$set(_this.formObject, 'staff_department', stockOutData.customer.department.name);
                                _this.$set(_this.formObject, 'staff_email', stockOutData.customer.email);
                                _this.$set(_this.formObject, 'staff_phone', stockOutData.customer.phone);
                                break;
                        }
                    } else {
                        _this.$toastr('error', response.message, "Error");
                    }
                });
            });
        },

        stockOutValidation(stockOutQuantity, stockInQuantity) {
            this.$validator.reset();
            const quantity = parseFloat(stockOutQuantity);
            const stockQuantity = parseFloat(stockInQuantity);

            if (quantity > stockQuantity) {
                this.$toastr('error', `The requested quantity exceeds the available stock. Only ${stockQuantity} available in stock.`, 'Error');
                this.formObject.quantity = stockQuantity;
            }
        },

        stockDetails: function (data) {
            const _this = this;
            var URL = `${_this.urlGenerate()}/${data.id}`;
            var title = 'Show Stock Out';
            _this.openModal('detailsModal', title, function () {
                _this.getData(URL, "get", {}, {}, function (retData) {
                    _this.details = retData;
                });
            });
        },
        totalPrice: function () {
            const _this = this;
            _this.$set(_this.formObject, 'total_price', (_this.formObject.quantity * _this.formObject.price));
        },
        validateQuantity() {

        },
        getCurrentDate() {
            const date = new Date();
            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        },
        customerTypeCapitalize(type) {
            if (!type) return '';
            return type.charAt(0).toUpperCase() + type.slice(1).toLowerCase();
        }
    },
    watch: {
        'formObject.quantity': function (newQuantity) {
            this.formObject.total_price = this.formObject.price * newQuantity;
        }
    },
    mounted() {
        this.getDataList();
    }
}
</script>

<style scoped>
.border_bottom{
        border-bottom: 1px solid #ebebeb !important;
        line-height: 32px !important;
    }
    .border_bottom label{
        margin-bottom: 0 !important;
    }
    .border_bottom strong{
        margin-right: 5px !important;
    }
</style>
