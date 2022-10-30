<template>
  <a :href="linkHref" target="_blank">
    <img
      class="h-8"
      :class="{'grayscale opacity-25': !isActive}"
      :src="logo.src"
    />
  </a>
</template>

<script>
export default {
  name: "SiteLogos",
  props: {
    logo: {
      type: Object,
      required: true
    },
    isActive: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    linkHref() {
      return `https://${this.logo.base_url}`;
    }
  }
}
</script>

<style scoped>

</style>
