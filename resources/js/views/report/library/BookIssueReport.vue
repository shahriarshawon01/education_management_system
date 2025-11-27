<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]"
                    :default-object="{ school_id: '', book_id: '', publisher_id: '', author_id: '', edition_id: '', book_type: '' }"
                    :defaultFilter="false" :default-pagination="false" table-title="Issue Book Report"
                    :table-responsive="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Issue Book Report" :default-add-button="false">
                            <div @click="printData('printDiv')">
                                <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i>
                                    Print</button>
                            </div>
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.book_id" name="book_id" class="form-control">
                                <option value="">Select Book</option>
                                <template v-for="(eachData, index) in requiredData.books">
                                    <option :value="eachData.id">{{ eachData.book_title }}
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="formFilter.author_id" name="author_id" class="form-control">
                                <option value="">Select Author</option>
                                <template v-for="(eachData, index) in requiredData.books_author">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="formFilter.publisher_id" name="publisher_id" class="form-control">
                                <option value="">Select Publisher</option>
                                <template v-for="(eachData, index) in requiredData.books_publishers">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <select class="form-control" v-model="formFilter.status" name="status">
                                <option value="">Select Status</option>
                                <option value="">All</option>
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                            </select>
                        </div>
                    </template>

                    <template v-slot:bottom_data>
                        <template v-if="dataList.data">
                            <div class="main-header mt-3">
                                <div class="row mb-3 mt-3">

                                    <!-- Reusable School Info Component -->
                                    <schoolInfo title="Issue Books Report" />

                                    <div class="row">
                                        <div class="col-6 text-start"></div>
                                        <div class="col-6 text-end">
                                            <strong>Print Date : {{ formatDate(new Date()) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Book Name</th>
                                        <th>Author Name</th>
                                        <th>Publisher Name</th>
                                        <th>Edition</th>
                                        <th>Language</th>
                                        <th>Cell Number</th>
                                        <th>Book Type</th>
                                        <th>Stock Date</th>
                                        <th>Net Price</th>
                                        <th>Purchase Price</th>
                                        <th>Total Quantity</th>
                                        <th>Available Quantity</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in dataList.data" :key="index">
                                        <td class="fw-medium">{{ index + 1 }}</td>
                                        <td>{{ data.book_name }}</td>
                                        <td>{{ data.author_name }}</td>
                                        <td>{{ data.publisher_name }}</td>
                                        <td>{{ data.book_edition }}</td>
                                        <td>{{ data.book_language }}</td>
                                        <td>{{ data.cell_number }}</td>
                                        <td>{{ getBookType(data.book_type) }}</td>
                                        <td style="text-align: center;">{{ data.stock_date }}</td>
                                        <td style="text-align: right;">{{ data.net_price }}</td>
                                        <td style="text-align: right;">{{ data.purchase_price }}</td>
                                        <td style="text-align: right;">{{ data.quantity }}</td>
                                        <td style="text-align: right;">{{ data.available_quantity }}</td>
                                        <td style="text-align: center;">
                                            <a v-if="parseInt(data.status) === 0">
                                                <span class="badge badge-soft-warning">Inactive</span>
                                            </a>
                                            <a v-if="parseInt(data.status) === 1">
                                                <span class="badge badge-soft-success">Active</span>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="table-success">
                                        <th colspan="9" class="fw-bold">Total</th>
                                        <th>{{ totalNetPrice.toFixed(2) }}</th>
                                        <th>{{ totalPurchasePrice.toFixed(2) }}</th>
                                        <th>{{ totalQuantity }}</th>
                                        <th>{{ totalAvailableQuantity }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </template>
                    </template>
                </data-table>
            </div>
        </div>
    </div>
</template>
<script>
import DataTable from "../../../components/dataTable";
import PageTop from "../../../components/pageTop";
import schoolInfo from "../../../components/schoolInfo";
export default {
    name: "bookReport",
    components: { PageTop, DataTable, schoolInfo },

    data() {
        return {
        };
    },
    methods: {
        formatDate(date) {
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = String(d.getFullYear()).slice(-2);
            return `${day}-${month}-${year}`;
        },
        getBookType(bookType) {
            if (bookType === 1) {
                return 'Academic';
            } else if (bookType === 2) {
                return 'Non-Academic';
            } else {
                return 'Unknown';
            }
        }
    },
    computed: {
        totalBooks() {
            return this.dataList.data.length;
        },
        totalNetPrice() {
            return this.dataList.data.reduce((sum, data) => sum + parseFloat(data.net_price), 0);
        },
        totalPurchasePrice() {
            return this.dataList.data.reduce((sum, data) => sum + parseFloat(data.purchase_price), 0);
        },
        totalQuantity() {
            return this.dataList.data.reduce((sum, data) => sum + parseInt(data.quantity), 0);
        },
        totalAvailableQuantity() {
            return this.dataList.data.reduce((sum, data) => sum + parseInt(data.available_quantity), 0);
        },
    },
    mounted() {
        const _this = this;
        _this.getGeneralData(['books', 'books_publishers', 'books_author', 'books_edition']);
    },
}
</script>
<style scoped>
.table thead th {
    background-color: #cccbcb;
    text-align: center;
}

.table tfoot th {
    background-color: #cccbcb;
    text-align: center;
}

.report-header {
    text-align: center;
    margin-bottom: 6px;
}

.report-header .school-name {
    font-size: 20px;
    font-weight: bold;
    color: #2c3e50;
    margin: 0;
}

.report-header .school-address {
    font-size: 16px;
    color: #7f8c8d;
    margin: 2px 0;
}

.report-header .report-title {
    font-size: 18px;
    font-weight: 600;
    color: #34495e;
    text-transform: uppercase;
    border-top: 1px solid #bdc3c7;
    border-bottom: 1px solid #bdc3c7;
    padding: 6px 0;
    display: inline-block;
    margin-top: 0px;
}
</style>
