<template>
    <div class="input-item">
        <label :for="id" class="input-item__label">
            {{ label }}
        </label>
        <TextInput :inputId="id" :placeholder="placeholder" :name="name" :type="type" :defalutValue="defaultValue"
            v-model="value" ref="textInput" :numberonly="numberonly || false"></TextInput>
    </div>
</template>

<script>
export default {
    name: 'InputItem',
    emits: ['update:modelValue'],
    props: {
        modelValue: [String, Number],
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
        defaultValue: [String, Number],
        name: String,
        placeholder: String,
        numberonly: Boolean
    },
    data() {
        return {
            value: ''
        }
    },
    watch: {
        value() {
            this.$emit('update:modelValue', this.value);
        },
        defaultValue(){
            if(!this.$refs.textInput.value)
                this.$refs.textInput.value = this.defaultValue;
        }
    },
    methods: {
        refresh(){
            this.$refs.textInput.refresh();
        }
    }
}
</script>

<style></style>