<template>
    <li class="skill-item" :class="{ '__shown-links': isShownLinks }" ref="rootElem">
        <div class="skill-item__buttons">
            <button v-if="data.image" class="skill-item__button icon-image" :class="{ '__active': mediaType === 'image' }"
                type="button" @click="changeMedia('image')"></button>
            <button v-if="data.video" class="skill-item__button icon-video" :class="{ '__active': mediaType === 'video' }"
                type="button" @click="changeMedia('video')"></button>
            <button class="portfolio-item__button icon-question-mark" :class="{ '__active': isShownLinks }" type="button"
                @click="toggleLinks"></button>
            <RouterLink v-if="isMe" class="skill-item__button icon-pencil"
                :to="{ name: 'EditSkill', params: { id: data.id } }">
            </RouterLink>
        </div>
        <div class="skill-item__container" ref="itemContainer">
            <div class="skill-item__folder-back" :style="{ backgroundImage: folderBackgroundBack }">
            </div>
            <div class="skill-item__links">
                <ol class="skill-item__links-list" ref="linksList"></ol>
                <div v-if="linksPages.length > 0" class="skill-item__links-pagination">
                    <button class="skill-item__links-prev icon-chevron-right" type="button"
                        @click="turnOverLinksPage('prev')"></button>
                    <span>|</span>
                    <button class="skill-item__links-next icon-chevron-right" type="button"
                        @click="turnOverLinksPage('next')"></button>
                </div>
            </div>
            <div class="skill-item__media-container" ref="mediaContainer">
                <Transition name="shrink-out" mode="out-in">
                    <img v-if="imageSrc && mediaType === 'image'" class="skill-item__media" :src="imageSrc"
                        alt="Изображение навыка">
                    <VideoPlayer v-else-if="videoSrc && mediaType === 'video'" class="skill-item__media" :src="videoSrc">
                    </VideoPlayer>
                </Transition>
            </div>
            <div class="skill-item__folder-front" :style="{ backgroundImage: folderBackgroundFront }">
            </div>
        </div>
        <div class="skill-item__text">
            <button class="skill-item__title" type="button" @click="toggleLinks">
                {{ data.title }}
            </button>
        </div>
    </li>
</template>

<script>
import { useMyStore } from '@/stores/store.js';
import { mapState } from 'pinia';
import { onTransitionEnd, createElement, doInFade, getCoords } from '@/assets/scripts/scripts.js';
import VideoPlayer from './VideoPlayer.vue';

import folderBackUrl from '@/assets/images/portfolio-item/folder-back.png';
import folderFrontUrl from '@/assets/images/portfolio-item/folder-front.png';
import folderBackDarkUrl from '@/assets/images/portfolio-item/folder-dark-back.png';
import folderFrontDarkUrl from '@/assets/images/portfolio-item/folder-dark-front.png';

