<template>
    <div class="wrapper" ref="wrapper">
        <MyHeader ref="header"></MyHeader>

        <Transition name="fade-in">
            <div v-if="mediaModalComponent" class="media-modal-window" ref="mediaModalWindow"
                @pointerdown.self="returnMediaModalComponent">
            </div>
        </Transition>

        <Transition name="fade-in">
            <div v-if="modalWindows.length > 0" class="modal-windows-container" ref="modalWindowsContainer"
                @pointerdown.self="removeFirstModalWindow">
                <Transition name="fade-in" mode="out-in">
                    <ModalWindow :propsForModalWindow="modalWindows[0]"></ModalWindow>
                </Transition>
            </div>
        </Transition>

        <Transition name="fade-in">
            <MyLoading v-if="isLoading" isFixed></MyLoading>
        </Transition>

        <RouterView />
    </div>
</template>

<script>
import { RouterLink, RouterView } from 'vue-router';
import ModalWindow from '@/components/global/ModalWindow.vue';
import { nextTick } from 'vue';
import { mapState } from 'pinia';
import { useMyStore } from '@/stores/store.js';
import { useModalsStore } from '@/stores/modals.js';
import { qsAll, getCoords } from '@/assets/scripts/scripts.js';

export default {
    components: {
        RouterLink,
        RouterView,
        ModalWindow
    },
    created() {
        useMyStore().loadAllData();
    },
    mounted() {
        // цветовая тема
        this.setThemeClassname();
        const myStore = useMyStore();
        myStore.$subscribe((mutation, state) => {
            localStorage.setItem('kb96_portfolio_theme', state.theme);
            this.setThemeClassname();

            if (!state.isMe && this.$route.meta.mustBeMe)
                this.$router.push({ name: "Home" });
            if (state.isMe && this.$route.meta.mustNotBeMe)
                this.$router.push({ name: "Home" });
        });

        const modalsStore = useModalsStore();
        // модальное окно медиа
        modalsStore.$onAction(async (actionData) => {
            switch (actionData.name) {
                case 'exportForMediaModal':
                    this.appendMediaModal(actionData);
                    break;
            }
        });

        // сделать плавный скролл при нажатии на ссылки-якоря
        this.modifyAnchorLinks();
        new MutationObserver(this.modifyAnchorLinks)
            .observe(document.body, { childList: true, subtree: true });
    },
    data() {
        return {
            modifiedAnchorLinks: [],
            mediaModalComponent: null,
        }
    },
    computed: {
        ...mapState(useModalsStore, ['modalWindows']),
        ...mapState(useMyStore, ['isLoading']),
    },
    methods: {
        setThemeClassname() {
            const store = useMyStore();
            if (store.theme === 'dark')
                this.$refs.wrapper.classList.add('dark-theme');
            if (store.theme === 'light')
                this.$refs.wrapper.classList.remove('dark-theme');
        },
        async modifyAnchorLinks() {
            await nextTick();

            const newAnchorLinks = qsAll('a[href^="#"]')
                .filter(link => !this.modifiedAnchorLinks.includes(link));
            newAnchorLinks.forEach(link => {
                link.addEventListener('click', this.onAnchorLinkClick);
            });

            this.modifiedAnchorLinks = this.modifiedAnchorLinks.concat(newAnchorLinks);
        },
        onAnchorLinkClick(event) {
            event.preventDefault();
            const href = event.target.getAttribute('href').replace(/\#/, '');
            const el = document.querySelector(`#${href}`);
            if (!el)
                return;

            let headerRef = this.$refs.header;
            if (headerRef)
                headerRef = headerRef.$refs.rootElem;
            const coef = headerRef
                ? headerRef.offsetHeight + 20
                : document.documentElement.clientHeight || window.innerHeight;
            let top = getCoords(el).top - coef;
            if (top < 100)
                top = 0;
            window.scrollTo({
                top,
                behavior: 'smooth'
            });
        },
        async appendMediaModal(actionData) {
            if (!actionData.args[0])
                return;

            this.mediaModalComponent = actionData.args[0];
            // вставить компонент в окно
            if (this.mediaModalComponent.isExpanded) {
                await nextTick();
                const mediaModalWindow = this.$refs.mediaModalWindow;
                this.mediaModalComponent.mediaModalParentNode = this.mediaModalComponent.$el.parentNode;
                if (mediaModalWindow) {
                    mediaModalWindow.innerHTML = '';
                    mediaModalWindow.append(this.mediaModalComponent.$el);
                }
            }
            // вернуть компонент обратно
            else
                this.returnMediaModalComponent();
        },
        returnMediaModalComponent(event) {
            if (!this.mediaModalComponent)
                return;

            const parentNode = this.mediaModalComponent.mediaModalParentNode;
            if (parentNode) {
                parentNode.append(this.mediaModalComponent.$el);
            }
            this.mediaModalComponent.$data.isExpanded = false;
            this.mediaModalComponent = null;
        },
        removeFirstModalWindow() {
            const modalsStore = useModalsStore();
            modalsStore.removeFirstModalWindow();
        }
    }
}
</script>

<style></style>
