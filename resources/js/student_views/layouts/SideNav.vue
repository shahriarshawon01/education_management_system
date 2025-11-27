<template>
  <div class="app-menu navbar-menu">
    <div id="scrollbar">
      <div class="container-fluid">
        <div id="two-column-menu"></div>
        <ul class="navbar-nav" id="navbar-nav">
          <li class="nav-item hide_menu">
            <a class="nav-link menu-link" @click="hideMenu()">
              <i class="fa fa-chevron-left"></i> Hide
            </a>
            <hr>
          </li>
          <li class="menu-title"><span data-key="t-menu">Dashboard</span></li>
          <li class="nav-item">
            <router-link :to="`/student/dashboard`" class="nav-link menu-link">
              <i class="ri-dashboard-2-line"></i>
              <span data-key="t-dashboards">Dashboards</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/student/teachers" class="nav-link menu-link">
              <i class="ri-dashboard-2-line"></i>
              <span data-key="t-dashboards">Teachers</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link :to="`/student/notice`" class="nav-link menu-link">
              <i class="ri-dashboard-2-line"></i>
              <span data-key="t-dashboards">Notice</span>
            </router-link>
          </li>
          <li class="menu-title"><span data-key="t-menu">Academic</span></li>
          <li class="nav-item">
            <router-link to="/student/subjects" class="nav-link menu-link">
              <i class="ri-dashboard-2-line"></i>
              <span data-key="t-dashboards">Subjects</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/student/library" class="nav-link menu-link">
              <i class="ri-dashboard-2-line"></i>
              <span data-key="t-dashboards">Library</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/student/routine" class="nav-link menu-link">
              <i class="ri-dashboard-2-line"></i>
              <span data-key="t-dashboards">Class Routine</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link :to="`/student/payments`" class="nav-link menu-link">
              <i class="ri-dashboard-2-line"></i>
              <span data-key="t-dashboards">Payments</span>
            </router-link>
          </li>
          <li class="menu-title"><span data-key="t-menu">Canteen</span></li>
          <li class="nav-item">
            <router-link :to="`/student/canteen_meal`" class="nav-link menu-link">
              <i class="ri-dashboard-2-line"></i>
              <span data-key="t-dashboards">Canteen </span>
            </router-link>
          </li>
        </ul>
      </div>
      <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
  </div>
</template>

<script>
export default {
  name: "SideNav",
  mounted() {
    this.getConfigurations();
  },
};
</script>

<style scoped></style>
