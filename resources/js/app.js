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
    logoMap: window.__INITIAL_STATE__.logo_map,
    scrapeItems: window.__INITIAL_STATE__.scrape_items
  }),
  created() {
    setInterval(this.pollProgress, 1500);
  },
  methods: {
    onItemDelete(itemId) {
      axios.delete(`/${itemId}/delete`)
        .then(response => {
          const index = this.scrapeItems.findIndex(item => {
            return item.id === itemId;
          });

          if (index > -1) {
            this.scrapeItems.splice(index, 1);
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
            const index = this.scrapeItems.findIndex(video => video.id === item.id);
            // if item already exists, refresh the entire item for new status, progress, etc.
            if (index > -1) {
              this.$set(this.scrapeItems, index, {...this.scrapeItems[index], ...item});
            } else {
              // else a new item being added, push it to the list.
              this.scrapeItems.push(item);
            }
          });
        })
        .catch(error => {
          console.error(error);
        });
    }
  }
})
