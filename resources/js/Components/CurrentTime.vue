<script setup>

import {onMounted, onUnmounted, ref} from "vue";
let clock = ref(getTime());



onMounted(() => {
    let timer = setInterval(() => {
        clock.value = getTime();
    }, 1000);
    onUnmounted(() => {
        clearInterval(timer);
    })
})

function getTime() {
    const date = new Date();
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');
    return `${hours}:${minutes}:${seconds}`;
}
</script>

<template>
    <span>{{ clock }}</span>
</template>

<style scoped>

</style>
