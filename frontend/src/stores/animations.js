
// сделать один store
import { defineStore } from "pinia";
import { delay } from '@/assets/scripts/scripts.js';
export const useAnimationsStore = defineStore("animationsStore", {
    state() {
        return {
            queue: [],
            isAnimating: false
        }
    },
    actions: {
        addToQueue(obj) {
            const isInQueue = this.queue.find(o => o.id === obj.id);
            if (isInQueue)
                return;

            this.queue.push(obj);
            if (!this.isAnimating)
                this.doAnimQueueNext();
        },
        async doAnimQueueNext() {
            if (this.isAnimating)
                return;

            const firstObj = this.queue[0];
            if (!firstObj)
                return;

            this.isAnimating = true;

            await delay(250);

            // анимировать
            await firstObj.doAnim();
            // убрать из очереди анимированный/ушедший из зоны видимости экрана элемент
            this.queue = this.queue.filter(o => o.id !== firstObj.id);
            // запустить анимацию следующего элемента
            this.isAnimating = false;
            if (this.queue[0])
                this.doAnimQueueNext();
        }
    }
});