export default {
    name: 'PortfolioItem',
    components: {
        VideoPlayer
    },
    props: {
        data: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            theme: '',
            isShownLinks: true,
            linksList: [
                'AudioFreeDesign (на странице товара)',
                'Flowers Club (в карточке товара, в описании)',
                'Webovio',
                'Streetster (связаны с кружочками выбора цвета)',
                'Freshnesecom (на странице товара)',
                'Freshnesecom (на странице товара)',
                'Webovio',
                'Streetster (связаны с кружочками выбора цвета)',
                'Flowers Club (в карточке товара, в описании)',
                'AudioFreeDesign (на странице товара)',
            ],
            linksPages: [],
            linksPageNumber: 1,
            mediaType: '',
        }
    },
    mounted() {
        const store = useMyStore();
        this.theme = store.theme;
        store.$subscribe((m, state) => this.theme = state.theme);

        this.sliceLinksPages();
        window.addEventListener("resize", () => {
            // таким образом метод будет вызван спустя некоторое время после того, как пользователь изменит размер экрана, иначе он будет вызываться очень много раз
            if (this.sliceLinksTimeout)
                clearInterval(this.sliceLinksTimeout);
            this.sliceLinksTimeout = setTimeout(this.sliceLinksPages, 500);
        });
    },
    methods: {
        async toggleLinks() {
            if (this.isTogglingLinks) return;

            this.isTogglingLinks = true;
            const mediaContainer = this.$refs.mediaContainer;

            this.$refs.rootElem.style.zIndex = '100';
            let translateCoef = mediaContainer.offsetWidth;
            // если при сдвиге вправо изображение будет выходить за границы документа, сдвинуть его влево
            if (getCoords(mediaContainer).right + translateCoef > document.documentElement.clientWidth)
                translateCoef = -1 * translateCoef;

            mediaContainer.style.cssText = `
                transition: all .2s;
                transform: translateX(${translateCoef}px);
            `;
            await onTransitionEnd(mediaContainer);

            // показать изображение/видео, спрятать ссылки
            if (this.isShownLinks)
                this.isShownLinks = false;
            // показать ссылки, спрятать изображение/видео
            else
                this.isShownLinks = true;

            mediaContainer.style.removeProperty('transform');
            await onTransitionEnd(mediaContainer, () => mediaContainer.style.removeProperty('transition'));
            this.$refs.rootElem.style.removeProperty('z-index');
            this.isTogglingLinks = false;
        },
        async changeMedia(mediaType) {
            if (this.mediaType === mediaType) return;

            this.mediaType = mediaType;
        },
        sliceLinksPages() {
            const self = this;
            if (!this.$refs.linksList) return;

            const linksListClone = this.$refs.linksList.cloneNode();
            const origStyles = getComputedStyle(linksListClone);
            const origWidth = this.$refs.linksList.offsetWidth < 100
                ? 250 : this.$refs.linksList.offsetWidth;
            linksListClone.style.cssText = `
                width: ${origWidth}px;
                position: absolute;
                top: 0;
                left: 0;
                opacity: 0;
                z-index: -999;
                font-size: ${origStyles.fontSize};
            `;
            document.body.append(linksListClone);

            let i = 0;
            let linkItem = this.linksList[i];
            this.linksPages = [];

            const coef = window.matchMedia('(max-width: 479px)').matches
                ? 2.5 : 1.85;
            const maxHeight = this.$refs.itemContainer.offsetHeight / coef;
            createLinksPage();
            this.setLinksPage();
            linksListClone.remove();

            function createLinksPage() {
                let array = [];
                while (linkItem && linksListClone.offsetHeight < maxHeight) {
                    const li = createElement('li', 'skill-item__link-item', `
                        <a class="link" href="#">${i + 1}. ${linkItem}</a>
                    `);
                    linksListClone.append(li);
                    array.push(li);
                    i++;
                    linkItem = self.linksList[i];
                }
                if (linksListClone.offsetHeight > maxHeight) {
                    const lastItem = linksListClone.querySelector(".skill-item__link-item:last-child");
                    if (!lastItem)
                        return;

                    lastItem.remove();
                    array = array.slice(0, array.length - 1);
                    i--;
                    linkItem = self.linksList[i];
                }
                self.linksPages.push(array);
                linksListClone.innerHTML = "";

                if (linkItem)
                    createLinksPage();
            }
        },
        setLinksPage(pageNumber = 1) {
            const linksList = this.$refs.linksList;
            let page = this.linksPages[pageNumber - 1];
            if (!page)
                page = this.linksPages[this.linksPages.length - 1];

            this.linksPageNumber = this.linksPages.findIndex(arr => arr[0] === page[0]) + 1;

            doInFade(linksList, () => {
                linksList.innerHTML = "";
                page.forEach(li => linksList.append(li));
                linksList.style.opacity = "1";
            });
        },
        turnOverLinksPage(action) {
            switch (action) {
                case 'prev':
                    if (this.linksPageNumber <= 1)
                        return;
                    this.linksPageNumber--;
                    break;
                case 'next':
                    if (this.linksPageNumber >= this.linksPages.length)
                        return;
                    this.linksPageNumber++;
                    break;
            }
            this.setLinksPage(this.linksPageNumber);
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
        imageSrc() {
            if (this.data.image) {
                if (!this.mediaType) {
                    this.mediaType = 'image';
                    this.isShownLinks = false;
                }
                return `${import.meta.env.VITE_BACKEND_LINK}${this.data.image.path}`;
            }

            return null;
        },
        videoSrc() {
            if (this.data.video) {
                if (!this.mediaType) {
                    this.mediaType = 'video';
                    this.isShownLinks = false;
                }
                return `${import.meta.env.VITE_BACKEND_LINK}${this.data.video.path}`;
            }

            return null;
        },
    },
}
</script>