<template>
    <div class="input-item">
        <label :for="id" class="input-item__label">
            {{ label }}
        </label>
        <TextInput :inputId="id" :placeholder="placeholder" :name="name" :type="type" :value="value"></TextInput>
    </div>
</template>

<script>
export default {
    name: 'InputItem',
    props: {
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
        value: String,
        name: String,
        placeholder: String,
    }
}
</script>

<style></style>