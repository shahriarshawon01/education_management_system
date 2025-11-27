<template>
    <header id="page-topbar">
        <div class="layout-width">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box horizontal-logo">
                        <a href="/admin/dashboard" class="logo logo-dark">
                            <span class="logo-sm">
                                <img :src="getImage(null, 'images/logotpsc.png')" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img :src="getImage(null, 'images/logotpsc.png')" alt="" height="17">
                            </span>
                        </a>

                        <a href="/admin/dashboard" class="logo logo-light">
                            <span class="logo-sm">
                                <img :src="getImage(null, 'images/logotpsc.png')" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img :src="getImage(null, 'images/logotpsc.png')" alt="" height="17">
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                        id="topnav-hamburger-icon" @click="toggleAppMenu()">
                        <span class="menu_colups_icon">
                            <i v-if="httpRequest" class="fa fa-spin fa-spinner"></i>
                            <i v-else class="fa fa-list"></i>
                        </span>
                    </button>

                    <!-- App Search-->
                    <form class="app-search d-none d-md-block">
                        <div class="position-relative">
                            <h2 class="mt-1">
                                <strong class="uppercase">
                                    <span class="app_name text-info">{{ showData(Config.config, 'name') }}</span>
                                </strong>
                            </h2>
                        </div>
                    </form>
                </div>
                <div class="d-flex align-items-center">

                    <div class="dropdown d-md-none topbar-head-dropdown header-item">
                        <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                            id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <i class="bx bx-search fs-22"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                            aria-labelledby="page-header-search-dropdown">
                            <form class="p-3">
                                <div class="form-group m-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search ..."
                                            aria-label="Recipient's username">
                                        <button class="btn btn-primary" type="submit"><i
                                                class="mdi mdi-magnify"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

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
                            <router-link class="dropdown-item" to="profile">
                                <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                                <span class="align-middle">Profile</span>
                            </router-link>
                            <a class="dropdown-item" :href="`${baseUrl}/logout`">
                                <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                                <span class="align-middle">Logout</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </header>
</template>

<script>
export default {
    name: "topNavBar",
    data() {
        return {}
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
        toggleAppMenu: function () {
            $('body').addClass('vertical-sidebar-enable');
        },

        getInitials(username) {
            if (!username || typeof username !== 'string' || !username.trim()) return '';
            const parts = username.trim().split(' ');
            if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        },
    },
}
</script>

<style scoped>
.avatar-circle {
    width: 36px;
    height: 36px;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
}

.header-profile-user {
    width: 36px;
    height: 36px;
    object-fit: cover;
}
</style>
