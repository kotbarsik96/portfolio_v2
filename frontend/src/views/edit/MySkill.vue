<template>
    <section id="my-skill">
        <h3 class="section-title">
            {{ $route.meta.isEditing ? 'Редактирование' : 'Добавление' }}
            навыка
        </h3>
        <div class="add" ref="form">
            <div class="add__inputs-section">
                <InputItem class="input-item--full" ref="titleInput" label="Название" placeholder="Название"
                    id="skill-title" name="title" :defaultValue="title" v-model="title">
                </InputItem>
            </div>
            <div class="add__load-media">
                <div class="add__load-media-item">
                    <LoadImage title="Изображение" ref="imageLoadComponent" subfolderName="skill"
                        :placeholderData="skillData.image" v-model="imageId" v-model:isUploading="isUploadingImage">
                    </LoadImage>
                </div>
                <div class="add__load-media-item">
                    <LoadVideo title="Видео" ref="videoLoadComponent" subfolderName="skill"
                        :placeholderData="skillData.video" v-model="videoId" v-model:isUploading="isUploadingVideo">
                    </LoadVideo>
                </div>
            </div>
            <div class="add__buttons">
                <button v-if="$route.meta.isEditing" class="button button--color_2" type="button" @click="update">
                    Сохранить
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
import axios from 'axios';

export default {
    name: 'MySkill',
    components: {
        LoadImage,
        LoadVideo
    },
    data() {
        return {
            title: '',
            skillData: {},
            videoId: null,
            imageId: null,
            isUploadingImage: false,
            isUploadingVideo: false,
        }
    },
    async beforeRouteEnter(to, from, next) {
        const store = useMyStore();
        store.isLoading = true;

        let skillData = {};

        const skillId = to.params.id;
        // если это страница добавления навыка, просто закончить инициализацию
        if (!skillId) {
            next(callback);
            return;
        }

        // если есть id, загрузить навык по этому id
        const url = `${import.meta.env.VITE_API_LINK}skill/${skillId}`;
        try {
            const res = await axios.get(url);
            if (res.data.not_found) {
                store.isLoading = false;
                next((self) => self.$router.push({ name: 'AddSkill' }));
                return;
            } else {
                skillData = res.data;
            }
        } catch (err) { }

        // в callback загруженная работа отправится в this.skillData (this === self)
        next(callback);

        function callback(self) {
            self.nullifyData();
            self.skillData = skillData;
            self.setSkillData();
            store.isLoading = false;
        }
    },
    beforeRouteLeave() {
        this.nullifyData();
    },
    methods: {
        nullifyData() {
            this.title = '';
            this.skillData = {};
            this.videoId = null;
            this.imageId = null;

            const refreshableRefs = ['titleInput'];
            refreshableRefs.forEach(str => {
                if (this.$refs[str] && typeof this.$refs[str].refresh === 'function')
                    this.$refs[str].refresh();
            });
        },
        setSkillData() {
            this.title = this.skillData.title;
        },
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
        getDataForUploading() {
            return {
                title: this.title,
                image_id: this.imageId,
                video_id: this.videoId,
            }
        },
        async load() {
            if (this.checkIfUploading('load'))
                return;

            const store = useMyStore();
            store.isLoading = true;

            const data = this.getDataForUploading();
            try {
                const res = await axios.post(`${import.meta.env.VITE_API_LINK}skill`, data);
                if (res && res.data && res.data.id) {
                    store.isLoading = false;
                    this.$router.push({ name: 'EditSkill', params: { id: res.data.id } });
                }
            } catch (err) { }

            this.afterAnyAction();
        },
        async update() {
            if (this.checkIfUploading('update'))
                return;

            const store = useMyStore();
            store.isLoading = true;

            const data = this.getDataForUploading();
            try {
                const url = `${import.meta.env.VITE_API_LINK}skill/${this.skillData.id}`;
                const res = await axios.post(url, data);
                if (res && res.data && res.data.id) {
                    store.isLoading = false;
                }
            } catch (err) { }

            this.afterAnyAction();
        },
        async remove() {
            if (!this.skillData)
                return;

            const store = useMyStore();
            store.isLoading = true;

            try {
                const res = await axios.delete(`${import.meta.env.VITE_API_LINK}skill/${this.skillData.id}`);
                if (!res.data.error) {
                    store.isLoading = false;
                    this.$router.push({ name: 'AddSkill' });
                }
            } catch (err) { }

            this.afterAnyAction();
        },
        afterAnyAction() {
            const store = useMyStore();
            store.loadAllData();
        }
    },
    computed: {
        imageData() {
            if (this.skillData && this.skillData.image)
                return this.skillData.image;

            return null;
        },
        videoData() {
            if (this.skillData && this.skillData.video)
                return this.skillData.video;

            return null;
        },
    }
}
</script>