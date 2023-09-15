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
                        <ul v-else-if="works.length > 0" class="portfolio__list">
                            <PortfolioItem v-for="work in works" :key="work.id" :data="work"></PortfolioItem>
                        </ul>
                        <div v-else class="empty">
                            <img :src="emptyIcon" alt="" class="empty__icon">
                            <div class="empty__text">
                                <p>
                                    К сожалению, ничего не найдено
                                </p>
                                <p>
                                    Попробуйте выбрать другие фильтры
                                </p>
                            </div>
                        </div>
                    </Transition>
                    <Transition name="fade-in">
                        <button v-if="works && works.length > 0" class="button list-more" type="button">
                            Показать еще
                        </button>
                    </Transition>
                </div>
                <MyFilter class="portfolio__filter modal"></MyFilter>
            </div>
        </div>
    </section>
</template>

<script>
import PortfolioItem from '../PortfolioItem.vue';
import MyFilter from '../MyFilter.vue';
import emptyIcon from '@/assets/images/icons/cricket.svg';

export default {
    name: 'MyPortfolio',
    components: {
        PortfolioItem,
        MyFilter
    },
    async mounted() {
        
    },
    data() {
        return {
            works: null,
            emptyIcon
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