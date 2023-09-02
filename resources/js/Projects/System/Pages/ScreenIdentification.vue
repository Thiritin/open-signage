<script setup>
import {defineProps} from 'vue'

defineProps(['screen'])
</script>

<template>
    <div class="text-white z-50 h-full flex justify-center items-center">
        <div class="text-center">
            <div class="text-9xl">#{{ screen.id }}</div>
            <h1 class="text-7xl font-bold">{{ screen.name }}</h1>
        </div>
    </div>
</template>

<style scoped>

</style>
