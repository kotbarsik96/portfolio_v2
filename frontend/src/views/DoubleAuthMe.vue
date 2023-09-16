<template>
    <div class="container">
        <section class="hello-form" ref="form" @keyup="(event) => event.key === 'Enter' ? login() : false">
            <Transition name="fade-in">
                <MyLoading v-if="isLoading" isAbsolute></MyLoading>
            </Transition>
            <div class="input-item input-item--full">
                <label for="me_letsgo" class="input-item__label">
                    Код подтверждения
                </label>
                <TextInput inputId="me_letsgo" placeholder="Вводи, не бойся" name="secret"></TextInput>
            </div>
            <button class="button" @click="login" type="button">
                Дадая
            </button>
        </section>
    </div>
</template>

<script>
import TextInput from '@/components/global/TextInput.vue';
import { mapState } from 'pinia';
import { useMyStore } from '@/stores/store';
import { getDataFromForms } from '@/assets/scripts/scripts.js';
import axios from 'axios';
import Cookies from 'js-cookie';

export default {
    name: 'DoubleAuthMe',
    components: {
        TextInput
    },
    data() {
        return {
            isLoading: false
        }
    },
    methods: {
        async login() {
            this.isLoading = true;
            const data = Object.assign(getDataFromForms(this.$refs.form), this.authData);

            const res = await axios.post(`${import.meta.env.VITE_API_LINK}doubleauth`, data);
            const store = useMyStore();
            if (!res.data.success) {
                store.authData = null;
                this.$router.push({ name: 'Home' });
                return;
            }

            Cookies.set('user_id', res.data.user.id, { expires: 365 });
            Cookies.set('user', res.data.userSecret, { expires: 365 });

            store.checkIfIsMe();
            store.authData = null;
            this.isLoading = false;
        }
    },
    computed: {
        ...mapState(useMyStore, ['authData', 'isMe'])
    },
}
</script>