import { defineStore } from "pinia";
import axios from "axios";
import Cookie from "js-cookie";

axios.defaults.withCredentials = true;

export const useMyStore = defineStore("myStore", {
    state() {
        return {
            theme: localStorage.getItem("kb96_portfolio_theme") || "light",
            isMe: false,
            qrCode: null
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
        }
    }
});