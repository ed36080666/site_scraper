<template>
  <section>
    <div class="mb-2">
      <input
        v-model="query"
        class="block w-full p-2 text-sm rounded-sm bg-gray-50 border border-gray-300 text-gray-900 focus:outline-none focus:border-blue-400 focus:ring-blue-400 focus-visible:ring"
        type="text"
        placeholder="Filter by name..."
      />
    </div>
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
        v-for="(item, index) in filteredItems"
        :is-striped="index % 2 !== 0"
        :item="item"
        :key="item.id"
        @delete="e => $emit('delete', e)"
      />
      </tbody>
    </table>
    <div
      v-if="!filteredItems.length"
      class="py-10 text-gray-800 font-bold bg-gray-100"
    >
      <span>No results found</span>
    </div>
  </section>
</template>

<script>
import ScrapeListTableRow from "./ScrapeListTableRow";

export default {
  name: "ScrapeListTable",
  components: {
    ScrapeListTableRow
  },
  props: {
    items: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      query: ''
    }
  },
  computed: {
    filteredItems() {
      // pull all items where processing has been started and order them.
      const inProgress = this.items
        .filter(item => Boolean(item.started_at))
        .sort((a, b) => {
          return (a.started_at.localeCompare(b.started_at));
        })
        .reverse();

      // pull the queued items that have not started processing.
      const queued = this.items.filter(item => !Boolean(item.started_at));

      // concat these item sets with in progress at top and then filter with our search query.
      return inProgress.concat(queued)
        .filter(item => item.name.toLowerCase().includes((this.query || '').toLowerCase()));
    }
  }
}
</script>
