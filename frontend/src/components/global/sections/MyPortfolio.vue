<template>
    <section id="portfolio">
        <div class="container">
            <h1 class="section-title">
                <MyTypeText text="Портфолио: мои работы" speed="75"></MyTypeText>
            </h1>
            <div class="portfolio">
                <div class="portfolio__list-container">
                    <Transition name="fade-in" mode="out-in">
                        <MyLoading v-if="!works"></MyLoading>
                        <TransitionGroup v-else-if="works.length > 0" tag="ul" class="portfolio__list"
                            name="portfolio-item">
                            <PortfolioItem v-for="work in works" :key="work.id" :data="work"></PortfolioItem>
                        </TransitionGroup>
                        <div v-else class="empty">
                            <img :src="emptyIcon" alt="" class="empty__icon">
                            <div class="empty__text">
                                <p>
                                    К сожалению, ничего не найдено
                                </p>
                                <p>
                                    Возможно, стоит выбрать другие фильтры
                                </p>
                            </div>
                        </div>
                    </Transition>
                    <Transition name="fade-in">
                        <button v-if="showMoreButton" class="button list-more" type="button" @click="loadMoreWorks">
                            Показать еще
                        </button>
                    </Transition>
                </div>
                <MyFilter class="portfolio__filter modal" isHideable :body="worksFilterBody" v-model="checkedFilters">
                </MyFilter>
            </div>
        </div>
    </section>
</template>

<script>
import PortfolioItem from '../PortfolioItem.vue';
import MyFilter from '../MyFilter.vue';
import emptyIcon from '@/assets/images/icons/cricket.svg';
import axios from 'axios';
import { useMyStore } from '@/stores/store';
import { mapState } from 'pinia';

const loadPerQuery = 2;
const firstLoadedCount = 4;

export default {
    name: 'MyPortfolio',
    components: {
        PortfolioItem,
        MyFilter
    },
    created() {
        this.loadWorks();
        this.loadWorksCount();
    },
    data() {
        return {
            works: null,
            emptyIcon,
            worksCount: 0,
            checkedFilters: [],
            loadPerQuery,
            loadedWorksCount: firstLoadedCount,
            offset: firstLoadedCount,
            isLoadingWorks: false,
        }
    },
    computed: {
        ...mapState(useMyStore, ['worksFilterBody']),
        showMoreButton() {
            return this.works
                && this.works.length > 0
                && this.worksCount > this.works.length
                && !this.isLoadingWorks;
        },
    },
    methods: {
        async loadWorksCount() {
            try {
                const res = await axios(`${import.meta.env.VITE_API_LINK}works/count`);
                this.worksCount = parseInt(res.data);
            } catch (err) { }
        },
        getFilters() {
            const data = {};
            this.checkedFilters.forEach(obj => {
                data[obj.name] = obj.values.map(o => o.value);
            });
            return data;
        },
        // первичная загрузка работ и загрузка работ после применения фильтра. Увеличивает loadedWorksCount  только при первой загрузке
        async loadWorks() {
            this.isLoadingWorks = true;

            const url = `${import.meta.env.VITE_API_LINK}works/filter`;
            const params = Object.assign(this.getFilters(), {
                limit: this.loadedWorksCount
            });

            try {
                const res = await axios(url, { params });
                if (Array.isArray(res.data)) {
                    this.works = res.data;
                } else {
                    this.works = [];
                }
            } catch (err) { }

            this.isLoadingWorks = false;
            if (this.loadedWorksCount < this.works.length)
                this.loadedWorksCount = this.works.length;
        },
        // загрузка работ после нажатия кнопки "Показать еще". Сдвигает offset и увеличивает loadedWorksCount
        async loadMoreWorks() {
            this.isLoadingWorks = true;

            let newWorks = [];
            const url = `${import.meta.env.VITE_API_LINK}works/filter`;
            const params = Object.assign(this.getFilters(), {
                limit: this.loadPerQuery,
                offset: this.offset
            });

            try {
                const res = await axios(url, { params });
                if (Array.isArray(res.data)) {
                    newWorks = res.data;
                    this.works = this.works.concat(newWorks);
                }
            } catch (err) { }

            this.offset += newWorks.length;
            this.loadedWorksCount += newWorks.length;
            this.isLoadingWorks = false;
        }
    },
    watch: {
        checkedFilters: {
            deep: true,
            handler() {
                this.offset = 0;
                this.loadWorks();
            }
        }
    }
}
</script>

<style>
.fade-in-enter-active,
.fade-in-leave-active {
    transition: all .2s;
}

.fade-in-enter-from,
.fade-in-leave-to {
    opacity: 0;
}

.fade-in-enter-to,
.fade-in-leave-from {
    opacity: 1;
}
</style>