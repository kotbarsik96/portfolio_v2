<template>
    <div class="select icon-chevron-right" :class="{ '__shown': isShown }" @click="isShown = !isShown">
        <div class="select__value">
            {{ value }}
        </div>
        <Transition name="fade-in">
            <ul v-show="isShown" class="select__list" ref="list">
                <li v-for="value in values" class="select__item" @click="setValue(value)">
                    {{ value }}
                </li>
            </ul>
        </Transition>
    </div>
</template>

<script>
export default {
    name: 'MySelect',
    props: {
        values: {
            type: Array,
            default: []
        }
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
    methods: {
        setValue(value) {
            if (this.value === value)
                return;
            this.value = value;
        }
    }
}
</script>

<style></style>