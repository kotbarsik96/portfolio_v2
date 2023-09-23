<template>
    <div class="modal-window" :class="[propsForModalWindow.modalClassNames]">
        <button class="modal-window__close icon-cancel" type="button" @click="removeSelf"></button>
        <h3 v-if="propsForModalWindow.title" class="modal-window__title">
            {{ propsForModalWindow.title }}
        </h3>
        <div v-if="modalBody && typeof modalBody === 'string'" v-html="modalBody" class="modal-window__body"></div>
        <div v-if="modalBody && Array.isArray(modalBody)" class="modal-window__body">
            <div v-for="comp in modalBody">
                <component v-if="comp.type.name === 'TextInput'" :is="comp" v-model="textInputValue"></component>
                <component v-else :is="comp"></component>
            </div>
        </div>
        <div v-if="haveButtons" class="modal-window__buttons">
            <button v-if="propsForModalWindow.confirm" class="button" type="button" @click="onConfirmClick">
                {{ propsForModalWindow.confirm.title || 'Принять' }}
            </button>
            <button v-if="propsForModalWindow.decline || propsForModalWindow.confirm" class="button button--color-2"
                type="button" @click="onDeclineClick">
                {{ propsForModalWindow.decline ? propsForModalWindow.decline.title || 'Отменить' : 'Отменить' }}
            </button>
        </div>
    </div>
</template>

<script>
import { useModalsStore } from '@/stores/modals.js';

export default {
    name: 'ModalWindow',
    props: {
        /* propsForModalWindow: {
            title: '',

            body: 
            ВАРИАНТ 1. передача строки: 
            body: 'somestring, maybe <strong>even HTML</strong>',
            ВАРИАНТ 2. передача массива: 
                можно передать в массив компонент, сделанный через h(), например: 
                    someComponent = h(SomeComponent, { propsValue1: 'test', propsValue2: 'test2' });

            confirm: {
                callback(){},
                title: ''
            },
            decline: {
                callback(){},
                title: ''
            },
        }
        */
        propsForModalWindow: {
            type: Object,
            default: {},
            wasConfirmed: false
        }
    },
    data() {
        return {
            wasConfirmed: false,
            textInputValue: ''
        }
    },
    methods: {
        removeSelf(fromDeclineClick = false) {
            if (!this.wasConfirmed && !fromDeclineClick)
                this.onDeclineClick();

            const store = useModalsStore();
            store.removeFirstModalWindow();
        },
        onConfirmClick() {
            this.wasConfirmed = true;
            const callback = this.propsForModalWindow.confirm.callback
                ? this.propsForModalWindow.confirm.callback : null;

            if (typeof callback === 'function') {
                const args = this.propsForModalWindow.confirm.args || {};
                callback(this, args);
            }

            this.removeSelf();
        },
        onDeclineClick() {
            const callback = this.propsForModalWindow.decline
                ? this.propsForModalWindow.decline.callback : null;
            if (typeof callback === 'function') {
                const args = this.propsForModalWindow.decline.args || {};
                callback(this, args);
            }

            this.removeSelf(true);
        }
    },
    computed: {
        haveButtons() {
            return this.propsForModalWindow.confirm
                || this.propsForModalWindow.decline;
        },
        modalBody() {
            return this.propsForModalWindow.body;
        }
    }
}
</script>

<style></style>