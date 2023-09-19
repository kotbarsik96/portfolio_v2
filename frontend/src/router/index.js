import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '@/views/HomeView.vue';
import { useMyStore } from '@/stores/store';

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'Home',
            component: HomeView,
            meta: {
                isHome: true
            }
        },
        {
            path: '/me',
            name: 'Me',
            component: () => import('@/views/MyView.vue'),
            meta: {
                mustBeMe: true,
                isMe: true
            },
            children: [
                {
                    path: 'add-work',
                    name: 'AddWork',
                    component: () => import('@/views/edit/MyWork.vue'),
                },
                {
                    path: 'add-skill',
                    name: 'AddSkill',
                    component: () => import('@/views/edit/MySkill.vue'),
                },
                {
                    path: 'edit-work/:id',
                    name: 'EditWork',
                    component: () => import('@/views/edit/MyWork.vue'),
                    meta: {
                        isEditing: true
                    }
                },
                {
                    path: 'edit-skill/:id',
                    name: 'EditSkill',
                    component: () => import('@/views/edit/MySkill.vue'),
                    meta: {
                        isEditing: true
                    }
                },
                {
                    path: 'add-types',
                    name: 'MyTypes',
                    component: () => import('@/views/edit/MyTaxonomies.vue'),
                    meta: {
                        title: 'Тип',
                        titleGenitive: 'типа',
                        titleNew: 'Добавить тип',
                        taxonomyTitle: 'type'
                    }
                },
                {
                    path: 'add-stack',
                    name: 'MyStack',
                    component: () => import('@/views/edit/MyTaxonomies.vue'),
                    meta: {
                        title: 'Стек',
                        titleGenitive: 'стека',
                        titleNew: 'Добавить стек',
                        taxonomyTitle: 'stack'
                    }
                },
                {
                    path: 'add-tag',
                    name: 'MyTags',
                    component: () => import('@/views/edit/MyTaxonomies.vue'),
                    meta: {
                        title: 'Тег',
                        titleGenitive: 'тега',
                        titleNew: 'Добавить тег',
                        taxonomyTitle: 'tag'
                    }
                }
            ]
        },
        {
            path: '/yahochupizzu',
            name: 'Auth',
            component: () => import('@/views/AuthMe.vue'),
            meta: {
                mustNotBeMe: true
            }
        },
        {
            path: '/whoami',
            name: 'Register',
            component: () => import('@/views/RegisterMe.vue'),
            meta: {
                mustNotBeMe: true
            }
        },
        {
            path: '/qr-code',
            name: 'ShowQr',
            component: () => import('@/views/ShowQr.vue'),
        },
        {
            path: '/isitreallyyou',
            name: 'DoubleAuth',
            component: () => import('@/views/DoubleAuthMe.vue'),
            meta: {
                mustNotBeMe: true
            }
        }
    ]
});

router.beforeEach(async (to) => {
    const myStore = useMyStore();
    const isMe = await myStore.checkIfIsMe();

    if (!isMe && to.meta.mustBeMe)
        return { name: "Home" };
    if (isMe && to.meta.mustNotBeMe)
        return { name: "Home" };
});

export default router
