<template>
    <section id="add-work">
        <h3 class="section-title">
            {{ $route.meta.isEditing ? 'Редактирование' : 'Добавление' }}
            работы
        </h3>
        <div class="add">
            <div class="add__inputs-section">
                <InputItem class="input-item--full" ref="titleInput" label="Название" placeholder="Название" id="work-title"
                    name="work_title" :defaultValue="workData.title" v-model="workTitle"></InputItem>
            </div>
            <div class="add__inputs-section">
                <InputItem class="input-item--full" ref="urlInput" label="URL" placeholder="URL" id="work-url"
                    name="work_url" :defaultValue="workData.url" v-model="workUrl"></InputItem>
            </div>
            <div class="add__inputs-section">
                <InputItem class="input-item--full" ref="pagesCountInput" numberonly label="Количество страниц" placeholder="Количество страниц" id="work-pages-count"
                    name="pages_count" :defaultValue="workData.pages_count" v-model="pagesCount"></InputItem>
            </div>
            <div class="add__inputs-section">
                <div class="input-item">
                    <span class="input-item__label">
                        Тег
                    </span>
                    <MySelect :values="tags" v-model="tagValue" ref="tagSelect"></MySelect>
                </div>
            </div>
            <div class="add__inputs-section">
                <MyFilter class="modal" ref="filter" :body="filterBody" v-model="checkedValues"></MyFilter>
            </div>
            <div class="add__load-media">
                <div class="add__load-media-item">
                    <LoadImage title="Десктоп версия" :placeholderData="workData.image_desktop" subfolderName="works"
                        v-model="imageDesktopId"></LoadImage>
                </div>
                <div class="add__load-media-item add__load-media-item--mobile">
                    <LoadImage title="Мобильная версия" :placeholderData="workData.image_mobile" subfolderName="works"
                        v-model="imageMobileId"></LoadImage>
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
import MyFilter from '@/components/global/MyFilter.vue';
import LoadImage from '@/components/private/LoadImage.vue';
import axios from 'axios';
import { useMyStore } from '@/stores/store.js';
import { mapState } from 'pinia';

export default {
    name: 'MyWork',
    components: {
        MyFilter,
        LoadImage
    },
    async beforeRouteEnter(to, from, next) {
        const store = useMyStore();
        store.isLoading = true;

        let workData = {};

        const workId = to.params.id;
        // если это страница добавления работы, просто закончить инициализацию
        if (!workId) {
            next(callback);
            return;
        }

        // если есть id, загрузить работу по этому id
        const url = `${import.meta.env.VITE_API_LINK}work/${workId}`;
        try {
            const res = await axios.get(url);
            if (res.data.not_found) {
                store.isLoading = false;
                next(self => self.$router.push({ name: 'AddWork' }));
                return;
            } else {
                workData = res.data;
            }
        } catch (err) { }

        // в callback загруженная работа отправится в this.workData (this === self)
        next(callback);

        function callback(self) {
            self.nullifyData();
            self.workData = workData;
            self.setWorkData();
            store.isLoading = false;
        }
    },
    beforeRouteLeave() {
        this.nullifyData();
    },
    data() {
        return {
            workData: {},
            workTitle: '',
            workUrl: '',
            pagesCount: '',
            tagValue: '',
            imageDesktopId: null,
            imageMobileId: null,
            checkedValues: []
        }
    },
    computed: {
        ...mapState(useMyStore, ['taxonomies', 'worksFilterBody']),
        filterBody() {
            // чтобы не затрагивать исходный массив, делается клон массива и его содержимого. Объекты в массиве при этом также клонируются
            const arr = this.worksFilterBody.map(obj => Object.assign({}, obj));
            return arr.map(obj => {
                if (obj.name === 'skills') {
                    obj.allowComment = true;
                    obj.allowUrl = true;
                }
                return obj;
            });
        },
        tags() {
            if (!this.taxonomies.tags)
                return [];

            return this.taxonomies.tags.map(obj => obj.title);
        },
    },
    methods: {
        // загрузить все данные
        nullifyData() {
            this.workData = {};
            this.workUrl = '';
            this.tagValue = ''
            this.workTitle = '';
            this.tagValue = '';
            this.imageDesktopId = null;
            this.imageMobileId = null;

            const refreshableRefs = ['titleInput', 'urlInput', 'filter', 'tagSelect'];
            refreshableRefs.forEach(str => {
                if (this.$refs[str] && typeof this.$refs[str].refresh === 'function')
                    this.$refs[str].refresh();
            });
        },
        setWorkData() {
            if (this.workData.tag) {
                this.$refs.tagSelect.setValue(this.workData.tag);
            }

            const filterValues = ['skills', 'stack', 'types'];
            filterValues.forEach(val => {
                if (Array.isArray(this.workData[val])) {
                    this.workData[val].forEach(obj => {
                        if (!obj)
                            return;

                        this.$refs.filter.setChecked(obj.title, {
                            comment: obj.description,
                            url: obj.url
                        });
                    });
                }
            });
        },
        // ================ загрузить все данные - конец

        // rest api
        getDataToUpload() {
            return {
                title: this.workTitle,
                url: this.workUrl,
                checkedValues: this.checkedValues,
                tag: this.tagValue,
                pages_count: this.pagesCount,
                image_desktop_id: this.imageDesktopId,
                image_mobile_id: this.imageMobileId
            }
        },
        async update() {
            const id = this.workData.id;
            if (!id)
                return;

            const store = useMyStore();
            store.isLoading = true;

            try {
                const url = `${import.meta.env.VITE_API_LINK}work/${id}`;
                const data = this.getDataToUpload();
                const res = await axios.post(url, data);
                if (res.data.id) {
                    this.workData = res.data;
                }
            } catch (err) { }

            store.isLoading = false;
            this.afterAnyAction();
        },
        async remove() {
            const url = `${import.meta.env.VITE_API_LINK}work/${this.workData.id}`;
            const store = useMyStore();
            store.isLoading = true;
            try {
                const res = await axios.delete(url);
                if (!res.data.error) {
                    this.$router.push({ name: 'AddWork' });
                }
            } catch (err) { }

            store.isLoading = false;
            this.afterAnyAction();
        },
        async load() {
            const store = useMyStore();
            const data = this.getDataToUpload();

            store.isLoading = true;
            const url = `${import.meta.env.VITE_API_LINK}work`;
            try {
                const res = await axios.post(url, data);
                if (res.data.id) {
                    this.$router.push({ name: 'EditWork', params: { id: res.data.id } });
                }
            } catch (err) { 
                console.log(err);
            }

            store.isLoading = false;
            this.afterAnyAction();
        },
        afterAnyAction() {
            const store = useMyStore();
            store.loadAllData();
        }
        //================ rest api - конец
    },
}
</script>