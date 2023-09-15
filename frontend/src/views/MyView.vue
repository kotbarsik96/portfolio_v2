<template>
    <div class="container">
        <RouterView></RouterView>
    </div>
</template>

<script>
import '@/assets/style/mystyle.scss';
import { RouterView } from 'vue-router';

export default {
    name: 'MyView',
    created() {
    },
    components: { RouterView }
}
</script>

<style></style>