<template>
    <li class="portfolio-item" :class="{ '__shown-skills': isShownSkills }">
        <div class="portfolio-item__buttons">
            <button v-if="data.image_desktop" class="portfolio-item__button icon-desktop"
                :class="{ '__active': imageType === 'desktop' }" type="button" @click="changeImage('desktop')"></button>
            <button class="portfolio-item__button icon-question-mark" :class="{ '__active': isShownSkills }" type="button"
                @click="toggleSkills"></button>
            <button v-if="data.image_mobile" class="portfolio-item__button icon-mobile"
                :class="{ '__active': imageType === 'mobile' }" type="button" @click="changeImage('mobile')"></button>
            <RouterLink v-if="isMe" class="portfolio-item__button icon-pencil"
                :to="{ name: 'EditWork', params: { id: data.id } }" type="button"></RouterLink>
        </div>
        <div class="portfolio-item__container" ref="itemContainer">
            <div class="portfolio-item__folder-back" :style="{ backgroundImage: folderBackgroundBack }">
            </div>
            <div class="portfolio-item__skills">
                <ol class="portfolio-item__skills-list" ref="skillsList"></ol>
                <div v-if="skillsPages.length > 0" class="portfolio-item__skills-pagination">
                    <button class="portfolio-item__skills-prev icon-chevron-right" type="button"
                        @click="turnOverSkillsPage('prev')"></button>
                    <span>|</span>
                    <button class="portfolio-item__skills-next icon-chevron-right" type="button"
                        @click="turnOverSkillsPage('next')"></button>
                </div>
            </div>
            <div class="portfolio-item__image" ref="imageContainer">
                <img :src="imageUrl" alt="" ref="image">
            </div>
            <div class="portfolio-item__folder-front" :style="{ backgroundImage: folderBackgroundFront }">
            </div>
        </div>
        <div class="portfolio-item__text">
            <a :href="data.url">
                <span class="portfolio-item__title">
                    {{ data.tag }} {{ data.title }}
                </span>
                <span class="portfolio-item__stack">
                    Стек:
                    {{ stackList }}
                </span>
            </a>
        </div>
    </li>
</template>

<script>
import { useMyStore } from '@/stores/store.js';
import { mapState } from 'pinia';
import { onTransitionEnd, createElement, doInFade } from '@/assets/scripts/scripts.js';

import folderBackUrl from '@/assets/images/portfolio-item/folder-back.png';
import folderFrontUrl from '@/assets/images/portfolio-item/folder-front.png';
import folderBackDarkUrl from '@/assets/images/portfolio-item/folder-dark-back.png';
import folderFrontDarkUrl from '@/assets/images/portfolio-item/folder-dark-front.png';

