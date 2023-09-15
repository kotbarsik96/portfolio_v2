<template>
    <div class="text-input">
        <div class="text-input__wrapper">
            <span v-if="iconName" class="text-input__icon" :class="[iconName]"></span>
            <input class="text-input__input" :type="type" :placeholder="placeholder || ''" :id="inputId" :name="name" :value="value" ref="input">
        </div>
    </div>
</template>

<script>
export default {
    name: 'MySearch',
    props: {
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
        value: {
            type: String,
            default: ''
        }
    },
}
</script>

<style></style>