<template>
  <div class="relative font-bold w-72 rounded-sm text-left bg-gray-200">
    <div
      class="rounded-sm h-5"
      :class="{
        'bg-green-200': progress === 100,
        'bg-red-200': status === 'error',
        'bg-blue-200': status === 'processing' && progress !== 100,
        'streaming': isStream && status === 'processing' && progress !== 100
      }"
      role="progressbar"
      :title="caption"
      :style="{ width: isStream ? '100%' : progress + '%' }"
      aria-valuenow="0"
      aria-valuemin="0"
      aria-valuemax="100"
    >
    </div>
    <span
      class="absolute text-left w-1/4"
      :class="{
        'text-red-800': status === 'error',
        'text-green-800': progress === 100,
        'text-blue-800': status === 'processing' && progress !== 100 && !isStream
      }"
      style="top: 50%; left: 50%; transform: translateY(-50%) translateX(-50%);"
    >
      {{ caption }}
    </span>
  </div>
</template>

<script>
export default {
  name: "ProgressBar",
  props: {
    progress: {
      type: Number,
      required: true
    },
    status: {
      type: String,
      required: true
    },
    isStream: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    caption() {
      return this.isStream && this.status === 'processing' && this.progress !== 100
        ? 'Streaming'
        : `${this.progress.toFixed(2)}%`;
    }
  }
}
</script>

<style scoped>
  .streaming {
    background-image: repeating-linear-gradient(120deg, #cdd0d1, #cdd0d1 20px, #eee 20px, #eee 40px);
    background-size: 5000%;
    animation: animateStreaming 200s linear infinite;
  }

  @keyframes animateStreaming {
    from {
      background-position: 0 0;
    }
    to {
      background-position: -100% 0;
    }
  }
</style>
