<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="StockIn List">
                    <template v-slot:page_top>
                        <page-top :default-object="selectedObject" topPageTitle="StockIn List"
                            :default-add-button="can('stock_in_add')" page-modal-title="StockIn Add/Edit"></page-top>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ showData(data.store_category, 'name') }}</td>
                            <td>{{ showData(data.product, 'name') }}</td>
                            <td>{{ data.product_code }}</td>
                            <td>{{ data.purchase_date }}</td>
                            <td>{{ data.purchase_price }}</td>
                            <td>{{ data.sale_price }}</td>
                            <td>{{ data.purchase_quantity }}</td>
                            <td>
                                <div class="hstack gap-3 fs-15">
                                    <a class="link-primary" @click="stockDetails(data, data.name)"><i
                                            class="fa fa-eye"></i></a>
                                    <a v-if="can('stock_in_update')" class="link-primary"
                                        @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('stock_in_delete')" class="link-danger"
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
                    <label class="col-form-label">Product Category</label>
                    <select type="text" class="form-control" v-model="formObject.category_id" name="category_id">
                        <option value="">Select Category</option>
                        <template v-for="(eachData, index) in requiredData.store_category">
                            <option :value="eachData.id">{{ eachData.name }}</option>
                        </template>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="col-form-label">Product Name</label>
                    <select type="text" class="form-control" v-model="formObject.product_id" name="product_id">
                        <option value="">Select Product</option>
                        <template v-for="(eachData, index) in requiredData.product">
                            <option :value="eachData.id">{{ eachData.name }}</option>
                        </template>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="col-form-label">Product Code</label>
                    <input type="text" v-model="formObject.product_code" v-validate="'required'" name="product_code"
                        class="form-control" placeholder="Enter Product Code">
                </div>
                <div class="col-md-6">
                    <label class="col-form-label">Purchase Date</label>
                    <datepicker input_class="form-control" v-validate="'required'" v-model="formObject.purchase_date"
                        name="purchase_date" placeholder="Enter Purchase Date"></datepicker>
                </div>
                <div class="col-md-6">
                    <label class="col-form-label">Purchase Price</label>
                    <input type="text" v-model="formObject.purchase_price" v-validate="'required'" name="purchase_price"
                        class="form-control" placeholder="Enter Purchase Price">
                </div>
                <div class="col-md-6">
                    <label class="col-form-label">Sale Price</label>
                    <input type="text" v-model="formObject.sale_price" v-validate="'required'" name="sale_price"
                        class="form-control" placeholder="Enter Sale Price">
                </div>
                <div class="col-md-6">
                    <label class="col-form-label">Purchase Quantity</label>
                    <input type="text" v-model="formObject.purchase_quantity" name="purchase_quantity"
                        class="form-control" placeholder="Enter Purchase Quantity">
                </div>
            </div>
        </formModal>

        <general-modal modal-id="detailsModal" modalSize="modal-lg">
            <template v-slot:body>
                <div class="row border_bottom">
                    <label class="col-3">Product Category </label>
                    <div class="col-md-9">
                        <strong>: </strong> <span>{{ showData(details.store_category, 'name') }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Product Name</label>
                    <div class="col-md-9">
                        <strong>: </strong> <span>{{ showData(details.product, 'name') }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Product Code</label>
                    <div class="col-md-9">
                        <strong>: </strong> <span>{{ details.product_code }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Purchase Date</label>
                    <div class="col-md-9">
                        <strong>: </strong> <span>{{ details.purchase_date }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Purchase Price</label>
                    <div class="col-md-9">
                        <strong>: </strong> <span>{{ details.purchase_price }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Sale Price</label>
                    <div class="col-md-9">
                        <strong>: </strong> <span>{{ details.sale_price }}</span>
                    </div>
                </div>
                <div class="row border_bottom">
                    <label class="col-3">Purchase Quantity</label>
                    <div class="col-md-9">
                        <strong>: </strong> <span>{{ details.purchase_quantity }}</span>
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
    name: "StockInList",
    components: { PageTop, Pagination, DataTable, formModal, GeneralModal },
    data() {
        return {
            tableHeading: ['Sl', 'Product Category', 'Product Name', 'Product Code', 'Purchase Date', 'Purchase Price', 'Sale Price', 'Purchase Quantity', 'Action'],
            formModalId: 'formModal',
            details: {},
            selectedObject: {
                category_id: '',
                product_id: '',
            }
        }
    },
    methods: {
        stockDetails: function (data) {
            const _this = this;
            var URL = `${_this.urlGenerate()}/${data.id}`;
            var title = `${_this.showData(data.product, 'name')}`;
            _this.openModal('detailsModal', title, function () {
                _this.getData(URL, "get", {}, {}, function (retData) {
                    _this.details = retData;
                });
            });
        },

        // assign() {
        //     const _this = this;
        //     _this.$set(_this.formObject, 'purchase_date', '');
        // },
    },
    mounted() {
        const _this = this;
        _this.getDataList();
        // _this.assign();
        _this.getGeneralData(['store_category', 'product']);
        _this.$set(_this.formObject, 'purchase_date', '');
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
