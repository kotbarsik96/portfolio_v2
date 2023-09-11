<template>
    <div class="container">
        <section class="hello-form">
            <div class="input-item input-item--full">
                <label for="me_letsgo" class="input-item__label">
                    Код подтверждения
                </label>
                <TextInput inputId="me_letsgo" placeholder="Вводи, не бойся" name="me_doubleauth"></TextInput>
            </div>
            <button class="button" @click="login" type="button">
                Дадая
            </button>
        </section>
    </div>
</template>

<script>
import TextInput from '@/components/global/TextInput.vue';

export default {
    name: 'DoubleAuthMe',
    components: {
        TextInput
    },
    methods: {
        login() {

        }
    }
}
</script>