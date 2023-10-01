import { defineStore } from "pinia";
export const useModalsStore = defineStore("modalsStore", {
    state() {
        return {
            modalWindows: []
        }
    },
    actions: {
        addModalWindow(propsForModal) {
            this.modalWindows.push(propsForModal);
        },
        removeFirstModalWindow() {
            this.modalWindows = this.modalWindows.slice(1, this.modalWindows.length);
        },
        exportForMediaModal(component) {
            return component;
        },
    }
});