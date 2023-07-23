require('./bootstrap');

// Globals
import Vue from "vue";

// Components
import ContentScraper from "./components/ContentScraper";
import ScrapeListTable from "./components/ScrapeListTable";

Vue.component('content-scraper', ContentScraper);
Vue.component('scrape-list-table', ScrapeListTable);

var app = new Vue({
  el: '#app',
  data: () => ({
    logoMap: window.__INITIAL_STATE__.logo_map,
  }),
})
