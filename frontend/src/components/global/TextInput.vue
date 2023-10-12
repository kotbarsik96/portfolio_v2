<template>
    <div class="text-input">
        <div class="text-input__wrapper">
            <span v-if="iconName" class="text-input__icon" :class="[iconName]"></span>
            <input class="text-input__input" ref="input" v-model="value" :type="type" :placeholder="placeholder || ''"
                :id="inputId" :name="name" @keyup.enter="$emit('enterKeyup')" @input="onInput">
        </div>
    </div>
</template>

<script>
export default {
    name: 'TextInput',
    emits: ['enterKeyup', 'update:modelValue'],
    props: {
        modelValue: [String, Number],
        iconName: {
            type: String
        },
        inputId: {
            type: String,
            required: true
        },
        type: {
            type: String,
            default: 'text'
        },
        placeholder: String,
        name: {
            type: String,
            default: 'input'
        },
        defaultValue: {
            type: String,
            default: ''
        },
        numberonly: Boolean
    },
    data() {
        return {
            value: this.defaultValue
        }
    },
    watch: {
        value() {
            this.$emit('update:modelValue', this.value);
        },
        defaultValue() {
            if (!this.value) {
                this.value = this.defaultValue;
            }
        }
    },
    methods: {
        refresh() {
            this.value = '';
        },
        onInput(event) {
            if (this.numberonly)
                event.target.value = event.target.value.replace(/\D/g, '');
        }
    }
}
</script>

<style></style>