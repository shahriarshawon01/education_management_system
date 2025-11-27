<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]"
                    :default-object="{ school_id: '', product_id: '', category_id: '', from_date: '', to_date: '', }"
                    :defaultFilter="false" :default-pagination="false" table-title="Stock Report"
                    :table-responsive="false">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Stock Report" :default-add-button="false">
                            <div @click="printData('printDiv')">
                                <button class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i>
                                    Print</button>
                            </div>
                        </page-top>
                    </template>
                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.category_id" name="category_id" class="form-control">
                                <option value="">Select Category</option>
                                <template v-for="(eachData, index) in requiredData.store_category">
                                    <option :value="eachData.id">{{ eachData.name }}
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="formFilter.product_id" name="product_id" class="form-control">
                                <option value="">Select Product</option>
                                <template v-for="(eachData, index) in requiredData.product">
                                    <option :value="eachData.id">{{ eachData.name }}
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <datepicker class="form-control" v-model="formFilter.from_date" placeholder="From Date"
                                name="from_date">
                            </datepicker>
                        </div>
                        <div class="col-md-2">
                            <datepicker class="form-control" v-model="formFilter.to_date" placeholder="To Date"
                                name="to_date"></datepicker>
                        </div>
                        <div class="col-md-2">
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
                                    <schoolInfo title="Stock Report" />

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
                                        <th rowspan="2" style="text-align: left; vertical-align: middle !important;">Sl
                                        </th>
                                        <th>Product Category</th>
                                        <th>Product Name</th>
                                        <th>Purchase Price</th>
                                        <th>Sale Price</th>
                                        <th>Purchase Date</th>
                                        <th>Sale Date</th>
                                        <th>Stock In</th>
                                        <th>Stock Out</th>
                                        <th>Current Stock</th>
                                        <th>Status</th>
                                    </tr>
                                    <tr>
                                        <th>1</th>
                                        <th>2</th>
                                        <th>3</th>
                                        <th>4</th>
                                        <th>5</th>
                                        <th>6</th>
                                        <th>7</th>
                                        <th>8</th>
                                        <th>9 = (7 - 8)</th>
                                        <th>10</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in dataList.data" :key="index">
                                        <td class="fw-medium">{{ index + 1 }}</td>
                                        <td>{{ data.category_name }}</td>
                                        <td>{{ data.product_name }}</td>
                                        <td style="text-align: right;">{{ data.purchase_price }}</td>
                                        <td style="text-align: right;">{{ data.sale_price }}</td>
                                        <td style="text-align: center;">{{ data.purchase_date }}</td>
                                        <td style="text-align: center;">{{ data.sale_date }}</td>
                                        <td style="text-align: right;">{{ data.purchase_quantity }}</td>
                                        <td style="text-align: right;">{{ data.sale_quantity }}</td>
                                        <td
                                            :class="currentStock(data) === 'No Stock' ? 'text-center text-danger fw-bold' : 'text-end'">
                                            {{ currentStock(data) }}
                                        </td>
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
                                        <th colspan="3" class="fw-bold">Total</th>
                                        <th>{{ totalPurchasePrice.toFixed(2) }}</th>
                                        <th>{{ totalSalePrice.toFixed(2) }}</th>
                                        <th>-</th>
                                        <th>-</th>
                                        <th>{{ totalStockIn.toFixed(2) }}</th>
                                        <th>{{ totalStockOut.toFixed(2) }}</th>
                                        <th>{{ totalCurrentStock.toFixed(2) }}</th>
                                        <th>-</th>
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
    name: "stockReport",
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
        formatPrice(value) {
            return parseFloat(value).toFixed(2);
        },
        getBookType(bookType) {
            if (bookType === 1) {
                return 'Academic';
            } else if (bookType === 2) {
                return 'Non-Academic';
            } else {
                return 'Unknown';
            }
        },
        currentStock(data) {
            const stock = data.purchase_quantity - data.sale_quantity;
            return stock > 0 ? stock : 'No Stock';
        }
    },
    computed: {
        totalBooks() {
            return this.dataList.data.length;
        },
        totalPurchasePrice() {
            return this.dataList.data.reduce((sum, data) => sum + parseFloat(data.purchase_price), 0);
        },
        totalSalePrice() {
            return this.dataList.data.reduce((sum, data) => sum + parseFloat(data.sale_price), 0);
        },
        totalStockIn() {
            return this.dataList.data.reduce((sum, data) => sum + parseInt(data.purchase_quantity), 0);
        },
        totalStockOut() {
            return this.dataList.data.reduce((sum, data) => sum + parseInt(data.sale_quantity), 0);
        },
        totalCurrentStock() {
            return this.dataList.data.reduce((sum, data) => sum + (parseInt(data.purchase_quantity) - parseInt(data.sale_quantity)), 0);
        },
    },
    mounted() {
        const _this = this;
        _this.getGeneralData(['store_category', 'product']);
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
