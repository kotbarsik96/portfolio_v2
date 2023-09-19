<template>
    <div class="text-input">
        <div class="text-input__wrapper">
            <span v-if="iconName" class="text-input__icon" :class="[iconName]"></span>
            <input class="text-input__input" ref="input" v-model="value" :type="type" :placeholder="placeholder || ''" :id="inputId"
                :name="name" @keyup.enter="$emit('enterKeyup')">
        </div>
    </div>
</template>

<script>
export default {
    name: 'TextInput',
    emits: ['enterKeyup', 'update:modelValue'],
    props: {
        modelValue: String,
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
        defaultValue: String
    },
    mounted() {
        if (this.defaultValue)
            this.$refs.input.value = this.defaultValue;
    },
    data() {
        return {
            value: ''
        }
    },
    watch: {
        value() {
            this.$emit('update:modelValue', this.value);
        }
    }
}
</script>

<style></style>