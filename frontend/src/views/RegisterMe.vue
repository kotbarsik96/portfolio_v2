<template>
    <div class="container">
        <section class="hello-form" ref="form" @keyup="(event) => event.key === 'Enter' ? register() : false">
            <MyLoading v-if="isLoading" isAbsolute></MyLoading>
            <InputItem class="input-item--full" label="Логин" id="name" name="name" placeholder="Логин">
            </InputItem>
            <InputItem class="input-item--full" label="Email" id="email" name="email" placeholder="Email">
            </InputItem>
            <InputItem class="input-item--full" label="Телеграм" id="telegram" name="telegram" placeholder="Телеграм">
            </InputItem>
            <InputItem class="input-item--full" label="Пароль" id="pass" type="password" placeholder="Пароль"
                name="password">
            </InputItem>
            <InputItem class="input-item--full" label="Подтверждение пароля" id="pass_confirm" type="password"
                placeholder="Подтверждение пароля" name="password_confirmation">
            </InputItem>
            <button class="button" type="submit" @click.prevent="register">
                Регистрация
            </button>
        </section>
    </div>
</template>

<script>
import { getDataFromForms, restApiPostMethod } from '@/assets/scripts/scripts.js';
import { useMyStore } from '@/stores/store';
import { mapState } from 'pinia';

export default {
    name: 'RegisterMe',
    methods: {
        async register() {
            this.isLoading = true;
            const data = getDataFromForms(this.$refs.form);
            const res = await restApiPostMethod(data, 'json', { method: 'register' });
            if (res.user) {
                this.$router.push({ name: 'Auth' });
            }
            this.isLoading = false;
        },
    },
    data() {
        return {
            isLoading: false,
        }
    },
    computed: {
        ...mapState(useMyStore, ['isMe'])
    },
    watch: {
        isMe(bool) {
            if (bool) {
                this.$router.push({ name: 'Home' });
            }
        }
    }
}
</script>