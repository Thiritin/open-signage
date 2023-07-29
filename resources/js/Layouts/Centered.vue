<script setup>
defineProps(['component'])

</script>

<template>
    <div class="h-screen flex justify-center items-center">
        <KeepAlive>
            <component :is="component"></component>
        </KeepAlive>
    </div>
</template>

<style scoped>

</style>
