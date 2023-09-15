<template>
    <div class="container">
        <section class="hello-form" ref="form" @keyup="(event) => event.key === 'Enter' ? register() : false">
            <MyLoading v-if="isLoading" isAbsolute></MyLoading>
            <InputItem class="input-item--full" label="Логин" id="name" name="name" placeholder="Логин" value="illusiveman">
            </InputItem>
            <InputItem class="input-item--full" label="Email" id="email" name="email" placeholder="Email" value="kotbarsik96@gmail.com">
            </InputItem>
            <InputItem class="input-item--full" label="Телеграм" id="telegram" name="telegram" placeholder="Телеграм" value="t.me/kotbarsik96">
            </InputItem>
            <InputItem class="input-item--full" label="Пароль" id="pass" type="password" placeholder="Пароль"
                name="password" value="Wh19t30213035;">
            </InputItem>
            <InputItem class="input-item--full" label="Подтверждение пароля" id="pass_confirm" type="password"
                placeholder="Подтверждение пароля" name="password_confirmation" value="Wh19t30213035;">
            </InputItem>
            <button class="button" type="submit" @click.prevent="register">
                Регистрация
            </button>
        </section>
    </div>
</template>

<script>
import { getDataFromForms } from '@/assets/scripts/scripts.js';
import { useMyStore } from '@/stores/store';
import { mapState } from 'pinia';
import axios from 'axios';

export default {
    name: 'RegisterMe',
    methods: {
        async register() {
            this.isLoading = true;
            const data = getDataFromForms(this.$refs.form);
            const res = await axios.post(`${import.meta.env.VITE_API_LINK}register`, data);
            console.log(res);
            useMyStore().qrCode = res.data.auth_qr_code;
            if (res.data.user) {
                this.$router.push({ name: 'ShowQr' });
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