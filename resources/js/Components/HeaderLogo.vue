<template>
    <div class="text-white flex flex-col xl:flex-row items-center gap-16 border-b-[15px] border-secondary p-8 drop-shadow-2xl"
         :class="{'justify-between': title,' justify-center': title.length === 0 }">
        <div class="flex gap-6 items-center">
            <img class="w-[12rem] xl:w-[15vh]" src="../../assets/img/logo.png">
            <div>
                <h1 class="text-white themeFont text-6xl xl:text-8xl uppercase">Wild Times 2023</h1>
                <h2 class="text-accent text-6xl themeFont uppercase">Heroes and Villains</h2>
            </div>
        </div>
        <div>
            <PageTitle v-if="title.length !== 0">{{ title }}</PageTitle>
            <CurrentTime class="xl:text-right text-7xl themeFont text-center"></CurrentTime>
        </div>
    </div>
</template>
<script setup>
import PageTitle from "@/Components/PageTitle.vue";
import {ref, watch} from "vue";
import CurrentTime from "@/Components/CurrentTime.vue";

defineProps({
    title: {
        type: String,
        default: ''
    }
})




</script>
