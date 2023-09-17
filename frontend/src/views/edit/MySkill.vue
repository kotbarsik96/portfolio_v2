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
                    <Transition name="fade-in">
                        <div v-if="videoLoadProgress && videoLoadProgress > 0" class="loading-scale">
                            <div class="loading-scale__progressbar" :style="{ 'width': `${videoLoadProgress}%` }"></div>
                            <div class="loading-scale__percentage">
                                {{ videoLoadProgress }}
                                %
                            </div>
                        </div>
                    </Transition>
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
import Resumable from 'resumablejs';
import Cookie from 'js-cookie';

export default {
    name: 'MySkill',
    components: {
        LoadImage,
        LoadVideo
    },
    data(){
        return {
            videoLoadProgress: 0
        }
    },
    methods: {
        async update() {

        },
        async remove() {

        },
        async load() {
            const self = this;
            const imageLoadComponent = this.$refs.imageLoadComponent;
            const videoLoadComponent = this.$refs.videoLoadComponent;
            if (!imageLoadComponent || !videoLoadComponent)
                return;

            const image = imageLoadComponent.$refs.input.files;
            if (image.length > 0)
                loadImage();

            const video = videoLoadComponent.$refs.input.files;
            if (video.length > 0)
                loadVideo();

            async function loadImage() {
                const data = new FormData();
                data.append('image', image[0]);
                await axios.post(`${import.meta.env.VITE_API_LINK}image`, data);
            }
            async function loadVideo() {
                const resumable = new Resumable({
                    target: `${import.meta.env.VITE_API_LINK}video-upload`,
                    fileType: ['mp4'],
                    headers: {
                        'X-XSRF-TOKEN': Cookie.get('XSRF-TOKEN'),
                        'Accept': 'application/json'
                    },
                    withCredentials: true,
                    throttleProgressCallbacks: 1,
                    testChunks: false,
                    setChunkTypeFromFile: true
                });

                resumable.on('fileAdded', (file) => {
                    file.resumableObj.upload();
                });

                resumable.on('fileProgress', (file) => {
                    self.videoLoadProgress = Math.round(file.progress() * 100);
                });

                resumable.on('fileSuccess', () => {
                    self.videoLoadProgress = 0;
                });

                resumable.addFile(video[0]);
            }
        }
    }
}
</script>

<style></style>