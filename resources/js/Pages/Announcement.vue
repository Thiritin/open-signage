<script setup>
    defineProps({
        title: {
            type: String,
            default: "Announcement"
        },
        text: {
            type: String,
            default: "Welp!\n\nLooks like there's no announcement right now."
        },
        centerContent: {
            type: Boolean,
            default: false
        }
    })
</script>

<template>
    <div class="h-full m-16" :class="{'text-center max-w-[80vw]': centerContent}">
        <h1 class="themeFont text-white text-9xl mb-12">{{ title }}</h1>
        <p class="text-white font-semibold mx-auto text-6xl whitespace-pre-wrap">
            {{ text }}
        </p>
    </div>
</template>

<style scoped>

</style>
