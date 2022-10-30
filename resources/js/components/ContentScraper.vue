<template>
  <section>
    <div class="flex gap gap-x-3.5 mb-2.5">
      <site-logo
        v-for="logo in logoMap"
        :is-active="url.includes(logo.base_url)"
        :key="logo.base_url"
        :logo="logo"
      />
    </div>
    <form class="flex flex-col gap-y-2" action="/scrape-page" method="POST">
      <input type="hidden" name="_token" :value="csrf">
      <input
        v-model="url"
        class="block w-full p-2 text-sm rounded-sm bg-gray-50 border border-gray-300 text-gray-900 focus:outline-none focus:border-blue-800 focus:ring-blue-400 focus-visible:ring"
        name="video_url"
        placeholder="URL"
        required
        type="text"
      />
      <input
        v-model="filename"
        class="block w-full p-2 text-sm rounded-sm bg-gray-50 border border-gray-300 text-gray-900 focus:outline-none focus:border-blue-800 focus:ring-blue-400 focus-visible:ring"
        name="filename"
        placeholder="Filename"
        required
        type="text"
      />
      <div class="text-left mt-2">
        <button
          class="py-1.5 px-4 rounded bg-transparent font-semibold text-blue-700 border border-blue-500  hover:bg-blue-500 hover:text-white hover:border-transparent"
        >
          Scrape
        </button>
      </div>
    </form>
  </section>
</template>

<script>
import SiteLogo from "./SiteLogo";

export default {
  name: "ContentScraper",
  components: {
    SiteLogo
  },
  props: {
    logoMap: {
      type: Array,
      required: true
    },
    csrf: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      filename: "",
      url: ""
    };
  }
}
</script>
