<script setup>
import {computed} from "vue";

defineProps({
    artworks: {
        type: Array,
        required: false
    },
    screen: {
        type: Object,
        required: true
    },
    playSpeed: {
        type: Number,
        required: false,
        default: 1600+5000
    },
    transition: {
        type: Number,
        required: false,
        default: 1600
    },
});

const screenType = computed(() => {
    if (screen.orientation === "normal" || screen.orientation === "inverted") {
        return "horizontal"
    } else {
        return "vertical"
    }
});

import {Hooper, Slide} from 'hooper-vue3';
import 'hooper-vue3/dist/hooper.css';
</script>

<template>
    <div class="h-screen">
        <Hooper :mouse-drag="false" :keys-control="false" class="h-screen w-full" :transition="transition" :wheel-control="false" :center-mode="true"  :auto-play="true" :itemsToShow="1" :pagination="false">
            <Slide v-for="slide in artworks" :duration="playSpeed" :index="slide.id" :key="slide.id">
                <div>
                    <img :src="slide[screenType]" :alt="slide.name" class="object-cover h-full w-full">
                </div>
            </Slide>
        </Hooper>
    </div>
</template>

<style scoped>
.hooper {
    height: 100%!important;
}
</style>
