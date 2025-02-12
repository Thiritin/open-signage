<script setup>
import CurrentTime from "@/Components/CurrentTime.vue";

const props = defineProps({
    showTime: {
        type: String,
        default: "true"
    },
})
import Logo from "@/Projects/FURCIETY2025/Pages/Logo.vue";
</script>

<template>
    <div class="flex justify-center items-center flex-col">
        <Logo></Logo>
        <CurrentTime class="text-white themeFont text-9xl uppercase text-center"></CurrentTime>
    </div>
</template>

<style scoped>

</style>
