require('./bootstrap');

import Vue from "vue";
import axios from "axios";

var app = new Vue({
  el: '#app',
  data: () => ({
    videos: window.__INITIAL_STATE__.videos,
    logoMap: window.__INITIAL_STATE__.logo_map,
    query: null,
    videoUrl: null,
  }),
  created() {
    setInterval(this.pollProgress, 1500);
  },
  computed: {
    filteredVideos() {
      // pull all videos where processing has been started and order them.
      const inProgress = this.videos
        .filter(video => Boolean(video.started_at))
        .sort((a, b) => {
          return (a.started_at.localeCompare(b.started_at));
        })
        .reverse();

      // pull the queued videos that have not started processing.
      const queued = this.videos.filter(video => !Boolean(video.started_at));

      // concat these video sets with in progress at top and then filter with our search query.
      return inProgress.concat(queued).filter(video => video.name.toLowerCase().includes((this.query || '').toLowerCase()));
    }
  },
  methods: {
    resolution(video) {
      if (video.height && video.width) {
        return `${video.width}x${video.height}`;
      }
      return "";
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
    },

    deleteVideo(video) {
      axios.delete(`/${video.id}/delete`)
        .then(response => {
          const index = this.videos.findIndex(item => {
            return item.id === video.id;
          });

          if (index > -1) {
            this.videos.splice(index, 1);
          }
        })
        .catch(error => {
          console.error(error);
          alert("Could not delete!");
        })
    }
  }
})
