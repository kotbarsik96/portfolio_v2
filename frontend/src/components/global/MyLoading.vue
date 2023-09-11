<template>
    <div class="loading" :class="{ 'loading--absolute': isAbsolute }"></div>
</template>

<script>
export default {
    name: 'MyLoading',
    props: {
        isAbsolute: Boolean
    }
}
</script>

<style></style>