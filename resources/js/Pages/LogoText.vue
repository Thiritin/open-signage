<script setup>
import CurrentTime from "@/Components/CurrentTime.vue";

const props = defineProps({
    showTime: {
        type: String,
        default: "true"
    },
})
import Logo from "@/Pages/Logo.vue";
</script>

<template>
    <div class="flex justify-center items-center flex-col">
        <Logo></Logo>
        <h1 class="text-white themeFont text-9xl uppercase text-center mt-16">Wild Times 2023</h1>
        <h2 class="text-accent text-7xl themeFont uppercase text-center">Heroes and Villains</h2>
        <CurrentTime class="text-white themeFont text-9xl uppercase text-center mt-16"></CurrentTime>
    </div>
</template>

<style scoped>

</style>
