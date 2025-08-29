<script setup>
defineProps(['title', 'message'])
</script>

<template>
    <div class="text-white p-24 h-full flex justify-center items-center">
        <div class="text-center">
            <h1 class="text-12xl text-white font-bold mb-4">{{ title }}</h1>
            <p class="text-white text-8xl/snug font-semibold whitespace-pre-wrap">{{ message }}</p>
        </div>
    </div>
</template>

<style scoped>

</style>
