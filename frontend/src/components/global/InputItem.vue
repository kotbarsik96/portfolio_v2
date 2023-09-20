<template>
    <div class="input-item">
        <label :for="id" class="input-item__label">
            {{ label }}
        </label>
        <TextInput :inputId="id" :placeholder="placeholder" :name="name" :type="type" :defalutValue="defaultValue"
            v-model="value"></TextInput>
    </div>
</template>

<script>
export default {
    name: 'InputItem',
    emits: ['update:modelValue'],
    props: {
        modelValue: String,
        label: {
            type: String,
            default: ''
        },
        id: {
            type: String,
            required: true
        },
        type: {
            type: String,
            default: 'text'
        },
        defaultValue: String,
        name: String,
        placeholder: String,
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