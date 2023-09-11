<template>
    <div class="load-media">
        <div class="load-media__title" @click="openExplorer">
            {{ title }}
            <Transition name="scale-up">
                <button v-if="loadedVideoSrc" class="load-media__cancel icon-cancel" @click.stop="removeVideo"></button>
            </Transition>
        </div>
        <div class="load-media__container" :class="{ 'load-media__container--loaded': loadedVideoSrc }"
            ref="loadedImageContainer" @click.self="openExplorer">
            <Transition name="scale-up" mode="out-in">
                <VideoPlayer v-if="loadedVideoSrc" :src="loadedVideoSrc"></VideoPlayer>
                <span v-else class="icon-add-video" @click.self="openExplorer"></span>
            </Transition>
        </div>
        <input type="file" ref="input" accept="video/mp4, video/*" @change="onInputChange">
    </div>
</template>

<script>
import VideoPlayer from '@/components/global/VideoPlayer.vue';

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
        }
    },
    data() {
        return {
            imageWidth: this.width ? parseInt(this.width) : 0,
            imageHeight: this.height ? parseInt(this.height) : 0,
            loadedVideoSrc: null
        }
    },
    methods: {
        openExplorer() {
            this.$refs.input.click();
        },
        onInputChange() {
            const input = this.$refs.input;
            const file = input.files[0];
            if (!file)
                return;

            const reader = new FileReader();
            reader.onload = () => {
                this.loadedVideoSrc = reader.result;
            };
            reader.readAsDataURL(file);
        },
        removeVideo() {
            this.loadedVideoSrc = null;
            this.$refs.input.files = new DataTransfer().files;
        }
    }
}
</script>

<style></style>