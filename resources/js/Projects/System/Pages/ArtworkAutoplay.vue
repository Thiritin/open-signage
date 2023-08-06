<script setup>
import {computed, onMounted, ref} from "vue";

const props = defineProps({
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

// If screen orientation changes, we need to recompute the screenType
screen.orientation.onchange = () => {
    console.log("Orientation changed");
    console.log(screen.orientation);
}

const screenOrientation = ref("vertical");

onMounted(() => {
    setScreenOrientation();
    window.addEventListener(
        "orientationchange",
        handleOrientationChange
    );
});

const setScreenOrientation = () => {
    if (props.screen.orientation === "normal" || props.screen.orientation === "inverted") {
        screenOrientation.value = "horizontal"
    }
}

const handleOrientationChange = () => {
    if (screen.orientation.angle === 90) {
        screenOrientation.value = "vertical"
    } else {
        screenOrientation.value = "horizontal"
    }
}

const artworksFilteredWithoutMissingOrientation = computed(() => {
    let filteredArt = props.artworks.filter(artwork => {
        return artwork[screenOrientation.value] !== null
    })
    // Randomize the order of the artworks
    return filteredArt.sort(() => Math.random() - 0.5);
});

import {Hooper, Slide} from 'hooper-vue3';
import 'hooper-vue3/dist/hooper.css';
</script>

<template>
    <div class="h-screen">
        <Hooper :mouse-drag="false" :hover-pause="false" :keys-control="false" class="h-screen w-full" :transition="transition" :wheel-control="false" :center-mode="false"  :auto-play="true" :itemsToShow="1" :pagination="false">
            <Slide v-for="slide in artworksFilteredWithoutMissingOrientation" :duration="playSpeed" :index="slide.id" :key="slide.id">
                <div>
                    <img :src="slide[screenOrientation]" :alt="slide.name" class="object-cover h-full w-full">
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
