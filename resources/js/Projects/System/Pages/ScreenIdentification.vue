<script setup>
import {defineProps} from 'vue'

defineProps(['appScreen'])
</script>

<template>
    <div class="text-white z-50 h-full flex justify-center items-center">
        <div class="text-center">
            <div class="text-9xl">#{{ appScreen.id }}</div>
            <h1 class="text-7xl font-bold">{{ appScreen.name }}</h1>
        </div>
    </div>
</template>
