require('./bootstrap');

// Globals
import Vue from "vue";
import axios from "axios";

// Components
import ContentScraper from "./components/ContentScraper";
import ScrapeListTable from "./components/ScrapeListTable";

Vue.component('content-scraper', ContentScraper);
Vue.component('scrape-list-table', ScrapeListTable);

var app = new Vue({
  el: '#app',
  data: () => ({
    videos: window.__INITIAL_STATE__.videos,
    logoMap: window.__INITIAL_STATE__.logo_map
  }),
  created() {
    setInterval(this.pollProgress, 1500);
  },
  methods: {
    onItemDelete(itemId) {
      axios.delete(`/${itemId}/delete`)
        .then(response => {
          const index = this.videos.findIndex(item => {
            return item.id === itemId;
          });

          if (index > -1) {
            this.videos.splice(index, 1);
          }
        })
        .catch(error => {
          console.error(error);
          alert("Could not delete!");
        })
    },

    pollProgress() {
      axios.get('/in-progress')
        .then(response => {
          response.data.forEach(item => {
            const index = this.videos.findIndex(video => {
              return video.id === item.id;
            });

            if (index > -1) {
              this.videos[index].progress = item.progress;

              // if video currently waiting as queued, we will reset its data when it starts processing
              // because we have some info we didn't have before (resolution, size, etc.)
              if (this.videos[index].status === 'queued') {
                this.$set(this.videos, index, {...this.videos[index], ...item});
              }
            }
          });
        })
        .catch(error => {
          console.error(error);
        });
    }
  }
})
