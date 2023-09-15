<template>
    <section id="about">
        <div class="container">
            <h2 class="section-title">
                <MyTypeText text="О себе и о сайте" speed="75"></MyTypeText>
            </h2>
            <div class="about">
                <div class="about__images">
                    <div class="about__image-container">
                        <img :src="avatar" alt="Изображение" class="about__image">
                    </div>
                </div>
                <div class="about__text">
                    <ul class="list">
                        <li class="list__item icon-person">
                            <span class="list__item-title">
                                Имя:
                            </span>
                            <MyTypeText class="list__item-content" text="Лёша" queue></MyTypeText>
                        </li>
                        <li class="list__item icon-activity">
                            <span class="list__item-title">
                                Деятельность:
                            </span>
                            <MyTypeText class="list__item-content" text="frontend-разработка" queue></MyTypeText>
                        </li>
                        <li class="list__item icon-programming">
                            <span class="list__item-title">
                                Стек:
                            </span>
                            <span class="list__item-content">
                                <MyTypeText text="HTML/CSS, чистый Javascript, Vue JS 3, Gulp, Vite, PHP, Wordpress" queue></MyTypeText>
                            </span>
                        </li>
                        <li class="list__item icon-portfolio">
                            <a class="link list__item-title" href="#portfolio">
                                Работ в портфолио:
                            </a>
                            <MyTypeText class="list__item-content" text="10" queue></MyTypeText>
                        </li>
                        <li class="list__item icon-web-development">
                            <a class="link list__item-title" href="#skills">
                                Навыков в портфолио:
                            </a>
                            <MyTypeText class="list__item-content" text="15" queue></MyTypeText>
                        </li>
                    </ul>
                    <p class="about__text-par">
                        На этом сайте я собрал свои работы, которые могу опубликовать. Так я чётко могу увидеть свой прогресс и продемонстрировать его тем, кто заинтересован в работе со мной
                    </p>
                    <p class="about__text-par">
                        Я работаю и с чистым JS, и с фреймворком Vue JS
                    </p>
                    <p class="about__text-par" queue>
                        Тут я собрал достаточно навыков, которые могу продемонстрировать в
                        <a class="link" href="#skills">
                            своём списке
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import avatar from '@/assets/images/about/avatar.png';
import MyTypeText from '../MyTypeText.vue';

export default {
    name: 'AboutMe',
    data() {
        return {
            avatar
        };
    },
    components: { MyTypeText }
}
</script>

<style></style>