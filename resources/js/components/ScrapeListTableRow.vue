<template>
  <tr
    class="border-b border-gray-200 text-left text-sm"
    :class="isStriped ? 'bg-gray-100' : 'bg-white'"
  >
    <td class="pl-0 py-2">
      <scrape-status-badge
        :status="item.status"
        :progress="item.progress"
      />
      <span v-if="!item.file_exists" class="text-amber-500" style="position: absolute; transform: translateX(-90px);" title="File missing">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 13.5H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
        </svg>
      </span>
      <span>
        {{ item.name }}
      </span>
    </td>
    <td class="py-2">
      <span>{{ item.started_at }}</span>
    </td>
    <td class="py-2">
      <span>{{ formatResolution(item.height, item.width) }}</span>
    </td>
    <td class="py-2 flex justify-center">
      <progress-bar
        :is-stream="item.is_stream || false"
        :progress="item.progress"
        :status="item.status"
      />
    </td>
    <td class="py-2 pr-2">
      <span class="flex justify-end gap-2 relative">
        <a v-if="item.log_exists" class="text-gray-600" :href="item.id + '/log'" target="_blank">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zm3.75 11.625a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
          </svg>
        </a>
        <span @click="$emit('delete', item.id)" role="button">
          <svg class="h-6 w-6 text-red-400 cursor-pointer" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </span>
      </span>
    </td>
  </tr>
</template>

<script>
import ProgressBar from "./ProgressBar";
import ScrapeStatusBadge from "./ScrapeStatusBadge";

export default {
  name: "ScrapeListTableRow",
  components: {
    ProgressBar,
    ScrapeStatusBadge
  },
  props: {
    item: {
      type: Object,
      required: true
    },
    isStriped: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    formatResolution(height, width) {
      if (height && width) {
        return `${width}x${height}`;
      }
      return "";
    }
  }
}
</script>

<style scoped>

</style>