export default {
    name: 'PortfolioItem',
    props: {
        data: {
            type: Object,
            required: true
        }
    },
    mounted() {
        const store = useMyStore();
        this.theme = store.theme;
        store.$subscribe((m, state) => this.theme = state.theme);

        this.sliceSkillsPages();
        window.addEventListener("resize", () => {
            // таким образом метод будет вызван спустя некоторое время после того, как пользователь изменит размер экрана, иначе он будет вызываться очень много раз
            if (this.sliceSkillsTimeout)
                clearInterval(this.sliceSkillsTimeout);
            this.sliceSkillsTimeout = setTimeout(this.sliceSkillsPages, 500);
        });
    },
    data() {
        return {
            theme: '',
            imageType: 'desktop',
            isShownSkills: false,
            skillsPages: [],
            skillsPageNumber: 1
        }
    },
    methods: {
        async toggleSkills() {
            if (this.isTogglingSkills) return;

            this.isTogglingSkills = true;
            const img = this.$refs.image;

            img.style.cssText = `
                transition: all .2s;
                transform: translateX(${img.offsetWidth}px);
            `;
            await onTransitionEnd(img);

            // показать изображение, спрятать навыки
            if (this.isShownSkills)
                this.isShownSkills = false;
            // показать навыки, спрятать изображение
            else
                this.isShownSkills = true;

            img.style.removeProperty('transform');
            await onTransitionEnd(img, () => img.style.removeProperty('transition'));
            this.isTogglingSkills = false;
        },
        async changeImage(imageType) {
            if (this.isChangingImage || this.imageType === imageType)
                return;

            const self = this;
            const imgContainer = this.$refs.imageContainer;
            imgContainer.classList.add("shrink");
            this.isChangingImage = true;
            this.$refs.image.addEventListener("load", onLoadSrc);

            switch (imageType) {
                default:
                case 'desktop':
                    await onTransitionEnd(imgContainer);
                    this.imageType = imageType;
                    break;
                case 'mobile':
                    await onTransitionEnd(imgContainer);
                    this.imageType = imageType;
                    break;
            }

            function onLoadSrc() {
                self.$refs.image.removeEventListener("load", onLoadSrc);
                imgContainer.classList.add("unshrink");
                imgContainer.classList.remove("shrink");
                onTransitionEnd(imgContainer, () => imgContainer.classList.remove("unshrink"));
                self.isChangingImage = false;
            }
        },
        sliceSkillsPages() {
            const self = this;
            if (!this.$refs.skillsList) return;

            const skillsListClone = this.$refs.skillsList.cloneNode();
            const origStyles = getComputedStyle(skillsListClone);
            const origWidth = this.$refs.skillsList.offsetWidth < 100
                ? 250 : this.$refs.skillsList.offsetWidth;
            skillsListClone.style.cssText = `
                width: ${origWidth}px;
                position: absolute;
                top: 0;
                left: 0;
                opacity: 0;
                z-index: -999;
                font-size: ${origStyles.fontSize};
            `;
            document.body.append(skillsListClone);

            let i = 0;
            let skillItem = this.skillsList[i];
            this.skillsPages = [];

            const coef = window.matchMedia('(max-width: 479px)').matches
                ? 2.5 : 2;
            const maxHeight = this.$refs.itemContainer.offsetHeight / coef;
            createSkillPage();
            this.setSkillsPage();
            skillsListClone.remove();

            function createSkillPage() {
                let array = [];
                while (skillItem && skillsListClone.offsetHeight < maxHeight) {
                    const li = createElement('li', 'portfolio-item__skill-item', `${i + 1}. ${skillItem}`);
                    skillsListClone.append(li);
                    array.push(li);
                    i++;
                    skillItem = self.skillsList[i];
                }
                if (skillsListClone.offsetHeight > maxHeight) {
                    const lastItem = skillsListClone.querySelector(".portfolio-item__skill-item:last-child");
                    if (!lastItem)
                        return;

                    lastItem.remove();
                    array = array.slice(0, array.length - 1);
                    i--;
                    skillItem = self.skillsList[i];
                }
                self.skillsPages.push(array);
                skillsListClone.innerHTML = "";

                if (skillItem)
                    createSkillPage();
            }
        },
        setSkillsPage(pageNumber = 1) {
            const skillsList = this.$refs.skillsList;
            let page = this.skillsPages[pageNumber - 1];
            if (!page)
                page = this.skillsPages[this.skillsPages.length - 1];

            this.skillsPageNumber = this.skillsPages.findIndex(arr => arr[0] === page[0]) + 1;

            doInFade(skillsList, () => {
                skillsList.innerHTML = "";
                page.forEach(li => skillsList.append(li));
                skillsList.style.opacity = "1";
            });
        },
        turnOverSkillsPage(action) {
            switch (action) {
                case 'prev':
                    if (this.skillsPageNumber <= 1)
                        return;
                    this.skillsPageNumber--;
                    break;
                case 'next':
                    if (this.skillsPageNumber >= this.skillsPages.length)
                        return;
                    this.skillsPageNumber++;
                    break;
            }
            this.setSkillsPage(this.skillsPageNumber);
        }
    },
    computed: {
        ...mapState(useMyStore, ['isMe']),
        folderBackgroundBack() {
            if (this.theme === 'light')
                return `url(${folderBackUrl})`;
            if (this.theme === 'dark')
                return `url(${folderBackDarkUrl})`;
        },
        folderBackgroundFront() {
            if (this.theme === 'light')
                return `url(${folderFrontUrl})`;
            if (this.theme === 'dark')
                return `url(${folderFrontDarkUrl})`;
        },
        skillsList() {
            if (!this.data.skills)
                return [];

            return this.data.skills.map(obj => {
                const descr = obj.description ? ` (${obj.description.toLowerCase()})` : '';
                return `${obj.title}${descr}`;
            });
        },
        stackList() {
            if (!this.data.stack)
                return '';

            const arr = [];

            this.data.stack.forEach(obj => arr.push(obj.title));
            return arr.join(', ');
        },
        imageUrl() {
            if (this.imageType === 'desktop' && this.data.image_desktop)
                return `${import.meta.env.VITE_BACKEND_LINK}${this.data.image_desktop.path}`;
            if (this.imageType === 'mobile' && this.data.image_mobile)
                return `${import.meta.env.VITE_BACKEND_LINK}${this.data.image_mobile.path}`;

            return null;
        }
    }
}
</script>