<template>
    <section id="skills">
        <div class="container">
            <h3 class="section-title">
                <MyTypeText text="Навыки: что я умею" speed="75"></MyTypeText>
            </h3>
            <div class="skills">
                <div class="skills__search">
                    <MySearch inputId="skills-search" placeholder="Поиск" name="search" v-model="searchValue"></MySearch>
                </div>
                <div class="skills__list-container">
                    <TransitionGroup v-if="items.length > 0" tag="ul" class="skills__list" name="skill-item">
                        <SkillItem v-for="item in items" :key="item.id" :data="item"></SkillItem>
                    </TransitionGroup>
                    <div v-else class="empty">
                        <img :src="emptyIcon" alt="" class="empty__icon">
                        <div class="empty__text">
                            <p>
                                К сожалению, ничего не найдено
                            </p>
                            <p>
                                Попробуйте поиск по другим словам
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div ref="intersection"></div>
    </section>
</template>

<script>
import MySearch from '../MySearch.vue';
import SkillItem from '../SkillItem.vue';
import emptyIcon from '@/assets/images/icons/cricket.svg';
import axios from 'axios';
import { mapState } from 'pinia';
import { useMyStore } from '@/stores/store.js';

const loadPerQuery = 2;
const initialSkillsCount = 4;


export default {
    name: 'MySkills',
    components: {
        MySearch,
        SkillItem
    },
    data() {
        return {
            emptyIcon,
            searchValue: '',
            intersectObserver: null,
            isLoadingItems: false,
            items: [],
            loadedCount: initialSkillsCount,
            loadPerQuery,
        }
    },
    created() {
        this.loadSkills();
    },
    mounted() {
        this.intersectObserver = new IntersectionObserver(this.onIntersect, {
            root: null,
            threshold: 0.5
        });
        this.intersectObserver.observe(this.$refs.intersection);
    },
    computed: {
        ...mapState(useMyStore, ['counts'])
    },
    methods: {
        async onIntersect(entries) {
            if (entries.find(e => e.isIntersecting)) {
                await this.loadMoreSkills();
                if (this.loadedCount >= this.counts.skills)
                    this.intersectObserver.disconnect();
            }
        },
        getFilters() {
            return {
                search: this.searchValue
            };
        },
        async loadSkills() {
            this.isLoadingItems = true;

            const url = `${import.meta.env.VITE_API_LINK}skills/filter`;
            const params = Object.assign(this.getFilters(), {
                limit: this.loadedCount
            });
            try {
                const res = await axios(url, { params });
                if (Array.isArray(res.data)) {
                    this.items = res.data;
                } else {
                    this.items = [];
                }
            } catch (err) { }

            if (this.loadedCount < this.items.length)
                this.loadedCount = this.items.length;

            this.isLoadingItems = false;
        },
        async loadMoreSkills() {
            this.isLoadingItems = true;

            const url = `${import.meta.env.VITE_API_LINK}skills/filter`;
            const params = Object.assign(this.getFilters(), {
                limit: this.loadPerQuery,
                offset: this.loadedCount
            });

            try {
                const res = await axios(url, { params });
                if (Array.isArray(res.data)) {
                    this.items = this.items.concat(res.data);
                    this.loadedCount += res.data.length;
                }
            } catch (err) { }

            this.isLoadingItems = false;
        }
    },
    watch: {
        searchValue() {
            this.loadSkills();
        }
    }
}
</script>