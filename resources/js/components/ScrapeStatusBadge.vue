<template>
  <span
    class="text-xs font-semibold mr-2 px-2.5 py-0.5 rounded"
    :class="{
      'text-gray-800 bg-gray-100' : status === 'queued',
      'text-blue-800 bg-blue-100' : status === 'processing',
      'text-green-800 bg-green-100' : status === 'done' || progress === 100,
      'text-red-800 bg-red-100' : status === 'error'
    }"
  >
    {{ progress === 100 ? 'done' : status }}
  </span>
</template>

<script>
export default {
  name: "ScrapeStatusBadge",
  props: {
    status: {
      type: String,
      required: true
    },
    progress: {
      type: Number,
      required: true
    }
  }
}
</script>

<style scoped>

</style>
