<template>
    <div class="content">
        <MyPortfolio></MyPortfolio>
        <MyContacts></MyContacts>
        <AboutMe></AboutMe>
        <MySkills></MySkills>
    </div>
</template>

<script>
import MyPortfolio from '@/components/global/sections/MyPortfolio.vue';
import MyContacts from '@/components/global/sections/MyContacts.vue';
import AboutMe from '@/components/global/sections/AboutMe.vue';
import MySkills from '@/components/global/sections/MySkills.vue';

export default {
    name: 'HomeView',
    components: {
        MyPortfolio,
        MyContacts,
        AboutMe,
        MySkills
    },
}
</script>

<style></style>