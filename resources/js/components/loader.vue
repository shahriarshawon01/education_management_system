<template>
  <div v-if="visible" class="loader-overlay">
    <div class="spinner-icon"></div>
  </div>
</template>

<script>
export default {
  name: "Loader",
  props: {
    visible: {
      type: Boolean,
      default: false
    }
  }
};
</script>

<style scoped>
.loader-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.spinner-icon {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  border: 5px solid transparent;
  border-top: 5px solid #ffffff;
  border-right: 5px solid #3498db;
  border-bottom: 5px solid #8e44ad;
  border-left: 5px solid #f1c40f;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
