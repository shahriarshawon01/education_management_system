<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="[]" :default-object="{ year: '', month: '', day: '' }"
                    :defaultFilter="false" :default-pagination="false" :defaultSearchButton="false"
                    table-title="Weekly Canteen Menu" :table-responsive="false">

                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select v-model="formFilter.year" name="year" class="form-control">
                                <option value="">Year</option>
                                <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select v-model="formFilter.month" name="month" class="form-control">
                                <option value="">Month</option>
                                <option v-for="(month, index) in months" :key="index" :value="index + 1">
                                    {{ month }}
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-primary" @click.prevent.stop="getCanteenMenu">
                                Get Canteen Menu
                            </button>
                        </div>
                    </template>

                    <template v-slot:bottom_data>
                        <div class="menu_report_gen">
                            <div class="menu-card-wrapper" v-for="(menuItems, weekNumber) in weeklyCanteenMenu"
                                :key="weekNumber">

                                <div class="menu-card">
                                    <div class="canteen-menu-info">
                                        <div class="logo">
                                            <img :src="getImage('uploads/cafeteria-logo.png')" class="cafeteria-logo" />
                                        </div>
                                        <div class="menu-info-container">
                                            <div class="menu-date-info">
                                                <h4 class="institute-info"><strong>TPSC Cafeteria - Weekly Menu</strong>
                                                </h4>
                                                <h4 class="institute-info" style="margin-bottom: 12px;">
                                                    <strong>Joypurpara, Bogura-5800</strong>
                                                </h4>

                                                <h5 class="date-style"><strong>Year :</strong> {{ menuItems[0].year }}
                                                </h5>
                                                <h5 class="date-style"><strong>Month :</strong> {{
                                                    getMonthName(menuItems[0].month) }}</h5>
                                                <h5 class="date-style"><strong>Week :</strong> {{
                                                    getWeekText(weekNumber) }}</h5>


                                                <h5 class="date-style week-date">
                                                    <strong>Week Date :</strong>
                                                    {{ formatDate(menuItems[0].menu_start_date) }}
                                                    <strong> To </strong>
                                                    {{ formatDate(menuItems[menuItems.length - 1].menu_date) }}
                                                </h5>

                                                <div class="download-button">
                                                    <a :href="'/api/canteen_weekly_menu/download/' + menuItems[0].month + '/' + weekNumber"
                                                        class="btn btn-primary btn-lg">
                                                        <i class="fa-solid fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </data-table>
            </div>
        </div>
    </div>
</template>
<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
export default {
    name: "canteenWeeklyMenu",
    components: { PageTop, Pagination, DataTable },

    data() {

        const today = new Date();
        const currentMonth = today.getMonth() + 1;
        const currentYear = today.getFullYear();

        const years = Array.from({ length: 10 }, (_, i) => currentYear - i);
        return {
            months: [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ],
            weekDays: ["Saturday", "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
            // years: [...Array(5).keys()].map(i => new Date().getFullYear() - i),
            years: years,
            weeklyCanteenMenu: [],
        };
    },
    methods: {

        formatDate(date) {
            const d = new Date(date);
            const day = ("0" + d.getDate()).slice(-2);
            const month = ("0" + (d.getMonth() + 1)).slice(-2);
            const year = d.getFullYear().toString().slice(-2);
            return `${day}-${month}-${year}`;
        },

        getMonthName(monthNumber) {
            const monthNames = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            return monthNames[monthNumber - 1];
        },

        getWeekText(weekNumber) {
            const suffixes = ['th', 'st', 'nd', 'rd'];
            const v = weekNumber % 100;
            return weekNumber + (suffixes[(v - 20) % 10] || suffixes[v] || suffixes[0]) + ' Week';
        },

        getCanteenMenu() {
            const { year, month } = this.formFilter;

            const validationMessages = {
                year: 'Please select the year.',
                month: 'Please select the month.',
            };

            if (!year) {
                this.$toastr('error', validationMessages.year, "Validation Error");
                return;
            }
            if (!month) {
                this.$toastr('error', validationMessages.month, "Validation Error");
                return;
            }

            this.axios.get('/api/canteen_weekly_menu', {
                params: { year, month }
            }).then(response => {
                const result = response.data.result;

                if (!result.data || result.data.length === 0) {
                    this.$toastr('error', 'No Food Menu Found', "Validation Error");
                    return;
                }

                this.weeklyCanteenMenu = result.data;

            }).catch(error => {
                console.error("Error fetching admit card and seat plan:", error.response ? error.response.data : error.message);
                this.$toastr('error', 'Failed to fetch data.', "Error");
            });
        },
    },

    mounted() {
        const _this = this;
        const today = new Date();
        const currentMonth = String(today.getMonth() + 1).padStart(2, '0');
        const currentYear = today.getFullYear();
        _this.formFilter.month = currentMonth;
        _this.formFilter.year = currentYear;
        _this.getGeneralData(['class', 'session', 'examinationType']);
    },
}
</script>

<style scoped>
table,
th,
td {
    border: 1px solid black;
}


.menu_report_gen {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 16px;
    margin: 20px 0;
    page-break-inside: avoid;
}

.menu-card-wrapper {
    width: 32%;
    min-width: 300px;
    box-sizing: border-box;
    page-break-inside: avoid;
    transition: transform 0.3s ease;
}

.menu-card-wrapper:hover {
    transform: translateY(-4px);
}

.menu-card {
    position: relative;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 16px;
    background-color: #fefefe;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    height: auto;
}

.cafeteria-logo {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
}

.canteen-menu-info {
    display: flex;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 10px;
    margin-bottom: 8px;
}

.menu-info-container {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
}

.menu-date-info {
    text-align: center;
}

.institute-info {
    font-weight: bold;
    margin: 4px 0;
    color: #333;
}

.date-style {
    margin-top: 4px;
    font-size: 14px;
    color: #444;
}

.week-date {
    background: linear-gradient(135deg, #0f766e, #14b8a6);
    color: white;
    padding: 6px 12px;
    border-radius: 8px;
    font-weight: bold;
    display: inline-block;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    margin-top: 6px;
    font-size: 14px;
}

.download-button {
    margin-top: 12px;
    text-align: center;
}

.download-button .btn {
    width: 50%;
    padding: 6px 14px;
    font-size: 14px;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.download-button .btn i {
    margin: 0;
}
</style>
