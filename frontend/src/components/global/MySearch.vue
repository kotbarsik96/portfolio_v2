<template>
    <TextInput iconName="icon-search" :inputId="inputId" :name="name"></TextInput>
</template>

<script>
import TextInput from '@/components/global/TextInput.vue';

export default {
    name: 'MySearch',
    components: {
        TextInput
    },
    props: {
        inputId: {
            type: String,
            required: true
        },
        name: String
    }
}
</script>

<style></style>