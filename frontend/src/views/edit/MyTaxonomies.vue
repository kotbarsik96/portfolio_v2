<template>
    <section id="add-type">
        <h3 class="section-title">
            {{ $route.meta.title }}
        </h3>
        <div class="add edit-taxonomy">
            <div class="edit-taxonomy__search-container">
                <MySearch ref="searchInput" inputId="search-type" placeholder="Поиск" name="search-type"
                    v-model="searchValue"></MySearch>
            </div>
            <div class="edit-taxonomy__new-input">
                <TextInput ref="addTaxInput" v-model="newTaxValue" :placeholder="$route.meta.titleNew"
                    inputId="new_taxonomy" :name="`new_${$route.meta.taxonomyTitle}`" @enterKeyup="addTax"></TextInput>
                <button class="button" type="button" @click="addTax">
                    {{ $route.meta.titleNew }}
                </button>
            </div>
            <ul class="edit-taxonomy__list">
                <TransitionGroup :css="false" @before-enter="onBeforeEnter" @enter="onEnter" @leave="onLeave">
                    <TaxonomyItem v-for="(obj, index) in taxonomiesSearch" :key="obj.id" :tax="obj.title"
                        :data-index="index" @changeTax="onChangeTax" @removeTax="onRemoveTax">
                    </TaxonomyItem>
                </TransitionGroup>
            </ul>
            <div class="add__buttons">
                <button class="button button--color_2" type="button" @click="saveTaxes">
                    Сохранить
                </button>
            </div>
        </div>
    </section>
</template>

<script>
import MySearch from '@/components/global/MySearch.vue';
import TaxonomyItem from '@/components/private/TaxonomyItem.vue';
import axios from 'axios';
import gsap from 'gsap';
import { useMyStore } from '@/stores/store.js';
import { getHeight } from '@/assets/scripts/scripts.js';

export default {
    name: 'MyTypes',
    components: {
        MySearch,
        TaxonomyItem
    },
    beforeRouteEnter(to, from, next) {
        next(self => {
            self.loadTaxonomies();
        });
    },
    beforeRouteLeave() {
        this.taxonomies = [];
        this.newTaxValue = '';
    },
    data() {
        return {
            searchValue: '',
            newTaxValue: '',
            taxonomies: [],
            taxonomiesSearch: []
        }
    },
    methods: {
        async loadTaxonomies() {
            const store = useMyStore();
            store.isLoading = true;

            const url = `${import.meta.env.VITE_API_LINK}taxonomies/${this.taxonomyTitle}`;
            const res = await axios.get(url);

            store.isLoading = false;

            if (res.data && !res.data.error) {
                // оставить те, у которых не стоит пометка 'modified'
                const newTaxonomies = res.data.filter(newObj => {
                    const currentObj = this.taxonomies.find(o => o.id === newObj.id);
                    if (!currentObj)
                        return true;

                    return !currentObj.modified;
                });
                // отфильтровать немодифицированные и соединить с пришедшими newTaxonomies
                this.taxonomies = this.taxonomies
                    .filter(currentObj => {
                        const isNotDeleted = res.data.find(o => o.id === currentObj.id);
                        return currentObj.modified && isNotDeleted;
                    })
                    .concat(newTaxonomies)
                    .sort((obj1, obj2) => {
                        if (obj1.id < obj2.id) return -1;
                        if (obj1.id < obj2.id) return 1;
                        return 0;
                    });
            }
        },
        async addTax() {
            const store = useMyStore();
            store.isLoading = true;

            const data = { title: this.newTaxValue };
            const url = `${import.meta.env.VITE_API_LINK}taxonomy/${this.taxonomyTitle}`;
            try {
                const res = await axios.post(url, data);

                store.isLoading = false;
                if (!res.data.error) {
                    this.$refs.addTaxInput.value = '';
                    this.loadTaxonomies();
                }
            } catch (err) { }

            this.afterAnyAction();
        },
        onChangeTax(value, oldValue, initialValue) {
            this.taxonomies = this.taxonomies.map(obj => {
                if (obj.title !== oldValue)
                    return obj;

                obj.title = value;

                if (value !== initialValue)
                    obj.modified = true;
                else
                    obj.modified = false;

                return obj;
            });
        },
        async saveTaxes() {
            const taxonomies = this.taxonomies.filter(obj => obj.modified);
            const store = useMyStore();
            store.isLoading = true;

            const url = `${import.meta.env.VITE_API_LINK}taxonomies/${this.taxonomyTitle}`;
            try {
                await axios.post(url, { taxonomies });
                this.loadTaxonomies();
            } catch (err) { }

            this.afterAnyAction();
            store.isLoading = false;
        },
        async onRemoveTax(value) {
            const obj = this.taxonomies.find(o => o.title === value);
            if (!obj)
                return;

            const url = `${import.meta.env.VITE_API_LINK}taxonomy/${this.taxonomyTitle}/${obj.id}`;
            try {
                await axios.delete(url);
                this.loadTaxonomies();
            } catch (err) { }

            this.afterAnyAction();
        },
        afterAnyAction() {
            const store = useMyStore();
            store.loadAllData();
        },
        doSearch() {
            const query = this.searchValue.trim();
            const regexp = new RegExp(query, 'i');

            if (!this.searchValue) {
                this.taxonomiesSearch = this.taxonomies;
                return;
            }

            this.taxonomiesSearch = this.taxonomies.filter(obj => {
                return obj.title.match(regexp);
            });
        },
        onBeforeEnter(el) {
            gsap.set(el, {
                opacity: 0,
                height: 0
            });
        },
        onEnter(el, onComplete) {
            const height = getHeight(el);
            gsap.to(el, {
                opacity: 1,
                height: `${height}px`,
                delay: el.dataset.index * 0.15,
                onComplete
            });
        },
        onLeave(el, onComplete) {
            gsap.to(el, {
                opacity: 0,
                height: 0,
                delay: el.dataset.index * 0.15,
                onComplete
            });
        }
    },
    computed: {
        taxonomyTitle() {
            return this.$route.meta.taxonomyTitle;
        }
    },
    watch: {
        searchValue() {
            this.doSearch();
        },
        taxonomies() {
            this.doSearch();
        }
    },
}
</script>