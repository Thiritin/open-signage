<script setup>
defineProps(['component'])
defineOptions({
    inheritAttrs: false
})
</script>

<template>
    <KeepAlive>
        <component v-bind="$attrs" :is="component"></component>
    </KeepAlive>
</template>

<style scoped>

</style>
