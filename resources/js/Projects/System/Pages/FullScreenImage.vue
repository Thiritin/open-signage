<script setup>
defineProps({
    image: {
        type: String,
        required: true
    }
})
</script>

<template>
    <div class="fullscreen-image h-screen w-screen">
        <img :src="image" class="object-cover h-full w-full" alt="Full Screen Image">
    </div>
</template>

<style scoped>

</style>
