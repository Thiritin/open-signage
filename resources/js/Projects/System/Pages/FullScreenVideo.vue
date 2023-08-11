<script setup>
defineProps({
    video: {
        type: String,
        required: true
    }
})
</script>

<template>
    <div class="fullscreen-image h-screen w-screen">
        <video autoplay muted loop class="object-cover h-full w-full">
            <source :src="video" type="video/mp4">
        </video>
    </div>
</template>

<style scoped>

</style>
