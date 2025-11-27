<template>
  <div>
    <div class="d-flex">
      <!-- Nav tabs -->
      <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1"
        role="tablist">
        <li class="nav-item">
          <router-link to="/student/dashboard" class="nav-link fs-14">
            <i class="ri-airplay-fill d-inline-block d-md-none"></i>
            <span class="d-none d-md-inline-block">Dashboard</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/student/teachers" class="nav-link fs-14">
            <i class="ri-airplay-fill d-inline-block d-md-none"></i>
            <span class="d-none d-md-inline-block">Teachers</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/student/subjects" class="nav-link fs-14">
            <i class="ri-airplay-fill d-inline-block d-md-none"></i>
            <span class="d-none d-md-inline-block">Subjects</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/student/routine" class="nav-link fs-14">
            <i class="ri-airplay-fill d-inline-block d-md-none"></i>
            <span class="d-none d-md-inline-block">Class Routine</span>
          </router-link>
        </li>
        <!-- <li class="nav-item">
          <router-link to="/student/exam_routine" class="nav-link fs-14">
            <i class="ri-airplay-fill d-inline-block d-md-none"></i>
            <span class="d-none d-md-inline-block">Exam Routine</span>
          </router-link>
        </li> -->
        <li class="nav-item">
          <router-link to="/student/canteen_meal" class="nav-link fs-14">
            <i class="ri-airplay-fill d-inline-block d-md-none"></i>
            <span class="d-none d-md-inline-block">Canteen</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/student/payments" class="nav-link fs-14">
            <i class="ri-airplay-fill d-inline-block d-md-none"></i>
            <span class="d-none d-md-inline-block">Payments</span>
          </router-link>
        </li>
      </ul>
      <div class="flex-shrink-0">
        <ul style="list-style: none">
          <li class="nav-item">
            <router-link to="/student/profile" class="btn btn-success">
              <i class="ri-airplay-fill d-inline-block d-md-none"></i>
              <i class="fa-solid fa-user"></i> Profile
            </router-link>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "pageTopMenu",
};
</script>

<style scoped></style>
