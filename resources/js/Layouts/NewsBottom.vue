<script setup>
defineProps(['component'])
</script>

<template>
    <KeepAlive>
        <component :is="component"></component>
    </KeepAlive>
</template>

<style scoped>

</style>
