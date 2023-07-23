<template>
  <div class="loading-overlay">
    <div
      v-if="isLoading"
      class="loading-overlay__overlay"
    >
      <p>Loading...</p>
    </div>

    <slot />
  </div>
</template>

<script>
export default {
  name: "SiteLogos",
  props: {
    isLoading: {
      type: Boolean,
      default: false
    }
  },
}
</script>

<style scoped>
.loading-overlay {
  position: relative;
  min-height: 200px;
}

.loading-overlay__overlay {
  z-index: 99;
  position: absolute;
  width: 100%;
  height: 100%;
  background: rgba(200, 200, 200, 0.10);
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>
