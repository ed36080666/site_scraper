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
            if (this.query) {
                return this.videos.filter(video => video.name.toLowerCase().includes(this.query.toLowerCase()));
            }
            return this.videos;
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
                .then (response => {
                    response.data.forEach(item => {
                        const index = this.videos.findIndex(video => {
                            return video.id === item.id;
                        });

                        if (index > -1) {
                            this.videos[index].progress = item.progress;
                        }
                    });
                })
                .catch (error => {
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
