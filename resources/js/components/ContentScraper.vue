<template>
  <section>
    <div class="flex flex-wrap gap gap-x-3.5 gap-y-3.5 mb-2.5">
      <site-logo
        v-for="logo in logoMap"
        :is-active="url.includes(logo.base_url)"
        :key="logo.base_url"
        :logo="logo"
      />
    </div>
    <form class="flex flex-col gap-y-2" @submit.prevent="scrapeUrl">
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
        <button class="py-1.5 px-4 rounded bg-transparent font-semibold text-blue-700 border border-blue-500  hover:bg-blue-500 hover:text-white hover:border-transparent">
          Scrape
        </button>
      </div>
      <ul class="flex flex-col gap-y-4 mt-3">
        <currently-scraping-item
          v-for="(video, index) in currentlyScraping"
          :key="index"
          :item="video"
        />
      </ul>
    </form>
  </section>
</template>

<script>
import CurrentlyScrapingItem from "./CurrentlyScrapingItem.vue";
import SiteLogo from "./SiteLogo";

export default {
  name: "ContentScraper",
  components: {
    CurrentlyScrapingItem,
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
      filename: '',
      currentlyScraping: [],
      url: ''
    };
  },
  methods: {
    scrapeUrl(e) {
      const payload = {
        filename: this.filename,
        video_url: this.url,
      };
      this.currentlyScraping.push({
        ...payload,
        status: 'scraping',
        removing: false
      });

      // clear form inputs
      this.filename = '';
      this.url = '';

      axios.post('/scrape-page', payload)
        .then(res => {
          const index = this.currentlyScraping.findIndex(item => item.filename === payload.filename);

          if (res.data.success) {
            this.currentlyScraping[index].status = 'done';
            this.currentlyScraping[index].removing = true;

            setTimeout(() => {
              this.currentlyScraping.splice(index, 1);
            }, 2500);
          } else {
            console.error(res.data);
            this.currentlyScraping[index].status = 'error';
          }
        });
    }
  }
}
</script>
