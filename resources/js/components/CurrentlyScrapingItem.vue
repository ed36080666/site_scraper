<template>
  <li class="flex currently-scraping-item" :class="{'removing': item.removing}">
    <span class="mr-3 mt-1">
      <exclamation v-if="item.status === 'error'" class="text-red-400" />
      <checkmark v-else-if="item.status === 'done'" class="text-green-600" />
      <spinner v-else />
    </span>
    <div class="flex flex-col text-left">
      <span class="font-semibold">{{ item.filename }}</span>
      <span class="leading-4">{{ item.video_url }}</span>
    </div>
  </li>
</template>

<script>
import Checkmark from "./icons/Checkmark.vue";
import Exclamation from "./icons/Exclamation.vue";
import Spinner from "./icons/Spinner.vue";

export default {
  name: "CurrentlyScrapingItem",
  components: {
    Checkmark,
    Exclamation,
    Spinner,
  },
  component: {
    Spinner,
  },
  props: {
    item: {
      type: Object,
      required: true
    },
  },
  data() {
    return {
      //
    };
  },
  methods: {
    //
  }
}
</script>

<style scoped>
  .currently-scraping-item {
    transition: opacity 1.5s ease;
  }
  .removing {
    opacity: 0;
  }
</style>
