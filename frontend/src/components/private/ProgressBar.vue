<template>
    <Transition :name="transitionName">
        <div v-if="loadProgress && loadProgress > 0" class="loading-scale">
            <div class="loading-scale__progressbar" :style="{ 'width': `${loadProgress}%` }"></div>
            <div class="loading-scale__percentage">
                {{ loadProgress }}
                %
            </div>
        </div>
    </Transition>
</template>

<script>
export default {
    name: 'ProgressBar',
    props: {
        transitionName: {
            type: String,
            default: 'fade-in'
        },
        loadProgress: {
            type: Number,
            default: 0
        }
    }
}
</script>

<style></style>