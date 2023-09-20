<template>
    <section id="add-work">
        <h3 class="section-title">
            {{ $route.meta.isEditing ? 'Редактирование' : 'Добавление' }}
            работы
        </h3>
        <div class="add">
            <div class="add__inputs-section">
                <InputItem class="input-item--full" label="Название" placeholder="Название" id="work-title"
                    name="work_title" :defaultValue="workData.title" v-model="workTitle"></InputItem>
            </div>
            <div class="add__inputs-section">
                <div class="input-item">
                    <span class="input-item__label">
                        Тег
                    </span>
                    <MySelect :values="tags" v-model="tagValue"></MySelect>
                </div>
            </div>
            <div class="add__inputs-section">
                <MyFilter class="modal" :body="filterBody" v-model="checkedValues"></MyFilter>
            </div>
            <div class="add__load-media">
                <div class="add__load-media-item">
                    <LoadImage title="Десктоп версия" subfolderName="works" v-model="imageDesktopId"></LoadImage>
                </div>
                <div class="add__load-media-item add__load-media-item--mobile">
                    <LoadImage title="Мобильная версия" subfolderName="works" v-model="imageMobileId"></LoadImage>
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

export default {
    name: 'MyWork',
    components: {
        MyFilter,
        LoadImage
    },
    async mounted() {
    },
    created() {
        this.loadSettingsData();
    },
    data() {
        return {
            workData: {},
            workTitle: '',
            tagValue: '',
            taxonomies: {},
            skills: [],
            imageDesktopId: null,
            imageMobileId: null,
            checkedValues: []
        }
    },
    computed: {
        tags() {
            if (!this.taxonomies.tag)
                return [];

            return this.taxonomies.tag.map(obj => obj.title);
        },
        types() {
            if (!this.taxonomies.type)
                return [];

            return this.taxonomies.type.map(obj => obj.title);
        },
        stack() {
            if (!this.taxonomies.stack)
                return [];

            return this.taxonomies.stack.map(obj => obj.title);
        },
        filterBody() {
            const array = [];

            array.push({
                name: 'types',
                title: 'Типы',
                values: this.types
            }, {
                name: 'skills',
                title: 'Навыки',
                values: this.skills.map(obj => {
                    return obj.title;
                }),
                allowComment: true
            });

            return array;
        }
    },
    methods: {
        loadSettingsData() {
            this.loadTaxonomies();
            this.loadSkills();
        },
        loadTaxonomies() {
            const taxonomiesTitles = ['tag', 'type', 'stack'];
            taxonomiesTitles.forEach(async (taxonomyTitle) => {
                const url = `${import.meta.env.VITE_API_LINK}taxonomies/${taxonomyTitle}`;
                const res = await axios.get(url);

                if (res.data && !res.data.error)
                    this.taxonomies[taxonomyTitle] = res.data;
            });
        },
        async loadSkills() {
            const url = `${import.meta.env.VITE_API_LINK}skills`;
            const res = await axios.get(url);

            if (res.data && !res.data.error)
                this.skills = res.data;
        },
        update() { },
        remove() { },
        async load() {
            const store = useMyStore();
            const data = {
                title: this.workTitle,
                checkedValues: this.checkedValues,
                tag: this.tagValue,
                image_desktop_id: this.imageDesktopId,
                image_mobile_id: this.imageMobileId
            }

            store.isLoading = true;
            const url = `${import.meta.env.VITE_API_LINK}work`;
            try {
                const res = await axios.post(url, data);
                if(res.data.id) {
                    this.$router.push({ name: 'EditWork', params: { id: res.data.id } });
                }
            } catch (err) { }

            store.isLoading = false;
        },
    },
}
</script>

<style></style>