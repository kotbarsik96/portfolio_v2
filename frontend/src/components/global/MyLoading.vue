<template>
    <div class="loading" :class="{ 'loading--absolute': isAbsolute, 'loading--fixed': isFixed }"></div>
</template>

<script>
export default {
    name: 'MyLoading',
    props: {
        isAbsolute: Boolean,
        isFixed: Boolean
    }
}
</script>

<style></style>