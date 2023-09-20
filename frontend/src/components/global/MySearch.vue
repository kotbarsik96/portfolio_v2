<template>
    <TextInput iconName="icon-search" :inputId="inputId" :name="name" v-model="searchValue"></TextInput>
</template>

<script>
import TextInput from '@/components/global/TextInput.vue';

export default {
    name: 'MySearch',
    components: {
        TextInput
    },
    emits: ['update:modelValue'],
    props: {
        modelValue: String,
        inputId: {
            type: String,
            required: true
        },
        name: String,
    },
    data() {
        return {
            searchValue: ''
        }
    },
    watch: {
        searchValue() {
            this.$emit('update:modelValue', this.searchValue);
        }
    }
}
</script>