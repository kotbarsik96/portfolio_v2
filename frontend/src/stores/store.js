import { defineStore } from "pinia";
import axios from "axios";
import Cookie from "js-cookie";

axios.defaults.withCredentials = true;

export const useMyStore = defineStore("myStore", {
    state() {
        return {
            theme: localStorage.getItem("kb96_portfolio_theme") || "light",
            isMe: false,
            qrCode: null,
            isLoading: false,
            taxonomies: {},
        }
    },
    actions: {
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
                return false;
            } catch (err) {
                // console.error(err);
            }
        },
        async logout() {
            this.isMe = false;

            try {
                await axios.post(`${import.meta.env.VITE_API_LINK}logout`);
            } catch (err) { }

            Cookie.remove("user");
            Cookie.remove("user_id");
        },
        loadAllData() {
            return new Promise(resolve => {
                const apiUrl = import.meta.env.VITE_API_LINK;
                const loadable = [
                    { key: 'stack', url: `${apiUrl}taxonomies/stack` },
                    { key: 'tags', url: `${apiUrl}taxonomies/tags` },
                    { key: 'types', url: `${apiUrl}taxonomies/types` },
                    { key: 'skills', url: `${apiUrl}skills` }
                ];
                loadable.forEach(async (obj, index) => {
                    try {
                        const res = await axios.get(obj.url);
                        if (!res.data.error) {
                            this.taxonomies[obj.key] = res.data;
                        }
                    } catch (err) { }
                    
                    if (loadable.length - 1 === index)
                        resolve(true);
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