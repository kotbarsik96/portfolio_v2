<template>
    <div class="filter" :class="{ '__shown': isBodyShown }">
        <button class="filter__tab-button icon-filter" type="button" @click="toggleBody">
            <h4 class="filter__title">
                Фильтр
            </h4>
        </button>
        <div class="filter__body" ref="filterBody">
            <div v-for="section in sections" :key="section.title" class="filter__section">
                <h5 class="filter__section-title">
                    {{ section.title }}
                </h5>
                <ul class="filter__section-items">
                    <li v-for="value in section.values" :key="value" class="filter__section-item">
                        <label class="filter__section-label checkbox-label">
                            <input type="checkbox" :name="section.name" :value="value">
                            <span class="checkbox-label__box"></span>
                            <span class="checkbox-label__text">
                                {{ value }}
                            </span>
                        </label>
                        <span v-if="allowComment" class="filter__section-item-comment editable" ref="comment" @blur="onCommentBlur"
                            @input="() => toggleBody(true)"></span>
                        <button v-if="allowComment" class="filter__section-item-write-button icon-pencil" type="button"
                            @click="writeComment"></button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import { getHeight, onTransitionEnd, setEditable } from '@/assets/scripts/scripts.js';

export default {
    name: 'MyFilter',
    props: {
        allowComment: Boolean
    },
    mounted() {
        const self = this;
        const media = window.matchMedia('(max-width: 1279px)');
        media.addEventListener("change", onMediaChange);
        onMediaChange();

        function onMediaChange() {
            if (media.matches) {
                self.toggleBody(false);
            } else {
                self.toggleBody(true);
            }
        }
    },
    data() {
        return {
            isBodyShown: false,
            sections: [
                {
                    name: 'type',
                    title: 'Тип',
                    values: [
                        'Посадка на Wordpress',
                        'Вёрстка по макету'
                    ]
                },
                {
                    name: 'skills',
                    title: 'Навыки',
                    values: [
                        'Слайдеры',
                        'Спойлеры',
                        'Анимации при прокрутке страницы и последующем текст екст текст текст'
                    ]
                }
            ]
        }
    },
    methods: {
        async toggleBody(bool = null) {
            if (typeof bool !== 'boolean')
                this.isBodyShown = !this.isBodyShown;
            else this.isBodyShown = bool;

            const fbody = this.$refs.filterBody;
            if (this.isBodyShown) {
                const height = getHeight(fbody);
                fbody.style.removeProperty('padding');
                fbody.style.removeProperty('margin');
                fbody.style.cssText = `
                    transition: all .3s;
                    max-height: ${height + 10}px;
                `;
                await onTransitionEnd(fbody);
            } else {
                fbody.style.cssText = `
                    transition: all .3s;
                    padding: 0;
                    margin: 0;
                    max-height: 0px;
                `;
            }
            await onTransitionEnd(fbody);
            fbody.style.removeProperty('transition');
        },
        writeComment(event) {
            const comment = this.$refs.comment.find(node => node.closest('li') === event.target.closest('li'));
            setEditable(comment);
        },
        onCommentBlur(event) {
            event.target.removeAttribute('contenteditable');
        },
    },
}
</script>

<style></style>