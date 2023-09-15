<template>
    <header class="header" :class="{ __scrolled: isScrolled }" ref="rootElem">
        <div class="header__container" ref="headerContainer">
            <nav class="header__nav">
                <button class="header__nav-button" :class="{ '__active': isNavMenuOpened }" @click="toggleNavMenu">
                    <span class="header__nav-button-item"></span>
                    <span class="header__nav-button-item"></span>
                    <span class="header__nav-button-item"></span>
                </button>
                <ul v-if="$route.meta.isMe" class="header__nav-list">
                    <li class="header__nav-item" :class="{ '__shown': activeSublist === 'works' }">
                        <button class="header__nav-link" type="button" @click="toggleSublist('works')">
                            Работы
                        </button>
                        <ul class="header__nav-sublist">
                            <li class="header__nav-subitem">
                                <RouterLink :to="{ name: 'AddWork' }" class="link header__nav-link">
                                    Добавить
                                </RouterLink>
                            </li>
                            <li class="header__nav-subitem">
                                <RouterLink to="/" class="link header__nav-link">
                                    Изменить
                                </RouterLink>
                            </li>
                        </ul>
                    </li>
                    <li class="header__nav-item" :class="{ '__shown': activeSublist === 'skills' }">
                        <button class="header__nav-link" type="button" @click="toggleSublist('skills')">
                            Навыки
                        </button>
                        <ul class="header__nav-sublist">
                            <li class="header__nav-subitem">
                                <RouterLink :to="{ name: 'AddSkill' }" class="link header__nav-link">
                                    Добавить
                                </RouterLink>
                            </li>
                            <li class="header__nav-subitem">
                                <RouterLink to="/" class="link header__nav-link">
                                    Изменить
                                </RouterLink>
                            </li>
                        </ul>
                    </li>
                    <li class="header__nav-item" :class="{ '__shown': activeSublist === 'types' }">
                        <button class="header__nav-link" type="button" @click="toggleSublist('types')">
                            Типы
                        </button>
                        <ul class="header__nav-sublist">
                            <li class="header__nav-subitem">
                                <RouterLink :to="{ name: 'MyTypes' }" class="link header__nav-link">
                                    Добавить
                                </RouterLink>
                            </li>
                            <li class="header__nav-subitem">
                                <RouterLink to="/" class="link header__nav-link">
                                    Изменить
                                </RouterLink>
                            </li>
                        </ul>
                    </li>
                    <li class="header__nav-item" :class="{ '__shown': activeSublist === 'stack' }">
                        <button class="header__nav-link" type="button" @click="toggleSublist('stack')">
                            Стек
                        </button>
                        <ul class="header__nav-sublist">
                            <li class="header__nav-subitem">
                                <RouterLink :to="{ name: 'MyStack' }" class="link header__nav-link">
                                    Добавить
                                </RouterLink>
                            </li>
                            <li class="header__nav-subitem">
                                <RouterLink to="/" class="link header__nav-link">
                                    Изменить
                                </RouterLink>
                            </li>
                        </ul>
                    </li>
                </ul>
                <ul v-else class="header__nav-list">
                    <li class="header__nav-item">
                        <a v-if="$route.meta.isHome" href="#portfolio" class="header__nav-link"
                            :class="{ '__active': activeLink === 'portfolio' }">
                            Портфолио
                        </a>
                        <RouterLink v-else to="/" class="header__nav-link">
                            Портфолио
                        </RouterLink>
                    </li>
                    <li class="header__nav-item">
                        <a v-if="$route.meta.isHome" href="#contacts" class="header__nav-link"
                            :class="{ '__active': activeLink === 'contacts' }">
                            Связь
                        </a>
                        <RouterLink v-else to="/" class="header__nav-link">
                            Связь
                        </RouterLink>
                    </li>
                    <li class="header__nav-item">
                        <a v-if="$route.meta.isHome" href="#about" class="header__nav-link"
                            :class="{ '__active': activeLink === 'about' }">
                            О себе
                        </a>
                        <RouterLink v-else to="/#about" class="header__nav-link">
                            О себе
                        </RouterLink>
                    </li>
                    <li class="header__nav-item">
                        <a v-if="$route.meta.isHome" href="#skills" class="header__nav-link"
                            :class="{ '__active': activeLink === 'skills' }">
                            Мои навыки
                        </a>
                        <RouterLink v-else to="/" class="header__nav-link">
                            Мои навыки
                        </RouterLink>
                    </li>
                    <li v-if="isMe && $route.meta.isHome" class="header__nav-item">
                        <RouterLink to="/me" class="header__nav-link header__nav-link--me">
                            КТО Я?
                        </RouterLink>
                    </li>
                </ul>
            </nav>
            <div class="header__theme-switcher">
                <button v-if="isMe" class="header__theme-switcher-item icon-exit" @click="logout" type="button"></button>
                <div v-if="isMe" class="header__theme-switcher-delimeter">
                    /
                </div>
                <button class="header__theme-switcher-item icon-sun" :class="theme === 'light' ? '__active' : ''"
                    @click="() => changeTheme('light')" type="button"></button>
                <div class="header__theme-switcher-delimeter">
                    /
                </div>
                <button class="header__theme-switcher-item icon-moon" :class="theme === 'dark' ? '__active' : ''"
                    @click="() => changeTheme('dark')" type="button"></button>
            </div>
        </div>
    </header>
</template>

<script>
import { mapState, mapActions } from 'pinia';
import { useMyStore } from '@/stores/store.js';
import { qsAll, getCoords, delay } from '@/assets/scripts/scripts.js';
import { RouterLink } from 'vue-router';

export default {
    name: 'MyHeader',
    components: { RouterLink },
    async mounted() {
        window.addEventListener('scroll', this.onScroll);
        document.addEventListener('click', (event) => {
            if (event.target.closest('.header__nav-subitem') || !event.target.closest('.header__nav-item'))
                this.activeSublist = '';
        });
        await delay();
        this.onScroll();
    },
    data() {
        return {
            isScrolled: window.scrollY > 0,
            isNavMenuOpened: false,
            activeLink: '',
            activeSublist: ''
        };
    },
    computed: {
        ...mapState(useMyStore, ['theme', 'isMe'])
    },
    methods: {
        ...mapActions(useMyStore, ['logout']),
        changeTheme(theme = 'light') {
            const store = useMyStore();
            if (theme === 'light')
                store.theme = 'light';
            if (theme === 'dark')
                store.theme = 'dark';
        },
        onScroll() {
            this.isScrolled = window.scrollY > 50;
            this.setActiveNavLink();
        },
        setActiveNavLink() {
            const sections = qsAll(".content > section")
                .map(section => {
                    return { section, coords: getCoords(section) };
                });
            const closestToUserScroll = sections.sort(sortCallback)[0];
            if (!closestToUserScroll)
                return;
            const id = closestToUserScroll.section.getAttribute("id");
            this.activeLink = id;
            function sortCallback(s1, s2) {
                const s1Result = Math.abs(window.scrollY - s1.coords.top);
                const s2Result = Math.abs(window.scrollY - s2.coords.top);
                if (s1Result < s2Result)
                    return -1;
                if (s1Result > s2Result)
                    return 1;
                return 0;
            }
        },
        toggleNavMenu() {
            this.isNavMenuOpened = !this.isNavMenuOpened;
        },
        toggleSublist(name) {
            if (this.activeSublist === name)
                this.activeSublist = '';
            else
                this.activeSublist = name;
        }
    },
}
</script>

<style></style>