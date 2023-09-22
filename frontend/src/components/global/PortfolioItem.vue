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
                <div class="portfolio-item__skills-container">
                    <ol class="portfolio-item__skills-list" v-html="currentShownSkills" ref="skillsList"></ol>
                </div>
                <div v-if="slicedSkillsPages.length > 0" class="portfolio-item__skills-pagination">
                    <button class="portfolio-item__skills-prev icon-chevron-right" type="button" @click="skillsPageNumber--"
                        :disabled="isFirstSkillsPage"></button>
                    <span>|</span>
                    <button class="portfolio-item__skills-next icon-chevron-right" type="button" @click="skillsPageNumber++"
                        :disabled="isLastSkillsPage"></button>
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
import { onTransitionEnd } from '@/assets/scripts/scripts.js';

import folderBackUrl from '@/assets/images/portfolio-item/folder-back.png';
import folderFrontUrl from '@/assets/images/portfolio-item/folder-front.png';
import folderBackDarkUrl from '@/assets/images/portfolio-item/folder-dark-back.png';
import folderFrontDarkUrl from '@/assets/images/portfolio-item/folder-dark-front.png';

export default {
    name: 'PortfolioItem',
    props: {
        data: {
            type: Object,
            required: true,
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
            skillsPageNumber: 1,
            slicedSkillsPages: [],
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
            const skillsList = this.$refs.skillsList;
            if (!skillsList)
                return;

            const width = skillsList.offsetWidth;
            const maxHeight = skillsList.offsetHeight;
            const skillsListClone = skillsList.cloneNode();
            const origStyles = getComputedStyle(skillsList);
            skillsListClone.style.cssText = `   
                width: ${width}px;
                line-height: ${origStyles.lineHeight};
                font-size: ${origStyles.fontSize};
                height: auto;
                position: absolute;
                opacity: 0;
                z-index: -999;
                font-family: ${origStyles.fontFamily};
                top: 0;
                left: 0;
            `;
            document.body.append(skillsListClone);
            const skillsArray = this.skillsList.map(str => str);
            let skillsPages = [];

            // функция будет вызываться, пока не заполнятся все страницы в skillsPages
            createPage();
            this.slicedSkillsPages = skillsPages;
            skillsListClone.remove();

            function createPage() {
                let page = [];
                skillsListClone.innerHTML = '';
                while (skillsArray[0] && skillsListClone.offsetHeight <= maxHeight) {
                    const newItem = `<li>${skillsArray[0]}</li>`;
                    skillsListClone.innerHTML += newItem;

                    if (skillsListClone.offsetHeight <= maxHeight) {
                        page.push(newItem);
                        skillsArray.splice(0, 1);
                    } else {
                        break;
                    }
                }

                if (page.length > 0)
                    skillsPages.push(page);

                if (skillsArray[0])
                    createPage();
            }
        },
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
        currentShownSkills() {
            const sList = this.$refs.skillsList;
            if (sList) {
                sList.style.cssText = `opacity: 0; transition: all 0s;`;
                setTimeout(async () => {
                    sList.style.cssText = 'opacity: 1; transition: all .5s;';
                    await onTransitionEnd(sList);
                    sList.style.removeProperty('opacity');
                    sList.style.removeProperty('transition');
                }, 0);
            }

            const page = this.slicedSkillsPages[this.skillsPageNumber - 1];
            if (!page)
                return this.slicedSkillsPages[0] ?
                    this.slicedSkillsPages[0].join('') : ''
                    || '';

            return page.join('');
        },
        isFirstSkillsPage() {
            return this.skillsPageNumber === 1;
        },
        isLastSkillsPage() {
            return this.skillsPageNumber === this.slicedSkillsPages.length;
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
    },
    watch: {
        skillsPageNumber(num) {
            if (num < 1)
                this.skillsPageNumber = 1;

            if (num > this.slicedSkillsPages.length)
                this.skillsPageNumber = this.slicedSkillsPages.length;
        }
    }
}
</script>