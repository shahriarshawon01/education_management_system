<template>
    <div id="layout-wrapper">
        <top-nav-bar></top-nav-bar>
        <side-nav-bar></side-nav-bar>
        <div class="vertical-overlay"></div>
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <router-view></router-view>
                </div>
            </div>
            <adminFooter></adminFooter>
        </div>
        <details-modal></details-modal>

    </div>
</template>

<script>
import TopNavBar from "./topNavBar";
import SideNavBar from "./sideNavBar";
import AdminFooter from "./adminFooter";
import DetailsModal from "../../components/detailsModal";

export default {
    name: "baseLayouts",
    components: { SideNavBar, TopNavBar, AdminFooter, DetailsModal }
}
</script>

<style scoped></style>
