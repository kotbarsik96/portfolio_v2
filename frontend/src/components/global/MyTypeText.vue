<template>
    <span ref="rootElem"></span>
</template>

<script>
import { getCoords, createElement } from '@/assets/scripts/scripts.js';
import { useAnimationsStore } from '@/stores/animations.js';
import { nextTick } from 'vue';

export default {
    name: 'MyTypeText',
    props: {
        text: {
            type: [String, Number],
        },
        textsList: {
            /* 0: { tagName: 'a', attributes: { href: '#' }, text: '' }
            */
            type: Array,
        },
        speed: {
            type: [Number, String],
            default: 50
        },
        queue: Boolean
    },
    data() {
        return {
            id: parseInt(Date.now() + (Math.random() * 10) * (Math.random() * 5)),
            textsListComponent: [],
            minHeight: 0,
            isInSight: false,
            notYetAnimated: []
        }
    },
    mounted() {
        if (this.text)
            this.textsListComponent.push({ text: this.text });
        if (Array.isArray(this.textsList))
            this.textsListComponent = this.textsListComponent.concat(this.textsList);

        this.$refs.rootElem.style.opacity = '0';
        this.textsListComponent.forEach(obj => this.$refs.rootElem.textContent = obj.text);
        this.minHeight = this.$refs.rootElem.offsetHeight;
        this.$refs.rootElem.textContent = '';
        this.$refs.rootElem.style.removeProperty('opacity');

        this.$refs.rootElem.style.cssText = `
            min-height: ${this.minHeight}px;
        `;

        this.notYetAnimated = this.textsListComponent;
        window.addEventListener('scroll', this.onWindowScroll);
        nextTick().then(this.onWindowScroll);
    },
    beforeUnmount() {
        window.removeEventListener('scroll', this.onWindowScroll);
    },
    methods: {
        onWindowScroll() {
            const coords = getCoords(this.$refs.rootElem);
            const windowHeight = document.documentElement.clientHeight || window.innerHeight;
            const coef = windowHeight - windowHeight * 0.9;
            const isInSight = window.scrollY < coords.top - coef
                && window.scrollY + windowHeight > coords.bottom + coef;
            if (isInSight) {
                this.onEnteringSight();
            } else {
                this.onLeavingSight();
            }
        },
        onEnteringSight() {
            this.isInSight = true;

            if (this.queue) {
                const store = useAnimationsStore();
                store.addToQueue({ id: this.id, doAnim: this.doAnim });
            } else {
                this.doAnim();
            }
        },
        onLeavingSight() {
            this.isInSight = false;
            const store = useAnimationsStore();
            store.queue = store.queue.filter(o => o.id !== this.id);
        },
        // запускает всю анимацию
        async doAnim() {
            if (this.isAnimating)
                return false;
            if (!this.isInSight)
                return false;

            this.isAnimating = true;
            const self = this;
            /* запускает анимацию первого элемента из массива this.notYetAnimated. Когда анимация закончится, этот первый элемент удалится из массива и функция будет применена заново, уже со следующим элементом, который станет первым */
            await launchAnim();
            /* сюда дойдет, когда launchAnim() будет вызван для всех элементов анимации, ЛИБО, когда изменится this.isInSight с true на false */
            this.isAnimating = false;
            if (this.notYetAnimated.length < 1)
                window.removeEventListener('scroll', this.onWindowScroll);

            return true;

            async function launchAnim() {
                const obj = self.notYetAnimated[0];
                if (!obj)
                    return;

                const rootElem = self.$refs.rootElem;
                let container = obj.tagName
                    ? createElement(obj.tagName, '', '', obj.attributes)
                    : rootElem;
                if (container !== rootElem)
                    rootElem.append(container);

                // если весь блок ушел из видимости экрана, не выполнять анимацию других элементов
                if (!self.isInSight)
                    return;

                // если же блок еще в зоне видимости экрана, выполнить анимацию текущего элемента и запустить анимацию следующего
                await self.animText(obj.text, container);
                self.notYetAnimated = self.notYetAnimated.filter(o => o.id !== obj.id);
                await launchAnim();

                return;
            }
        },
        // анимирует конкретный участок текста
        animText(text, el = this.$refs.rootElem) {
            text = text.replace(/\s{2,}/g, '');

            return new Promise(resolve => {
                const split = text.split('');
                split.forEach((letter, index, array) => {
                    setTimeout(() => {
                        el.textContent += letter;

                        if (array.length === index + 1) {
                            el.textContent += ' ';
                            resolve();
                        }
                    }, parseInt(this.speed) * index);
                });
            });
        }
    },
}
</script>