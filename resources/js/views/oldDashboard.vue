<template>
    <div class="row">
        <div class="col">
            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-16 mb-1">Welcome, <strong>{{ showData(Config.user, 'username') }}</strong>
                                </h4>
                                <p class="text-muted mb-0">Thanks for back again.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div v-if="can('dashboard_user_view')" class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <a href="/admin/users" style="text-decoration: none; color: inherit;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
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
                                                            class="counter-value"> {{ dataList.user ? dataList.user : 0
                                                            }}
                                                        </span></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <!-- <div class="col-xl-4 col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1 overflow-hidden">
                                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">School</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-end justify-content-between mt-4">
                                            <div>
                                                <h4 class="fs-22 fw-semibold ff-secondary mb-4"><i class="fa fa-school text-success"></i><span class="counter-value"> {{ dataList.school ? dataList.school:0 }} </span></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            <div v-if="can('dashboard_student_view')" class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <a href="/admin/student" style="text-decoration: none; color: inherit;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                        Student</p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><i
                                                            class="fa fa-user text-primary"></i><span
                                                            class="counter-value">
                                                            {{ dataList.student ? dataList.student : 0 }} </span></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div v-if="can('dashboard_teacher_view')" class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <a href="/admin/teacher" style="text-decoration: none; color: inherit;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                        Teacher</p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><i
                                                            class="fa fa-user text-primary"></i><span
                                                            class="counter-value">
                                                            {{ dataList.teacher ? dataList.teacher : 0 }} </span></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div v-if="can('dashboard_staff_view')" class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <a href="/admin/staff" style="text-decoration: none; color: inherit;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                        Staff
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><i
                                                            class="fa fa-user text-primary"></i><span
                                                            class="counter-value">
                                                            {{ dataList.staff ? dataList.staff : 0 }} </span></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div v-if="can('dashboard_invoice_view')" class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <a href="/admin/generate_invoice" style="text-decoration: none; color: inherit;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                        Total
                                                        Invoice Amount</p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><i
                                                            class="fa fa-bangladeshi-taka-sign text-success"></i><span
                                                            class="counter-value"
                                                            v-if="dataList.invoices !== undefined"> {{
                                                                dataList.invoices[0].total_amount ?
                                                                    dataList.invoices[0].total_amount : 0 }} </span></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div v-if="can('dashboard_waiver_view')" class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <a href="/admin/generate_invoice" style="text-decoration: none; color: inherit;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                        Total
                                                        Waiver</p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><i
                                                            class="fa fa-bangladeshi-taka-sign text-warning"></i><span
                                                            class="counter-value"
                                                            v-if="dataList.invoices !== undefined"> {{
                                                                dataList.invoices[0].total_waiver ?
                                                                    dataList.invoices[0].total_waiver : 0 }} </span></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div v-if="can('dashboard_payable_amount_view')" class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <a href="/admin/generate_invoice" style="text-decoration: none; color: inherit;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                        Net Invoice Amount
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><i
                                                            class="fa fa-bangladeshi-taka-sign text-primary"></i><span
                                                            class="counter-value"
                                                            v-if="dataList.invoices !== undefined"> {{
                                                                dataList.invoices[0].total_payable ?
                                                                    dataList.invoices[0].total_payable : 0 }} </span></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div v-if="can('dashboard_paid_amount_view')" class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <a href="/admin/student_payment" style="text-decoration: none; color: inherit;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                        Total
                                                        Paid Amount</p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><i
                                                            class="fa fa-bangladeshi-taka-sign text-success"></i><span
                                                            class="counter-value"
                                                            v-if="dataList.payments !== undefined"> {{
                                                                dataList.payments[0].total_payed ?
                                                                    dataList.payments[0].total_payed : 0 }} </span></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div v-if="can('dashboard_notice_view')" class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <a href="/admin/noticeboard" style="text-decoration: none; color: inherit;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                        Notice
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><i
                                                            class="fa-solid fa-bell"></i><span class="counter-value"> {{
                                                                dataList.notice_boards ? dataList.notice_boards : 0 }}
                                                        </span>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div v-if="can('dashboard_dormitory_view')" class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <a href="/admin/assign_dormitory" style="text-decoration: none; color: inherit;">
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
                                                            {{ dataList.assign_dormitories ? dataList.assign_dormitories
                                                                : 0
                                                            }} </span>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div v-if="can('dashboard_library_view')" class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <a href="/admin/library" style="text-decoration: none; color: inherit;">
                                        <div class="card-body">
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
                                                            Book Author: <strong>{{ dataList.book_authors ?
                                                                dataList.book_authors : 0 }}</strong>
                                                        </a>
                                                    </p>
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                        <a href="/admin/library/publisher"
                                                            style="text-decoration: none; color: inherit;">
                                                            <i class="fa fa-book"></i>
                                                            Book Publisher: <strong>{{ dataList.book_publishers ?
                                                                dataList.book_publishers : 0 }}</strong>
                                                        </a>
                                                    </p>
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                        <i class="fa fa-book"></i>
                                                        <a href="/admin/stock_books"
                                                            style="text-decoration: none; color: inherit;">
                                                            Book Stocks: <strong>{{ dataList.stock_books ?
                                                                dataList.stock_books : 0 }}</strong>
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "dashboardPage",
    components: {},
    data() {
        return {

        }
    },
    mounted() {
        this.getDataList();
    }
}
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
</style>
