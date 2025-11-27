<template>

    <div class="page-wrapper">
        <i v-if="httpRequest" class="fa fa-spin fa-spinner"></i>
        <div class="page-content" v-else>
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h3 class="fs-20 mb-1">Welcome, <strong class="text-success">{{ showData(Config.user, 'username') }}</strong>
                            </h3>
                            <p class="text-muted mb-0">Thanks for back again.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 " v-if="can('dashboard_user_view')">
                    <div class="card radius-10 card1">
                        <div class="card-body">
                            <div class="d-flex align-items-center text-white">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                        Users
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><i
                                            class="fa fa-users text-success"></i><span
                                            class="counter-value"> {{ dashboard_data.user ? dashboard_data.user : 0
                                                            }}
                                                        </span></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" v-if="can('dashboard_student_view')">
                    <div class="card radius-10 card2">
                        <div class="card-body">
                            <div class="d-flex align-items-center ">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0 ">
                                        Student</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><i
                                            class="fa fa-user text-primary"></i><span
                                            class="counter-value">
                                                            {{ dashboard_data.student ? dashboard_data.student : 0 }} </span></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" v-if="can('dashboard_invoice_view')">
                    <div class="card radius-10 card3">
                        <div class="card-body">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total
                                Invoice Amount</p>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <i class="fa fa-bangladeshi-taka-sign text-success"></i>
                                    <span>
                                                        {{ amountFormat(dashboard_data.invoices?.[0]?.total_amount || 0) }}
                                                    </span>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" v-if="can('dashboard_waiver_view')">
                    <div class="card radius-10 card4">
                        <div class="card-body">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total
                                Waiver</p>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <i class="fa fa-bangladeshi-taka-sign text-warning"></i>
                                    <span>
                                                    {{ amountFormat(dashboard_data.invoices?.[0]?.total_waiver || 0) }}
                                                </span>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" v-if="can('dashboard_payable_amount_view')">
                    <div class="card radius-10 card5">
                        <div class="card-body">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Net
                                Invoice Amount</p>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <i class="fa fa-bangladeshi-taka-sign text-primary"></i>
                                    <span>
                                                        {{ amountFormat(dashboard_data.invoices?.[0]?.total_payable || 0) }}
                                                    </span>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" v-if="can('dashboard_paid_amount_view')">
                    <div class="card radius-10 card6">
                        <div class="card-body">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Paid
                                Amount</p>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <i class="fa fa-bangladeshi-taka-sign text-success"></i>
                                    <span>
                                                        {{ amountFormat(dashboard_data.payments?.[0]?.total_payed || 0) }}
                                                    </span>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" v-if="can('dashboard_dormitory_view')">
                    <div class="card radius-10 card7">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                        Dormitory</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                        <i class="fas fa-hotel"></i><span class="counter-value">
                                                            {{ dashboard_data.assign_dormitories ? dashboard_data.assign_dormitories
                                                                : 0
                                                            }} </span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" v-if="can('dashboard_library_view')">
                    <div class="card radius-10 card8">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <a href="/admin/library" style="text-decoration: none; color: inherit;">

                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1 overflow-hidden">
                                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                    <i class="fa-solid fa-book-open"></i> Library
                                                </p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-end justify-content-between mt-4">
                                            <div>
                                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                    <a href="/admin/library/book_author"
                                                       style="text-decoration: none; color: inherit;">
                                                        <i class="fa fa-book"></i>
                                                        Book Author: <strong>{{ dashboard_data.book_authors ?
                                                        dashboard_data.book_authors : 0 }}</strong>
                                                    </a>
                                                </p>
                                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                    <a href="/admin/library/publisher"
                                                       style="text-decoration: none; color: inherit;">
                                                        <i class="fa fa-book"></i>
                                                        Book Publisher: <strong>{{ dashboard_data.book_publishers ?
                                                        dashboard_data.book_publishers : 0 }}</strong>
                                                    </a>
                                                </p>
                                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                    <i class="fa fa-book"></i>
                                                    <a href="/admin/stock_books"
                                                       style="text-decoration: none; color: inherit;">
                                                        Book Stocks: <strong>{{ dashboard_data.stock_books ?
                                                        dashboard_data.stock_books : 0 }}</strong>
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body" >
                            <div style="width: 100%; max-width: 800px;">
                            <apexchart
                                    :options="attendance_charts.chartOptions"
                                    :series="attendance_charts.series">
                            </apexchart>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <apexchart :options="payment_charts.chartOptions" :series="payment_charts.series"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-body">
                            <apexchart
                                    :options="student_calss_wise_chart.chartOptions"
                                    :series="student_calss_wise_chart.series">
                            </apexchart>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <apexchart  :options="teacher_charts.chartOptions" :series="teacher_charts.series" />
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-body">
                            <apexchart :options="payment_charts.chartOptions" :series="payment_charts.series"/>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <apexchart :options="invoice_category_Chart.chartOptions" :series="invoice_category_Chart.series "/>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
    import VueApexCharts from "vue-apexcharts";

    export default {
        name: "DashboardPage",
        components: {
            apexchart: VueApexCharts,
        },
        data() {
            return {
                dashboard_data: '',
                attendance_charts: {
                    series: [],
                    chartOptions: {
                        chart: { id: 'attendance-chart', type: 'bar',height:800 },
                        xaxis: { categories: [] }
                    }
                },
                payment_charts: {
                    series: [],
                    chartOptions: {
                        chart: { id: 'payment-chart', type: 'bar' ,height: 800 },
                        xaxis: { categories: [] }
                    }
                },
                student_calss_wise_chart: {
                    series: [],
                    chartOptions: {
                        chart: { id: 'student-class-chart', type: 'bar' },
                        xaxis: { categories: [] }
                    }
                },
                teacher_charts: {
                    series: [],
                    chartOptions: {
                        chart: {
                            id: 'teacher-chart',
                            type: 'radialBar',
                            height: 300,
                            stacked: true
                        },
                        labels: [],
                    }
                },
                invoice_category_Chart: {
                    series: [],
                    chartOptions: {
                        chart: {
                            id: 'invoice-chart',
                            type: 'donut',
                            height: 200,
                            foreColor: '#fff'
                        },
                        labels: [],
                    }
                }



            };
        },
        methods: {
            amountFormat(amount) {
                return Number(amount).toLocaleString("en-BD", {
                    style: "decimal",
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
            },
            getDashboardData(){
                const _this = this;
                _this.$store.commit('httpRequest', true);

                _this.httpReq(_this.urlGenerate(),'get',{},{} , function (returnData) {
                    _this.dashboard_data = returnData;
                    _this.invoice_category_Chart = returnData.invoice_category_Chart;
                    _this.teacher_charts = returnData.teacher_charts;
                    _this.student_calss_wise_chart = returnData.student_calss_wise_chart;
                    _this.payment_charts = returnData.payment_charts;
                    _this.attendance_charts = returnData.attendance_charts
                });
                _this.$store.commit('httpRequest', false);

            }
        },
        mounted() {
            const _this = this;

            _this.getDashboardData();
        },
    };
</script>


<style scoped>
.card.card-animate {
    color: black;
}

.card.card-animate:hover {
    background-color: rgb(59, 110, 103);
    color: black;
}

.card.card-animate p,
.card.card-animate h4 {
    color: inherit;
}

.card.card-animate:hover p,
.card.card-animate:hover h4,
.card.card-animate:hover a {
    color: white !important;
}
.card1 {
    background-color: rgb(10, 179, 156); /* #0ab39c */
    color: rgb(255, 255, 255);
    transition: transform 0.3s, box-shadow 0.3s;
}
.card1 p, .card1 h4, .card1 span, .card1 i {
    color: rgb(255, 255, 255) !important;
}
.card1:hover {
    background-color: rgb(20, 179, 108); /* #14b36c */
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

/* ------------ card2 ------------ */
.card2 {
    background-color: rgb(255, 107, 107); /* #ff6b6b */
    color: #fff;
    transition: transform 0.3s, box-shadow 0.3s;
}
.card2 p, .card2 h4, .card2 span, .card2 i {
    color: #fff !important;
}
.card2:hover {
    background-color: rgb(255, 47, 87); /* #ff2f57 */
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

/* ------------ card3 ------------ */
.card3 {
    background-color: rgb(28, 126, 214); /* #1c7ed6 */
    color: #fff;
    transition: transform 0.3s, box-shadow 0.3s;
}
.card3 p, .card3 h4, .card3 span, .card3 i {
    color: #fff !important;
}
.card3:hover {
    background-color: rgb(9, 55, 214); /* #0937d6 */
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

/* ------------ card4 ------------ */
.card4 {
    background-color: rgb(252, 196, 25); /* #fcc419 */
    color: #000;
    transition: transform 0.3s, box-shadow 0.3s;
}
.card4 p, .card4 h4, .card4 span, .card4 i {
    color: #000 !important;
}
.card4:hover {
    background-color: rgb(252, 126, 13); /* #fc7e0d */
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

/* ------------ card5 ------------ */
.card5 {
    background-color: rgb(132, 94, 247); /* #845ef7 */
    color: #fff;
    transition: transform 0.3s, box-shadow 0.3s;
}
.card5 p, .card5 h4, .card5 span, .card5 i {
    color: #fff !important;
}
.card5:hover {
    background-color: rgb(160, 113, 247); /* #a071f7 */
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

/* ------------ card6 ------------ */
.card6 {
    background-color: rgb(255, 146, 43); /* #ff922b */
    color: #fff;
    transition: transform 0.3s, box-shadow 0.3s;
}
.card6 p, .card6 h4, .card6 span, .card6 i {
    color: #fff !important;
}
.card6:hover {
    background-color: rgb(255, 119, 20); /* #ff7714 */
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

/* ------------ card7 ------------ */
.card7 {
    background-color: rgb(32, 201, 151); /* #20c997 */
    color: #fff;
    transition: transform 0.3s, box-shadow 0.3s;
}
.card7 p, .card7 h4, .card7 span, .card7 i {
    color: #fff !important;
}
.card7:hover {
    background-color: rgb(14, 201, 117); /* #0ec975 */
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

/* ------------ card8 ------------ */
.card8 {
    background-color: rgb(51, 154, 240); /* #339af0 */
    color: #fff;
    transition: transform 0.3s, box-shadow 0.3s;
}
.card8 p, .card8 h4, .card8 span, .card8 i {
    color: #fff !important;
}
.card8:hover {
    background-color: rgb(21, 125, 240); /* #157df0 */
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}


</style>
