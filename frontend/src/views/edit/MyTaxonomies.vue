<template>
    <section id="add-type">
        <h3 class="section-title">
            Добавление 
            {{ $route.meta.taxonomyTitleGenitive }}
        </h3>
        <div class="add edit-taxonomy">
            <div class="edit-taxonomy__search-container">
                <MySearch inputId="search-type" placeholder="Поиск" name="search-type"></MySearch>
            </div>
            <ul class="edit-taxonomy__list">
                <TaxonomyItem v-for="obj in taxonomiesKeyed" :key="obj.key" :tax="obj.value" @change-tax="onChangeTax"
                    @remove-tax="onRemoveTax">
                </TaxonomyItem>
            </ul>
            <div class="add__buttons">
                <button class="button button--color_2" type="button">
                    Сохранить
                </button>
            </div>
        </div>
    </section>
</template>

<script>
import MySearch from '@/components/global/MySearch.vue';
import TaxonomyItem from '@/components/private/TaxonomyItem.vue';

export default {
    name: 'MyTypes',
    components: {
        MySearch,
        TaxonomyItem
    },
    created() {
        this.taxonomies = [
            'Вёрстка по макету',
            'Посадка на Wordpress'
        ];
        this.taxonomiesKeyed = this.taxonomies.map(value => {
            return { value, key: value }
        });

        document.addEventListener('keyup', this.onDocumentKeyup);
    },
    data() {
        return {
            taxonomies: [],
            taxonomiesKeyed: [],
            taxonomiesKeyedStepBack: []
        }
    },
    methods: {
        onDocumentKeyup(event) {
            // Ctrl + Z
            if (event.code === 'KeyZ' && event.ctrlKey) {
                this.taxonomiesKeyed = this.taxonomiesKeyedStepBack;
            }
        },
        onChangeTax(value, oldValue) {
            this.taxonomiesKeyed = this.taxonomiesKeyed.map(obj => {
                if (obj.value !== oldValue)
                    return obj;

                obj.value = value;
                return obj;
            });
        },
        onRemoveTax(value) {
            this.taxonomiesKeyed = this.taxonomiesKeyed.filter(obj => obj.value !== value);
        }
    },
    watch: {
        taxonomiesKeyed(newArr, oldArr) {
            this.taxonomies = newArr.map(obj => obj.value);
            this.taxonomiesKeyedStepBack = oldArr;
        }
    }
}
</script>

<style></style>