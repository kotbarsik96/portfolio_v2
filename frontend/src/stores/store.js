import { defineStore } from "pinia";
import axios from "axios";
import Cookie from "js-cookie";

axios.defaults.withCredentials = true;

export const useMyStore = defineStore("myStore", {
    state() {
        return {
            theme: localStorage.getItem("kb96_portfolio_theme"),
            isMe: false,
            qrCode: null,
            isLoading: false,
            taxonomies: {},
            counts: {}
        }
    },
    actions: {
        init() {
            // если в localstorage нет темы, будет установлена отсюда
            if (!localStorage.getItem("kb96_portfolio_theme")) {
                const isPreferredDarkTheme = window.matchMedia('(prefers-color-scheme: dark)').matches;

                if (isPreferredDarkTheme)
                    this.theme = 'dark';
                else this.theme = 'light';
            }
        },
        async checkIfIsMe() {
            try {
                const user = Cookie.get("user");
                const userId = Cookie.get("user_id");
                if (!user || !userId) {
                    this.isMe = false;
                    return false;
                }

                const res = await axios.post(`${import.meta.env.VITE_API_LINK}check-auth`, {
                    user,
                    userId
                });
                if (res && res.data && res.data.success) {
                    this.isMe = true;
                    return true;
                }

                this.isMe = false;
            } catch (err) {
            }

            if (!this.isMe) {
                this.logout();
                return false;
            }
        },
        async logout() {
            try {
                await axios.post(`${import.meta.env.VITE_API_LINK}logout`);
            } catch (err) { }

            this.isMe = false;
            Cookie.remove("user");
            Cookie.remove("user_id");
        },
        async loadAllData() {
            const url = `${import.meta.env.VITE_API_LINK}alldata`;
            try {
                const res = await axios.get(url);
                if (typeof res.data === 'object' && res.data) {
                    this.taxonomies = res.data;
                }
            } catch (err) { }

            return true;
        },
        loadCounts() {
            return new Promise(resolve => {
                const keys = ['works', 'skills'];
                keys.forEach(async (key, i, arr) => {
                    const url = `${import.meta.env.VITE_API_LINK}${key}/count`;
                    try {
                        const res = await axios(url);
                        if (!res.data.error) {
                            this.counts[key] = parseInt(res.data);
                        }
                    } catch (err) { }

                    if (arr.length - 1 === i)
                        resolve();
                });
            });
        }
    },
    getters: {
        worksFilterBody(state) {
            return [
                {
                    name: 'types',
                    title: 'Типы',
                    values: state.taxonomies.types
                        ? state.taxonomies.types.map(obj => obj.title)
                        : []
                },
                {
                    name: 'stack',
                    title: 'Стек',
                    values: state.taxonomies.stack
                        ? state.taxonomies.stack.map(obj => obj.title)
                        : []
                },
                {
                    name: 'skills',
                    title: 'Навыки',
                    values: state.taxonomies.skills
                        ? state.taxonomies.skills.map(obj => {
                            return obj.title;
                        })
                        : []
                }
            ]
        }
    }
});