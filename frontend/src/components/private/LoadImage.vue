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
        }
    },
    data() {
        return {
            imageWidth: this.width ? parseInt(this.width) : 0,
            imageHeight: this.height ? parseInt(this.height) : 0,
            loadedImageSrc: null
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
        },
        removeImage() {
            this.loadedImageSrc = null;
            this.$refs.input.files = new DataTransfer().files;
        }
    }
}
</script>

<style></style>