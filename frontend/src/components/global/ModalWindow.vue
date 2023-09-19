<template>
    <div class="modal-window">
        <button class="modal-window__close icon-cancel" type="button" @click="removeSelf"></button>
        <h3 v-if="propsForModalWindow.title" class="modal-window__title">
            {{ propsForModalWindow.title }}
        </h3>
        <div v-if="propsForModalWindow.body" class="modal-window__body">
            {{ propsForModalWindow.body }}
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
        propsForModalWindow: {
            type: Object,
            default: {},
            wasConfirmed: false
        }
    },
    data() {
        return {
            wasConfirmed: false,

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
                callback(args);
            }

            this.removeSelf();
        },
        onDeclineClick() {
            const callback = this.propsForModalWindow.decline
                ? this.propsForModalWindow.decline.callback : null;
            if (typeof callback === 'function') {
                const args = this.propsForModalWindow.decline.args || {};
                callback(args);
            }

            this.removeSelf(true);
        }
    },
    computed: {
        haveButtons() {
            return this.propsForModalWindow.confirm
                || this.propsForModalWindow.decline;
        }
    }
}
</script>

<style></style>