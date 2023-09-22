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
                <div class="skill-item__links-container">
                    <ol class="skill-item__links-list" ref="linksList" v-html="currentShownLinks"></ol>
                </div>
                <div v-if="slicedLinksPages.length > 1" class="skill-item__links-pagination">
                    <button class="skill-item__links-prev icon-chevron-right" type="button" @click="linksPageNumber--"
                        :disabled="isFirstLinksPage"></button>
                    <span>|</span>
                    <button class="skill-item__links-next icon-chevron-right" type="button" @click="linksPageNumber++"
                        :disabled="isLastLinksPage"></button>
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
import { onTransitionEnd, getCoords } from '@/assets/scripts/scripts.js';
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
            linksPageNumber: 1,
            slicedLinksPages: [],
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
            const linksList = this.$refs.linksList;
            if (!linksList)
                return;

            const width = linksList.offsetWidth;
            const maxHeight = linksList.offsetHeight;
            const linksListClone = linksList.cloneNode();
            const origStyles = getComputedStyle(linksList);
            linksListClone.style.cssText = `   
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
            document.body.append(linksListClone);
            const skillsArray = this.data.links.map(obj => {
                const description = obj.description ? `(${obj.description})` : '';
                return `<a href="${obj.url}">${obj.title}${description}</a>`;
            });
            let skillsPages = [];

            let number = 1;
            // функция будет вызываться, пока не заполнятся все страницы в skillsPages
            createPage();
            linksListClone.remove();
            this.slicedLinksPages = skillsPages;

            function createPage() {
                let page = [];
                linksListClone.innerHTML = '';
                while (skillsArray[0] && linksListClone.offsetHeight <= maxHeight) {
                    const newItem = `
                        <li class="skill-item__link-item">${number}. ${skillsArray[0]}</li>`;
                    linksListClone.innerHTML += newItem;

                    if (linksListClone.offsetHeight <= maxHeight) {
                        page.push(newItem);
                        skillsArray.splice(0, 1);
                        number++;
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
        currentShownLinks() {
            const lList = this.$refs.linksList;
            if (lList) {
                lList.style.cssText = `opacity: 0; transition: all 0s;`;
                setTimeout(async () => {
                    lList.style.cssText = 'opacity: 1; transition: all .5s;';
                    await onTransitionEnd(lList);
                    lList.style.removeProperty('opacity');
                    lList.style.removeProperty('transition');
                }, 0);
            }

            if (this.slicedLinksPages.length < 1)
                return 'Не добавлены сайты, где реализован этот навык';

            const page = this.slicedLinksPages[this.linksPageNumber - 1];
            if (!page)
                return this.slicedLinksPages[0] ?
                    this.slicedLinksPages[0].join('') : ''
                    || '';


            return page.join('');
        },
        isFirstLinksPage() {
            return this.linksPageNumber === 1;
        },
        isLastLinksPage() {
            return this.linksPageNumber === this.slicedLinksPages.length;
        }
    },
    watch: {
        linksPageNumber(num) {
            if (num < 1)
                this.linksPageNumber = 1;

            if (num > this.slicedLinksPages.length)
                this.linksPageNumber = this.slicedLinksPages.length;
        }
    }
}
</script>