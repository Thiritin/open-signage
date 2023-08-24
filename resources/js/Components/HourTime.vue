<script setup>
defineProps(['time'])
let options = {
    hour: "2-digit", minute: "2-digit"
};
</script>

<template>
    <span>{{ new Date(time).toLocaleTimeString('de-DE', options) }}</span>
</template>

<style scoped>

</style>
