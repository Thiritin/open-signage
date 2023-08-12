<script setup>
defineProps({
    image: {
        type: String,
        required: true
    },
    cover: {
        type: Boolean,
        default: true
    }
})
</script>

<template>
    <div class="fullscreen-image h-screen w-screen flex justify-center items-center">
        <img :src="image" :class="{'object-cover h-full w-full': cover,'max-h-screen max-w-screen': !cover}" alt="Full Screen Image">
    </div>
</template>

<style scoped>

</style>
