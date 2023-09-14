<template>
    <div class="container">
        <div class="show-qr" v-html="qrCode"></div>
    </div>
</template>

<script>
import '@/assets/style/mystyle.scss';

import { mapState } from 'pinia';
import { useMyStore } from '@/stores/store.js';

export default {
    name: 'ShowQr',
    computed: {
        ...mapState(useMyStore, ['qrCode'])
    },
}
</script>

<style></style>