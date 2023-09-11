import { defineStore } from "pinia";
import { restApiPostMethod } from "@/assets/scripts/scripts.js";
export const useMyStore = defineStore("myStore", {
    state() {
        return {
            theme: localStorage.getItem("kb96_portfolio_theme") || "light",
            isMe: false,
        }
    },
    actions: {
        async checkIfIsMe() {
            const token = localStorage.getItem("kb96token");
            const name = localStorage.getItem("kb96user");
            if (!token || !name)
                return false;

            const data = { token, name };
            const res = await restApiPostMethod(data, "json", { method: "check-auth" });
            if (res.is_me) {
                this.isMe = true;
                return true;
            }

            return false;
        },
        async logout() {
            this.isMe = false;
            const token = localStorage.getItem("kb96token");
            const name = localStorage.getItem("kb96user");
            localStorage.removeItem("kb96token");
            localStorage.removeItem("kb96user");

            const data = { token, name };
            const res = await restApiPostMethod(data, "json", { method: "logout" });
        }
    }
});