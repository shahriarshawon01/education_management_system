<template>
    <div id="layout-wrapper" class="bg-light min-vh-100">
        <nav-header />
        <side-nav />
        <div class="vertical-overlay"></div>
        <div class="main-content">

            <div class="page-content py-4">
                <div class="container-fluid">
                    <top-profile />
                    <div class="row">
                        <div class="col-lg-12">
                            <page-top-menu />
                            <div class="tab-content pt-2 text-muted">
                                <div class="tab-pane active" id="overview-tab" role="tabpanel">
                                    <div class="row g-4">
                                        <!-- Basic Info Card -->
                                        <div class="col-xl-3">
                                            <div class="card shadow-sm border-0 rounded-3">
                                                <div class="card-body bg-white">
                                                    <h5 class="card-title text-primary mb-3">
                                                        <i class="bi bi-person-badge-fill me-2"></i> Basic Info
                                                    </h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-borderless mb-0 small">
                                                            <tbody>
                                                                <tr v-for="(value, label) in basicInfoFields"
                                                                    :key="label">
                                                                    <th class="ps-0 text-dark" scope="row">{{ label }}
                                                                    </th>
                                                                    <td class="text-muted">{{ value }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <!-- Parent Info -->
                                                        <h5 class="card-title text-success mt-4 mb-3">
                                                            <i class="bi bi-people-fill me-2"></i> Parent Info
                                                        </h5>
                                                        <table class="table table-borderless mb-0 small">
                                                            <tbody>
                                                                <tr v-for="(value, label) in parentInfoFields"
                                                                    :key="label">
                                                                    <th class="ps-0 text-dark" scope="row">{{ label }}
                                                                    </th>
                                                                    <td class="text-muted">{{ value }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Content Panel -->
                                        <div class="col-xl-9">
                                            <div class="card shadow-sm border-0 rounded-3">
                                                <div class="card-body">
                                                    <router-view />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <app-footer />
        </div>
    </div>
</template>

<script>
import NavHeader from "./navHeader";
import SideNav from "./SideNav";
import TopProfile from "./topProfile";
import PageTopMenu from "./pageTopMenu";
import AppFooter from "./appFooter";

export default {
    name: "studentBaseLayout",
    components: { PageTopMenu, TopProfile, SideNav, NavHeader, AppFooter },
    data() {
        return {
            studentData: {
                student: null,
            },
        };
    },
    computed: {
        basicInfoFields() {
            const s = this.studentData.student || {};
            return {
                "Full Name": this.showData(s, 'student_name_en'),
                "Student ID.": s.student_roll || '-',
                "Class": this.showData(s.classes, 'name'),
                "Roll No.": s.merit_roll || '-',
                "Blood Group": s.blood_group || '-',
                "Department": this.showData(s.departments, 'department_name'),
                "Section": this.showData(s.sections, 'name'),
                "Session": this.showData(s.sessions, 'title'),
                "Login E-mail": this.showData(this.Config.user, 'email'),
                "Birthday": this.showData(s, 'date_of_birth')
            };
        },
        parentInfoFields() {
            const s = this.studentData.student || {};
            return {
                "Father Name": this.showData(s, 'father_name_en'),
                "Father Phone": this.showData(s, 'father_phone'),
                "Mother Name": this.showData(s, 'mother_name_en'),
                "Mother Phone": this.showData(s, 'mother_phone'),
            };
        }
    },
    methods: {
        getDashboardData() {
            this.getData(this.urlGenerate('dashboard'), "get", {}, {}, (retData) => {
                this.studentData = retData;
            });
        }
    },
    mounted() {
        this.getDashboardData();
    }
};
</script>

<style scoped>
.table th {
    white-space: nowrap;
    width: 35%;
    padding-right: 1rem;
    color: #000;
    font-weight: 500;
}

.table td {
    white-space: nowrap;
    color: #6c757d;
}

.table {
    table-layout: auto;
}
</style>
