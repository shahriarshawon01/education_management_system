<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" table-title="Canteen Menu Roster">
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Canteen Menu Roster"
                            :default-add-button="can('canteen_menu_rostering_add')"></page-top>
                    </template>
                    <!-- <template v-slot:filter></template> -->
                    <template v-slot:data>
                        <table class="table table-bordered">
                            <thead>
                                <tr class="background-style">
                                    <th style="width: 3%;">SL</th>
                                    <th style="width: 7%;">Meal Times</th>
                                    <th style="width: 40%;">Menu Item</th>
                                    <th style="width: 10%;">Month</th>
                                    <th style="width: 10%;">Day</th>
                                    <th style="width: 10%;">Date</th>
                                    <th style="width: 3%;">Week Number</th>
                                    <th style="width: 7%;">Price</th>
                                    <th style="width: 10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, index) in dataList.data">
                                    <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                                    <td>{{ showData(data.meal_time, 'name') }}</td>
                                    <td>{{ showData(data.menu_item, 'item_name') }}</td>

                                    <td style="text-align: center;">{{ formatMonth(data.month) }}</td>
                                    <td style="text-align: center;">{{ data.day }}</td>
                                    <td style="text-align: center;">{{ data.menu_date }}</td>
                                    <td style="text-align: center;">{{ formatWeekNumber(data.week_number) }}</td>

                                    <td style="text-align: right;">{{ data.price }}</td>

                                    <td style="display:flex; justify-content: center">
                                        <div class="hstack gap-3 fs-15">
                                            <a v-if="can('canteen_menu_rostering_update')" class="link-primary"
                                                @click="editDataInformation(data, data.id)"><i
                                                    class="fa fa-edit"></i></a>

                                            <a v-if="can('canteen_menu_rostering_delete')" class="link-danger"
                                                @click="deleteInformation(index, data.id)"><i
                                                    class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </template>
                </data-table>
            </div>
        </div>

        <formModal modalSize="modal-xl" @submit="submitForm(formObject, 'formModal')">
            <div class="mb-3">
                <div class="row">
                    <!-- Year Selection -->
                    <div class="col-md-2">
                        <label class="form-label">Year</label>
                        <select name="year" v-model="formObject.year" class="form-select">
                            <option value="">Select Year</option>
                            <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                        </select>
                    </div>

                    <!-- Month Selection -->
                    <div class="col-md-2">
                        <label class="form-label">Month</label>
                        <select name="month" v-model="formObject.month" class="form-select">
                            <option value="">Select Month</option>
                            <option v-for="(month, index) in months" :key="index" :value="index + 1">
                                {{ month }}
                            </option>
                        </select>
                    </div>

                    <!-- Menu Start Date -->
                    <div class="col-md-3">
                        <label class="form-label">Menu Date</label>
                        <datepicker class="form-control" v-model="formObject.menu_date" name="menu_date"
                            v-validate="'required'" placeholder="Select Start Date" @input="setEndDateAndWeekNumber"
                            style="height: 36px !important;">
                        </datepicker>
                    </div>

                    <!-- Menu Day -->
                    <div class="col-md-3">
                        <label class="form-label">Menu Day</label>
                        <input type="text" class="form-control bg-light text-primary fw-bold text-center"
                            v-model="formObject.day" readonly style="height: 36px !important;" />
                    </div>

                    <!-- Week Number -->
                    <div class="col-md-2">
                        <label class="form-label">Week Number</label>
                        <input type="text" class="form-control bg-light text-success fw-bold text-center"
                            v-model="formObject.week_number" readonly style="height: 36px !important;" />
                    </div>
                </div>
            </div>

            <h4>Set Weekly Canteen Menu On
                <span class="menu-day">
                    {{ formObject.day || "No Day" }}
                </span>
            </h4>
            <table class="table table-bordered set-menu-table">
                <thead>
                    <tr>
                        <th style="width: 12% !important; text-align: center;">Meal Times</th>
                        <th style="width: 30% !important; text-align: center;">Menu Item</th>
                        <th style="width: 12% !important; text-align: center;">Amount</th>
                        <th style="width: 20% !important; text-align: center;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="(row, index) in formObject.set_canteen_menu" :key="index">
                        <td>
                            <select v-model="row.canteen_component_id" name="canteen_component_id"
                                v-validate="'required'" class="form-control">
                                <option value="" disabled>Meal Times</option>
                                <template v-for="(eachData, index) in requiredData.mealTimes">
                                    <option :value="eachData.id">{{ eachData.name }}</option>
                                </template>
                            </select>
                        </td>

                        <td>
                            <select v-select2 v-model="row.menu_item_id" name="menu_item_id" v-validate="'required'"
                                class="form-control">
                                <option value="" disabled>Menu Item</option>
                                <template v-for="(eachData, index) in requiredData.menuItem">
                                    <option :value="eachData.id">{{ eachData.item_name }}</option>
                                </template>
                            </select>
                        </td>

                        <td>
                            <input type="number" v-model="row.price" name="price" class="form-control"
                                placeholder="Enter Price" />
                        </td>

                        <td class="text-center">
                            <a class="btn btn-outline-success"
                                :class="{ 'disabled': isEditMode, 'no-pointer': isEditMode }" :disabled="isEditMode"
                                @click="addRow(formObject.set_canteen_menu, {
                                    canteen_component_id: '',
                                    menu_item_id: '',
                                    price: '',
                                })">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </a>
                            <a class="btn btn-outline-danger"
                                :class="{ 'disabled': isEditMode, 'no-pointer': isEditMode }" :disabled="isEditMode"
                                v-if="formObject.set_canteen_menu.length > 1"
                                @click="deleteRow(formObject.set_canteen_menu, index)">
                                <i class="fa fa-close" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </formModal>
    </div>
