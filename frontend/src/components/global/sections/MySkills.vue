<template>
    <section id="skills">
        <div class="container">
            <h3 class="section-title">
                <MyTypeText text="Навыки: что я умею" speed="75"></MyTypeText>
            </h3>
            <div class="skills">
                <div class="skills__search">
                    <MySearch inputId="skills-search" name="search"></MySearch>
                </div>
                <div class="skills__list-container">
                    <ul v-if="items.length > 0" class="skills__list">
                        <SkillItem></SkillItem>
                        <SkillItem></SkillItem>
                        <SkillItem></SkillItem>
                        <SkillItem></SkillItem>
                    </ul>
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
                    <button class="button list-more" type="button">
                        Показать еще
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import MySearch from '../MySearch.vue';
import SkillItem from '../SkillItem.vue';
import emptyIcon from '@/assets/images/icons/cricket.svg';

export default {
    name: 'MySkills',
    components: {
        MySearch,
        SkillItem
    },
    data(){
        return {
            emptyIcon,
            items: ['']
        }
    }
}
</script>

<style></style>