<template>
    <div class="select icon-chevron-right" :class="{ '__shown': isShown }" @click="isShown = !isShown">
        <div class="select__value">
            {{ valueToShow }}
        </div>
        <Transition name="fade-in">
            <ul v-show="isShown" class="select__list" ref="list">
                <li v-for="val in values" class="select__item" @click="setValue(val)">
                    {{ val }}
                </li>
            </ul>
        </Transition>
    </div>
</template>

<script>
export default {
    name: 'MySelect',
    emits: ['update:modelValue'],
    props: {
        modelValue: String,
        values: {
            type: Array,
            default: []
        },
    },
    mounted() {
        document.addEventListener('click', (event) => {
            if (event.target.closest('.select') === this.$el)
                return;

            this.isShown = false;
        });
    },
    data() {
        return {
            value: this.values[0],
            isShown: false
        }
    },
    computed: {
        valueToShow(){
            return this.value || '(нет значений)';
        }
    },
    methods: {
        setValue(value) {
            if (this.value === value)
                return;
            this.value = value;
        }
    },
    watch: {
        value() {
            this.$emit('update:modelValue', this.value);
        },
        values(newValues, oldValues){
            if(oldValues.length < 1 && newValues.length > 0) {
                this.value = this.values[0];
            }
        }
    }
}
</script>

<style></style>