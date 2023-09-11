<template>
    <li class="edit-taxonomy__list-item">
        <div class="edit-taxonomy__list-value editable" ref="value" @blur="onValueBlur" @input="onInput">
            {{ tax }}
        </div>
        <button class="edit-taxonomy__button edit-taxonomy__list-edit icon-pencil" type="button"
            @click="onEditClick"></button>
        <button class="edit-taxonomy__button edit-taxonomy__list-remove icon-circle-minus" type="button"
            @click="onRemoveClick">
        </button>
        <button v-if="savedValue !== initialValue"
            class="edit-taxonomy__button edit-taxonomy__list-turn-back icon-turn-back" type="button"
            @click="onTurnBackClick">
        </button>
    </li>
</template>

<script>
import { setEditable, getTextContent } from '@/assets/scripts/scripts.js';
import { useModalsStore } from '@/stores/modals.js';

export default {
    name: 'TaxonomyItem',
    props: {
        tax: String
    },
    emits: ['change-tax', 'remove-tax'],
    data() {
        return {
            initialValue: this.tax.slice(0, this.tax.length),
            value: this.tax,
            savedValue: this.tax,
        }
    },
    methods: {
        onEditClick() {
            const valueRef = this.$refs.value;
            this.changingValue = getTextContent(valueRef);
            setEditable(valueRef);
        },
        onValueBlur() {
            const valueRef = this.$refs.value;
            valueRef.removeAttribute('contenteditable');
            // save
            if (this.value) {
                const oldValue = this.savedValue;
                this.savedValue = this.value;
                this.$emit('change-tax', this.savedValue, oldValue);
            }
        },
        onInput() {
            const valueRef = this.$refs.value;
            const textContent = getTextContent(valueRef);
            this.value = textContent;
            if (this.value.length < 1) {
                const removeButton = this.$refs.removeButton.find(node => node.parentNode === event.target.parentNode);
                removeButton.click();
            }
        },
        onRemoveClick() {
            const modalsStore = useModalsStore();
            const self = this;

            modalsStore.addModalWindow({
                title: `Точно удалить "${this.savedValue}"?`,
                confirm: {
                    callback: onConfirm,
                    title: 'Удалить'
                }
            });

            function onConfirm() {
                self.$emit('remove-tax', self.savedValue);
            }
        },
        onTurnBackClick() {
            const modalsStore = useModalsStore();
            modalsStore.addModalWindow({
                title: `Вернуть исходное название: с "${this.savedValue}" на "${this.initialValue}"?`,
                confirm: {
                    callback: onConfirm,
                    title: 'Вернуть'
                },
            });

            function onConfirm() {

            }
        }
    }
}
</script>

<style></style>