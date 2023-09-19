<template>
    <section id="add-type">
        <h3 class="section-title">
            {{ $route.meta.title }}
        </h3>
        <div class="add edit-taxonomy">
            <div class="edit-taxonomy__search-container">
                <MySearch ref="searchInput" inputId="search-type" placeholder="Поиск" name="search-type"></MySearch>
            </div>
            <div class="edit-taxonomy__new-input">
                <TextInput ref="addTaxInput" v-model="newTaxValue" :placeholder="$route.meta.titleNew"
                    inputId="new_taxonomy" :name="`new_${$route.meta.taxonomyTitle}`" @enterKeyup="addTax"></TextInput>
                <button class="button" type="button" @click="addTax">
                    {{ $route.meta.titleNew }}
                </button>
            </div>
            <ul class="edit-taxonomy__list">
                <TaxonomyItem v-for="obj in taxonomies" :key="obj.id" :tax="obj.title" @changeTax="onChangeTax"
                    @removeTax="onRemoveTax">
                </TaxonomyItem>
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
import { useMyStore } from '@/stores/store.js';

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
            newTaxValue: '',
            taxonomies: []
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
            const res = await axios.post(url, data);

            store.isLoading = false;
            if (res.data.error) {
                return;
            }

            this.$refs.addTaxInput.value = '';
            this.loadTaxonomies();
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
            await axios.post(url, { taxonomies });
            this.loadTaxonomies();

            store.isLoading = false;
        },
        async onRemoveTax(value) {
            const obj = this.taxonomies.find(o => o.title === value);
            if (!obj)
                return;

            const url = `${import.meta.env.VITE_API_LINK}taxonomy/${this.taxonomyTitle}/${obj.id}`;
            await axios.delete(url);
            this.loadTaxonomies();
        }
    },
    computed: {
        taxonomyTitle() {
            return this.$route.meta.taxonomyTitle;
        }
    },
}
</script>

<style></style>