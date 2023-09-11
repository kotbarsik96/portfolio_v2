<template>
    <div class="container">
        <section class="hello-form" ref="form" @keyup="(event) => event.key === 'Enter' ? login() : false">
            <Transition name="fade-in">
                <MyLoading v-if="isLoading" isAbsolute></MyLoading>
            </Transition>
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
import { getDataFromForms, restApiPostMethod } from '@/assets/scripts/scripts.js';
import { useMyStore } from '@/stores/store.js';
import { mapState } from 'pinia';

export default {
    name: 'AuthMe',
    afterRouteEnter() {
        if (this.isMe)
            this.$router.push({ name: 'Home' });
    },
    data() {
        return {
            isLoading: false
        }
    },
    methods: {
        async login() {
            this.isLoading = true;

            const data = getDataFromForms(this.$refs.form);
            const res = await restApiPostMethod(data, "json", { method: 'login' });
            if(!res || !res.user) {
                this.isLoading = false;
                return;
            }

            localStorage.setItem('kb96token', res.token);
            localStorage.setItem('kb96user', res.user.name);

            const store = useMyStore();
            store.checkIfIsMe();
            this.isLoading = false;
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