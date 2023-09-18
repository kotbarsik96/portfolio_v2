<template>
    <div class="load-media">
        <div class="load-media__title" @click="openExplorer">
            {{ title }}
            <Transition name="scale-up">
                <button v-if="loadedImageSrc" class="load-media__cancel icon-cancel" @click.stop="removeImage"></button>
            </Transition>
        </div>
        <div class="load-media__container" :class="{ 'load-media__container--loaded': loadedImageSrc }"
            ref="loadedImageContainer" @click.self="openExplorer">
            <Transition name="scale-up" mode="out-in">
                <img v-if="loadedImageSrc" :src="loadedImageSrc">
                <span v-else class="icon-add-image" @click.self="openExplorer"></span>
            </Transition>
        </div>
        <input type="file" ref="input" accept="image/png, image/jpeg" @change="onInputChange">
    </div>
</template>

<script>
import { useMyStore } from '@/stores/store.js';
import axios from 'axios';

export default {
    name: 'LoadImage',
    props: {
        width: {
            type: [String, Number]
        },
        height: {
            type: [String, Number]
        },
        title: {
            type: String,
            default: 'Загрузка изображения'
        },
        modelValue: [String, Number]
    },
    emits: ['update:modelValue', 'update:isUploading'],
    data() {
        return {
            loadedImageSrc: null,
            loadedImageId: null,
            isLoading: false,
            imageId: null,
            isUploading: false
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
                this.loadedImageSrc = reader.result;
            };
            reader.readAsDataURL(file);

            this.loadImage();
        },
        async loadImage() {

            const image = this.$refs.input.files[0];
            if (!image)
                return;

            this.isUploading = true;
            const data = new FormData();
            data.append('image', image);
            try {
                // если к скиллу уже прикреплено изображение, обновить его
                if (this.imageId) {
                    const url = `${import.meta.env.VITE_API_LINK}image/${this.imageId}`;
                    const res = await axios.post(url, data);
                    if (res.data.id)
                        this.imageId = res.data.id;
                }
                // если у скилла еще нет изображения, добавить
                else {
                    const res = await axios.post(`${import.meta.env.VITE_API_LINK}image`, data);
                    if (res.data && res.data.id)
                        this.imageId = res.data.id;
                }
            } catch (err) { }

            this.isUploading = false;
        },
        async removeImage() {
            this.loadedImageSrc = null;
            this.$refs.input.files = new DataTransfer().files;

            if (!this.imageId)
                return;

            await axios.delete(`${import.meta.env.VITE_API_LINK}image/${this.imageId}`);

            this.imageId = null;
        }
    },
    watch: {
        imageId() {
            this.$emit('update:modelValue', this.imageId);
        },
        isUploading() {
            useMyStore().isLoading = this.isUploading;
            this.$emit('update:isUploading', this.isUploading);
        }
    }
}
</script>

<style></style>