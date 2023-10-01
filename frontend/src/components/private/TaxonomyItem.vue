<template>
    <li class="edit-taxonomy__list-item">
        <div class="edit-taxonomy__list-value editable" ref="value" @blur="onValueBlur" @input="onInput">
            {{ value }}
        </div>
        <button class="edit-taxonomy__button edit-taxonomy__list-edit icon-pencil" type="button"
            @click="onEditClick"></button>
        <button class="edit-taxonomy__button edit-taxonomy__list-remove icon-circle-minus" type="button"
            @click="onRemoveClick">
        </button>
        <button v-if="valueHistory.length > 0"
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
    emits: ['changeTax', 'removeTax'],
    data() {
        return {
            initialValue: this.tax.slice(0, this.tax.length),
            value: this.tax,
            savedValue: this.tax,
            valueHistory: []
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
            if (this.value && this.value !== this.savedValue) {
                this.valueHistory.push(this.savedValue);
                this.savedValue = this.value;
                const oldValue = this.valueHistory[this.valueHistory.length - 1];
                this.$emit('changeTax', this.savedValue, oldValue, this.initialValue);
            }
        },
        onInput() {
            const valueRef = this.$refs.value;
            const textContent = getTextContent(valueRef);
            this.value = textContent;
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
                self.$emit('removeTax', self.savedValue);
            }
        },
        onTurnBackClick() {
            const self = this;
            const modalsStore = useModalsStore();
            const lastValue = this.valueHistory[this.valueHistory.length - 1];
            modalsStore.addModalWindow({
                title: `Вернуть исходное название: с "${this.savedValue}" на "${lastValue}"?`,
                confirm: {
                    callback: onConfirm,
                    title: 'Вернуть'
                },
            });

            function onConfirm() {
                self.value = lastValue;
                self.onValueBlur();
                self.valueHistory = self.valueHistory.slice(0, self.valueHistory.length - 2);
            }
        }
    }
}
</script>

<style></style>