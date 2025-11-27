<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Stock Book">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Stock Book" page-modal-title="Add/Edit" v-if="can('stock_books_add')"></page-top>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{parseInt(dataList.from)+index}}</td>
                            <td>{{showData(data,'book_name')}}</td>
                            <td>{{showData(data,'books_language')}}</td>
                            <td>{{showData(data,'books_edition')}}</td>
                            <td>{{showData(data,'books_publisher')}}</td>
                            <td>{{showData(data,'author_name')}}</td>
                            <td>{{showData(data,'isbn')}}</td>
                            <td>{{data.stock_date}}</td>
                            <td>{{data.purchase_price}}</td>
                            <td>{{data.net_price}}</td>
                            <td>{{data.discount}}</td>
                            <td>{{data.quantity}}</td>
                            <td>{{data.available_quantity ? data.available_quantity:0}}</td>
                            <td>
                                <div class="hstack gap-3 fs-15">
                                    <a class="link-primary" @click="stockDetails(data)"><i class="fa fa-eye"></i></a>
                                    <a class="link-primary" v-if="can('stock_books_update')" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a class="link-danger" v-if="can('stock_books_delete')" @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
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
                    <label class="form-label">Title</label>
                    <select v-select2 class="form-control" v-model="formObject.book_accession_id" v-validate="'required'" name="books" id="books" @change="getBookDetails">
                        <option value="">Search Book</option>
                        <template v-for="(data, bIndex) in requiredData.books">
                            <option :value="data.id">{{data.book_title}}</option>
                        </template>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Stock Date</label>
                    <datepicker v-model="formObject.stock_date" name="stock_date" id="stock_date" input_class="form-control" v-validate="'required'"  placeholder="Stock Date"></datepicker>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Language</label>
                    <input type="text" class="form-control" readonly placeholder="Language" v-model="formObject.books_language" name="books_language" id="books_language">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Purchase Price</label>
                    <input type="text" class="form-control" placeholder="Purchase Price" v-model="formObject.purchase_price" v-validate="'required'" name="purchase_price" id="purchase_price">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Edition</label>
                    <input type="text" class="form-control" readonly placeholder="Edition" v-model="formObject.book_edition" name="book_edition" id="book_edition">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Net Price</label>
                    <input type="text" class="form-control" placeholder="Net Price" v-model="formObject.net_price" name="net_price" id="net_price">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Author</label>
                    <input type="text" class="form-control" readonly placeholder="Author" v-model="formObject.author_name" name="author_name" id="author_name">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Discount</label>
                    <input type="text" class="form-control" placeholder="Discount" v-model="formObject.discount" name="discount" id="discount">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ISBN</label>
                    <input type="text" class="form-control" readonly placeholder="ISBN" v-model="formObject.isbn" name="isbn" id="isbn">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" placeholder="Quantity" v-model="formObject.quantity" v-validate="'required'" name="quantity" id="quantity">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Publisher</label>
                    <input type="text" class="form-control" readonly placeholder="Publisher" v-model="formObject.books_publisher" name="books_publisher" id="books_publisher">
                </div>
            </div>
        </formModal>
        <general-modal modal-id="detailsModal" modalSize="modal-lg" :isEnableSubmit="false">
    <template v-slot:body>
        <div class="row border_bottom">
            <label class="col-md-3">Book Name</label>
            <div class="col-md-9">
               <strong>:</strong> <span>{{ details.book_name }}</span>
            </div>
        </div>
        <div class="row border_bottom">
            <label class="col-3">Book Language</label>
            <div class="col-md-9">
                <strong>:</strong> <span>{{ details.books_language }}</span>
            </div>
        </div>
        <div class="row border_bottom">
            <label class="col-3">Book Edition</label>
            <div class="col-md-9">
                <strong>:</strong> <span>{{ details.books_edition }}</span>
            </div>
        </div>
        <div class="row border_bottom">
            <label class="col-3">Author</label>
            <div class="col-md-9">
                <strong>:</strong> <span>{{ details.author_name }}</span>
            </div>
        </div>
        <div class="row border_bottom">
            <label class="col-3">ISBN</label>
            <div class="col-md-9">
                <strong>:</strong> <span>{{ details.isbn }}</span>
            </div>
        </div>
        <div class="row border_bottom">
            <label class="col-3">Publisher</label>
            <div class="col-md-9">
                <strong>:</strong> <span>{{ details.books_publisher }}</span>
            </div>
        </div>
        <div class="row border_bottom">
            <label class="col-3">Stock Date</label>
            <div class="col-md-9">
                <strong>:</strong> <span>{{ details.stock_date }}</span>
            </div>
        </div>
        <div class="row border_bottom">
            <label class="col-3">Purchase Price</label>
            <div class="col-md-9">
                <strong>:</strong> <span>{{ details.purchase_price }}</span>
            </div>
        </div>
        <div class="row border_bottom">
            <label class="col-3">Net Price</label>
            <div class="col-md-9">
                <strong>:</strong> <span>{{ details.net_price }}</span>
            </div>
        </div>
        <div class="row border_bottom">
            <label class="col-3">Discount</label>
            <div class="col-md-9">
                <strong>:</strong> <span>{{ details.discount }}</span>
            </div>
        </div>
        <div class="row border_bottom">
            <label class="col-3">Quantity</label>
            <div class="col-md-9">
                <strong>:</strong> <span>{{ details.quantity }}</span>
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
import axios from "axios";
export default {
    name: "stockBooks",
    components: {PageTop, Pagination, DataTable, formModal, GeneralModal},
    data() {
        return {
            tableHeading: ['Sl','Book Name','Language','Edition','Publisher','Author','ISBN No','Stock Date','Purchase Price','Net Price','Discount','Stock Quantity','Available Quantity','Action'],
            formModalId : 'formModal',
            details: {}
        }
    },
    methods: {
        getBookDetails: function () {
            const _this = this;
            const bookId = _this.formObject.book_accession_id;
            if (bookId) {axios.get('/api/get_book_details', { params: { book_accession_id: bookId } })
                .then(response => {
                    if (response.data.status === 2000) {
                        _this.$set(_this.formObject, 'books_language', response.data.result.books_language);
                        _this.$set(_this.formObject, 'book_edition', response.data.result.book_edition);
                        _this.$set(_this.formObject, 'author_name', response.data.result.author_name);
                        _this.$set(_this.formObject, 'isbn', response.data.result.isbn);
                        _this.$set(_this.formObject, 'books_publisher', response.data.result.books_publisher);
                    }
                })
            }
        },
        stockDetails: function (data) {
            const _this = this;
            var URL = `${_this.urlGenerate()}/${data.id}`;
            var title = `${_this.showData(data, 'book_name')}`;
            _this.openModal('detailsModal', title, function () {
                _this.getData(URL, "get", {}, {}, function (retData) {
                    _this.details = retData;
                });
            });
        },
    },
    mounted(){
        const _this = this;
        _this.$set(_this.formObject, 'book_accession_id', '');
        _this.$set(_this.formObject, 'stock_date', '');
        _this.getDataList();
        _this.getGeneralData(['books'])
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
