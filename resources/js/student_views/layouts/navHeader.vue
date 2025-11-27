<template>
    <header id="page-topbar">
        <div class="layout-width">
            <div class="navbar-header">
                <div class="d-flex">
                    <div class="d-flex align-items-center gap-1">
                        <button type="button"
                            class="btn btn-sm px-2 fs-16 header-item vertical-menu-btn topnav-hamburger"
                            id="topnav-hamburger-icon" @click="toggleSidebar">
                            <span class="menu_colups_icon">
                                <i v-if="httpRequest" class="fa fa-spin fa-spinner"></i>
                                <i v-else class="fa fa-list"></i>
                            </span>
                        </button>

                        <div class="uppercase">
                            <strong>
                                <h4 class="mb-0">{{ Config?.institutes?.[0]?.title }}</h4>
                            </strong>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center">
                    <div class="dropdown ms-sm-3 header-item topbar-user">
                        <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="d-flex align-items-center">
                                <img v-if="userImage" class="rounded-circle header-profile-user" :src="userImage"
                                    alt="User Image" />
                                <div v-else
                                    class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center header-profile-user"
                                    style="width: 40px; height: 40px; font-weight: 600; font-size: 14px;">
                                    {{ getInitials(showData(Config.user, 'username')) }}
                                </div>

                                <span class="text-start ms-xl-2 d-flex">
                                    <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">
                                        {{ showData(Config.user, 'username') }}
                                    </span>
                                </span>
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <h6 class="dropdown-header">Welcome {{ showData(Config.user, 'username') }}!</h6>
                            <router-link to="/student/profile" class="dropdown-item">
                                <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                                <span class="align-middle">Profile</span>
                            </router-link>
                            <a class="dropdown-item" href="/logout"><i
                                    class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span
                                    class="align-middle" data-key="t-logout">Logout</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script>
import SideNav from "./SideNav";
export default {
    name: "navHeader",
    components: { SideNav },
    data() {
        return {
            studentData: {
                student: null,
            },

            isSidebarCollapsed: false,
        }
    },

    computed: {
        userImage() {
            const img = this.showData(this.Config.user, 'image');
            if (!img || typeof img !== 'string' || img.trim() === '' || img === 'null' || img === 'NULL' || img === '-') {
                return null;
            }
            return this.getImage(img);
        },
    },

    methods: {
        toggleSidebar() {
            this.isSidebarCollapsed = !this.isSidebarCollapsed;
        },
        hideMenu() {
            this.isSidebarCollapsed = true;
        },

        getDashboardData: function () {
            const _this = this;
            _this.getData(_this.urlGenerate('dashboard'), "get", {}, {}, function (retData) {
                _this.studentData = retData;
            });
        },

        getInitials(username) {
            if (!username || typeof username !== 'string' || !username.trim()) return '';
            const parts = username.trim().split(' ');
            if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        },
    },

    mounted() {
        this.getDashboardData();
    }
}
</script>

<style scoped></style>
