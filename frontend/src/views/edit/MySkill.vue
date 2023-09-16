<template>
    <section id="my-skill">
        <h3 class="section-title">
            {{ $route.meta.isEditing ? 'Редактирование' : 'Добавление' }}
            навыка
        </h3>
        <div class="add">
            <div class="add__inputs-section">
                <InputItem class="input-item--full" label="Название" placeholder="Название" id="skill-title"
                    name="skill_title"></InputItem>
            </div>
            <div class="add__load-media">
                <div class="add__load-media-item">
                    <LoadImage title="Изображение" ref="imageLoadComponent"></LoadImage>
                </div>
                <div class="add__load-media-item">
                    <LoadVideo title="Видео" ref="videoLoadComponent"></LoadVideo>
                </div>
            </div>
            <div class="add__buttons">
                <button v-if="$route.meta.isEditing" class="button button--color_2" type="button" @click="update">
                    Редактировать
                </button>
                <button v-if="$route.meta.isEditing" class="button" type="button" @click="remove">
                    Удалить
                </button>
                <button v-else class="button button--color_2" type="button" @click="load">
                    Добавить
                </button>
            </div>
        </div>
    </section>
</template>

<script>
import LoadImage from '@/components/private/LoadImage.vue';
import LoadVideo from '@/components/private/LoadVideo.vue';
import axios from 'axios';

export default {
    name: 'MySkill',
    components: {
        LoadImage,
        LoadVideo
    },
    methods: {
        async update() {

        },
        async remove() {

        },
        async load() {
            const imageLoadComponent = this.$refs.imageLoadComponent;
            const videoLoadComponent = this.$refs.videoLoadComponent;
            if (!imageLoadComponent || !videoLoadComponent)
                return;

            const image = imageLoadComponent.$refs.input.files;
            const video = videoLoadComponent.$refs.input.files;
            if (image.length > 0)
                loadImage();
            if (video.length > 0)
                loadVideo();

            function loadImage() {
                const data = new FormData();
                data.append('image', image[0]);
                axios.post(`${import.meta.env.VITE_API_LINK}image`, data);
            }
            function loadVideo() {
                const data = new FormData();
                data.append('video', video[0]);
                console.log(video[0]);
                axios.post(`${import.meta.env.VITE_API_LINK}video`, data);
            }
        }
    }
}
</script>

<style></style>