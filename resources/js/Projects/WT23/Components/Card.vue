<script setup>
defineProps({
    color: {
        type: String,
        default: 'bg-primary-300'
    }
})
</script>

<template>
    <div :class="color" class="rounded-lg shadow-2xl">
        <slot></slot>
    </div>
</template>

<style scoped>

</style>
