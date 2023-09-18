<template>
    <div class="load-media">
        <div class="load-media__title" @click.self="openExplorer">
            {{ title }}
            <Transition name="scale-up">
                <button v-if="videoSrc" class="load-media__cancel icon-cancel" @click.stop="removeVideo"></button>
            </Transition>
        </div>
        <div class="load-media__container" :class="{ 'load-media__container--loaded': videoSrc }" ref="loadedImageContainer"
            @click.self="openExplorer">
            <Transition name="scale-up" mode="out-in">
                <VideoPlayer v-if="videoSrc" :src="videoSrc"></VideoPlayer>
                <span v-else class="icon-add-video" @click.self="openExplorer"></span>
            </Transition>
        </div>
        <ProgressBar :loadProgress="videoLoadProgress"></ProgressBar>
        <input ref="inputFile" type="file" accept="video/mp4"
            :style="{ display: 'opacity: 0', visibility: 'hidden', width: '0', height: '0' }" @change="uploadVideo">
    </div>
</template>

<script>
import VideoPlayer from '@/components/global/VideoPlayer.vue';
import Resumable from 'resumablejs';
import Cookie from 'js-cookie';
import axios from 'axios';

export default {
    name: 'LoadVideo',
    components: {
        VideoPlayer
    },
    props: {
        width: {
            type: [String, Number]
        },
        height: {
            type: [String, Number]
        },
        title: {
            type: String,
            default: 'Загрузка видео'
        },
        modelValue: [String, Number], // id видео из БД
        src: String
    },
    emits: ['update:modelValue'],
    data() {
        return {
            imageWidth: this.width ? parseInt(this.width) : 0,
            imageHeight: this.height ? parseInt(this.height) : 0,
            videoSrc: this.src,
            videoLoadProgress: 0,
            videoId: null,
            resumable: null
        }
    },
    mounted() {
        this.initResumable();
    },
    methods: {
        initResumable() {
            this.resumable = new Resumable({
                target: `${import.meta.env.VITE_API_LINK}video-upload`,
                fileType: ['mp4'],
                query: {
                    videoId: this.videoId
                },
                headers: {
                    'X-XSRF-TOKEN': Cookie.get('XSRF-TOKEN'),
                    'Accept': 'application/json'
                },
                withCredentials: true,
                throttleProgressCallbacks: 1,
                testChunks: false,
                setChunkTypeFromFile: true
            });

            this.resumable.on('fileAdded', (file) => {
                this.resumable.opts.query.videoId = this.videoId;
                file.resumableObj.upload();
            });

            this.resumable.on('fileProgress', (file) => {
                this.videoLoadProgress = Math.round(file.progress() * 100);
            });

            this.resumable.on('fileSuccess', (file, message) => {
                this.videoLoadProgress = 0;
                try {
                    const jsonMessage = JSON.parse(message);
                    const path = `${import.meta.env.VITE_BACKEND_LINK}${jsonMessage.path}`;
                    this.videoSrc = path;
                    this.videoId = jsonMessage.id;
                } catch (err) {
                    throw new Error(err);
                }

                this.resumable.removeFile(file);
            });
        },
        openExplorer() {
            this.$refs.inputFile.click();
        },
        uploadVideo() {
            const input = this.$refs.inputFile;
            const files = input.files;
            if (files.length > 0) {
                this.resumable.addFile(files[0]);
            }
        },
        async removeVideo() {
            const input = this.$refs.inputFile;
            const dt = new DataTransfer();
            input.files = dt.files;
            input.value = '';
            input.dispatchEvent(new Event('change'));
            await axios.delete(`${import.meta.env.VITE_API_LINK}video/${this.videoId}`);
            this.videoSrc = null;
            this.videoId = null;
        }
    },
    watch: {
        videoId() {
            this.$emit('update:modelValue', this.videoId);
        },
    }
}
</script>

<style></style>