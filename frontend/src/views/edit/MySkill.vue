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
                    <LoadImage title="Изображение" ref="imageLoadComponent" v-model="imageId"
                        v-model:isUploading="isUploadingImage"></LoadImage>
                </div>
                <div class="add__load-media-item">
                    <LoadVideo title="Видео" ref="videoLoadComponent" v-model="videoId"
                        v-model:isUploading="isUploadingVideo"></LoadVideo>
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
import { useMyStore } from '@/stores/store.js';

export default {
    name: 'MySkill',
    components: {
        LoadImage,
        LoadVideo
    },
    data() {
        return {
            videoId: null,
            imageId: null,
            isUploadingImage: false,
            isUploadingVideo: false
        }
    },
    methods: {
        // пока загружается изображение или видео, не дает выполнить update()/upload() навыка. Эти методы будут вызваны как только все медиа будут загружены, если они загружаются
        checkIfUploading(methodName = '') {
            if (this.isUploadingImage || this.isUploadingVideo) {
                function stopWatcher() {
                    if (typeof method === 'function')
                        method();

                    store.isLoading = false;
                    watcher();
                }

                const store = useMyStore();
                const method = this[methodName];
                store.isLoading = true;

                const watcher = this.isUploadingImage
                    ? this.$watch('isUploadingImage', stopWatcher)
                    : this.$watch('isUploadingVideo', stopWatcher);

                return true;
            }

            return false;
        },
        async update() {
            if (this.checkIfUploading('update'))
                return;
        },
        async remove() {

        },
        async load() {
            if (this.checkIfUploading('load'))
                return;

            
        }
    },
}
</script>

<style></style>