</template>
<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";
export default {
    name: "CanteenMenuRoster",
    components: { PageTop, Pagination, DataTable, formModal },
    data() {
        return {
            formModalId: "formModal",
            months: [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ],
            years: [...Array(5).keys()].map(i => new Date().getFullYear() - i),
            set_canteen_menu: [
                {
                    canteen_component_id: '',
                    menu_item_id: '',
                    price: '',
                },
            ],
            isEditMode: false,
        };
    },

    watch: {
        'formObject.set_canteen_menu': {
            handler(newValue) {
                if (!newValue || newValue.length === 0) {
                    this.formObject.set_canteen_menu = [{
                        canteen_component_id: '',
                        menu_item_id: '',
                        price: '',
                    }];
                }
            },
            deep: true,
        },
    },

    methods: {
        formatMonth(month) {
            const months = [
                "", "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];
            return months[month] || '';
        },

        formatWeekNumber(weekNumber) {
            if (!weekNumber || weekNumber < 1 || weekNumber > 5) return '';

            const suffixes = ['th', 'st', 'nd', 'rd', 'th'];
            return `${weekNumber}${suffixes[weekNumber]}`;
        },

        setEndDateAndWeekNumber() {
            if (!this.formObject.menu_date) return;

            const startDate = new Date(this.formObject.menu_date);
            const selectedYear = this.formObject.year;
            const selectedMonth = this.formObject.month;

            if (
                startDate.getFullYear() !== selectedYear ||
                startDate.getMonth() + 1 !== selectedMonth
            ) {
                this.formObject.menu_date = "";
                this.formObject.menu_end_date = "";
                this.formObject.week_number = "";
                this.formObject.day = "";
                return;
            }

            const daysOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            this.formObject.day = daysOfWeek[startDate.getDay()];

            let endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + 6);

            const lastDayOfMonth = new Date(selectedYear, selectedMonth, 0);
            if (endDate > lastDayOfMonth) {
                endDate = lastDayOfMonth;
            }
            this.formObject.menu_end_date = endDate.toISOString().split("T")[0];

            const dayOfMonth = startDate.getDate();
            if (dayOfMonth >= 1 && dayOfMonth <= 7) {
                this.formObject.week_number = 1;
            } else if (dayOfMonth >= 8 && dayOfMonth <= 14) {
                this.formObject.week_number = 2;
            } else if (dayOfMonth >= 15 && dayOfMonth <= 21) {
                this.formObject.week_number = 3;
            } else if (dayOfMonth >= 22 && dayOfMonth <= 28) {
                this.formObject.week_number = 4;
            } else {
                this.formObject.week_number = 5;
            }
        },

        getMatchingId(name, list) {
            const item = list.find(each => each.name === name || each.item_name === name);
            return item ? item.id : '';
        },

        editDataInformation(data, id) {
            const _this = this;
            _this.isEditMode = true;
            _this.editData(data, id, "formModal", function (retData) {
                const editUrl = `${_this.urlGenerate()}/${id}/edit`;

                _this.getData(editUrl, "get", {}, {}, function (retData) {

                    if (!retData) {
                        _this.$toastr('error', 'Menu data is missing', "Error");
                        return;
                    }
                    const menuData = retData;

                    _this.$set(_this.formObject, "year", menuData.year || '');
                    _this.$set(_this.formObject, "month", menuData.month || '');
                    _this.$set(_this.formObject, "day", menuData.day || '');
                    _this.$set(_this.formObject, "menu_date", menuData.menu_date || '');
                    _this.$set(_this.formObject, "week_number", menuData.week_number || '');

                    _this.$set(_this.formObject, "set_canteen_menu", [{
                        canteen_component_id: _this.getMatchingId(menuData.canteen_component_id, _this.requiredData.mealTimes) || '',
                        menu_item_id: _this.getMatchingId(menuData.menu_item_id, _this.requiredData.menuItem) || '',
                        price: menuData.price || 0,
                    }]);

                    _this.openModal('formModal', 'Edit Template');
                });
            });
        },

        assign() {
            const _this = this;
            _this.$set(_this.formObject, 'year', '');
            _this.$set(_this.formObject, 'month', '');
            _this.$set(_this.formObject, 'menu_date', '');
            _this.$set(_this.formObject, 'menu_end_date', '');
            _this.$set(_this.formObject, 'week_number', '');
            _this.$set(_this.formObject, 'day', '');
            _this.$set(_this.formObject, 'canteen_component_id', '');
            _this.$set(_this.formObject, 'menu_item_id', '');
            _this.$set(_this.formObject, 'price', '');
            _this.$set(_this.formObject, 'set_canteen_menu', [{
                canteen_component_id: '',
                menu_item_id: '',
                price: '',
            }]);
        },
    },
    mounted() {
        const _this = this;

        const currentDate = new Date();
        const currentMonth = currentDate.getMonth() + 1;
        const currentYear = currentDate.getFullYear();

        _this.assign();
        _this.getDataList();
        _this.getGeneralData(["mealTimes", "menuItem"]);

        _this.$set(_this.formObject, 'month', currentMonth);
        _this.$set(_this.formObject, 'year', currentYear);
        _this.$set(_this.formObject, 'set_canteen_menu', [{
            canteen_component_id: '',
            menu_item_id: '',
            price: '',
        }]);
    },
};
</script>

<style scoped>
.menu-day {
    background-color: #265da9 !important;
    color: #fff !important;
    font-size: 1rem;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 10px;
    display: inline-block;
}

.background-style {
    background: #cccbcb;
    text-align: center;
}

.set-menu-table {
    table-layout: fixed;
    border: 2px solid #dee2e6;
}

.set-menu-table th {
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
    vertical-align: middle;
}

.set-menu-table td {
    vertical-align: middle;
    background-color: white;
}

.set-menu-table tr:hover td {
    background-color: #f8f9fa !important;
}

.form-control {
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-outline-success {
    color: #28a745;
    border-color: #28a745;
}

.btn-outline-success:hover {
    background-color: #28a745;
    color: white;
}

.btn-outline-danger {
    color: #dc3545;
    border-color: #dc3545;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    color: white;
}

.custom-option {
    padding: 8px 12px;
    color: #212529;
}

.custom-option:hover {
    background-color: #007bff !important;
    color: white !important;
}
</style>