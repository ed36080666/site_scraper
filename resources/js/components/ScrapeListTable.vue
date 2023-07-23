<template>
  <section>
    <div class="mb-2">
      <div class="flex gap-1">
        <input
          v-model="query"
          class="block w-full p-2 text-sm rounded-sm bg-gray-50 border border-gray-300 text-gray-900 focus:outline-none focus:border-blue-400 focus:ring-blue-400 focus-visible:ring"
          type="text"
          placeholder="Filter by name..."
          @keyup.enter="onSearch"
        />
        <button
          class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-2 rounded"
          @click="onSearch"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
        </button>
      </div>
    </div>
    <loading-overlay :is-loading="loading">
      <table class="table-auto w-full text-gray-900">
        <thead>
        <tr class="text-left">
          <th class="py-2 z-10 border-b border-gray-200 bg-neutral-50 sticky top-0" style="transform: translateY(-1px);">Name</th>
          <th class="py-2 z-10 border-b border-gray-200 bg-neutral-50 sticky top-0" style="transform: translateY(-1px);">Started At</th>
          <th class="py-2 z-10 border-b border-gray-200 bg-neutral-50 sticky top-0" style="transform: translateY(-1px);" colspan="3">Resolution</th>
        </tr>
        </thead>
        <tbody>
        <scrape-list-table-row
          v-for="(item, index) in items"
          :is-striped="index % 2 !== 0"
          :item="item"
          :key="'item' + item.id"
          @delete="onDelete"
        />
        </tbody>
      </table>
      <div
        v-if="!items.length && !loading && !error"
        class="py-10 text-gray-800 font-bold bg-gray-100"
      >
        <span>No results found</span>
      </div>
      <div
        v-if="error"
        class="py-10 text-gray-800 font-bold bg-gray-100"
      >
        <span>Something went wrong. Check console.</span>
      </div>
    </loading-overlay>

    <div class="mt-4">
      <button
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-1 rounded"
        :class="{ 'opacity-50 cursor-not-allowed' : currentPage === 1 }"
        :disabled="currentPage === 1"
        @click="onPrevious"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
        </svg>
      </button>
      <button
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-1 rounded"
        :class="{ 'opacity-50 cursor-not-allowed' : !hasNext }"
        :disabled="!hasNext"
        @click="onNext"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
      </button>
    </div>
  </section>
</template>

<script>
import axios from 'axios';
import LoadingOverlay from "./LoadingOverlay.vue";
import ScrapeListTableRow from "./ScrapeListTableRow";

export default {
  name: "ScrapeListTable",
  components: {
    LoadingOverlay,
    ScrapeListTableRow
  },
  data() {
    return {
      currentPage: 1,
      error: null,
      items: [],
      loading: false,
      pagination: null,
      pollingDuration: 3000,
      pollingInterval: null,
      query: ''
    }
  },
  computed: {
    hasNext() {
      return this.pagination && this.pagination.has_more;
    },
  },
  async mounted() {
    this.loading = true;
    this.startPolling();
  },
  destroyed() {
    clearInterval(this.pollingInterval);
  },
  methods: {
    async onNext() {
      this.loading = true;
      this.currentPage = this.currentPage + 1;
      clearInterval(this.pollingInterval);
      await this.fetchItems();
      this.startPolling(2000);
    },
    async onPrevious() {
      this.loading = true;
      this.currentPage = this.currentPage > 1 ? this.currentPage - 1 : 1;
      clearInterval(this.pollingInterval);
      await this.fetchItems();
      this.startPolling(2000);
    },
    async onSearch() {
      this.loading = true;
      this.currentPage = 1;
      clearInterval(this.pollingInterval);
      await this.fetchItems();
      this.startPolling(2000);
    },
    startPolling(delay = 0) {
      setTimeout(() => {
        this.pollingInterval = setInterval(this.fetchItems, this.pollingDuration);
      }, delay);
    },
    async onDelete(id) {
      try {
        this.loading = true;
        clearInterval(this.pollingInterval);
        await axios.delete(`/${id}/delete`)
        await this.fetchItems();
        this.startPolling(2000);
      } catch (e) {
        this.error = e;
        console.error(error);
      } finally {
        this.loading = false;
      }
    },
    async fetchItems() {
      this.error = null;

      const params = new URLSearchParams({
        page: this.currentPage,
        query: this.query
      });

      try {
        const response = await axios(`api/items?${params}`);
        const { data, meta } = response.data;

        this.pagination = meta;
        this.currentPage = meta.current_page;
        this.items = data;
      } catch (e) {
        this.error = e;
        console.error(e);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>
