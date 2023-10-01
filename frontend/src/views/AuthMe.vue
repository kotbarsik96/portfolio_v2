<template>
    <div class="container">
        <section class="hello-form" ref="form" @keyup="(event) => event.key === 'Enter' ? login() : false">
            <InputItem class="input-item--full" label="Логин" id="name" name="name" placeholder="Логин">
            </InputItem>
            <InputItem class="input-item--full" label="Пароль" id="password" type="password" placeholder="Пароль"
                name="password">
            </InputItem>
            <button class="button" @click.prevent="login" type="submit">
                Войти
            </button>
        </section>
    </div>
</template>

<script>
import { useMyStore } from '@/stores/store.js';
import { mapState } from 'pinia';
import { getDataFromForms } from '@/assets/scripts/scripts.js';
import axios from 'axios';
import Cookies from 'js-cookie';

export default {
    name: 'AuthMe',
    data() {
        return {
            
        }
    },
    methods: {
        async login() {
            const store = useMyStore();
            store.isLoading = true;

            if (!Cookies.get("XSRF-TOKEN")) {
                await axios.get(`${import.meta.env.VITE_SANCTUM_CSRF_COOKIE}`);
            }

            const data = getDataFromForms(this.$refs.form);
            try {
                const res = await axios.post(`${import.meta.env.VITE_API_LINK}login`, data);
                if (res && res.data && res.data.success) {
                    const store = useMyStore();
                    store.authData = data;
                    this.$router.push({ name: 'DoubleAuth' });
                }
            } catch (e) {
                console.error(e);
            }

            store.isLoading = false;
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