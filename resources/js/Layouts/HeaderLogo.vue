<template>
    <div class="text-white flex flex-col xl:flex-row items-center gap-16"
         :class="{'justify-between': title,' justify-center': title.length === 0 }">
        <div class="flex gap-6 items-center">
            <img class="w-[12rem] xl:w-[15vh]" src="../../assets/img/logo.png">
            <div>
                <h1 class="text-white themeFont text-6xl xl:text-8xl uppercase">Wild Times 2023</h1>
                <h2 class="text-accent text-6xl themeFont uppercase">Heroes and Villains</h2>
            </div>
        </div>
        <PageTitle v-if="title.length !== 0">{{ title }}</PageTitle>
    </div>
</template>
<script setup>
import PageTitle from "@/Components/PageTitle.vue";

defineProps(['title'])
</script>
<style>
@font-face {
    font-family: 'primaryThemeFont';
    font-style: normal;
    src: local('Hansief'), url(../../assets/fonts/Hansief.otf) format('truetype');
}

.themeFont {
    font-family: 'primaryThemeFont', sans-serif;
}
</style>
