<template>
    <div class="text-white flex flex-row items-center gap-16 border-b-[15px] border-secondary p-8 drop-shadow-2xl"
         :class="{'justify-between': title,' justify-center': title.length === 0 }">
        <div class="flex gap-6 items-center">
            <img class="w-[12rem] xl:w-[10vw]" src="../Assets/images/logo.png">
            <div class="flex flex-col gap-0">
                <div class="text-white themeFont text-5xl text-[5vw] uppercase">Wild Times 2023</div>
                <div class="text-accent themeFont text-5xl text-[3.4vw] uppercase">Heroes and Villains</div>
            </div>
        </div>
        <div class="flex flex-col">
            <PageTitle class="text-5xl text-[5vw]" v-if="title.length !== 0">{{ title }}</PageTitle>
            <CurrentTime class="xl:text-right text-5xl text-[4vw] themeFont text-center"></CurrentTime>
        </div>
    </div>
</template>
<script setup>
import {ref, watch} from "vue";
import CurrentTime from "@/Components/CurrentTime.vue";
import PageTitle from "@/Projects/WT23/Components/PageTitle.vue";

defineProps({
    title: {
        type: String,
        default: ''
    }
})




</script>
